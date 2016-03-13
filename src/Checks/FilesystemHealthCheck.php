<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

use Exception;
use Storage;

/**
 * configuration is the disk name(s) configured in filesystems config file
 */
class FilesystemHealthCheck extends AbstractHealthCheck
{

    public function check()
    {
        try {
            $files = Storage::disk( $this->getInstanceName() )->files() + Storage::disk( $this->getInstanceName() )->directories();
            return ( $files !== false && !empty($files));
        } catch( Exception $e ) {
            return false;
        }
    }

}
