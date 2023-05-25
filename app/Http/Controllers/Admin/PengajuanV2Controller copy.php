<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Models\AnggotaMitra;
use App\Models\Commitment;
use App\Models\CommitmentBackdate;
use App\Models\PengajuanV2;
use App\Models\PksMitra;
use App\Models\User;
use App\Models\verif_commitment;

class PengajuanV2Controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 * ditampilkan untuk operator/pelaku usaha
	 */
	public function index(Request $request)
	{
		//
		$module_name = 'Permohonan';
		$page_title = 'Verifikasi';
		$page_heading = 'Daftar Pengajuan Verifikasi';
		$heading_class = 'fa fa-file-invoice';

		$user = Auth::user();
		$pengajuans = PengajuanV2::whereHas('commitmentBackdate', function ($query) use ($user) {
			$query->where('user_id', $user->id);
		})->paginate(10);

		return view('v2.pengajuanv2.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'user', 'user', 'pengajuans'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($id)
	{
		//load all commitments for current user
		$commitments = CommitmentBackdate::with(['user', 'pksmitra.anggotamitras'])
			->where('user_id', Auth::id())
			->findOrFail($id);

		if (!empty($commitments->status) && $commitments->status != 3) {
			return redirect()->route('admin.task.commitments.viewpengajuan', $commitments->id);
			$disabled = true;
		} else {
			$disabled = false; // input di-disable
		}

		$total_luastanam = $commitments->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('luas_tanam');

		$total_volume = $commitments->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('volume');

		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Pengajuan Verifikasi Realisasi';
		$heading_class = 'fal fa-file-invoice';

		return view('v2.pengajuanv2.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitments', 'total_luastanam', 'total_volume', 'disabled'));
	}

	/**
	 * 
	 */
	public function store(Request $request)
	{
		//validasi sebelum pengajuan di-submit
		$request->validate(
			[]
		);

		$pengajuan = new PengajuanV2();
		// get current month and year as 2-digit and 4-digit strings
		$month = date('m');
		$year = date('Y');
		// retrieve the latest record for the current month and year
		$latestRecord = PengajuanV2::where('no_pengajuan', 'like', "%/{$month}/{$year}")
			->orderBy('created_at', 'desc')
			->first();

		// get the current increment value for n
		$n = 1;
		if ($latestRecord) {
			$parts = explode('/', $latestRecord->no_pengajuan);
			$n = intval($parts[0]) + 1;
		}

		// mask the n part to always have 3 digits
		$nMasked = str_pad($n, 3, '0', STR_PAD_LEFT);

		// generate the new no_pengajuan value with timestamp and masked n
		$no_pengajuan = "{$nMasked}/PV." . time() . "/simethris/{$month}/{$year}";
		$pengajuan->no_pengajuan = $no_pengajuan;
		$pengajuan->status = '1';

		$verifCommitment = new verif_commitment();
		$verifCommitment->pengajuan_id = $pengajuan->id;
		$verifCommitment->commitmentbackdate_id = $request->input('commitmentbackdate_id');
		$verifCommitment->status = $pengajuan->status;

		$commitments = CommitmentBackdate::findOrFail($verifCommitment->commitmentbackdate_id);
		$commitments->formRiph = $request->input('formRiph');

		$commitments->status = '1';
		$commitments->pengajuan_id = $pengajuan->id;
		//tambahkan field-field lain

		$pengajuan->save();
		$verifCommitment->save();
		$commitments->save();
		return redirect()->route('admin.task.commitments.show', $pengajuan->commitmentbackdate_id)
			->with('success', 'Permintaan Anda telah kami terima saved successfully');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
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
	}
}
