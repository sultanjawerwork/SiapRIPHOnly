<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifTanam;
use App\Models\CommitmentCheck;
use App\Models\Pengajuan;
use App\Models\PullRiph;
use App\Models\Lokasi;
use App\Models\LokasiCheck;
use App\Models\MasterPoktan;
use App\Models\Pks;
use App\Models\PksCheck;
use App\Models\UserDocs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PengajuanController extends Controller
{
	//halaman daftar pengajuan untuk importir
	public function index(Request $request)
	{
		$module_name = 'Proses RIPH';
		$page_title = 'Daftar Pengajuan Verifikasi';
		$page_heading = 'Daftar Pengajuan Verifikasi';
		$heading_class = 'fal fa-ballot-check';

		$verifTanams = AjuVerifTanam::where('npwp', Auth::user()->data_user->npwp_company)
			->where('status', '<', 5)
			->whereHas('commitment', function ($query) {
				$query->where('status', '<', 5);
			})
			->get();

		$verifProduksis = AjuVerifProduksi::where('npwp', Auth::user()->data_user->npwp_company)
			// ->where('status', '>', 4)
			->whereHas('commitment', function ($query) {
				$query->where('status', '>', 4);
				$query->where('status', '<', 8);
			})
			->get();

		return view('admin.pengajuan.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifTanams', 'verifProduksis'));
	}

	public function create($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Data Pengajuan';
		$heading_class = 'fal fa-file-invoice';

		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		// $lokasi = AnggotaRiph::where('no_ijin', $commitment->no_ijin);

		if (request()->ajax()) {
			$lokasis = Lokasi::join('master_poktans', 'lokasis.poktan_id', '=', 'master_poktans.poktan_id')
				->join('master_anggotas', 'lokasis.anggota_id', '=', 'master_anggotas.anggota_id')
				->join('pks', 'lokasis.poktan_id', '=', 'pks.poktan_id')
				->where('lokasis.npwp', $npwp_company)
				->where('lokasis.no_ijin', $commitment->no_ijin)
				->orderBy('lokasis.poktan_id', 'asc')
				->select(
					sprintf('%s.*', (new Lokasi())->getTable()),
					'master_poktans.nama_kelompok as nama_kelompok',
					'master_anggotas.nama_petani as nama_petani'
				);

			$table = Datatables::of($lokasis);

			$table->addColumn('data_geolokasi', function ($row) {
				$nullCount = 0;
				$nulledColumns = [];

				if (empty($row->latitude)) {
					$nullCount++;
					$nulledColumns[] = 'lat?';
				}
				if (empty($row->longitude)) {
					$nullCount++;
					$nulledColumns[] = 'long?';
				}
				if (empty($row->polygon)) {
					$nullCount++;
					$nulledColumns[] = 'poly?';
				}
				if (empty($row->altitude)) {
					$nullCount++;
					$nulledColumns[] = 'alt?';
				}

				if ($nullCount === 4) {
					return '<span class="badge badge-xs badge-danger">Tidak Ada</span>';
				} elseif ($nullCount > 0) {
					$nulledColumnsHtml = '';
					foreach ($nulledColumns as $column) {
						$nulledColumnsHtml .= '<span class="badge badge-xs badge-warning">' . $column . '</span> ';
					}
					return $nulledColumnsHtml;
				} else {
					return '<span class="badge badge-xs badge-success">Lengkap</span>';
				}
			});

			$table->editColumn('id', function ($row) {
				return $row->id ? $row->id : '';
			});
			$table->editColumn('nama_kelompok', function ($row) {
				return $row->nama_kelompok ? $row->nama_kelompok : '';
			});
			$table->editColumn('nama_lokasi', function ($row) {
				return $row->nama_lokasi ? $row->nama_lokasi : '';
			});
			$table->editColumn('anggota_id', function ($row) {
				return $row->anggota_id ? $row->anggota_id : '';
			});
			$table->editColumn('nama_petani', function ($row) {
				return $row->nama_petani ? $row->nama_petani : '';
			});
			$table->editColumn('luas_tanam', function ($row) {
				return $row->luas_tanam ? $row->luas_tanam : '';
			});
			$table->editColumn('volume', function ($row) {
				return $row->volume ? $row->volume : '';
			});

			$table->rawColumns(['data_geolokasi']);

			return $table->make(true);
		}

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}

		// dd($row->data_geolokasi);
		return view('admin.pengajuan.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'total_luastanam', 'total_volume', 'pks', 'disabled'));
	}

	public function store($id, Request $request)
	{
		//validasi sebelum pengajuan di-submit
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::findOrFail($id);
		// $totalVolume = $commitment->anggotariph->sum('volume');
		// $targetVolume = $commitment->volume_riph * 0.05;

		// if ($totalVolume < $targetVolume) {
		// 	return redirect()->back()->withErrors([
		// 		'volume' => 'Total volume produksi/panen yang dilaporkan TIDAK MEMENUHI SYARAT untuk pengajuan verifikasi. Silahkan lengkapi dan penuhi persyaratan terlebih dahulu.',
		// 	]);
		// }

		$pengajuan = new Pengajuan();
		// get current month and year as 2-digit and 4-digit strings
		$month = date('m');
		$year = date('Y');
		// retrieve the latest record for the current month and year
		$latestRecord = Pengajuan::where('no_pengajuan', 'like', "%/{$month}/{$year}")
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
		$pengajuan->no_ijin = $commitment->no_ijin;
		$pengajuan->npwp = $npwp_company;
		$pengajuan->commitment_id = $commitment->id;
		$pengajuan->status = '1';
		$pengajuan->save();

		$commitment->status = '1';
		$commitment->save();

		$no_pengajuan = $pengajuan->no_pengajuan;
		$pengajuanId = Pengajuan::where('no_pengajuan', $no_pengajuan)->first();
		$commitmentcheck = new CommitmentCheck();
		$commitmentcheck->pengajuan_id = $pengajuanId->id;
		$commitmentcheck->no_pengajuan = $no_pengajuan;
		$commitmentcheck->pullriph_id = $commitment->id;
		$commitmentcheck->npwp = $npwp_company;
		$commitmentcheck->no_ijin = $pengajuan->no_ijin;
		$commitmentcheck->status = $pengajuan->status;
		// dd($commitmentcheck);
		$commitmentcheck->save();
		return redirect()->route('admin.task.commitment.realisasi', $pengajuan->commitment_id)
			->with('success', 'Pengajuan Verifikasi Anda telah kami terima, dan akan segera Kami tindaklanjut. Terima Kasih.');
	}

	public function showAjuTanam($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Data Pengajuan';
		$heading_class = 'fal fa-file-invoice';

		// Populate related data
		$verifikasi = AjuVerifTanam::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$userDocs = UserDocs::where('no_ijin', $verifikasi->no_ijin)->first();
		// $commitmentcheck = CommitmentCheck::where('pengajuan_id', $verifikasi->id)->firstOrFail();
		$pkschecks = PksCheck::where('pengajuan_id', $verifikasi->id)->get();
		$lokasichecks = LokasiCheck::where('pengajuan_id', $verifikasi->id)->orderBy('created_at', 'desc')->get();

		$pkss = Pks::withCount('lokasi')->where('no_ijin', $verifikasi->no_ijin)
			->where('berkas_pks', '!=', null)
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

		$anggotas = Lokasi::where('no_ijin', $commitment->no_ijin);

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		// $pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();
		$countAnggota = $anggotas->count();
		$hasGeoloc = $anggotas->count('polygon');
		// dd($hasGeoloc);

		return view('admin.pengajuan.veriftanam.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pkschecks', 'lokasichecks', 'pkss', 'poktans', 'lokasis', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks', 'countAnggota', 'hasGeoloc', 'userDocs'));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Models\Pengajuan  $pengajuan
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Pengajuan $pengajuan)
	{
		//
	}



	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Pengajuan  $pengajuan
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Pengajuan $pengajuan)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Pengajuan  $pengajuan
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Pengajuan $pengajuan)
	{
		//
	}
}
