<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ListeNoireIp;
use App\Models\ListeBlancheIp;
use Symfony\Component\HttpFoundation\Response;

class IpRestrictionMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $ip = $request->ip();

        // dd($request->ip()); 
        if (ListeNoireIp::where('adresse_ip', $ip)->exists()) {
            return response()->json(['message' => 'Access denied: Your IP is blacklisted'], 403);
        }
        if (ListeBlancheIp::exists() && !ListeBlancheIp::where('adresse_ip', $ip)->exists()) {
            return response()->json(['message' => 'Access denied: Your IP is not whitelisted'], 403);
        }

        return $next($request);
    }
}
