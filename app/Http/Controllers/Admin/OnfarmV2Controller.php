<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaMitra;
use App\Models\PengajuanV2;
use App\Models\Commitmentbackdate;
use App\Models\PksMitra;
use App\Models\verif_commitment;
use App\Models\verif_pksmitra;
use App\Models\verif_lokasi;

use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class OnfarmV2Controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Permohonan';
		$page_title = 'Daftar Pengajuan';
		$page_heading = 'Pengajuan Verifikasi Lapangan';
		$heading_class = 'fal fa-file-search';

		$verifikasis = PengajuanV2::where('onlinestatus', '1')->get();
		return view('v2.verifikasi.onfarm.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasis'));
	}

	public function list($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Verifikasi';
		$page_title = 'Verifikasi Onfarm';
		$page_heading = 'Lokasi Sampling';
		$heading_class = 'fal fa-map-marked';

		$verifikasi = PengajuanV2::findOrFail($id);
		$onfarms = verif_lokasi::where('pengajuan_id', $id)->get();
		return view('v2.verifikasi.onfarm.list', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'onfarms'));
	}

	public function check($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Verifikasi';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		$veriflokasi = verif_lokasi::findOrFail($id);
		$anggotamitra = AnggotaMitra::find($veriflokasi->anggotamitra_id);
		$pksmitra = PksMitra::find($anggotamitra->pks_mitra_id);
		$commitment = CommitmentBackdate::find($pksmitra->commitmentbackdate_id);
		$verifikasi = PengajuanV2::find($veriflokasi->pengajuan_id);

		return view('v2.verifikasi.onfarm.onfarmcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'veriflokasi', 'anggotamitra', 'pksmitra', 'commitment', 'verifikasi'));
	}


	public function create()
	{
		//
	}


	public function store(Request $request)
	{
		//
	}

	public function baonfarm(Request $request, $id)
	{
		$user = Auth::user();
		$verifikasi = PengajuanV2::findOrFail($id);
		$verifikasi->onfarmstatus = $request->input('onfarmstatus');
		$verifikasi->onfarmnote = $request->input('onfarmnote');
		$verifikasi->onfarmdate = Carbon::now();
		$verifikasi->luas_verif = $request->input('luas_verif');
		$verifikasi->volume_verif = $request->input('volume_verif');
		$commitment = CommitmentBackdate::find($verifikasi->commitmentbackdate_id);

		if ($verifikasi->onfarmstatus == '1') {
			$verifikasi->status = '4';
			$commitment->status = '4';
		} elseif ($verifikasi->onfarmstatus == '2') {
			$verifikasi->status = '5';
			$commitment->status = '5';
		}

		if ($request->hasFile('onfarmattch')) {
			$attch = $request->file('onfarmattch');
			$attchname = 'onfarmba_' . $verifikasi->id . '_commitment_' . $commitment->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/' . $commitment->periodetahun . '/commitment_' . $commitment->id . '/onfarmba/', $attch, $attchname);
			$verifikasi->onfarmattch = $attchname;
		}

		$verifikasi->onfarmverificator = $user->id;
		// dd($verifikasi);
		$verifikasi->save();
		$commitment->save();
		return redirect()->route('admin.task.onfarmv2.list', $verifikasi->id)
			->with('success', 'Data Verifikasi Lapangan berhasil disimpan');
	}

	public function show($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Permohonan';
		$page_title = 'Verifikasi Lapangan';
		$page_heading = 'Data-data Verifikasi';
		$heading_class = 'fal fa-ballot-check';

		$verifikasi = PengajuanV2::findOrFail($id);
		$commitment = verif_commitment::where('pengajuan_id', $verifikasi->id)
			->latest()
			->first();

		$pksmitras = verif_pksmitra::where('pengajuan_id', $verifikasi->id)
			->get();

		$onfarms = verif_lokasi::where('pengajuan_id', $verifikasi->id)
			->get();
		return view('v2.verifikasi.online.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pksmitras', 'onfarms'));
	}

	public function edit($id)
	{
		//
	}

	public function update(Request $request, $id)
	{
		$user = Auth::user();
		$veriflokasi = verif_lokasi::findOrFail($id);
		$veriflokasi->latitude = $request->input('latitude');
		$veriflokasi->longitude = $request->input('longitude');
		$veriflokasi->altitude = $request->input('altitude');
		$veriflokasi->polygon = $request->input('polygon');
		$veriflokasi->luas_verif = $request->input('luas_verif');
		// $veriflokasi->tgl_ukur = $request->input('tgl_ukur');
		$veriflokasi->volume_verif = $request->input('volume_verif');
		// $veriflokasi->tgl_timbang = $request->input('tgl_timbang');
		// $veriflokasi->onfarmverif = $request->input('onfarmverif');
		$veriflokasi->onfarmstatus = $request->input('onfarmstatus');
		$veriflokasi->onfarmnote = $request->input('onfarmnote');
		$veriflokasi->onfarmverif_at = Carbon::now();
		$veriflokasi->onfarmverificator_id = $user->id;

		$veriflokasi->save();
		return redirect()->route('admin.task.onfarmv2.list', $veriflokasi->pengajuan_id)
			->with('success', 'Pemeriksaan Lapangan lokasi tanam dan Produksi berhasil disimpan/diubah/perbarui.');
	}

	public function destroy($id)
	{
		//
	}
}
