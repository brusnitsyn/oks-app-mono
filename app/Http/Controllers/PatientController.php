<?php

namespace App\Http\Controllers;

use App\Models\ControlCallResult;
use App\Models\Patient;
use App\Models\SurveyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::query()->where('organization_id', auth()->user()->currentOrganization()->id);
        $sortColumn = request('sort_column');
        $sortOrder = request('sort_order');

        $filters = [
            request('search_field') => request('search_value')
        ];

        $patients = $patients->filter($filters);

        if (request('search_value')) {

//            switch(request('search_field')) {
//                case 'fio': {
//                    $fio = explode(' ', request('search_value'));
//
//                    $family = $fio[0];
//                    $name = $fio[1] ?? null;
//                    $ot = $fio[2] ?? null;
//
//                    $patients = $patients->where('family', 'ilike', '%' . $family . '%')
//                        ->where('name', 'like', '%' . $name . '%')
//                        ->where('ot', 'like', '%' . $ot . '%');
//                }
//                default: {
//                    $patients = $patients->where(request('search_field'), 'ilike', request('search_value') . '%');
//                }
//            }
        }

//        if (request('order_by')) {
//            switch(request('order_by')) {
//                case 'id': {
//                    $patients = $patients->orderBy(request('order_by'), '');
//                }
//            }
//        }

        if ($sortColumn && $sortOrder) {
            $patients->sort($sortColumn, $sortOrder);
        } else {
            $patients->orderBy('id');
        }

        $customPaginate = collect([
            'total_closed' => $patients->clone()->whereHas('lastMedcard', function ($query) {
                $query->where('closed_at', '<>', null);
            })->count()
        ]);

        $patientsCollect = $patients->with([
                'lastMedcard',
                'lastMedcard.day3',
                'lastMedcard.mes1',
                'lastMedcard.mes3',
                'lastMedcard.mes6',
                'lastMedcard.mes12'
            ])
            ->paginate(30);

