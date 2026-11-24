<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IranyitoszamRequest;
use App\Models\Iranyitoszam;
use Illuminate\Http\Request;

/**
 * @apiDefine Vedett
 * @apiHeader {String} Authorization Bearer &lt;token&gt; (a /api/login adja vissza)
 * @apiHeader {String} Accept        application/json
 * @apiError (401) Unauthenticated Hiányzó vagy érvénytelen token.
 * @apiErrorExample {json} Hiba (401):
 *     { "message": "Unauthenticated." }
 */

/**
 * @apiDefine IrszTorzs
 * @apiBody {String{4}} irsz    Irányítószám (pontosan 4 számjegy).
 * @apiBody {Number}    varosid Város azonosító (létező város).
 * @apiError (422) ValidationError Hibás vagy hiányzó mező.
 * @apiErrorExample {json} Hiba (422):
 *     {
 *       "message": "Az irányítószám pontosan 4 számjegy legyen.",
 *       "errors": { "irsz": ["Az irányítószám pontosan 4 számjegy legyen."] }
 *     }
 */

class IranyitoszamController extends Controller
{
    /**
     * @api {get} /api/iranyitoszamok Irányítószámok listája (keresés, szűrés)
     * @apiName IrszLista
     * @apiGroup 4_Iranyitoszamok
     * @apiVersion 1.0.0
     *
     * @apiQuery {String} [kereses] Irányítószám eleje (pl. "40") VAGY városnév része (pl. "deb").
     * @apiQuery {Number} [megye]   Szűrés megye azonosítóra (pl. 10 = Hajdú-Bihar).
     *
     * @apiExample {url} Példák:
     *     /api/iranyitoszamok?kereses=deb
     *     /api/iranyitoszamok?megye=10
     *     /api/iranyitoszamok?kereses=40&megye=10
     *
     * @apiSuccessExample {json} Siker (200):
     *     [
     *       {
     *         "id": 248, "irsz": "4000", "varosid": 62,
     *         "varos": {
     *           "id": 62, "vnev": "Debrecen", "megyeid": 10, ...,
     *           "megye": { "id": 10, "mnev": "Hajdú-Bihar" }
     *         }
     *       }
     *     ]
     */
    public function index(Request $request)
    {
        return Iranyitoszam::with('varos.megye')
            ->kereses($request->query('kereses'))
            ->megyeben($request->query('megye'))
            ->orderBy('irsz')
            ->get();
    }

    /**
     * @api {get} /api/iranyitoszamok/:id Egy irányítószám
     * @apiName IrszEgy
     * @apiGroup 4_Iranyitoszamok
     * @apiVersion 1.0.0
     *
     * @apiParam {Number} id Irányítószám rekord azonosító.
     *
     * @apiSuccessExample {json} Siker (200):
     *     {
     *       "id": 1, "irsz": "8127", "varosid": 1,
     *       "varos": { "id": 1, "vnev": "Aba", ..., "megye": { "id": 7, "mnev": "Fejér" } }
     *     }
     *
     * @apiError (404) NotFound Nincs ilyen rekord.
     */
    public function show(Iranyitoszam $iranyitoszam)
    {
        return $iranyitoszam->load('varos.megye');
    }

    /**
     * @api {post} /api/iranyitoszamok Új irányítószám
     * @apiName IrszUj
     * @apiGroup 4_Iranyitoszamok
     * @apiVersion 1.0.0
     * @apiPermission Bearer token
     * @apiUse Vedett
     * @apiUse IrszTorzs
     *
     * @apiParamExample {json} Kérés törzse:
     *     { "irsz": "4099", "varosid": 62 }
     *
     * @apiSuccessExample {json} Siker (201):
     *     { "id": 751, "irsz": "4099", "varosid": 62, "varos": { "id": 62, "vnev": "Debrecen", ... } }
     */
    public function store(IranyitoszamRequest $request)
    {
        $uj = Iranyitoszam::create($request->validated());

        return response()->json($uj->load('varos.megye'), 201);
    }

    /**
     * @api {put} /api/iranyitoszamok/:id Irányítószám módosítása
     * @apiName IrszModosit
     * @apiGroup 4_Iranyitoszamok
     * @apiVersion 1.0.0
     * @apiPermission Bearer token
     * @apiUse Vedett
     * @apiUse IrszTorzs
     *
     * @apiParam {Number} id Irányítószám rekord azonosító.
     *
     * @apiSuccessExample {json} Siker (200):
     *     { "id": 751, "irsz": "4098", "varosid": 62, "varos": { ... } }
     *
     * @apiError (404) NotFound Nincs ilyen rekord.
     */
    public function update(IranyitoszamRequest $request, Iranyitoszam $iranyitoszam)
    {
        $iranyitoszam->update($request->validated());

        return $iranyitoszam->load('varos.megye');
    }

    /**
     * @api {delete} /api/iranyitoszamok/:id Irányítószám törlése
     * @apiName IrszTorol
     * @apiGroup 4_Iranyitoszamok
     * @apiVersion 1.0.0
     * @apiPermission Bearer token
     * @apiUse Vedett
     *
     * @apiParam {Number} id Irányítószám rekord azonosító.
     *
     * @apiSuccessExample {json} Siker (200):
     *     { "message": "Törölve." }
     *
     * @apiError (404) NotFound Nincs ilyen rekord.
     */
    public function destroy(Iranyitoszam $iranyitoszam)
    {
        $iranyitoszam->delete();

        return response()->json(['message' => 'Törölve.']);
    }
}
