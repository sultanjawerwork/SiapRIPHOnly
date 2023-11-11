<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SklReads extends Model
{
	use HasFactory;

	public $table = 'skl_reads';

	protected $fillable = [
		'skl_id',
		'user_id',
		'read_at',
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function skl()
	{
		return $this->belongsTo(Skl::class, 'skl_id', 'id');
	}

	// Di dalam model SklReads.php
	public static function getNewSklCount()
	{
		$userId = Auth::id();
		$userRole = Auth::user()->roles[0]->title;

		if ($userRole === 'Admin' || $userRole === 'Pejabat') {
			// Jika peran pengguna adalah Admin atau Pejabat, hitung semua skl yang belum dilihat
			return Skl::whereNotIn('id', function ($query) use ($userId) {
				$query->select('skl_id')->from('skl_reads')->where('user_id', $userId);
			})->count();
		} elseif ($userRole === 'User') {
			// Jika peran pengguna adalah User, hitung hanya SKL yang sesuai dengan npwp_company dari DataUser
			$userNpwp = Auth::user()->data_user->npwp_company;
			return Skl::whereNotIn('id', function ($query) use ($userId) {
				$query->select('skl_id')->from('skl_reads')->where('user_id', $userId);
			})->where('npwp', $userNpwp)->count();
		}
	}
}
