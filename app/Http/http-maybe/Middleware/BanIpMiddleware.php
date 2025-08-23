<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BanIpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bannedIps = config('banned_ips.ips', []);
        $clientIp = $request->ip();
        
        \Log::info('IP Check', ['client_ip' => $clientIp, 'banned_ips' => $bannedIps]);
        
        if (in_array($clientIp, $bannedIps)) {
            abort(403, 'Your IP address has been banned.');
        }
        
        // Check country based on IP
        try {
            $allowedCountries = config('banned_ips.allowed_countries', []);
            if (!empty($allowedCountries)) {
                $reader = new \GeoIp2\Database\Reader(storage_path('app/geoip/GeoLite2-Country.mmdb'));
                $record = $reader->country($clientIp);
                $countryCode = $record->country->isoCode;
                
                \Log::info('Country Check', ['client_ip' => $clientIp, 'country_code' => $countryCode, 'allowed_countries' => $allowedCountries]);
                
                if (!in_array($countryCode, $allowedCountries)) {
                    abort(403, 'Access from your country is restricted.');
                }
            }
        } catch (\Exception $e) {
            \Log::error('GeoIP Check Failed', ['error' => $e->getMessage(), 'client_ip' => $clientIp]);
        }

        return $next($request);
    }
}
