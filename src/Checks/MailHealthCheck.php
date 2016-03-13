<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

use Exception;
use Mail;

class MailHealthCheck extends AbstractHealthCheck
{

    protected $type = 'mail';
    protected $emailAddr;
    protected $method;

    public function configure( $config = null )
    {
        parent::configure($config);
        $this->emailAddr = $config['email'];
        $this->method = array_get($config, 'method', 'send');
    }

    public function check()
    {
        try {
            $method = $this->method;
            Mail::$method('laravel-health-check::emails.test', array(), function($message) {
                $message
                    ->from($this->emailAddr)
                    ->to($this->emailAddr)
                    ->subject('Health Check');
            });
            return true;
        } catch( Exception $e ) {
            \Log::debug("Exception on " . __METHOD__ . ": " . $e->getMessage());
            return false;
        }
    }

}
