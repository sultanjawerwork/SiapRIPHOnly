<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\DataUser;
use App\Models\Skl;
use App\Models\PullRiph;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Completed;

use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;


class SklController extends Controller
{
	//digunakan oleh Administrator/Verifikator untuk melihat daftar verifikasi yang siap direkomendasikan terbit skl.
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

	//untuk pejabat melihat rekomendasi penerbitan skl.
	public function recomendations()
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
		// dd($recomends);
		return view('admin.verifikasi.skl.recomendations', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	//dilakukan oleh verifikator/administrator
	public function recomend(Request $request)
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
		return redirect()->route('verification.skl')
			->with('success', 'Komitmen No. RIPH: ' . $commitment->no_ijin . ', berhasil diajukan untuk penerbitan SKL.');
	}

	//oleh pejabat
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

		return view('admin.verifikasi.skl.recomshow', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'pengajuan', 'importir', 'wajib_tanam', 'luas_verif', 'wajib_produksi', 'volume_verif'));
	}

	//oleh pejabat
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

		$completed = new Completed();
		$completed->no_skl = $skl->no_skl;
		$completed->npwp = $skl->npwp;
		$completed->no_ijin = $skl->no_ijin;
		$completed->periodetahun = $commitment->periodetahun;
		$completed->published_date = Carbon::now();
		$completed->luas_tanam = $pengajuan->luas_verif;
		$completed->volume = $pengajuan->volume_verif;
		$completed->status = 'Lunas';
		$completed->url = route('verification.arsip.skl', $skl->id);

		$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
		$noIjin = str_replace(['.', '/'], '', $skl->no_ijin);

		// if ($request->hasFile('sklfile')) {
		// 	$file = $request->file('sklfile');
		// 	$filename = 'skl_' . $noIjin . '.' . $file->getClientOriginalExtension();
		// 	$filePath = $this->uploadFile($file, $filenpwp, $request->input('periodetahun'), $filename);
		// 	$oldskl->sklfile = $filename;
		// 	$completed->url = $filePath;
		// }
		// dd($completed);

		$skl->save();
		$pengajuan->save();
		$commitment->save();
		$completed->save();

		return redirect()->route('verification.skl.published', ['id' => $skl->id]);
	}

	//daftar ini digunakan oleh semua user role.
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

		return view('admin.verifikasi.skl.publishes', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	/**
	 * Menampilkan halaman data SKL
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$module_name = 'SKL';
		$page_title = 'Data SKL';
		$page_heading = 'Data SKL Terbit';
		$heading_class = 'fal fa-file-certificate';

		$skl = Skl::find($id);

		return view('admin.verifikasi.skl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl'));
	}

	/**
	 * Menampilkan halaman SKL (print)
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
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
			'Tautan' => route('verification.skl.show', $skl->id),
		];

		// $QrCode = QrCode::size(70)->generate(json_encode($data));
		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		// dd($commitment);
		return view('admin.verifikasi.skl.skl', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
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

		return view('admin.verifikasi.skl.sklPdf', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	}

	public function completedindex()
	{
		$module_name = 'SKL';
		$page_title = 'Surat Keterangan Lunas';
		$page_heading = 'SKL Diterbitkan';
		$heading_class = 'fa fa-award';

		$completeds = Completed::all();

		return view('admin.verifikasi.skl.completed', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'completeds'));
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
