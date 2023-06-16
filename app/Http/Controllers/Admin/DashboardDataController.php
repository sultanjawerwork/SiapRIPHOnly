<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PullRiph;
use App\Models\Lokasi;
use App\Models\Pks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
		$ajucount = $commitments->filter(function ($commitment) {
			return $commitment->status == '1';
		})->count();

		$jumlahImportir = $riphData->sum('jumlah_importir');
		$v_pengajuan_import = $riphData->sum('v_pengajuan_import');
		$v_beban_tanam = $riphData->sum('v_beban_tanam');
		$v_beban_produksi = $riphData->sum('v_beban_produksi');
		$volume_import = $commitments->sum('volume_riph');
		$company = $commitments->count('no_ijin');

		$total_luastanam = 0;
		$total_volume = 0;
		$luas_verif = 0;
		$volume_verif = 0;
		$sumLunasTanam = 0;
		$sumLunasProduksi = 0;
		$verifikasis = [];
		// $skls = [];

		// Check if the denominator is 0 and set the percentages to 0
		if ($v_beban_tanam == 0) {
			$ltTowt = 0;
		} else {
			$ltTowt = $total_luastanam / $v_beban_tanam;
		}

		if ($v_beban_produksi == 0) {
			$vpTowp = 0;
		} else {
			$vpTowp = $total_volume / $v_beban_produksi;
		}

		foreach ($commitments as $commitment) {
			foreach ($commitment->lokasi as $lokasi) {
				if (!empty($commitment->status)) {
					$total_luastanam += $lokasi->luas_tanam;
					$total_volume += $lokasi->volume;
				}
			}

			foreach ($commitment->pengajuan->whereIn('status', [4, 6, 7]) as $pengajuan) {
				$luas_verif += $pengajuan->luas_verif;
				$volume_verif += $pengajuan->volume_verif;
			}

			foreach ($commitment->lokasi as $lokasi) {
				if ($commitment->status == 7) {
					$sumLunasTanam += $lokasi->luas_tanam;
					$sumLunasProduksi += $lokasi->volume;
				}
			}
			$verifikasis = array_merge($verifikasis, $commitment->pengajuan->map(function ($verifikasi) {
				return [
					'no_pengajuan' => $verifikasi->no_pengajuan,
					'commitment' => [
						'datauser' => [
							'company_name' => $verifikasi->commitment->datauser->company_name,
						],
					],
					'no_ijin' => $verifikasi->commitment->no_ijin,

					'status' => $verifikasi->status,
					'onlinestatus' => $verifikasi->onlinestatus,
					'onfarmstatus' => $verifikasi->onfarmstatus,
				];
			})->toArray());

			// $skls[] = $commitment->skl ? $commitment->skl->toArray() : null;
		}

		$proccesscount = $commitments->filter(function ($commitment) {
			return $commitment->pengajuan->where('onlinestatus', '2')->where('onfarmstatus', '')->count() > 0;
		})->count();

		$verifiedcount = $commitments->filter(function ($commitment) {
			return $commitment->pengajuan->where('onfarmstatus', '!=', '')->count() > 0;
		})->count();

		$lunascount = $commitments->where('status', 7)->count();

		$lvtowt = $luas_verif / $v_beban_tanam;
		$vvTowp = $volume_verif / $v_beban_produksi;

		$data = [
			'jumlah_importir'		=> $jumlahImportir,
			'ajucount'				=> $ajucount,
			'v_pengajuan_import'	=> $v_pengajuan_import,
			'v_beban_tanam'			=> $v_beban_tanam,
			'v_beban_produksi'		=> $v_beban_produksi,
			'company'				=> $company,
			'volume_import'			=> $volume_import,
			'total_luastanam'		=> $total_luastanam,
			'total_volume'			=> $total_volume,
			'luas_verif'			=> $luas_verif,
			'volume_verif'			=> $volume_verif,
			'ltTowt'				=> $ltTowt,
			'vpTowp'				=> $vpTowp,
			'lvTowt'				=> $lvtowt,
			'vvTowp'				=> $vvTowp,
			'proccesscount'			=> $proccesscount,
			'verifiedcount'			=> $verifiedcount,
			'lunascount'			=> $lunascount,
			'sumLunasTanam'			=> $sumLunasTanam,
			'sumLunasProduksi'		=> $sumLunasProduksi,
			'verifikasis'			=> $verifikasis,
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

	public function rekapRiphData(Request $request)
	{
		try {
			$options = array(
				'soap_version' => SOAP_1_1,
				'exceptions' => true,
				'trace' => 1,
				'cache_wsdl' => WSDL_CACHE_MEMORY,
				'connection_timeout' => 25,
				'style' => SOAP_RPC,
				'use' => SOAP_ENCODED,
			);

			$client = new \SoapClient('http://riph.pertanian.go.id/api.php/simethris?wsdl', $options);
			$parameter = array(
				'user' => 'simethris',
				'pass' => 'wsriphsimethris',
				'tahun' => $request->string('periodetahun')
			);
			$response = $client->__soapCall('get_rekap', $parameter);
		} catch (\Exception $e) {

			Log::error('Soap Exception: ' . $e->getMessage());
			throw new \Exception('Problem with SOAP call');
		}
		$res = json_decode(json_encode((array)simplexml_load_string($response)), true);

		return $res;
	}
}