//        dd($patients);

        $patientsCollect->getCollection()->transform(function (Patient $patient) {
            return [
                'total' => $patient->total,
                'address' => $patient->address,
                'id' => $patient->id,
                'full_name' => $patient->full_name,
                'family' => $patient->family,
                'name' => $patient->name,
                'ot' => $patient->ot,
                'extract_at' => $patient->lastMedcard?->extract_at,
                'brith_at' => $patient->brith_at,
                'ds' => $patient->lastMedcard?->mkb->ds,
                'disp_status' => $patient->lastMedcard?->disp ? 'Взят' : 'Не взят',
                'med_drugs_status' => $patient->lastMedcard?->medDrugsStatus->name,
                'additional_treatment' => $patient->lastMedcard?->medCardAdditionalTreatment->short_name,
                'has_closed' => (bool)$patient->lastMedcard?->closed_at,
                'day3' => [
                    'call_at' => $patient->lastMedcard?->day3->call_at,
                    'called_at' => $patient->lastMedcard?->day3->called_at,
                    'control_call_result_id' => $patient->lastMedcard?->day3->control_call_result_id,
                    'survey_result_id' => $patient->lastMedcard?->day3->survey_result_id,
                ],
                'mes1' => [
                    'call_at' => $patient->lastMedcard?->mes1->call_at,
                    'called_at' => $patient->lastMedcard?->mes1->called_at,
                    'control_call_result_id' => $patient->lastMedcard?->mes1->control_call_result_id,
                    'survey_result_id' => $patient->lastMedcard?->mes1->survey_result_id,
                ],
                'mes3' => [
                    'call_at' => $patient->lastMedcard?->mes3->call_at,
                    'called_at' => $patient->lastMedcard?->mes3->called_at,
                    'control_call_result_id' => $patient->lastMedcard?->mes3->control_call_result_id,
                    'survey_result_id' => $patient->lastMedcard?->mes3->survey_result_id,
                ],
                'mes6' => [
                    'call_at' => $patient->lastMedcard?->mes6->call_at,
                    'called_at' => $patient->lastMedcard?->mes6->called_at,
                    'control_call_result_id' => $patient->lastMedcard?->mes6->control_call_result_id,
                    'survey_result_id' => $patient->lastMedcard?->mes6->survey_result_id,
                ],
                'mes12' => [
                    'call_at' => $patient->lastMedcard?->mes12->call_at,
                    'called_at' => $patient->lastMedcard?->mes12->called_at,
                    'control_call_result_id' => $patient->lastMedcard?->mes12->control_call_result_id,
                    'survey_result_id' => $patient->lastMedcard?->mes12->survey_result_id,
                ],
                'phone' => $patient->phone
            ];
        });

        $patientsCollect = $customPaginate->merge(
            $patientsCollect
        );

        return Inertia::render('Patients/Show', [
            'patients' => $patientsCollect,

            // Для добавления пациента
            'medDrugsStatuses' => fn () => \App\Models\MedDrugsStatus::all()->except(['id', 'name']),
            'medDrugsPeriods' => fn () => \App\Models\MedDrugsPeriod::all()->except(['id', 'name']),
            'genders' => fn () => \App\Models\Gender::all()->except(['id', 'name']),
            'mkbs' => fn () => \App\Models\Mkb::all()->except(['id', 'ds', 'name']),
            'complications' => fn () => \App\Models\Complication::all()->except(['id', 'name']),
            'lpus' => fn () => \App\Models\Lpu::all()->except(['id', 'name']),
            'additionalTreatment' => fn () => \App\Models\MedCardAdditionalTreatment::all()->except(['id', 'name']),
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'patient' => ['required', 'array'],
            'patient.family' => ['required', 'string'],
            'patient.name' => ['required', 'string'],
            'patient.ot' => ['required', 'string'],
            'patient.snils' => ['required', 'string'],
            'patient.phone' => ['required', 'string'],
            'patient.dop_phone' => ['nullable', 'string'],
            'patient.brith_at' => ['required', 'numeric'],
            'patient.gender_id' => ['required', 'numeric'],

            'patient.fias_objectid' => ['nullable', 'numeric'],
            'patient.address' => ['required', 'string'],

            'medcard' => ['required', 'array'],
            'medcard.lpu_id' => ['required', 'numeric'],
            'medcard.recipient_at' => ['required', 'numeric'],
            'medcard.extract_at' => ['required', 'numeric'],
            'medcard.mkb_id' => ['required', 'numeric'],
            'medcard.med_drugs_status_id' => ['required', 'numeric'],
            'medcard.med_drugs_period_id' => ['nullable', 'numeric'],
            'medcard.mkb_attendant_ids' => ['nullable', 'array'],
            'medcard.complication_ids' => ['nullable', 'array'],
            'medcard.med_card_additional_treatment_id' => ['required', 'numeric'],
        ]);

        $patientData = $data['patient'];
        $medcardData = $data['medcard'];

        $patient = Patient::whereSnils($patientData['snils'])->first();

        if ($patient) {return;}

        DB::beginTransaction();

        $patientData['organization_id'] = auth()->user()->currentOrganization()->id;
        $patientData['creater_user_id'] = auth()->user()->id;
        $patient = Patient::create($patientData);

        if (!$patient) {
            DB::rollBack();
            return;
        }

        $medcardData['organization_id'] = auth()->user()->currentOrganization()->id;
        $medcardData['creater_user_id'] = auth()->user()->id;
        $medcard = $patient->medcards()->create($medcardData);

        if (!$medcard) {
            DB::rollBack();
            return;
        }

        DB::commit();

        if ($medcardData['complication_ids'] && count($medcardData['complication_ids']) > 0)
        {
            foreach ($medcardData['complication_ids'] as $complication_id) {
                $medcard->relationComplications()->create([
                    'complication_id' => $complication_id,
                ]);
            }
        }

        if ($medcardData['mkb_attendant_ids'] && count($medcardData['mkb_attendant_ids']) > 0)
        {
            foreach ($medcardData['mkb_attendant_ids'] as $mkb_id) {
                $medcard->relationMkbAccompanying()->create([
                    'mkb_id' => $mkb_id,
                ]);
            }
        }

        return \Redirect::route('patients.show', $patient);
    }

    public function show(Patient $patient)
    {
        $patient = $patient->with([
            'gender',
            'lastMedcard.mkb',
            'lastMedcard.complications',
            'lastMedcard.complication_ids',
            'lastMedcard.mkbAccompanying',
            'lastMedcard.mkb_attendant_ids',
            'lastMedcard.medDrugsStatus',
            'lastMedcard.medDrugsPeriod',
            'lastMedcard.medCardReasonClose',
            'lastMedcard.medCardAdditionalTreatment',
            'lastMedcard.disp',
            'lastMedcard.control_call.survey.surveyChapters.questions.answers',
            'lastMedcard.control_call.surveyAnswers',
            'lastMedcard.control_call.survey.surveyAnswers',
        ])->find($patient->id);

        foreach ($patient->lastMedcard?->control_call as $cc) {
            $cc->answers = $cc->answers();
        }

        $complication_ids = $patient->lastMedcard?->complication_ids->pluck('id');
        $mkb_attendant_ids = $patient->lastMedcard?->mkb_attendant_ids->pluck('id');

        $patient = $patient->toArray();
        $patient['last_medcard']['complication_ids'] = $complication_ids;
        $patient['last_medcard']['mkb_attendant_ids'] = $mkb_attendant_ids;

        return Inertia::render('Patient/Show', [
            'patient' => fn () => $patient,

            // Для редактирования пациента и кт
            'medDrugsStatuses' => fn () => \App\Models\MedDrugsStatus::all()->except(['id', 'name']),
            'genders' => fn () => \App\Models\Gender::all()->except(['id', 'name']),
            'medDrugsPeriods' => fn () => \App\Models\MedDrugsPeriod::all()->except(['id', 'name']),
            'mkbs' => fn () => \App\Models\Mkb::all()->except(['id', 'ds', 'name']),
            'complications' => fn () => \App\Models\Complication::all()->except(['id', 'name']),
            'lpus' => fn () => \App\Models\Lpu::all()->except(['id', 'name']),
            'additionalTreatment' => fn () => \App\Models\MedCardAdditionalTreatment::all()->except(['id', 'name']),
            'reasonCloses' => fn () => \App\Models\MedCardReasonClose::all()->except(['id', 'name']),
            // кт
            'callResults' => fn () => ControlCallResult::all()->except(['id', 'name']),
            'surveyResults' => fn () => SurveyResult::all()->except(['id', 'name']),
        ]);
    }

    public function update(Patient $patient, Request $request)
    {
        $data = $request->validate([
            'patient' => ['required', 'array'],
            'patient.id' => ['required', 'numeric'],
            'patient.family' => ['required', 'string'],
            'patient.name' => ['required', 'string'],
            'patient.ot' => ['required', 'string'],
            'patient.snils' => ['required', 'string'],
            'patient.phone' => ['required', 'string'],
            'patient.dop_phone' => ['nullable', 'string'],
            'patient.brith_at' => ['required', 'numeric'],
            'patient.gender_id' => ['required', 'numeric'],

            'patient.fias_objectid' => ['nullable', 'numeric'],
            'patient.address' => ['required', 'string'],

            'medcard' => ['required', 'array'],
            'medcard.id' => ['required', 'numeric'],
            'medcard.lpu_id' => ['required', 'numeric'],
            'medcard.recipient_at' => ['required', 'numeric'],
            'medcard.extract_at' => ['required', 'numeric'],
            'medcard.mkb_id' => ['required', 'numeric'],
            'medcard.med_drugs_status_id' => ['required', 'numeric'],
            'medcard.med_drugs_period_id' => ['nullable', 'numeric'],
            'medcard.mkb_attendant_ids' => ['nullable', 'array'],
            'medcard.complication_ids' => ['nullable', 'array'],
            'medcard.med_card_additional_treatment_id' => ['required', 'numeric'],
        ]);

        $patientData = $data['patient'];
        $medcardData = $data['medcard'];

        $patient = Patient::whereId($patientData['id'])->first();

        $patient->update($patientData);

        $medcard = $patient->medcards()->whereId($medcardData['id'])->first();

        $medcard->update($medcardData);

        $medcard->relationComplications()->delete();
        if (count($medcardData['complication_ids']) > 0)
        {
            foreach ($medcardData['complication_ids'] as $complication_id) {
                $medcard->relationComplications()->create([
                    'complication_id' => $complication_id,
                ]);
            }
        }

        $medcard->relationMkbAccompanying()->delete();
        if (count($medcardData['mkb_attendant_ids']) > 0)
        {
            foreach ($medcardData['mkb_attendant_ids'] as $mkb_id) {
                $medcard->relationMkbAccompanying()->create([
                    'mkb_id' => $mkb_id,
                ]);
            }
        }

        return \Redirect::route('patients.show', $patient);
    }

    public function delete(Patient $patient, Request $request)
    {
        $data = $request->validate([
            'med_card_reason_close_id' => ['required', 'numeric'],
            'closed_at' => ['required', 'numeric'],
        ]);

        $patient->lastMedcard()->update([
            'closed_at' => $data['closed_at'],
            'med_card_reason_close_id' => $data['med_card_reason_close_id'],
        ]);

        $patient->lastMedcard?->disp()->update([
            'closed_at' => $data['closed_at']
        ]);

        $patient->lastMedcard?->disp()->delete();
        $patient->lastMedcard()->delete();

        return Redirect::route('patients.index');
    }

    public function restore(Patient $patient)
    {
        $patient->lastMedcard()->restore();
        $patient->lastMedcard?->disp()->restore();

        $patient->lastMedcard()->update([
            'closed_at' => null,
            'med_card_reason_close_id' => null,
        ]);

        $patient->lastMedcard?->disp()->update([
            'closed_at' => null
        ]);

        return Redirect::route('patients.show', ['patient' => $patient->id]);
    }
}
