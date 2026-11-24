<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Az új / módosított irányítószám ellenőrzése, mielőtt az adatbázisba kerül.
 * Hiba esetén a Laravel automatikusan 422-es JSON választ küld.
 */
class IranyitoszamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // a tokent az auth:sanctum middleware már ellenőrizte
    }

    public function rules(): array
    {
        return [
            'irsz' => [
                'required',
                'digits:4',
                // ugyanaz az irsz ugyanannál a városnál ne legyen kétszer
                // (módosításnál a saját sorát nem számolja ütközésnek)
                Rule::unique('iranyitoszam')
                    ->where('varosid', $this->input('varosid'))
                    ->ignore($this->route('iranyitoszam')),
            ],
            'varosid' => ['required', 'integer', 'exists:varos,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'irsz.required'    => 'Az irányítószám megadása kötelező.',
            'irsz.digits'      => 'Az irányítószám pontosan 4 számjegy legyen.',
            'irsz.unique'      => 'Ennél a városnál már van ilyen irányítószám.',
            'varosid.required' => 'A város megadása kötelező.',
            'varosid.exists'   => 'Nincs ilyen azonosítójú város.',
        ];
    }
}
