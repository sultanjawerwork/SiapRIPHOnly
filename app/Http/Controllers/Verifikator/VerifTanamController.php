<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifTanam;
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
use App\Models\PullRiph;
use App\Models\MasterPoktan;
use App\Models\Pks;
use App\Models\UserDocs;

class VerifTanamController extends Controller
{
	public function index()
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		//page level
		$module_name = 'Permohonan';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Daftar Pengajuan Verifikasi Tanam';
		$heading_class = 'fal fa-file-search';

		//table pengajuan
		$verifikasis = AjuVerifTanam::where('status', '<=', '4')
			->orderBy('created_at', 'desc')
			->get();
		return view('admin.verifikasi.tanam.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasis'));
	}

	public function check($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Data Pengajuan';
		$page_heading = 'Data Pengajuan Verifikasi';
		$heading_class = 'fa fa-file-search';

		// Populate related data
		$verifikasi = AjuVerifTanam::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$userDocs = UserDocs::where('no_ijin', $verifikasi->no_ijin)->first();
		// $commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)->firstOrFail();
		$pkschecks = PksCheck::where('pengajuan_id', $verifikasi->id)->get();
		$lokasichecks = LokasiCheck::where('pengajuan_id', $verifikasi->id)->orderBy('created_at', 'desc')->get();

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $verifikasi->no_ijin)
			->with(['pkscheck' => function ($query) use ($id) {
				$query->where('pengajuan_id', $id);
			}])
			->get();
		$poktanIds = Pks::where('no_ijin', $verifikasi->no_ijin)
			->pluck('poktan_id'); // Retrieve the poktan_id values

		// Group poktan_id values and retrieve unique nama_kelompok values
		$poktans = MasterPoktan::whereIn('id', $poktanIds)
			->groupBy('poktan_id')
			->pluck('nama_kelompok', 'poktan_id');
		// dd($poktans);
		$lokasis = collect();
		foreach ($pkschecks as $pkscheck) {
			$lokasi = Lokasi::where('poktan_id', $pkscheck->poktan_id)
				->where('no_ijin', $verifikasi->no_ijin)
				->get();
			$lokasis->push($lokasi);
		}

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		// $pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();


		return view('admin.verifikasi.tanam.subindex', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pkschecks', 'lokasichecks', 'pkss', 'poktans', 'lokasis', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks', 'userDocs'));
	}

	public function checkBerkas(Request $request, $id)
	{
		$user = Auth::user();
		$verifTanam = AjuVerifTanam::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $verifTanam->no_ijin)->first();
		$docsStatus = UserDocs::where('no_ijin', $commitment->no_ijin)->first();
		$docsStatus->spvtcheck = $request->input('spvtcheck');
		$docsStatus->sptjmcheck = $request->input('sptjmcheck');
		$docsStatus->rtacheck = $request->input('rtacheck');
		$docsStatus->sphtanamcheck = $request->input('sphtanamcheck');
		$docsStatus->spdstcheck = $request->input('spdstcheck');
		$docsStatus->logbooktanamcheck = $request->input('logbookcheck');
		$docsStatus->tanamcheck_by = $user->id;
		$docsStatus->tanamverif_at = Carbon::now();

		$verifTanam->status = '2'; //pemeriksaan berkas selesai
		$commitment->status = '2'; //pemeriksaan berkas selesai
		// dd($verifTanam->status, $commitment->status, $docsStatus);

		$verifTanam->save();
		$docsStatus->save();
		$commitment->save();

		return redirect()->back()->with('success', 'Status updated successfully.');
	}

	public function pkscheck($poktan_id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$module_name = 'Verifikasi';
		$page_title = 'Verifikasi Data';
		$page_heading = 'Data dan Berkas PKS';
		$heading_class = 'fal fa-ballot-check';

		$pks = Pks::where('poktan_id', $poktan_id)->latest()->first();
		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)
			->first();
		$verifikasi = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)
			->latest()
			->first();

		$pkscheck = PksCheck::where('pks_id', $pks->id)->latest()->first();
		return view('admin.verifikasi.tanam.pkscheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pks', 'commitment', 'verifikasi', 'pkscheck'));
	}

	public function pksstore(Request $request, $poktan_id)
	{
		$user = Auth::user();

		$pks = Pks::find($poktan_id);
		$pkscheck = new PksCheck();
		$pkscheck->pengajuan_id = $request->input('pengajuan_id');
		// $pkscheck->commitcheck_id = $request->input('commitmentcheck_id');
		$pkscheck->pks_id = $request->input('pks_id');
		$pkscheck->poktan_id = $request->input('poktan_id');
		$pkscheck->npwp = $request->input('npwp');
		$pkscheck->no_ijin = $request->input('no_ijin');
		// $pkscheck->note = $request->input('note');
		$pkscheck->status = $request->input('status');
		$pkscheck->verif_at = Carbon::now();
		$pkscheck->verif_by = $user->id;
		// dd($pkscheck);
		$pkscheck->save();

		$verifikasi = AjuVerifTanam::where('id', $pkscheck->pengajuan_id)->first();
		$commitment = PullRiph::where('no_ijin', $pkscheck->no_ijin)->first();
		$status = '3';
		$verifikasi->status = $status;
		$commitment->status = $status;

		$verifikasi->save();
		$commitment->save();
		return redirect()->back()
			->with('success', 'Data Pemeriksaan berhasil disimpan');
	}

	public function pksedit($id)
	{

		$module_name = 'Verifikasi';
		$page_title = 'Verifikasi Data';
		$page_heading = 'Ubah data Verifikasi PKS';
		$heading_class = 'fal fa-ballot-check';

		$pkscheck = PksCheck::find($id);
		// dd($pkscheck);
		$pks = Pks::where('poktan_id', $pkscheck->poktan_id)->first();
		$commitment = PullRiph::where('no_ijin', $pkscheck->no_ijin)
			->first();
		$verifikasi = AjuVerifTanam::find($pkscheck->pengajuan_id);
		// $commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)
		// 	->first();

		// dd($pkscheck);
		return view('admin.verifikasi.online.pksedit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pks', 'commitment', 'verifikasi', 'pkscheck'));
	}

	public function pksupdate(Request $request, $id)
	{
		$user = Auth::user();

		$pkscheck = PksCheck::find($id);
		// dd($pkscheck);
		$pkscheck->note = $request->input('note');
		$pkscheck->status = $request->input('status');
		$pkscheck->verif_at = Carbon::now();
		$pkscheck->verif_by = $user->id;

		$pkscheck->save();
		return redirect()->back()
			->with('success', 'Data Pemeriksaan berhasil disimpan');
	}

	public function lokasicheck($noIjin, $anggota_id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$module_name = 'Verifikasi Data';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		$no_ijin = substr_replace($noIjin, '/', 4, 0);
		$no_ijin = substr_replace($no_ijin, '.', 7, 0);
		$no_ijin = substr_replace($no_ijin, '/', 11, 0);
		$no_ijin = substr_replace($no_ijin, '/', 13, 0);
		$no_ijin = substr_replace($no_ijin, '/', 16, 0);

		$lokasi = Lokasi::where('anggota_id', $anggota_id)
			->where('no_ijin', $no_ijin)
			->first();

		$pks = Pks::where('poktan_id', $lokasi->poktan_id)
			->where('no_ijin', $no_ijin)
			->latest()
			->first();
		$commitment = PullRiph::where('no_ijin', $no_ijin)->first();

		//karena verifikasi dengan nomor ijin yang sama dapat muncul berulang
		$verifikasi = AjuVerifTanam::where('no_ijin', $no_ijin)
			->latest()
			->first();
		$commitmentcheck = CommitmentCheck::where('no_pengajuan', $verifikasi->no_pengajuan)->first();
		$pkscheck = PksCheck::where('pengajuan_id', $verifikasi->id)->first();
		$lokasicheck = LokasiCheck::where('anggota_id', $anggota_id)
			->where('poktan_id', $pks->poktan_id)
			// ->where('pkscheck_id', $pkscheck->id)
			->first();

		$anggotamitra = $lokasi;
		$pksmitra = $pkscheck;
		$verifcommit = $commitmentcheck;
		$verifpks = $pkscheck;
		$veriflokasi = $lokasicheck;
		// dd($anggotamitra);
		return view('admin.verifikasi.tanam.locationcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'pks', 'commitment', 'verifikasi', 'commitmentcheck', 'pkscheck', 'lokasicheck', 'pksmitra', 'anggotamitra', 'verifcommit', 'verifpks', 'veriflokasi'));
	}

	public function lokasistore(Request $request)
	{
		$user = Auth::user();
		$locationcheck = new LokasiCheck();
		$locationcheck->pengajuan_id = $request->input('pengajuan_id');
		$locationcheck->commitcheck_id = $request->input('verifcommit_id');
		$locationcheck->pkscheck_id = $request->input('verifpks_id');
		$locationcheck->poktan_id = $request->input('poktan_id');
		$locationcheck->anggota_id = $request->input('anggotamitra_id');
		$locationcheck->npwp = $request->input('npwp');
		$locationcheck->no_ijin = $request->input('no_ijin');
		$locationcheck->onlineverif_at = Carbon::now();
		$locationcheck->onlineverif_by = $user->id;
		$locationcheck->onlinestatus = $request->input('onlinestatus');
		$locationcheck->onlinenote = $request->input('onlinenote');
		// dd($locationcheck);
		$locationcheck->save();
		return redirect()->route('verification.data.show', $locationcheck->pengajuan_id)
			->with('success', 'Data berhasil disimpan');
	}

	public function store(Request $request, $id)
	{
		//verifikator
		$user = Auth::user();

		//pilih tabel pengajuans
		$verifikasi = AjuVerifTanam::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$filenpwp = str_replace(['.', '-'], '', $commitment->npwp);
		$filecommitment = str_replace(['/', '.'], '', $commitment->no_ijin);
		abort_if(
			Gate::denies('online_access') ||
				($verifikasi->no_pengajuan != $request->input('no_pengajuan') &&
					$verifikasi->no_ijin != $request->input('no_ijin') &&
					$verifikasi->npwp != $request->input('npwp')),
			Response::HTTP_FORBIDDEN,
			'403 Forbidden'
		);

		//simpan data ke tabel pengajuans
		$status = '4';
		$verifikasi->metode = $request->input('metode');
		$verifikasi->status = $status;
		$verifikasi->note = $request->input('note');
		$verifikasi->check_by = $user->id;
		$verifikasi->verif_at = Carbon::now();
		if ($request->hasFile('batanam')) {
			$file = $request->file('batanam');
			$filename = 'batanam_' . $filecommitment . '.' . $file->getClientOriginalExtension();
			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$verifikasi->batanam = $filename;
		}
		if ($request->hasFile('ndhprt')) {
			$file = $request->file('ndhprt');
			$filename = 'notdin_' . $filecommitment . '.' . $file->getClientOriginalExtension();
			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$verifikasi->ndhprt = $filename;
		}
		$commitment->status = $status;
		// dd($verifikasi, $commitment);
		$verifikasi->save();
		$commitment->save();
		return redirect()->route('verification.tanam.show', $id)
			->with('success', 'Data berhasil disimpan');
	}

	public function show($id)
	{
		abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		// Page level
		$module_name = 'Permohonan';
		$page_title = 'Data Pengajuan';
		$page_heading = 'Hasil Verifikasi Tanam';
		$heading_class = 'fal fa-check-square';

		// Populate related data
		$verifikasi = AjuVerifTanam::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->firstOrFail();
		// $commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)->firstOrFail();
		$pkschecks = PksCheck::where('pengajuan_id', $verifikasi->id)->get();
		$lokasichecks = LokasiCheck::where('pengajuan_id', $verifikasi->id)->orderBy('created_at', 'desc')->get();

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $verifikasi->no_ijin)
			->with(['pkscheck' => function ($query) use ($id) {
				$query->where('pengajuan_id', $id);
			}])
			->get();
		$poktanIds = Pks::where('no_ijin', $verifikasi->no_ijin)
			->pluck('poktan_id'); // Retrieve the poktan_id values

		// Group poktan_id values and retrieve unique nama_kelompok values
		$poktans = MasterPoktan::whereIn('id', $poktanIds)
			->groupBy('poktan_id')
			->pluck('nama_kelompok', 'poktan_id');
		// dd($poktans);
		$lokasis = collect();
		foreach ($pkschecks as $pkscheck) {
			$lokasi = Lokasi::where('poktan_id', $pkscheck->poktan_id)
				->where('no_ijin', $verifikasi->no_ijin)
				->get();
			$lokasis->push($lokasi);
		}

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		// $pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();


		return view('admin.verifikasi.tanam.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pkschecks', 'lokasichecks', 'pkss', 'poktans', 'lokasis', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks'));
	}

	//  public function commitmentcheck($id)
	//  {
	// 	 abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

	// 	 $module_name = 'Verifikasi';
	// 	 $page_title = 'Verifikasi Data';
	// 	 $page_heading = 'Pemeriksaan Berkas Komitmen';
	// 	 $heading_class = 'fal fa-file-search';

	// 	 $user = Auth::user();
	// 	 $commitmentcheck = CommitmentCheck::findOrFail($id);
	// 	 $commitment = PullRiph::findOrFail($commitmentcheck->pengajuan->commitment_id);

	// 	 // dd($commitmentcheck);

	// 	 return view('admin.verifikasi.online.commitmentcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'user', 'commitmentcheck', 'commitment'));
	//  }

	//  public function commitmentstore(Request $request, $id)
	//  {
	// 	 abort_if(Gate::denies('online_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

	// 	 $user = Auth::user();
	// 	 $commitmentcheck = CommitmentCheck::findOrFail($id);

	// 	 $pengajuan = Pengajuan::find($commitmentcheck->pengajuan_id);
	// 	 $commitmentcheck->verif_by = $user->id;
	// 	 $commitmentcheck->verif_at = Carbon::now();
	// 	 $commitmentcheck->formRiph = $request->input('formRiph');
	// 	 $commitmentcheck->formSptjm = $request->input('formSptjm');
	// 	 $commitmentcheck->logbook = $request->input('logbook');
	// 	 $commitmentcheck->formRt = $request->input('formRt');
	// 	 $commitmentcheck->formRta = $request->input('formRta');
	// 	 $commitmentcheck->formRpo = $request->input('formRpo');
	// 	 $commitmentcheck->formLa = $request->input('formLa');
	// 	 $commitmentcheck->note = $request->input('note');

	// 	 $commitmentcheck->save();
	// 	 return redirect()->route('verification.data.show', $pengajuan->id)
	// 		 ->with('success', 'Data Pemeriksaan berhasil disimpan');
	//  }
}
