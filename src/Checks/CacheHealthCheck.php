<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

use Exception;
use Cache;
use Illuminate\Support\Str;

class CacheHealthCheck extends AbstractHealthCheck
{
    public function configure($config=null)
    {
        parent::configure($config);
    }

    protected function getCacheStore()
    {
        if(is_string($this->config)) {
            return Cache::store($this->config);
        } elseif(is_array($this->config)) {
            // TODO: add custom cache store configuration.
        } else {
            return null;
        }
    }

    public function check()
    {
        try {
            $store = $this->getCacheStore();
            $value = Str::quickRandom();
            $store->put('laravel-health-check', $value, 1);
            return $store->get('laravel-health-check') === $value;
        } catch(Exception $e) {
            return false;
        }
    }
}
