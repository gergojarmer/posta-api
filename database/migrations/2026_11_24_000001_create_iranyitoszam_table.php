<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Új tábla a meglévő "telepulesek" adatbázisba:
 * melyik városhoz melyik irányítószám(ok) tartoznak.
 *
 * A megye, varos és varostipus táblák már léteznek (phpMyAdmin-ból),
 * ezeket NEM migráció hozza létre.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iranyitoszam', function (Blueprint $table) {
            $table->id();
            $table->char('irsz', 4);        // pl. "4024"

            // A varos.id oszlop sima int(11), ezért itt is integer() kell.
            // (A foreignId() más típust csinálna, és a MySQL nem engedné összekötni.)
            $table->integer('varosid');
            $table->foreign('varosid')->references('id')->on('varos');

            // Ugyanaz az irányítószám ugyanannál a városnál csak egyszer szerepelhet
            $table->unique(['irsz', 'varosid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iranyitoszam');
    }
};
