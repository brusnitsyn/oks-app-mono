<?php

namespace App\Exports;

use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class ReportPatient implements FromView
{
    public function view(): View
    {
        return view('reports.patient', [
            'patients' => Patient::all()
        ]);
    }
}
