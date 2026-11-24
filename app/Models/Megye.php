<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Megye extends Model
{
    protected $table = 'megye';      // a tábla neve az adatbázisban
    public $timestamps = false;      // nincs created_at / updated_at oszlop

    // Egy megyében sok város van (varos.megyeid -> megye.id)
    public function varosok(): HasMany
    {
        return $this->hasMany(Varos::class, 'megyeid');
    }
}
