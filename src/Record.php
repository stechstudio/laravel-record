<?php
namespace STS\Record;

use Illuminate\Support\Collection;
use Illuminate\Support\HigherOrderCollectionProxy;
use Illuminate\Support\Str;

class Record extends Collection
{
    /**
     * Record constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->wrapArraysAsRecords();
    }

    /**
     * Make sure any array elements are setup as new Records
     */
    protected function wrapArraysAsRecords()
    {
        $this->items = array_map(function($item) {
            return is_array($item) ? new static($item) : $item;
        }, $this->items);
    }

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

        return $value;
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

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set'.Str::studly($key).'Attribute';

            return $this->{$method}($value);
        }

        return $this->put($key, $value);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasSetMutator($key)
    {
        return method_exists($this, 'set'.Str::studly($key).'Attribute');
    }
}
