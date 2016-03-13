<?php

namespace NpmWeb\LaravelHealthCheck\Controllers;

use Illuminate\Routing\Controller;
use Response;

class HealthCheckController extends Controller
{

    protected $healthChecks;

    public function __construct()
    {
        $this->healthChecks = app('health-checks');
    }

    public function index()
    {
        $checkNames = array_map( function($check) { return $check->getName(); }, $this->healthChecks->getChecks() );
        return Response::json([
            'status' => 'success',
            'checks' => $checkNames,
        ]);
    }

    public function show($checkName)
    {
        if($checkName != 'all' && !$this->healthChecks->hasCheck($checkName)) {
            return Response::json([
                'status' => false,
                'message' => "check [${checkName}] not found",
            ], 404);
        }

        if($checkName == 'all') {
            $result = $this->healthChecks->__invoke();
        } else {
            $result = $this->healthChecks->__invoke($checkName);
        }
        return Response::json([
            'status' => $result,
        ]);
    }

}
