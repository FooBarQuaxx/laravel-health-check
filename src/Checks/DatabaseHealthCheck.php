<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

use DB;
use Exception;

class DatabaseHealthCheck extends AbstractHealthCheck
{

    public function check() 
    {
        try {
            if ( $this->instanceName == 'default' ) {
                return false != DB::select('SELECT 1');
            } else {
                return false != DB::connection( $this->instanceName )->select('SELECT 1');
            }
        } catch( Exception $e ) {
            return false;
        }
    }

}
