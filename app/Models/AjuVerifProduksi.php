<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuVerifProduksi extends Model
{
	public $table = 'avproduksis';

	protected $fillable = [
		'npwp',
		'commitment_id',
		'no_ijin',
		'no_pengajuan',
		'status',
		'note',


		//file upload
		'baproduksi', //berita acara hasil pemeriksaan realisasi produksi
		'ndhprp', //nota dinas hasil pemeriksaan realisasi produksi

		'check_by',
		'verif_at',
		'metode',
	];

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
		return self::where('status', '1')->count();
	}

	public function NewRequest(): int
	{
		return self::where('status', '1')->count();
	}

	public function proceedVerif(): int
	{
		return self::whereIn('status', ['2', '3'])
			->whereNull('baproduksi')->count();
	}

	public static function getNewPengajuan()
	{
		return self::where('status', '1')->get();
	}
}
