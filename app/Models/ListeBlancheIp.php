<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeBlancheIp extends Model {
    use HasFactory;

    protected $table = 'liste_blanche_ips';

    protected $fillable = ['adresse_ip'];

    public static function isWhitelisted($ip) {
        return self::where('adresse_ip', $ip)->exists();
    }
}

