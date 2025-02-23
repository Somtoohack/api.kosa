<?php

namespace App\Services\KosaBaseMicroservices;

use Exception;
use Illuminate\Support\Facades\Http;

class KosaMicroservice
{
    protected $baseUrl;
    protected $integrationTargetKey;
    protected $integrationSourceKey;

    public function __construct()
    {
        $this->baseUrl = config('services.kosa_microservice.base_url');
        $this->integrationTargetKey = config('services.kosa_microservice.integration_target_key');
        $this->integrationSourceKey = config('services.kosa_microservice.integration_source_key');
    }

    protected function makeRequest($method, $endpoint, $data = [])
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
            'KOSA-MCS-KEY' => $this->integrationTargetKey,
            'KOSA-CORE-KEY' => $this->integrationSourceKey,
        ])
            ->withoutVerifying()
            ->$method("{$this->baseUrl}/{$endpoint}", $data);

        if ($response->failed()) {
            throw new Exception('Request to KosaMicroservice failed: ' . $response->body());
        }

        return $response->json();
    }
}