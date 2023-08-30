<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifTanam;
use App\Models\Lokasi;
use App\Models\MasterAnggota;
use App\Models\MasterPoktan;
use App\Models\PullRiph;
use Illuminate\Http\Request;

class DataLokasiTanamController extends Controller
{
	public function lokasiTanamByCommitment($id)
	{
		$verifikasi = AjuVerifTanam::findOrFail($id);

		$query = Lokasi::query()
			->where('no_ijin', $verifikasi->no_ijin)
			->with('masterkelompok', 'masteranggota')
			->select('id', 'npwp', 'no_ijin', 'poktan_id', 'anggota_id', 'nama_lokasi', 'luas_tanam');

		$lokasis = $query->get()->map(function ($lokasi) {
			$poktan = MasterPoktan::where('id', $lokasi->poktan_id)->value('nama_kelompok');
			$anggota = MasterAnggota::where('id', $lokasi->anggota_id)->value('nama_petani');
			$noIjin = str_replace(['/', '.', '-'], '', $lokasi->no_ijin);
			$showRoute = route('verification.tanam.lokasicheck', [
				'noIjin' => $noIjin,
				'anggota_id' => $lokasi->anggota_id
			]);
			return [
				'idPks'			=> $lokasi->id,
				'npwp'			=> $lokasi->npwp,
				'no_ijin'		=> $lokasi->no_ijin,
				'poktan_id'		=> $lokasi->poktan_id,
				'poktan'		=> $poktan,
				'anggota'		=> $anggota,
				'nama_lokasi'	=> $lokasi->nama_lokasi,
				'luas_tanam'	=> $lokasi->luas_tanam,
				'show'			=> $showRoute,
			];
		});

		$data = [
			'lokasis' => $lokasis,
		];
		return response()->json($data);
	}
}
