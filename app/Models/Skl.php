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

	public function NewRecomendation(): int
	{
		return self::whereNull('published_date')->count();
	}

	public function pengajuan()
	{
		return $this->belongsTo(Pengajuan::class, 'pengajuan_id', 'id');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function completed()
	{
		return $this->belongsTo(Completed::class, 'no_skl', 'no_skl');
	}
}
