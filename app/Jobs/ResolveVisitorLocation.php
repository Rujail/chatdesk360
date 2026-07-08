<?php

namespace App\Jobs;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResolveVisitorLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 10;
    public $backoff = 5; // Wait 5 seconds between retries

    protected $visitorId;
    protected $ip;

    public function __construct($visitorId, $ip)
    {
        $this->visitorId = $visitorId;
        $this->ip = $ip;
    }

    public function handle()
    {
        // \Log::info("[Location] Job started for IP: {$this->ip}");

        if ($this->isPrivateIp($this->ip)) {
            // \Log::info("[Location] Private IP skipped: {$this->ip}");
            return;
        }

        $url = "http://ip-api.com/json/{$this->ip}?fields=status,country,countryCode,regionName,city,lat,lon";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_USERAGENT      => 'ChatDesk360/1.0',
        ]);

        $res = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err || !$res) {
            throw new \Exception('cURL error: ' . ($err ?: 'Empty response'));
        }

        $data = json_decode($res, true);

        if (($data['status'] ?? '') !== 'success' || empty($data['country'])) {
            throw new \Exception('API failure: ' . $res);
        }

        Visitor::where('visitor_id', $this->visitorId)->update([
            'country' => $data['country']     ?? null,
            'countryCode' => $data['countryCode']     ?? null,
            'state'   => $data['regionName'] ?? null,
            'city'    => $data['city']        ?? null,
            'lat'     => $data['lat']         ?? null,
            'lon'     => $data['lon']         ?? null,
        ]);

        // \Log::info("[Location] Updated visitor {$this->visitorId}: {$data['city']}, {$data['country']}");
    }

    private function isPrivateIp(string $ip): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1']) ||
            str_starts_with($ip, '192.168.') ||
            str_starts_with($ip, '10.')) {
            return true;
        }

        if (str_starts_with($ip, '172.')) {
            $long = ip2long($ip);
            return $long >= ip2long('172.16.0.0') && $long <= ip2long('172.31.255.255');
        }

        return false;
    }
}