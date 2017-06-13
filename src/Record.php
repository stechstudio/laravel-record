<?php
namespace STS\Record;

use Illuminate\Support\Collection;
use Illuminate\Support\HigherOrderCollectionProxy;
use Illuminate\Support\Str;

class Record extends Collection
{
    /**
     * If we don't have a proxy for this key, see if it exists in our items array.
     *
     * @param string $key
     *
     * @return HigherOrderCollectionProxy|mixed|null
     */
    public function __get($key)
    {
        if (property_exists(static::class, 'proxies') && in_array($key, static::$proxies)) {
            return new HigherOrderCollectionProxy($this, $key);
        }

        return $this->getAttribute($key);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = $this->get($key);

        if($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        return is_array($value)
            ? new static($value)
            : $value;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasGetMutator($key)
    {
        return method_exists($this, 'get'.Str::studly($key).'Attribute');
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        return $this->{'get'.Str::studly($key).'Attribute'}($value);
    }
}
