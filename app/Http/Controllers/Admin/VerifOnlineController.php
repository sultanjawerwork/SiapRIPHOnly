<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Gate;

use App\Models\AnggotaCheck;
use App\Models\Pengajuan;
use App\Models\CommitmentCheck;
use App\Models\PksCheck;
use App\Models\AnggotaRiph;
use App\Models\PenangkarRiph;
use App\Models\PullRiph;
use App\Models\Anggotas;
use App\Models\DataUser;
use App\Models\PoktanRiph;
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
				->orderBy('created_at', 'desc')
				->get();
			$table = Datatables::of($pengajuans);
			$table->editColumn('id', function ($row) {
				return $row->id ? $row->id : '';
			});
			$table->editColumn('no_pengajuan', function ($row) {
				return $row->no_pengajuan ? $row->no_pengajuan : '';
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
		$data_user = DataUser::where('npwp_company', $verifikasi->npwp)
			->firstOrFail();
		$commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)
			->firstOrFail();
		$pkschecks = PksCheck::where('pengajuan_id', $verifikasi->id)->get();
		$lokasichecks = AnggotaCheck::where('pengajuan_id', $verifikasi->id)
			->orderBy('created_at', 'desc')
			->get();

		$pksriphs = PoktanRiph::where('no_ijin', $commitment->no_ijin)->get();
		$anggotariphs = collect();
		foreach ($pkschecks as $pkscheck) {
			$anggotariph = AnggotaRiph::where('poktan_id', $pksriphs->poktan_id)
				->where('no_ijin', $commitmentcheck->no_ijin)
				->get();
			$anggotariphs->push($anggotariph);
		};

		$total_luastanam = $commitment->anggotariph->sum('luas_tanam');
		$total_volume = $commitment->anggotariph->sum('volume');

		// dd($anggotariphs);
		return view('admin.verifikasi.online.check', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'commitmentcheck', 'pkschecks', 'lokasichecks', 'pksriphs', 'anggotariphs', 'total_luastanam', 'total_volume'));
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
