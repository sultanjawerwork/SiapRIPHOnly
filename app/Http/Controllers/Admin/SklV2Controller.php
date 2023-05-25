<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaMitra;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

use App\Models\PengajuanV2;
use App\Models\Commitmentbackdate;
use App\Models\verif_commitment;
use App\Models\verif_lokasi;
use App\Models\verif_pksmitra;
use App\Models\DaftarPejabat;
use App\Models\SklV2;

class SklV2Controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$user = Auth::user();
		// Check if the user's roleaccess is not equal to 1 or 5
		if ($user->id !== 1 && $user->id !== 5) {
			abort(403, 'Unauthorized'); // Return a 403 Forbidden error
		}

		$module_name = 'Permohonan';
		$page_title = 'Penerbitan SKL';
		$page_heading = 'Daftar Permohonan SKL';
		$heading_class = 'fa fa-file-certificate';

		$pengajuans = PengajuanV2::where('onlinestatus', '1')
			->where('onfarmstatus', '1')
			->where('status', '>=', '4')
			->where('status', '<>', '5')
			->get();

		return view('v2.sklv2.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pengajuans'));
	}

	public function list()
	{
		$user = Auth::user();
		$module_name = 'Permohonan';
		$page_title = 'Penerbitan SKL';
		$page_heading = 'Daftar Permohonan SKL';
		$heading_class = 'fa fa-file-certificate';

		$pengajuans = PengajuanV2::whereHas('commitmentBackdate', function ($query) use ($user) {
			$query->where('user_id', $user->id)
				->where('status', '6');
		})->get();

		return view('v2.sklv2.list', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'user', 'pengajuans'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function verifikasi($id)
	{
		$user = Auth::user();
		// Check if the user's roleaccess is not equal to 1 or 5
		if ($user->id !== 1 && $user->id !== 5) {
			abort(403, 'Unauthorized'); // Return a 403 Forbidden error
		}

		$module_name = 'Permohonan';
		$page_title = 'Penerbitan SKL';
		$page_heading = 'Pengajuan SKL';
		$heading_class = 'fa fa-file-certificate';

		$pengajuan = PengajuanV2::findOrfail($id);

		$commitment = CommitmentBackdate::with(['user', 'pksmitra.anggotamitras'])
			->findOrFail($pengajuan->commitmentbackdate_id);

		$total_luastanam = $commitment->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('luas_tanam');

		$total_volume = $commitment->pksmitra->flatMap(function ($pm) {
			return $pm->anggotamitras;
		})->sum('volume');

		$verifcommit = verif_commitment::where('pengajuan_id', $id)->first();
		$verifpks = verif_pksmitra::where('pengajuan_id', $pengajuan->id)->get();
		$veriflokasis = verif_lokasi::where('pengajuan_id', $pengajuan->id)->get();
		$pejabats = DaftarPejabat::all();

		return view('v2.sklv2.verifikasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'total_luastanam', 'total_volume', 'pengajuan', 'verifcommit', 'verifpks', 'veriflokasis', 'pejabats'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $id)
	{
		$user = Auth::user();
		$pengajuan = PengajuanV2::findOrFail($id);
		$commitment = CommitmentBackdate::find($pengajuan->commitmentbackdate_id);
		// dd($pengajuan);
		$pengajuan->status = '6';
		$pengajuan->save();

		$skl = new SklV2();
		$skl->pengajuan_id = $id;
		$skl->no_skl = $request->input('no_skl');
		$skl->published_date = $request->input('published_date');
		$skl->nota_attch = $request->input('nota_attch');
		$skl->publisher = $user->id;
		$skl->pejabat_id = $request->input('pejabat_id');


		$skl->save();


		$commitment->status = '6';
		$commitment->skl = $skl->id;
		$commitment->save();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$module_name = 'Permohonan';
		$page_title = 'Penerbitan SKL';
		$page_heading = 'Pengajuan SKL';
		$heading_class = 'fa fa-file-certificate';

		$skl = SklV2::findOrFail($id);
		$pengajuan = PengajuanV2::find($skl->pengajuan_id);
		$commitment = Commitmentbackdate::find($pengajuan->commitmentbackdate_id);
		$anggotamitras = AnggotaMitra::where('commitmentbackdate_id', $commitment->id)
			->get();
		$pejabat = DaftarPejabat::find($skl->pejabat_id);

		$total_luastanam = $anggotamitras->sum('luas_tanam');
		$total_produksi = $anggotamitras->sum('volume');
		// dd($total_luastanam);

		return view('v2.sklv2.form', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'pengajuan', 'commitment', 'total_luastanam', 'total_produksi', 'pejabat'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$user = Auth::user();
		// Check if the user's roleaccess is not equal to 1 or 5
		if ($user->id !== 1 && $user->id !== 5) {
			abort(403, 'Unauthorized'); // Return a 403 Forbidden error
		}
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
