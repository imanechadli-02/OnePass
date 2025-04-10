<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListeNoireIp extends Model {
    use HasFactory;

    protected $table = 'liste_noire_ips';

    protected $fillable = ['adresse_ip'];

    public static function isBlacklisted($ip) {
        return self::where('adresse_ip', $ip)->exists();
    }
}
