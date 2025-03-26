<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListeNoireIp;
use App\Models\ListeBlancheIp;

class IpManagementController extends Controller {


    public function addToBlacklist(Request $request)
    {
        try {
            $request->validate([
                'adresse_ip' => 'required|ip',
            ]);
    
            $ip = $request->input('adresse_ip');
            if (ListeNoireIp::where('adresse_ip', $ip)->exists()) {
                return response()->json(['message' => 'IP is already in the blacklist'], 400);
            }
            ListeNoireIp::create(['adresse_ip' => $ip]);
    
            return response()->json(['message' => 'IP added to blacklist'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'details' => $e->getMessage()
            ], 500);
        }
    }
    

    public function removeFromBlacklist($ip) {
        ListeNoireIp::where('adresse_ip', $ip)->delete();
        return response()->json(['message' => 'IP removed from blacklist']);
    }

    public function listBlacklist() {
        return response()->json(ListeNoireIp::all());
    }


    
    public function addToWhitelist(Request $request)
    {
        try {
           
            $ip = $request->ip(); 
            $request->validate([
                'adresse_ip' => 'required|ip',
            ]);

            if (ListeBlancheIp::where('adresse_ip', $ip)->exists()) {
                return response()->json(['message' => 'IP is already in the whitelist'], 400);
            }

            ListeBlancheIp::create(['adresse_ip' => $ip]);
    
            return response()->json(['message' => 'IP added to whitelist'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    public function removeFromWhitelist($ip) {
        ListeBlancheIp::where('adresse_ip', $ip)->delete();
        return response()->json(['message' => 'IP removed from whitelist']);
    }

    public function listWhitelist() {
        return response()->json(ListeBlancheIp::all());
    }
}
