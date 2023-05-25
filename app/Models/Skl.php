<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skl extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'skls';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'published_date',
    ];

    protected $fillable = [
        'pengajuan_id',
        'no_pengajuan',
        'no_skl',
        'npwp',
        'no_ijin',
        'published_date',
        'qrcode',
        'nota_attch',
        'publisher',
        'pejabat_id',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
    }
}
