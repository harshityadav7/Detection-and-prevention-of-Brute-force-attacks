<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\IpTracker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;



class ipTrackering
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        Log::info($ip);
        $now = Carbon::now();
        
        $user = IpTracker::where('ipaddress',$ip)->latest()->first();
       
        if($user){
            if ($user->times > 3 && $user->expire_at != null) {
                $expiredAt = Carbon::parse($user->expire_at);
                $currentTime = Carbon::now();
                
                if ($expiredAt->gt($currentTime)) {
                    // $expiredAt is greater than $currentTime
                    // This means the IP address is still within the expiry period
                    abort(401);
                } else {
                    $user->update(['times' => 0,'expire_at' => 'null']);
                }
            }
        }

        return $next($request);
    }
}
