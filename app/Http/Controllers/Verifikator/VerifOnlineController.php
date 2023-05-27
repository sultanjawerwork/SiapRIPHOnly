<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Gate;

use App\Models\LokasiCheck;
use App\Models\Pengajuan;
use App\Models\CommitmentCheck;
use App\Models\PksCheck;
use App\Models\Lokasi;
use App\Models\PenangkarRiph;
use App\Models\PullRiph;
use App\Models\MasterAnggota;
use App\Models\DataUser;
use App\Models\Pks;
use App\Models\Poktans;

class VerifOnlineController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		//page level
		$module_name = 'Permohonan';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Daftar Pengajuan Verifikasi Data';
		$heading_class = 'fa fa-file-search';

		//table pengajuan
		// $pengajuans = Pengajuan::orderBy('created_at', 'desc');
		if (request()->ajax()) {
			$pengajuans = Pengajuan::select(sprintf('%s.*', (new Pengajuan())->table))
				->where('status', '<=', '3')
				->orderBy('created_at', 'desc')
				->get();
			$table = Datatables::of($pengajuans);
			$table->editColumn('id', function ($row) {
				return $row->id ? $row->id : '';
			});
			$table->editColumn('no_pengajuan', function ($row) {
				return $row->no_pengajuan ? $row->no_pengajuan : '';
			});
			$table->editColumn('no_ijin', function ($row) {
				return $row->no_ijin ? $row->no_ijin : '';
			});
			$table->editColumn('npwp', function ($row) {
				return $row->npwp ? $row->npwp : '';
			});
			$table->editColumn('company_name', function ($row) {
				return $row->datauser->company_name ? $row->datauser->company_name : '';
			});
			$table->editColumn('created_at', function ($row) {
				return $row->created_at ? $row->created_at : '';
			});
			$table->editColumn('status', function ($row) {
				return $row->status ? $row->status : '';
			});
			return $table->make(true);
		}
		return view('admin.verifikasi.online.index', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
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
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		//page level
		$module_name = 'Permohonan';
		$page_title = 'Data Pengajuan';
		$page_heading = 'Data Pengajuan Verifikasi';
		$heading_class = 'fa fa-file-search';

		//populate related data
		$verifikasi = Pengajuan::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)
			->firstorFail();
		// $data_user = DataUser::where('npwp_company', $verifikasi->npwp)
		// 	->firstOrFail();
		$commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)
			->firstOrFail();
		// dd($commitmentcheck);
		$pkschecks = PksCheck::where('pengajuan_id', $verifikasi->id)->get();
		$lokasichecks = LokasiCheck::where('pengajuan_id', $verifikasi->id)
			->orderBy('created_at', 'desc')
			->get();

		$pkss = Pks::withCount('lokasi')
			->where('no_ijin', $commitment->no_ijin)->get();
		$lokasis = collect();
		foreach ($pkschecks as $pkscheck) {
			$anggotariph = Lokasi::where('poktan_id', $pkss->poktan_id)
				->where('no_ijin', $commitmentcheck->no_ijin)
				->get();
			$lokasis->push($anggotariph);
		};

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		return view('admin.verifikasi.online.subindex', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'commitmentcheck', 'pkschecks', 'lokasichecks', 'pkss', 'lokasis', 'total_luastanam', 'total_volume'));
	}

	/**
	 * berikut ini adalah detail-detail verifikasi.
	 * 1. Verifikasi Kommitmen (Unggahan Berkas Kelengkapan RIPH)
	 * 2. Verifikasi PKS/Perjanjian Kerjasama dengan Poktan
	 * 3. Sekilas data Lokasi
	 */

	public function commitmentcheck($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$module_name = 'Verifikasi';
		$page_title = 'Verifikasi Data';
		$page_heading = 'Pemeriksaan Berkas Komitmen';
		$heading_class = 'fal fa-file-search';

		$user = Auth::user();
		$commitmentcheck = CommitmentCheck::findOrFail($id);
		$commitment = PullRiph::findOrFail($commitmentcheck->pengajuan->commitment_id);

		// dd($commitmentcheck);

		return view('admin.verifikasi.online.commitmentcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'user', 'commitmentcheck', 'commitment'));
	}

	public function commitmentstore(Request $request, $id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$user = Auth::user();
		$commitmentcheck = CommitmentCheck::findOrFail($id);

		$pengajuan = Pengajuan::find($commitmentcheck->pengajuan_id);
		$commitmentcheck->verif_by = $user->id;
		$commitmentcheck->verif_at = Carbon::now();
		$commitmentcheck->formRiph = $request->input('formRiph');
		$commitmentcheck->formSptjm = $request->input('formSptjm');
		$commitmentcheck->logbook = $request->input('logbook');
		$commitmentcheck->formRt = $request->input('formRt');
		$commitmentcheck->formRta = $request->input('formRta');
		$commitmentcheck->formRpo = $request->input('formRpo');
		$commitmentcheck->formLa = $request->input('formLa');
		$commitmentcheck->note = $request->input('note');

		$commitmentcheck->save();
		return redirect()->route('verification.data.show', $pengajuan->id)
			->with('success', 'Data Pemeriksaan berhasil disimpan');
	}


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
