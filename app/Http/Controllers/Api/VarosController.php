<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Varos;
use Illuminate\Http\Request;

class VarosController extends Controller
{
    /**
     * @api {get} /api/varosok Városok listája (keresés, szűrés)
     * @apiName VarosokLista
     * @apiGroup 3_Varosok
     * @apiVersion 1.0.0
     *
     * @apiQuery {String} [kereses] Keresés a város nevében (pl. "bala").
     * @apiQuery {Number} [megye]   Szűrés megye azonosítóra (pl. 10 = Hajdú-Bihar).
     *
     * @apiExample {url} Példa:
     *     /api/varosok?kereses=bala&megye=20
     *
     * @apiSuccessExample {json} Siker (200):
     *     [
     *       {
     *         "id": 21, "vnev": "Balatonfüred", "vtipid": 4, "megyeid": 20,
     *         "jaras": "Balatonfüredi", "kisterseg": "Balatonfüredi",
     *         "nepesseg": 13532, "terulet": 46.45,
     *         "megye": { "id": 20, "mnev": "Veszprém" },
     *         "tipus": { "id": 4, "vtip": "járásszékhely város" }
     *       }
     *     ]
     */
    public function index(Request $request)
    {
        return Varos::with(['megye', 'tipus'])
            ->kereses($request->query('kereses'))
            ->megyeben($request->query('megye'))
            ->orderBy('vnev')
            ->get();
    }

    /**
     * @api {get} /api/varosok/:id Egy város az irányítószámaival
     * @apiName VarosEgy
     * @apiGroup 3_Varosok
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Város azonosító.
     *
     * @apiSuccessExample {json} Siker (200):
     *     {
     *       "id": 62, "vnev": "Debrecen", "vtipid": 2, "megyeid": 10, ...,
     *       "megye": { "id": 10, "mnev": "Hajdú-Bihar" },
     *       "tipus": { "id": 2, "vtip": "megyeszékhely megyei jogú város" },
     *       "iranyitoszamok": [
     *         { "id": 248, "irsz": "4000", "varosid": 62 },
     *         { "id": 249, "irsz": "4002", "varosid": 62 }
     *       ]
     *     }
     *
     * @apiError (404) NotFound Nincs ilyen város.
     */
    public function show(Varos $varos)
    {
        return $varos->load(['megye', 'tipus', 'iranyitoszamok']);
    }
}
