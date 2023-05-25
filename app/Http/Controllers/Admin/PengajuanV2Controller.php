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
use App\Models\SklV2;
use App\Models\User;
use App\Models\verif_commitment;

class PengajuanV2Controller extends Controller
{
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

	public function create($id)
	{
		//load all commitments for current user
		$commitment = CommitmentBackdate::with(['user', 'pksmitra.anggotamitras'])
			->where('user_id', Auth::id())
			->findOrFail($id);

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-disable
		} else {
			return redirect()->route('admin.task.submission.show', $commitment->id);
			$disabled = true;
		}


		$total_luastanam = $commitment->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('luas_tanam');

		$total_volume = $commitment->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('volume');

		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Pengajuan Verifikasi Realisasi';
		$heading_class = 'fal fa-file-invoice';

		return view('v2.pengajuanv2.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'total_luastanam', 'total_volume', 'disabled'));
	}

	public function store($id, Request $request)
	{
		//validasi sebelum pengajuan di-submit
		$request->validate(
			[]
		);
		// Find commitment_backdate id
		$commitment = CommitmentBackdate::find($id);

		//create new pengajuan
		$pengajuan = new PengajuanV2();
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
		$pengajuan->commitmentbackdate_id = $commitment->id;
		$pengajuan->status = '1'; //1 = verifikasi diajukan
		$pengajuan->jenis = 'Verifikasi'; //jenis pengajuan 'Verifikasi'
		// $pengajuan->jenis = 'verifikasi';
		$pengajuan->created_at = Carbon::now();

		$pengajuan->save();
		//set status pengajuan pada tabel commitment
		$commitment->status = '1'; //1 = verifikasi diajukan
		// $commitments->pengajuan_id = $pengajuan->id;
		$commitment->save();

		$verifCommitment = new verif_commitment();
		$verifCommitment->pengajuan_id = $pengajuan->id;
		$verifCommitment->commitmentbackdate_id = $commitment->id;
		$verifCommitment->status = '1';
		$verifCommitment->verif_at = Carbon::now();
		$verifCommitment->save();

		return redirect()->route('admin.task.submission.success', $pengajuan->id)->with('success', 'Permohonan Anda berhasil diajukan!');
	}

	//redirect sukses
	public function success($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Pengajuan Verifikasi Realisasi';
		$heading_class = 'fal fa-file-invoice';

		$pengajuan = PengajuanV2::findOrFail($id);
		return view('v2.pengajuanv2.successaju', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pengajuan'));
	}

	public function show($id)
	{
		$module_name = 'Pengajuan';
		$page_title = 'Verifikasi';
		$page_heading = 'Data Pengajuan';
		$heading_class = 'fal fa-file-invoice';

		//load data pengajuan
		$pengajuan = PengajuanV2::findOrFail($id);

		//load all commitment for current user
		$commitment = CommitmentBackdate::where('id', $pengajuan->commitmentbackdate_id)
			->latest()
			->first();

		if (!empty($commitment->status) && $commitment->status != 6) {
			$disabled = true; // input di-enable
		} else {
			$disabled = false; // input di-disable
		}

		$total_luastanam = $commitment->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('luas_tanam');

		$total_volume = $commitment->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('volume');

		$skl = SklV2::where('pengajuan_id', $pengajuan->id)
			->latest()
			->first();

		return view('v2.pengajuanv2.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pengajuan', 'commitment', 'total_luastanam', 'total_volume', 'skl', 'disabled'));
	}

	public function ulang($id, Request $request)
	{
		// Find commitment_backdate id

		$commitment = CommitmentBackdate::with('user')
			->where('user_id', Auth::id())
			->findOrFail($id);

		if ($commitment->status != 6) {
			return redirect()->back()->with('error', 'Halaman ini tidak dapat di akses!');
		}

		// Update pengajuanv2 status
		$pengajuan = PengajuanV2::where('no_pengajuan', $request->input('no_pengajuan'))
			->where('commitmentbackdate_id', $id)
			->firstOrFail();
		$pengajuan->status = '6';
		$pengajuan->save();

		// Update commitment_backdate
		$commitment->status = '6';
		$fileInputs = [
			'formRiph',
			'formSptjm',
			'logbook',
			'formRt',
			'formRta',
			'formRpo',
			'formLa'
		];
		$commitment_id = $commitment->id;
		$folder_name = "commitmentsv2/$commitment_id";

		foreach ($fileInputs as $fileInput) {
			if ($request->hasFile($fileInput)) {
				$file = $request->file($fileInput);
				$file_name = $fileInput . '_' . $commitment_id . '_' . date('Ymd') . '_' . time() . '.' . $file->getClientOriginalExtension();
				Storage::disk('public')->putFileAs("docs/$folder_name", $file, $file_name);
				$commitment->$fileInput = $file_name;
			}
		}

		$commitment->save();
		return redirect()->route('admin.task.commitments.show', $commitment->id)->with('success', 'Data Pengajuan submitted successfully');
	}
}
