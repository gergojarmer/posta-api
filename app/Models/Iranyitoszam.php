<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Iranyitoszam extends Model
{
    protected $table = 'iranyitoszam';
    public $timestamps = false;

    // Ezeket a mezőket lehet az API-n keresztül kitölteni (create / update)
    protected $fillable = ['irsz', 'varosid'];

    // Az irányítószám egy városhoz tartozik
    public function varos(): BelongsTo
    {
        return $this->belongsTo(Varos::class, 'varosid');
    }

    /**
     * KERESÉS: az irányítószám ELEJE vagy a város nevének BÁRMELY része.
     *   "40"   -> 4000, 4024 ... (Debrecen)
     *   "deb"  -> Debrecen összes irányítószáma
     */
    public function scopeKereses(Builder $query, ?string $szo): Builder
    {
        if (!$szo) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($szo) {
            $q->where('irsz', 'like', $szo . '%')
              ->orWhereHas('varos', fn (Builder $v) => $v->where('vnev', 'like', '%' . $szo . '%'));
        });
    }

    /**
     * SZŰRÉS megyére. Az irányítószám táblában nincs megye,
     * ezért a városon keresztül nézzük: iranyitoszam -> varos -> megyeid
     */
    public function scopeMegyeben(Builder $query, $megyeId): Builder
    {
        return $megyeId
            ? $query->whereHas('varos', fn (Builder $v) => $v->where('megyeid', $megyeId))
            : $query;
    }
}
