<?php

namespace STS\Record;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\HigherOrderCollectionProxy;
use Illuminate\Support\Str;

class Record extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->wrapArraysAsRecords();
    }

    public static function make($items = []): static
    {
        return new static($items);
    }

    protected function wrapArraysAsRecords()
    {
        $this->items = array_map(function ($item) {
            return is_array($item) ? new static($item) : $item;
        }, $this->items);
    }

    public function __get($key): mixed
    {
        if (property_exists(static::class, 'proxies') && in_array($key, static::$proxies)) {
            return new HigherOrderCollectionProxy($this, $key);
        }

        return $this->getAttribute($key);
    }

    public function getAttribute($key): mixed
    {
        $fallback = $key !== Str::snake($key) ? $this->get(Str::snake($key)) : null;

        $value = $this->get($key, $fallback);

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }

        return $value;
    }

    public function hasGetMutator($key): bool
    {
        return method_exists($this, 'get'.Str::studly($key).'Attribute');
    }

    protected function mutateAttribute($key, $value): mixed
    {
        return $this->{'get'.Str::studly($key).'Attribute'}($value);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function setAttribute($key, $value): static
    {
        if ($this->hasSetMutator($key)) {
            $method = 'set'.Str::studly($key).'Attribute';

            return $this->{$method}($value);
        }

        return $this->put($key, $value);
    }

    public function hasSetMutator($key): bool
    {
        return method_exists($this, 'set'.Str::studly($key).'Attribute');
    }

    public function toArray()
    {
        $recurse = function ($value) use (&$recurse) {
            return match(true) {
                $value instanceof self => $value->toArray(),
                $value instanceof Arrayable => $value->toArray(),
                is_array($value) => array_map($recurse, $value),
                default => $value,
            };
        };

        return array_map($recurse, $this->items);
    }
}
