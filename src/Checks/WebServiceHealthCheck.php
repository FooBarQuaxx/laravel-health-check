<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

use GuzzleHttp\Client as HttpClient;
use Exception;

class WebServiceHealthCheck extends AbstractHealthCheck
{

    protected function responseIsSuccess($response)
    {
        return $response->getStatusCode() < 400 && !empty($response->getBody());
    }

    public function check()
    {
        try {
            $httpClient = new HttpClient();
            $response = $httpClient->get($this->config['url']);
            if ($handler = array_get($this->config, 'check') && is_callable($handler)) {
                return $handler($response);
            } else {
                // by default just check that the response is not empty
                return $this->responseIsSuccess($response);
            }
        } catch( Exception $e ) {
            return false;
        }
    }

}
