<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::all()->map(function ($organization) {
            return [
                'label' => $organization->short_name,
                'value' => $organization->id,
            ];
        });

        return response()->json($organizations);
    }
}
