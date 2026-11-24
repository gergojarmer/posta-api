<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Varos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Teszt felhasználó a bejelentkezéshez (token kéréséhez)
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password']
        );

        // 2) Városnév -> id "szótár" a meglévő varos táblából
        //    pl. ['Aba' => 1, 'Abádszalók' => 2, ...]
        $varosIdk = Varos::pluck('id', 'vnev');

        // 3) A CSV beolvasása (a tanári PDF-ből: irsz;varos)
        $fajl = fopen(database_path('data/iranyitoszamok.csv'), 'r');
        fgetcsv($fajl, 0, ';');                 // fejléc sor átugrása

        $sorok = [];
        while (($sor = fgetcsv($fajl, 0, ';')) !== false) {
            [$irsz, $varosNev] = $sor;

            $sorok[] = [
                'irsz'    => $irsz,
                'varosid' => $varosIdk[$varosNev],   // a névből id lesz
            ];
        }
        fclose($fajl);

        // 4) Beszúrás egyszerre. Az insertOrIgnore kihagyja a már meglévő sorokat,
        //    így a seeder többször is futtatható hiba nélkül.
        DB::table('iranyitoszam')->insertOrIgnore($sorok);
    }
}
