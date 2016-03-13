<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

class FrameworkHealthCheck extends AbstractHealthCheck
{

    public function check()
    {
        return true; // if we get here, the framework is up
    }

}
