<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function changeOrganization(Request $request)
    {
        $data = $request->validate([
            'organization_id' => 'required',
        ]);

        session(['organization_id' => $data['organization_id']]);

        return redirect()->back();
    }
}
