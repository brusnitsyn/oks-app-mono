<?php

namespace App\Http\Controllers;

use App\Models\ControlCallResult;
use App\Models\Patient;
use App\Models\SurveyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::all();
        $registryPatient = Collection::empty();
        foreach ($patients as $patient) {
            $patient->load([
                'lastMedcard',
                'lastMedcard.day3',
                'lastMedcard.mes1',
                'lastMedcard.mes3',
                'lastMedcard.mes6',
                'lastMedcard.mes12'
            ]);

            $patientModel = [
                'id' => $patient->id,
                'family' => $patient->family,
                'name' => $patient->name,
                'ot' => $patient->ot,
                'recipient_at' => Carbon::createFromTimestampMs($patient->lastMedcard->recipient_at, config('app.timezone'))->format('d.m.Y'),
                'brith_at' => Carbon::createFromTimestampMs($patient->brith_at, config('app.timezone'))->format('d.m.Y'),
                'ds' => $patient->lastMedcard->mkb->ds,
                'disp_status' => $patient->lastMedcard->disp ? 'Взят' : 'Не взят',
                'med_drugs_status' => $patient->lastMedcard->medDrugsStatus->name,
                'additional_treatment' => $patient->lastMedcard->medCardAdditionalTreatment->short_name,
                'day3' => [
                    'call_at' => $patient->lastMedcard->day3->call_at,
                    'called_at' => $patient->lastMedcard->day3->called_at
                ],
                'mes1' => [
                    'call_at' => $patient->lastMedcard->mes1->call_at,
                    'called_at' => $patient->lastMedcard->mes1->called_at
                ],
                'mes3' => [
                    'call_at' => $patient->lastMedcard->mes3->call_at,
                    'called_at' => $patient->lastMedcard->mes3->called_at
                ],
                'mes6' => [
                    'call_at' => $patient->lastMedcard->mes6->call_at,
                    'called_at' => $patient->lastMedcard->mes6->called_at
                ],
                'mes12' => [
                    'call_at' => $patient->lastMedcard->mes12->call_at,
                    'called_at' => $patient->lastMedcard->mes12->called_at
                ],
                'phone' => $patient->phone
            ];

            $registryPatient->push($patientModel);
        }

        return Inertia::render('Patients/Show', [
            'patients' => $registryPatient,

            // Для добавления пациента
            'medDrugsStatuses' => \App\Models\MedDrugsStatus::all()->except(['id', 'name']),
            'medDrugsPeriods' => \App\Models\MedDrugsPeriod::all()->except(['id', 'name']),
            'mkbs' => \App\Models\Mkb::all()->except(['id', 'ds', 'name']),
            'complications' => \App\Models\Complication::all()->except(['id', 'name']),
            'lpus' => \App\Models\Lpu::all()->except(['id', 'name']),
            'additionalTreatment' => \App\Models\MedCardAdditionalTreatment::all()->except(['id', 'name']),
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

        $patient = Patient::create($patientData);

        $medcard = $patient->medcards()->create($medcardData);

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

        $complication_ids = $patient->lastMedcard->complication_ids->pluck('id');
        $mkb_attendant_ids = $patient->lastMedcard->mkb_attendant_ids->pluck('id');

        $patient = $patient->toArray();
        $patient['last_medcard']['complication_ids'] = $complication_ids;
        $patient['last_medcard']['mkb_attendant_ids'] = $mkb_attendant_ids;

        return Inertia::render('Patient/Show', [
            'patient' => $patient,

            // Для редактирования пациента и кт
            'medDrugsStatuses' => \App\Models\MedDrugsStatus::all()->except(['id', 'name']),
            'medDrugsPeriods' => \App\Models\MedDrugsPeriod::all()->except(['id', 'name']),
            'mkbs' => \App\Models\Mkb::all()->except(['id', 'ds', 'name']),
            'complications' => \App\Models\Complication::all()->except(['id', 'name']),
            'lpus' => \App\Models\Lpu::all()->except(['id', 'name']),
            'additionalTreatment' => \App\Models\MedCardAdditionalTreatment::all()->except(['id', 'name']),
            // кт
            'callResults' => ControlCallResult::all()->except(['id', 'name']),
            'surveyResults' => SurveyResult::all()->except(['id', 'name']),
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
}
