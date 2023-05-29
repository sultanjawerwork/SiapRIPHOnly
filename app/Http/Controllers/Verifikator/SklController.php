<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\Skl;
use App\Models\PullRiph;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Ui\Presets\React;
use Yajra\DataTables\Facades\DataTables;

class SklController extends Controller
{

	public function index()
	{
		abort_if(Gate::denies('verification_skl_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$module_name = 'SKL';
		$page_title = 'Daftar Rekomendasi';
		$page_heading = 'Daftar Rekomendasi & SKL';
		$heading_class = 'fa fa-file-signature';

		$recomends = Pengajuan::where('status', '>=', '4')
			->get();

		return view('admin.verifikasi.skl.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	public function submit(Request $request)
	{
		abort_if(Gate::denies('verification_skl_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$user = Auth::user();
		$recomend = new Skl();
		$recomend->pengajuan_id = $request->input('pengajuan_id');
		$recomend->no_pengajuan = $request->input('no_pengajuan');
		$recomend->no_ijin = $request->input('no_ijin');
		$recomend->npwp = $request->input('npwp');
		$recomend->submit_by = $user->id;

		$pengajuan = Pengajuan::where('no_pengajuan', $request->input('no_pengajuan'))->first();
		$pengajuan->status = '6';

		$commitment = PullRiph::where('no_ijin', $request->input('no_ijin'))->first();
		$commitment->status = '6';

		// dd($commitment);
		$recomend->save();
		$pengajuan->save();
		$commitment->save();
		return redirect()->route('verification.skladmin')
			->with('success', 'Komitmen No. RIPH: ' . $commitment->no_ijin . ', berhasil diajukan untuk penerbitan SKL.');
	}

	public function recomendations(Request $request)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Daftar Permohonan';
		$page_heading = 'Daftar Permohonan Penerbitan SKL';
		$heading_class = 'fa fa-file-signature';

		$recomends = Pengajuan::where('status', '6')
			->get();

		return view('admin.skl.recomendations', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	public function showrecom($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Permohonan Penerbitan';
		$page_heading = 'Permohonan SKL Terbit';
		$heading_class = 'fa fa-file-signature';

		$pengajuan = Pengajuan::findOrfail($id);

		return view('admin.skl.recomshow', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pengajuan'));
	}

	public function storerecom(Request $request)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$skl = new Skl();
		$skl->pengajuan_id = $request->input('pengajuan_id');
		$skl->npwp = $request->input('npwp');
		$skl->no_ijin = $request->input('no_ijin');
		$skl->no_skl = $request->input('no_skl');
		$skl->approved_by = Auth::user()->id;
		$skl->approved_at = Carbon::now();
		$skl->status = '7';

		//qrcode here

		return view('admin.skl.storerecom', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl'));
	}

	public function publishes()
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'Daftar SKL Terbit';
		$page_title = 'SKL Terbit';
		$page_heading = 'SKL Terbit';
		$heading_class = 'fa fa-file-certificate';

		$skl = Skl::whereNotNull('approved_by')
			->whereNotNull('approved_at')
			->where('status', '7');
		$skl->approved_by = Auth::user()->id;
		$skl->approved_at = Carbon::now();
		$skl->status = '7';

		return view('admin.skl.publishes', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl'));
	}

	public function published($id)
	{

		$module_name = 'SKL';
		$page_title = 'Permohonan Penerbitan';
		$page_heading = 'Permohonan SKL Terbit';
		$heading_class = 'fa fa-file-signature';

		$skl = Skl::findOrfail($id);

		return view('admin.skl.published', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl'));
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
		//
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
