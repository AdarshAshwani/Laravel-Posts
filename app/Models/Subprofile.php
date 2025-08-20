<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subprofile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','label','bio','is_default'];
    protected $casts = ['is_default' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
