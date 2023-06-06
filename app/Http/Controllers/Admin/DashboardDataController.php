<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PullRiph;
use App\Models\Lokasi;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

use App\Models\User;
use App\Models\RiphAdmin;

class DashboardDataController extends Controller
{
	public function monitoringDataByYear($periodetahun)
	{
		abort_if(Auth::user()->roleaccess != 1, Response::HTTP_FORBIDDEN, '403 Forbidden');
		// Retrieve all data from RiphAdmin based on the selected 'periode'
		$riphData = RiphAdmin::where('periode', $periodetahun)->get();
		$commitments = PullRiph::where('periodetahun', $periodetahun)->get();

		$jumlahImportir = $riphData->sum('jumlah_importir');
		$v_pengajuan_import = $riphData->sum('v_pengajuan_import');
		$v_beban_tanam = $riphData->sum('v_beban_tanam');
		$v_beban_produksi = $riphData->sum('v_beban_produksi');
		$volume_import = $commitments->sum('volume_riph');
		$company = $commitments->count('no_ijin');

		$total_luastanam = 0;
		$total_volume = 0;

		foreach ($commitments as $commitment) {
			foreach ($commitment->lokasi as $lokasi) {
				if (!empty($commitment->status)) {
					$total_luastanam += $lokasi->luas_tanam;
					$total_volume += $lokasi->volume;
				}
			}
		}

		$data = [
			'jumlah_importir'		=> $jumlahImportir,
			'v_pengajuan_import'	=> $v_pengajuan_import,
			'v_beban_tanam'			=> $v_beban_tanam,
			'v_beban_produksi'		=> $v_beban_produksi,
			'company'				=> $company,
			'volume_import'			=> $volume_import,
			'total_luastanam'		=> $total_luastanam,
			'total_volume'			=> $total_volume,
		];
		return response()->json($data);
	}

	public function userMonitoringDataByYear($periodetahun)
	{
		$npwpuser = Auth::user()->data_user->npwp_company;
		// Retrieve all data from RiphAdmin based on the selected 'periode'
		$commitment = PullRiph::where('periodetahun', $periodetahun)
			->where('npwp', $npwpuser)
			->first();

		$volumeImport = $commitment->sum('volume_riph');
		$wajib_tanam = 'jumlah wajib tanam';
		$wajib_produksi = 'jumlah wajib produksi';
		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$lokasi = Lokasi::where('no_ijin', $commitment->no_ijin)->get();

		// dd($lokasi);
		$jumlah_poktan = $pks->count('id');
		$realisasi_tanam = $lokasi->sum('luas_tanam');
		$realisasi_produksi = $lokasi->sum('volume');

		dd($jumlah_poktan);

		// foreach ($thiscommitment as $commitment) {
		// 	foreach ($commitment->lokasi as $lokasi) {
		// 		if (!empty($commitment->status)) {
		// 			$realisasi_tanam += $lokasi->luas_tanam;
		// 			$realisasi_produksi += $lokasi->volume;
		// 		}
		// 	}
		// }

		$data = [
			'volumeImport'		=> $volumeImport,
			'wajib_tanam'			=> $wajib_tanam,
			'wajib_produksi'		=> $wajib_produksi,
			'total_luastanam'		=> $realisasi_tanam,
			'total_volume'			=> $realisasi_produksi,
		];
		return response()->json($data);
	}
}
