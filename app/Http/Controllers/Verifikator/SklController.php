<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\DataUser;
use App\Models\Skl;
use App\Models\PullRiph;
use App\Models\Pengajuan;
use App\Models\User;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
		$recomend->no_skl = $request->input('no_skl');
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

		$skl = Skl::findOrfail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$importir = DataUser::where('npwp_company', $pengajuan->npwp)->first();
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;

		return view('admin.skl.recomshow', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'pengajuan', 'importir', 'wajib_tanam', 'luas_verif', 'wajib_produksi', 'volume_verif'));
	}

	public function storerecom($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$skl = Skl::find($id);
		$skl->approved_by = Auth::user()->id;
		$skl->approved_at = Carbon::now();
		$skl->published_date = Carbon::now();
		// dd($skl);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::find($pengajuan->commitment_id);
		//$commitment = PullRiph::where('no_ijin', $pengajuan->no_ijin)->first();

		$pengajuan->status = '7';
		$commitment->status = '7';
		$commitment->skl = $skl->no_skl;

		$skl->save();
		$pengajuan->save();
		$commitment->save();

		return redirect()->route('verification.skl.published', ['id' => $skl->id]);
	}

	public function publishes()
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'SKL Terbit';
		$page_heading = 'Daftar SKL Terbit';
		$heading_class = 'fa fa-file-certificate';

		$recomends = Pengajuan::where('status', '7')
			->get();

		return view('admin.skl.recomendations', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	public function published($id)
	{
		$module_name = 'SKL';
		$page_title = 'Surat Keterangan Lunas';
		$page_heading = 'SKL Diterbitkan';
		$heading_class = 'fa fa-award';

		$skl = Skl::findOrfail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');
		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.arsip.skl', $skl->id),
		];

		// $QrCode = QrCode::size(70)->generate(json_encode($data));
		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		// dd($commitment);
		return view('admin.skl.skl', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	}

	public function arsipskl($id)
	{
		$skl = Skl::findOrFail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.arsip.skl.topdf', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		return view('sklPdf', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
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
