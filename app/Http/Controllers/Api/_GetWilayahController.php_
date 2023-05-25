<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterProvinsi;
use App\Models\MasterKabupaten;
use App\Models\MasterKecamatan;
use App\Models\MasterDesa;

class GetWilayahController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getAllProvinsi()
	{
		$provinsis = MasterProvinsi::all();
		$result = [];

		foreach ($provinsis as $provinsi) {
			$result[] = [
				'provinsi_id' => $provinsi->provinsi_id,
				'nama' => $provinsi->nama,
			];
		}

		return response()->json($result);
	}


	public function getKabupatenByProvinsi($provinsiId)
	{
		$kabupatens = MasterKabupaten::where('provinsi_id', $provinsiId)->get();
		$result = [];

		foreach ($kabupatens as $kabupaten) {
			$result[] = [
				'provinsi_id' => $kabupaten->provinsi_id,
				'kabupaten_id' => $kabupaten->kabupaten_id,
				'nama_kab' => $kabupaten->nama_kab,
			];
		}

		return response()->json($result);
	}

	public function getKecamatanByKabupaten($kabupatenId)
	{
		$kecamatans = MasterKecamatan::where('kabupaten_id', $kabupatenId)->get();
		$result = [];

		foreach ($kecamatans as $kecamatan) {
			$result[] = [
				'kabupaten_id' => $kecamatan->kabupaten_id,
				'kecamatan_id' => $kecamatan->kecamatan_id,
				'nama_kecamatan' => $kecamatan->nama_kecamatan,
			];
		}

		return response()->json($result);
	}

	public function getDesaBykecamatan($kecamatanId)
	{
		$desas = MasterDesa::where('kecamatan_id', $kecamatanId)->get();
		$result = [];

		foreach ($desas as $desa) {
			$result[] = [
				'kecamatan_id' => $desa->kecamatan_id,
				'desa_id' => $desa->desa_id,
				'nama_desa' => $desa->nama_desa,
			];
		}

		return response()->json($result);
	}
}
