<?php 

namespace NpmWeb\LaravelHealthCheck\Facades;

use Illuminate\Support\Facades\Facade;

class HealthCheck extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'health-checks';
    }
}
