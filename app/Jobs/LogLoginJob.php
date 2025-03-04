<?php

// Create a new job class
namespace App\Jobs;

use App\Models\LoginLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogLoginJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, InteractsWithQueue;

    private $loggable;
    private $email;
    private $deviceId;
    private $deviceName;
    private $ipAddress;
    private $userAgent;

    public function __construct($loggable, $email, $deviceId, $deviceName, $ipAddress, $userAgent)
    {
        $this->loggable = $loggable;
        $this->email = $email;
        $this->deviceId = $deviceId;
        $this->deviceName = $deviceName;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    public function handle()
    {
        $loginLog = new LoginLog();
        $loginLog->loggable_id = $this->loggable->id;
        $loginLog->loggable_type = get_class($this->loggable);
        $loginLog->email = $this->email;
        $loginLog->device_id = $this->deviceId;
        $loginLog->device_name = $this->deviceName;
        $loginLog->ip_address = $this->ipAddress;
        $loginLog->user_agent = $this->userAgent;
        $loginLog->login_at = now();

        // Get additional details from IP address
        $ipDetails = getIpDetails($this->ipAddress);

        // Log::info('IP Details:', $ipDetails);

        // dump($ipDetails);

        if (!is_null($ipDetails)) {
            $loginLog->country = $ipDetails['country'];
            $loginLog->region = $ipDetails['region'];
            $loginLog->city = $ipDetails['city'];
            $loginLog->latitude = $ipDetails['latitude'];
            $loginLog->longitude = $ipDetails['longitude'];
        }

        $loginLog->save();
    }
}
