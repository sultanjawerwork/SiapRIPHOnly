<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LokasiCheck extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'lokasi_checks';

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $fillable = [
		'pengajuan_id',
		'commitcheck_id',
		'pkscheck_id',
		'poktan_id',
		'anggota_id',
		'npwp',
		'no_ijin',

		//pemeriksaan data online
		'onlinestatus',
		'onlinenote',
		'onlineverif_at',
		'onlineverif_by',

		//diisi oleh onfarm verifikator, data geolokasi
		'latitude',
		'longitude',
		'altitude',
		'polygon',

		//data tanam
		'luas_verif',
		'tgl_ukur',

		//data produksi
		'volume_verif',
		'tgl_timbang',

		'onfarmstatus',
		'onfarmnote',
		'onfarmverif_at',
		'onfarmverif_by',
	];
}
