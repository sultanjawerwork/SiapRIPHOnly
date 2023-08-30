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
		'spvp', //surat pengajuan verifikasi produksi
		'rpo', //realisasi produksi
		'sphproduksi', //sph produksi
		'spdsp', //surat pengantar dinas telah selesai produksi
		'logbookproduksi',
		'formLa',
		'ndhprp', //nota dinas hasil pemeriksaan realisasi tanam

		'check_by',
		'verif_at',
		'metode',
	];

	public function commitment()
	{
		return $this->belongsTo(PullRiph::class, 'no_ijin', 'no_ijin');
	}
}
