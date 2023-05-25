<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

use App\Models\MasterAnggota;
use App\Models\MasterKelompok;
use App\Models\PksMitra;
use App\Models\AnggotaMitra;
use App\Models\CommitmentBackdate;
use App\Models\MasterProvinsi;

class PksMitraController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
		$module_name = 'Komitmen';
		$page_title = 'Perjanjian Kerjasama';
		$page_heading = 'Daftar PKS';
		$heading_class = 'fa fa-file-invoice';

		$user = Auth::user();
		$masterkelompoks = MasterKelompok::all();
		$pksmitras = $user->commitmentbackdate->flatMap(function ($CommitmentBackdate) {
			return $CommitmentBackdate->pksmitra;
		});

		// dd($pksmitras);
		return view('v2.pksmitra.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pksmitras', 'masterkelompoks'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$pksmitra = new PksMitra();

		$pksmitra->commitmentbackdate_id = $request->input('commitmentbackdate_id');
		$pksmitra->master_kelompok_id = $request->input('master_kelompok_id');
		$pksmitra->no_ijin = $request->input('no_ijin');
		$pksmitra->no_perjanjian = $request->input('no_perjanjian');
		$pksmitra->tgl_perjanjian_start = $request->input('tgl_perjanjian_start');
		$pksmitra->tgl_perjanjian_end = $request->input('tgl_perjanjian_end');
		$pksmitra->luas_rencana = $request->input('luas_rencana');
		$pksmitra->varietas_tanam = $request->input('varietas_tanam');
		$pksmitra->periode_tanam = $request->input('periode_tanam');
		$pksmitra->provinsi_id = $request->input('provinsi_id');
		$pksmitra->kabupaten_id = $request->input('kabupaten_id');
		$pksmitra->kecamatan_id = $request->input('kecamatan_id');
		$pksmitra->kelurahan_id = $request->input('kelurahan_id');
		$pksmitra->berkas_pks = $request->input('berkas_pks');

		if ($request->hasFile('berkas_pks')) {
			$attch = $request->file('berkas_pks');
			$attchname = 'berkas_pks' . $pksmitra->id . '_' . 'riph' . $pksmitra->commitmentbackdate_id . '_' . 'poktan' . $pksmitra->master_kelompok_id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/pks/', $attch, $attchname);
			$pksmitra->berkas_pks = $attchname;
		}

		// dd($pksmitra);
		$pksmitra->save();
		return redirect()->route('admin.task.commitments.show', $pksmitra->commitmentbackdate_id)->with('success', 'Data Perjanjian Kerjasama saved successfully');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$module_name = 'Commitments';
		$page_title = 'Realisasi';
		$page_heading = 'Laporan Realisasi';
		$heading_class = 'fa fa-dolly';

		$pksmitra = PksMitra::findOrFail($id);
		$commitment = CommitmentBackdate::with('pksmitra.anggotamitras')->findOrFail($id);
		$masterkelompoks = MasterKelompok::all();
		$masterkelompok = MasterKelompok::findOrFail($id);
		$masteranggotas = MasterAnggota::where('master_kelompok_id', $pksmitra->master_kelompok_id)->get();
		$anggotamitras = AnggotaMitra::where('pks_mitra_id', $id)->get();

		// Define the groups and their associated fields

		$groups = [
			'Geolokasi' => ['latitude', 'longitude', 'altitude', 'nama_lokasi', 'polygon'],
			'Tanam' => ['tgl_tanam', 'luas_tanam', 'varietas', 'tanam_doc', 'tanam_pict'],
			'Produksi' => ['tgl_panen', 'volume', 'panen_doc', 'panen_pict'],
		];

		// Initialize variables to keep track of the warnings and errors
		$warns = [];
		$fault = [];

		// Loop through each group and check if any of the fields are empty or null
		foreach ($groups as $group => $fields) {
			$groupComplete = true;
			foreach ($anggotamitras as $anggotamitra) {
				foreach ($fields as $field) {
					if (empty($anggotamitra->$field)) {
						$groupComplete = false;
						break;
					}
				}
			}
			// dd($anggotamitra);
			if (!$groupComplete) {
				$warns[] = "Data $group belum dilengkapi.";
			} else {
				$fault[$group] = $groupComplete;
			}
		}

		return view('v2.commitment.pksmitra.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'masterkelompoks', 'masteranggotas', 'pksmitra', 'anggotamitras', 'commitment', 'masterkelompok', 'warns', 'fault'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */


	public function edit($id)
	{
		// ...
		$module_name = 'Commitments';
		$page_title = 'Kerjasama';
		$page_heading = 'Perjanjian Kerjasama';
		$heading_class = 'fa fa-file-signature';
		$provinsis = MasterProvinsi::all();
		$pksmitra = PksMitra::findOrFail($id);
		$commitment = CommitmentBackdate::findOrFail($pksmitra->commitmentbackdate_id);
		$masterkelompoks = MasterKelompok::all();
		// dd($masterkelompoks);

		return view('v2.commitment.pksmitra.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'masterkelompoks', 'pksmitra', 'provinsis'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
		$pksmitra = PksMitra::find($id);
		// $pksmitra->commitmentbackdate_id = $request->input('commitmentbackdate_id');
		$pksmitra->master_kelompok_id = $request->input('master_kelompok_id');
		// $pksmitra->no_ijin = $request->input('no_ijin');
		$pksmitra->no_perjanjian = $request->input('no_perjanjian');
		$pksmitra->tgl_perjanjian_start = $request->input('tgl_perjanjian_start');
		$pksmitra->tgl_perjanjian_end = $request->input('tgl_perjanjian_end');
		$pksmitra->luas_rencana = $request->input('luas_rencana');
		$pksmitra->varietas_tanam = $request->input('varietas_tanam');
		$pksmitra->periode_tanam = $request->input('periode_tanam');
		$pksmitra->provinsi_id = $request->input('provinsi_id');
		$pksmitra->kabupaten_id = $request->input('kabupaten_id');
		$pksmitra->kecamatan_id = $request->input('kecamatan_id');
		$pksmitra->kelurahan_id = $request->input('kelurahan_id');
		$pksmitra->berkas_pks = $request->input('berkas_pks');

		if ($request->hasFile('berkas_pks')) {
			// process uploaded file
			$attch = $request->file('berkas_pks');
			$attchname = 'berkas_pks' . $pksmitra->id . '_' . 'riph' . $pksmitra->commitmentbackdate_id . '_' . 'poktan' . $pksmitra->master_kelompok_id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/pks', $attch, $attchname);
			$pksmitra->berkas_pks = $attchname;
		} else {
			// use the previously uploaded file
			$pksmitra->berkas_pks = $request->input('prev_file_name');
		}


		// dd($pksmitra);
		$pksmitra->save();
		return redirect()->route('admin.task.pksmitra.edit', $pksmitra->id)->with('success', 'Data Perjanjian Kerjasama saved successfully');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
		$pksmitra = PksMitra::withTrashed()->findOrFail($id);
		// $commitments->childrelatedmodel()->delete(); //delete related object here
		$pksmitra->anggotamitras()->delete();
		$pksmitra->delete();
		return redirect()->route('admin.task.commitments.show', $pksmitra->commitmentbackdate_id)->with('success', 'Data Perjanjian deleted successfully');
	}
}
