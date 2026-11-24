<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Varos extends Model
{
    protected $table = 'varos';
    public $timestamps = false;

    // A város egy megyéhez tartozik (varos.megyeid -> megye.id)
    public function megye(): BelongsTo
    {
        return $this->belongsTo(Megye::class, 'megyeid');
    }

    // A város típusa, pl. "megyeszékhely megyei jogú város" (varos.vtipid -> varostipus.id)
    public function tipus(): BelongsTo
    {
        return $this->belongsTo(Varostipus::class, 'vtipid');
    }

    // Egy városnak több irányítószáma is lehet (iranyitoszam.varosid -> varos.id)
    public function iranyitoszamok(): HasMany
    {
        return $this->hasMany(Iranyitoszam::class, 'varosid');
    }

    // KERESÉS a város nevében: Varos::kereses('bala')
    public function scopeKereses(Builder $query, ?string $szo): Builder
    {
        return $szo ? $query->where('vnev', 'like', '%' . $szo . '%') : $query;
    }

    // SZŰRÉS megyére: Varos::megyeben(10)
    public function scopeMegyeben(Builder $query, $megyeId): Builder
    {
        return $megyeId ? $query->where('megyeid', $megyeId) : $query;
    }
}
