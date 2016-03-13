<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

abstract class AbstractHealthCheck implements HealthCheckInterface
{

    protected $instanceName = 'default';
    protected $config;
    protected $type = null;

    public function getType()
    {
        if($this->type) {
            return $this->type;
        }

        $shortname = (new \ReflectionClass($this))->getShortName();
        $pattern = '/^(?P<type>\w+)HealthCheck$/';
        if(preg_match($pattern, $shortname, $matches)) {
            return strtolower(array_get($matches, 'type'));
        }
        return null;
    }
    /**
     * only way to pass in the configuration easily for multiple instances
     * @param $config  mixed configuration specific to the provider
     */
    public function configure( $config )
    {
        $this->config = $config;
    }

    public function getInstanceName()
    {
        return $this->instanceName;
    }

    public function setInstanceName( $name )
    {
        return $this->instanceName = $name;
    }

    public function getName()
    {
        return $this->getType() . (
            ( $this->instanceName == 'default' ) ? '' : '.' . $this->instanceName
        );
    }

}
