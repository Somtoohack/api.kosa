<?php
namespace App\Services;

use GuzzleHttp\Client;

class ExchangeRateService
{
    protected $client;
    protected $apiUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = config('services.exchange_rate.api_url');
    }

    public function getExchangeRate($baseCurrency, $targetCurrency)
    {
        $response = $this->client->get("{$this->apiUrl}/latest", [
            'query' => ['base' => $baseCurrency, 'symbols' => $targetCurrency],
        ]);

        $data = json_decode($response->getBody(), true);

        return $data['rates'][$targetCurrency] ?? null;
    }

    public function convertCurrency($amount, $baseCurrency, $targetCurrency)
    {
        $rate = $this->getExchangeRate($baseCurrency, $targetCurrency);

        if ($rate === null) {
            throw new \Exception("Exchange rate not found for {$baseCurrency} to {$targetCurrency}");
        }

        return $amount * $rate;
    }
}
