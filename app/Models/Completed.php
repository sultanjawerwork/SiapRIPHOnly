<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Completed extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    public $table = 'completeds';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'periode',
        'no_ijin',
        'npwp',
        'tgl_terbit',
        'luas_tanam',
        'volume',
        'status',
        'url',
    ];

    public function datauser()
    {
        return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
    }
}
