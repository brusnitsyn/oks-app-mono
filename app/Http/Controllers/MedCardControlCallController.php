<?php

namespace App\Http\Controllers;

use App\Models\MedCardControlCall;
use App\Models\MedCardSurveyAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;

class MedCardControlCallController extends Controller
{
    public function update(MedCardControlCall $controlCall, Request $request)
    {
        $data = $request->validate([
            'med_card_control_call_id' => ['required', 'numeric'],
            'info' => ['nullable', 'string'],
            'answers' => ['nullable', 'array'],
            'survey_result_id' => ['required', 'integer'],
            'control_call_result_id' => ['required', 'integer'],
            'survey_id' => ['nullable', 'numeric'],
            'disp_start_at' => ['nullable', 'numeric'],
        ]);
        $disp = $controlCall->med_card->disp;
        if (!is_null($data['disp_start_at']) && is_null($disp)) {
            $disp = $controlCall->med_card->disp()->create([
                'start_at' => $data['disp_start_at'],
            ]);
            $controlCall->med_card()->update([
                'disp_id' => $disp->id,
            ]);
        }

        $medCardControlCallId = $data['med_card_control_call_id'];
        $surveyId = $data['survey_id'];
        $control = $request->only([
            'info',
            'survey_result_id',
            'control_call_result_id'
        ]);

        // Теперь будет вызываться всегда, когда данные будут обновлены
        $control['called_at'] = Carbon::now(config('app.timezone'))->getTimestampMs();

        $controlCall->update($control);

        if (isset($surveyId) && isset($data['answers'])) {
            foreach ($data['answers'] as $questionId => $answerId) {
                if (!is_null($answerId)) {
                    MedCardSurveyAnswer::updateOrCreate(
                        [
                            'med_card_id' => $controlCall->med_card_id,
                            'med_card_control_call_id' => $medCardControlCallId,
                            'survey_id' => $surveyId,
                            'survey_chapter_question_id' => $questionId,
                        ],
                        [
                            'med_card_id' => $controlCall->med_card_id,
                            'med_card_control_call_id' => $medCardControlCallId,
                            'survey_id' => $surveyId,
                            'survey_chapter_question_id' => $questionId,
                            'survey_chapter_answer_id' => $answerId,
                        ]
                    );
                }
            }
        }

        $patientId = $controlCall->med_card->patient_id;




        return Redirect::route('patients.show', [
            'patient' => $patientId,
        ]);
    }
}
