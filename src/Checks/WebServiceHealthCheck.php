<?php

namespace NpmWeb\LaravelHealthCheck\Checks;

use GuzzleHttp\Client as HttpClient;
use Log;
use Exception;

class WebServiceHealthCheck extends AbstractHealthCheck
{

    public function getType()
    {
        return 'webservice';
    }

    protected function responseIsSuccess($response)
    {
        return $response->getStatusCode() < 400 && !empty($response->getBody());
    }

    public function check()
    {
        try {
            $httpClient = new HttpClient();
            Log::debug(__METHOD__.':: checking URL '.$this->config['url']);
            $response = $httpClient->get($this->config['url']);
            if ($handler = array_get('check', $this->config) && is_callable($hander)) {
                return $handler($response);
            } else {
                // by default just check that the response is not empty
                return $this->responseIsSuccess($response);
            }
        } catch( Exception $e ) {
            Log::error('Exception doing web service check: '.$e->getMessage());
            return false;
        }
    }

}
