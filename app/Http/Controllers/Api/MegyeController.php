<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Megye;

class MegyeController extends Controller
{
    /**
     * @api {get} /api/megyek Megyék listája
     * @apiName MegyekLista
     * @apiGroup 2_Megyek
     * @apiVersion 1.0.0
     *
     * @apiSuccess {Object[]} -                Megyék tömbje, név szerint rendezve.
     * @apiSuccess {Number}   -.id             Megye azonosító (ezt kell a ?megye= szűrőbe írni).
     * @apiSuccess {String}   -.mnev           Megye neve.
     * @apiSuccess {Number}   -.varosok_count  Városok száma a megyében.
     *
     * @apiSuccessExample {json} Siker (200):
     *     [
     *       { "id": 1, "mnev": "Bács-Kiskun", "varosok_count": 22 },
     *       { "id": 2, "mnev": "Baranya", "varosok_count": 14 }
     *     ]
     */
    public function index()
    {
        return Megye::withCount('varosok')->orderBy('mnev')->get();
    }

    /**
     * @api {get} /api/megyek/:id Egy megye a városaival
     * @apiName MegyeEgy
     * @apiGroup 2_Megyek
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Megye azonosító.
     *
     * @apiSuccessExample {json} Siker (200):
     *     {
     *       "id": 10, "mnev": "Hajdú-Bihar",
     *       "varosok": [
     *         { "id": 26, "vnev": "Balmazújváros", "vtipid": 4, "megyeid": 10, ... }
     *       ]
     *     }
     *
     * @apiError (404) NotFound Nincs ilyen megye.
     */
    public function show(Megye $megye)
    {
        return $megye->load('varosok');
    }
}
