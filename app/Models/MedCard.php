<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class MedCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'num', // Номер карты
        'recipient_at', // Дата поступления в стационар
        'extract_at', // Дата выписки из стационара
        'mkb_id', // Основной диагноз
        'disp_id', // Дисп наблюдение
        'med_drugs_status_id', // Выдача мед препаратов
        'med_drugs_period_id', // Выданы мед препараты на период
        'med_card_reason_close_id', // Причина закрытия
        'med_card_additional_treatment_id', // Необходимость доп лечения
        'closed_at', // Дата выбытия из регистра
        'deleted_at', // Дата удаления из регистра
        'patient_id', // Пациент
        'lpu_id', // ЛПУ
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function lpu()
    {
        return $this->belongsTo(Lpu::class);
    }

    public function mkb()
    {
        return $this->belongsTo(Mkb::class);
    }

    public function relationComplications()
    {
        return $this->hasMany(MedCardComplication::class);
    }

    public function complications()
    {
        return $this->belongsToMany(Complication::class, 'med_card_complications', 'med_card_id', 'complication_id');
    }
    public function complication_ids()
    {
        return $this->belongsToMany(Complication::class, 'med_card_complications', 'med_card_id', 'complication_id')->select('complications.id');
    }

    public function relationMkbAccompanying()
    {
        return $this->hasMany(MedCardMkbAccompanying::class);
    }

    public function mkbAccompanying()
    {
        return $this->belongsToMany(Mkb::class, 'med_card_mkb_accompanyings', 'med_card_id', 'mkb_id');
    }
    public function mkb_attendant_ids()
    {
        return $this->belongsToMany(Mkb::class, 'med_card_mkb_accompanyings', 'med_card_id', 'mkb_id')->select('mkbs.id');
    }

    public function disp()
    {
        return $this->belongsTo(Disp::class);
    }

    public function activeDisp()
    {
        return $this->belongsToMany(Disp::class, 'med_card_disps', 'med_card_id', 'disp_id')
            ->where('closed_at', null)
            ->where('deleted_at', null)
            ->latest();
    }

    public function medDrugsStatus()
    {
        return $this->belongsTo(MedDrugsStatus::class);
    }

    public function medDrugsPeriod()
    {
        return $this->belongsTo(MedDrugsPeriod::class);
    }

    public function medCardReasonClose()
    {
        return $this->belongsTo(MedCardReasonClose::class);
    }

    public function medCardAdditionalTreatment()
    {
        return $this->belongsTo(MedCardAdditionalTreatment::class);
    }

    public function control_call()
    {
        return $this->hasMany(MedCardControlCall::class);
    }

    public function day3()
    {
        return $this->hasOne(MedCardControlCall::class)->where('name', '3-й день');
    }

    public function mes1()
    {
        return $this->hasOne(MedCardControlCall::class)->where('name', '1 мес');
    }

    public function mes3()
    {
        return $this->hasOne(MedCardControlCall::class)->where('name', '3 мес');
    }

    public function mes6()
    {
        return $this->hasOne(MedCardControlCall::class)->where('name', '6 мес');
    }

    public function mes12()
    {
        return $this->hasOne(MedCardControlCall::class)->where('name', '12 мес');
    }

    protected static function booted(): void
    {
        static::created(function (MedCard $medCard) {
            $medCard->control_call()->create([
                'name' => '3-й день',
                'call_at' => Carbon::createFromTimestampMs($medCard->extract_at, config('app.timezone'))->copy()->addDays(3)->getTimestampMs()
            ]);
            $medCard->control_call()->create([
                'name' => '1 мес',
                'call_at' => Carbon::createFromTimestampMs($medCard->extract_at, config('app.timezone'))->copy()->addMonths()->getTimestampMs()
            ]);
            $medCard->control_call()->create([
                'name' => '3 мес',
                'call_at' => Carbon::createFromTimestampMs($medCard->extract_at, config('app.timezone'))->copy()->addMonths(3)->getTimestampMs()
            ]);
            $medCard->control_call()->create([
                'name' => '6 мес',
                'call_at' => Carbon::createFromTimestampMs($medCard->extract_at, config('app.timezone'))->copy()->addMonths(6)->getTimestampMs()
            ]);
            $medCard->control_call()->create([
                'name' => '12 мес',
                'call_at' => Carbon::createFromTimestampMs($medCard->extract_at, config('app.timezone'))->copy()->addMonths(12)->getTimestampMs()
            ]);
        });
    }
}
