<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class FiasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $response = Http::post(config('fias.url') . '/find', [
            'search' => $search,
            'filter_by' => [
                'level' => '[5,6]',
                'isactive' => 1
            ],
            'sort_by' => [
                'level' => 'asc'
            ]
        ]);

        return $response->json();
    }
}
