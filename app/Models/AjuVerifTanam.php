<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuVerifTanam extends Model
{
	use HasFactory;

	public $table = 'avtanams';

	protected $fillable = [
		'npwp',
		'commitment_id',
		'no_ijin',
		'status',
		'note',


		//file upload
		'batanam', //berita acara hasil pemeriksaan realisasi tanam
		'ndhprt', //nota dinas hasil pemeriksaan realisasi tanam

		'check_by',
		'verif_at',
		'metode',
	];

	public function pks()
	{
		return $this->hasMany(Pks::class, 'no_ijin', 'no_ijin');
	}

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}

	public function datauser()
	{
		return $this->belongsTo(DataUser::class, 'npwp', 'npwp_company');
	}

	public static function newPengajuanCount(): int
	{
		return self::whereNull('verif_at')->count();
	}

	public function NewRequest(): int
	{
		return self::where('status', '1')
			->whereNull('verif_at')->count();
	}

	public static function getNewPengajuan()
	{
		return self::where('status', '1')->whereNull('verif_at')->get();
	}
}
