<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Laravel\Scout\Searchable;

class Patient extends Model
{
    use Filterable;

    protected static function booted(): void
    {
        static::saving(function (Patient $patient) {
            $patient->full_name = "$patient->family $patient->name $patient->ot";
        });

        static::saved(function (Patient $patient) {
            $response = Http::get(config('fias.url') . "/$patient->fias_objectid/district");
            $data = $response->json();
            $patient->fias_district_objectid = $data['objectid'];
            $patient->district = $data['full_name'];

            $patient->saveQuietly();
        });
    }

    // Доступные поля для фильтрации
    protected $filterable = [
        'full_name'
    ];

    // Доступные поля для сортировки
    protected $sortable = [
        'id',
        'brith_at',
        'full_name'
    ];

    protected $fillable = [
        'family',
        'name',
        'ot',
        'snils',
        'phone',
        'dop_phone',
        'brith_at',
        'gender_id',
        'full_name',

        'fias_objectid',
        'address',
        'creater_user_id',
        'organization_id',

        'fias_district_objectid',
        'district',
    ];

    public function creater()
    {
        return $this->belongsTo(User::class, 'creater_user_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function medcards()
    {
        return $this->hasMany(MedCard::class)->withTrashed();
    }

    public function activeMedcard()
    {
        return $this->hasOne(MedCard::class)
            ->where('closed_at', null)
            ->where('deleted_at', null);
    }

    public function lastMedcard()
    {
        return $this->hasOne(MedCard::class)->withTrashed()->latest();
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function toSearchableArray(): array
    {
        return array_merge([
            'id' => (string) $this->id,
            'full_name' => (string) $this->full_name,
            'snils' => (string) $this->snils,
            'phone' => (string) $this->phone,
            'created_at' => $this->created_at->timestamp,
        ]);
    }

    // Индекс для поискового движка
    public function searchableAs(): string
    {
        return 'oks_patients_index';
    }

    public function scopeFullName($query, $fio)
    {
        $fio = explode(' ', $fio);

        $family = $fio[0];
        $name = $fio[1] ?? null;
        $ot = $fio[2] ?? null;

        return $query->where('family', 'ilike', '%' . $family . '%')
            ->where('name', 'ilike', '%' . $name . '%')
            ->where('ot', 'ilike', '%' . $ot . '%');
    }
}
