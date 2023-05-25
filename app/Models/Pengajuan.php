<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\RandomId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengajuan extends Model
{
	use HasFactory;
	use SoftDeletes;
	use Auditable;
	use RandomId;


	public $table = 'pengajuans';

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		//main
		'no_pengajuan',
		'no_ijin',
		'commitment_id',
		'no_doc',
		'status',
		'note',

		//online
		'onlinestatus',
		'onlinenote',
		'onlinedate',
		'baonline',
		'onlinecheck_by',

		//onfarm
		'luas_verif',
		'volume_verif',
		'onfarmstatus',
		'onfarmnote',
		'onfarmdate',
		'baonfarm',
		'onfarmcheck_by',
		'verif_at',
	];
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
}
