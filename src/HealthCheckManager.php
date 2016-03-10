<?php

namespace NpmWeb\LaravelHealthCheck;

use Illuminate\Support\Manager;
use Exception;

class HealthCheckManager extends Manager
{

    static $packageName = 'laravel-health-check';
    private $config;
    private $checks = null;

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->config = config( self::$packageName . '.checks');
    }

    /**
     * instantiates each check defined in the config file
     *
     * @return array of HealthCheckInterface instances
     */
    protected function configureChecks()
    {
        if (is_null($this->checks)) {
            $this->checks = [];
            foreach( $this->config as $driver => $checkConfig ) {
                // check if multiple or just one
                if (is_array($checkConfig)) {
                    foreach( $checkConfig as $key => $config ) {
                        $instance = $this->createInstance( $driver, $config );
                        $instance->setInstanceName(is_string($key) ? $key : $config);
                        $this->checks[] = $instance;
                    }
                } else {
                    $instance = $this->createInstance( $driver, $checkConfig );
                    $this->checks[] = $instance;
                }
            }
        }
    }

    protected function isClassIntantiable($class)
    {
        try {
            $reflectionClass = new \ReflectionClass($class);
            return $reflectionClass->IsInstantiable();
        } catch(Exception $e) {
            return false;
        }
    }

    protected function createDriver($driver)
    {
        $namespace = 'NpmWeb\LaravelHealthCheck\Checks';
        $class = $namespace . '\\' . ucfirst($driver).'HealthCheck';

        // We'll check to see if a creator method exists for the given driver. If not we
        // will check for a custom driver creator, which allows developers to create
        // drivers using their own customized driver creator Closure to create it.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } elseif (class_exists($class) && $this->isClassIntantiable($class)) {
            return $this->app->make($class);
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Create a new instance of the driver
     *
     * @param  string  $driver
     * @param  mixed   $config
     * @return mixed
     */
    public function createInstance($driver, $config = null)
    {
        // use createDriver() because driver() only creates one instance
        $reference = $this->createDriver($driver);

        // any other setup needed
        if ($config) {
            $reference->configure($config);
        }

        return $reference;
    }

    /**
     * Get the default authentication driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $driver = $this->config[self::$packageName.'::driver'];
        return $driver;
    }

    /**
     * Set the default authentication driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']->set(self::$packageName.'::driver', $name);
    }

    public function getChecks()
    {
        if(is_null($this->checks)) {
            $this->configureChecks();
        }
        return $this->checks;
    }

    public function getHealthCheckByName($name)
    {
        foreach( $this->getChecks() as $check ) {
            if( $name == $check->getName() ) {
                return $check;
            }
        }
        return null;
    }

    public function checkAll()
    {
        $res = [];
        foreach ($this->getChecks() as $check) {
            $res[$check->getName()] = $check->check(); 
        }
        return $res;
    }

    public function checkOneByName($checkName)
    {
        $check = $this->getHealthCheckByName($checkName);

        if(!$check) {
            throw new InvalidArgumentException("Driver [$checkName] not supported.");
        }

        return $check->check();
    }

    public function __invoke($checkName=null)
    {
        return $checkName ? $this->checkOneByName($checkName) : $this->checkAll();
    }
}
