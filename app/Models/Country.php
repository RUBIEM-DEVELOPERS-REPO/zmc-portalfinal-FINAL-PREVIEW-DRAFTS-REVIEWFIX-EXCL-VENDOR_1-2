<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'code', 'phone_code', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all active countries as key-value pairs for select options
     */
    public static function getActiveForSelect()
    {
        return static::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * Get all active countries with phone codes for select options
     */
    public static function getActiveWithPhoneCodeForSelect()
    {
        return static::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($country) {
                return [
                    'name' => $country->name,
                    'code' => $country->code,
                    'phone_code' => $country->phone_code,
                ];
            })
            ->toArray();
    }
}
