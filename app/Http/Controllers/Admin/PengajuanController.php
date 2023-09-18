<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifSkl;
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
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

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
			->with('commitment')
			->get();

		$verifProduksis = AjuVerifProduksi::where('npwp', Auth::user()->data_user->npwp_company)
			->with('commitment')
			->get();

		$verifSkls = AjuVerifSkl::where('npwp', Auth::user()->data_user->npwp_company)
			->with('commitment')
			->get();
		// dd($verifSkls);
		/**
		 * memerlukan:
		 * migrasi dan model file verifSkl dan table avskl
		 * controller untuk verifikasi dan pengajuan SKL
		 * syarat pengajuan:
		 * 1. Volume Produksi yang dilaporkan sudah >100%
		 * 2. hasil verifikasi produksi = 8/telah di verifikasi produksi (cek lagi kode status)
		 */

		return view('admin.pengajuan.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifTanams', 'verifProduksis', 'verifSkls'));
	}

	//pindahkan pengajuan verifikasi tanam verifTanamController ke sini
	public function ajuVerifTanam($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi Tanam';
		$page_heading = 'Pengajuan Verifikasi Tanam';
		$heading_class = 'fal fa-file-invoice';

		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$countPoktan = $pks->count();
		$countPks = $pks->where('berkas_pks', '!=', null)->count();


		// $lokasi = AnggotaRiph::where('no_ijin', $commitment->no_ijin);

		if (request()->ajax()) {
			$lokasis = Lokasi::join('master_poktans', 'lokasis.poktan_id', '=', 'master_poktans.poktan_id')
				->join('master_anggotas', 'lokasis.anggota_id', '=', 'master_anggotas.anggota_id')
				->join('pks', 'lokasis.poktan_id', '=', 'pks.poktan_id')
				->where('lokasis.npwp', $npwp_company)
				->where('lokasis.no_ijin', $commitment->no_ijin)
				// ->where(function ($query) {
				// 	$query->whereNotNull('poktan_riphs.no_perjanjian')
				// 		->whereNotNull('poktan_riphs.berkas_pks');
				// })
				->orderBy('lokasis.poktan_id', 'asc')
				->select(
					sprintf('%s.*', (new Lokasi())->getTable()),
					'master_poktans.nama_kelompok as nama_kelompok',
					'master_anggotas.nama_petani as nama_petani'
				);

			$table = DataTables::of($lokasis);

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

		// dd($row->data_geolokasi);
		return view('admin.pengajuan.veriftanam.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'total_luastanam', 'total_volume', 'pks', 'countPoktan', 'countPks'));
	}

	public function ajuVerifTanamStore($id)
	{
		$commitment = PullRiph::findOrFail($id);

		// Find AjuVerifProduksi where no_ijin is $commitment->no_ijin.
		$ajuVerifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first();

		if ($ajuVerifTanam && $ajuVerifTanam->status === '10') {
			session()->flash('message', 'SKL telah terbit. Anda tidak dapat membuat pengajuan verifikasi kembali untuk RIPH ini.');
			return redirect()->back();
		}

		// Continue with creating or updating ajuVerifTanam.
		AjuVerifTanam::updateOrCreate(
			[
				'npwp' => $commitment->npwp,
				'commitment_id' => $commitment->id,
				'no_ijin' => $commitment->no_ijin,
			],
			[
				'status' => '1',
			]
		);

		return redirect()->route('admin.task.commitment')
			->with('success', 'Pengajuan verifikasi tanam berhasil dibuat.');
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

	//pindahkan pengajuan verifikasi produksi ke sini
	public function ajuVerifProduksi($id)
	{

		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		// aktifkan saat production
		// abort_if($total_volume < $commitment->volume_riph * 0.05 / 100 * 6, Response::HTTP_FORBIDDEN, 'Total produksi dilaporkan tidak memenuhi syarat');

		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi Produksi';
		$page_heading = 'Pengajuan Verifikasi Produksi';
		$heading_class = 'fal fa-file-invoice';

		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();
		$countPoktan = $pks->count();
		$countPks = $pks->where('berkas_pks', '!=', null)->count();

		// $lokasi = AnggotaRiph::where('no_ijin', $commitment->no_ijin);

		if (request()->ajax()) {
			$lokasis = Lokasi::join('master_poktans', 'lokasis.poktan_id', '=', 'master_poktans.poktan_id')
				->join('master_anggotas', 'lokasis.anggota_id', '=', 'master_anggotas.anggota_id')
				->join('pks', 'lokasis.poktan_id', '=', 'pks.poktan_id')
				->where('lokasis.npwp', $npwp_company)
				->where('lokasis.no_ijin', $commitment->no_ijin)
				// ->where(function ($query) {
				// 	$query->whereNotNull('poktan_riphs.no_perjanjian')
				// 		->whereNotNull('poktan_riphs.berkas_pks');
				// })
				->orderBy('lokasis.poktan_id', 'asc')
				->select(
					sprintf('%s.*', (new Lokasi())->getTable()),
					'master_poktans.nama_kelompok as nama_kelompok',
					'master_anggotas.nama_petani as nama_petani'
				);

			$table = DataTables::of($lokasis);

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

		// dd($disabled);

		// dd($row->data_geolokasi);
		return view('admin.pengajuan.verifproduksi.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'total_luastanam', 'total_volume', 'pks', 'countPoktan', 'countPks'));
	}

	public function ajuVerifProduksiStore($id)
	{
		$commitment = PullRiph::findOrFail($id);

		// Find AjuVerifProduksi where no_ijin is $commitment->no_ijin.
		$ajuVerifProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->first();

		if ($ajuVerifProduksi && $ajuVerifProduksi->status === '10') {
			session()->flash('message', 'SKL telah terbit. Anda tidak dapat membuat pengajuan verifikasi kembali untuk RIPH ini.');
			return redirect()->back();
		}

		// Continue with creating or updating AjuVerifProduksi.
		AjuVerifProduksi::updateOrCreate(
			[
				'npwp' => $commitment->npwp,
				'commitment_id' => $commitment->id,
				'no_ijin' => $commitment->no_ijin,
			],
			[
				'status' => '1',
			]
		);

		return redirect()->route('admin.task.commitment')
			->with('success', 'Pengajuan verifikasi produksi berhasil dibuat.');
	}

	public function showAjuProduksi($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Verifikasi';
		$page_heading = 'Data Pengajuan';
		$heading_class = 'fal fa-file-invoice';

		// Populate related data
		$verifikasi = AjuVerifProduksi::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $verifikasi->no_ijin)->first();
		$userDocs = UserDocs::where('no_ijin', $verifikasi->no_ijin)->first();
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
		foreach ($pkss as $pkscheck) {
			$lokasi = Lokasi::where('poktan_id', $pkscheck->poktan_id)
				->where('no_ijin', $verifikasi->no_ijin)
				->get();
			$lokasis->push($lokasi);
		}

		$anggotas = Lokasi::where('no_ijin', $commitment->no_ijin);

		$total_luastanam = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');
		$countPoktan = $pkss->count();
		$countPks = $pkss->where('berkas_pks', '!=', null)->count();
		$countAnggota = $anggotas->count();
		$hasGeoloc = $anggotas->count('polygon');
		// dd($hasGeoloc);

		return view('admin.pengajuan.verifproduksi.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'verifikasi', 'commitment', 'pkss', 'poktans', 'lokasis', 'total_luastanam', 'total_volume', 'countPoktan', 'countPks', 'countAnggota', 'hasGeoloc', 'userDocs'));
	}

	//pengajuan verifikasi skl
	public function ajuVerifSkl($id)
	{
		/**
		 * syarat pengajuan SKL
		 * 1. Status Verifikasi Produksi = 4
		 * 2. Berkas SPH-SBS (Produksi) = ada
		 * 3. total volume produksi >= komitment
		 */
		$user = Auth::user();

		$module_name = 'Komitmen';
		$page_title = 'Pengajuan Penerbitan SKL';
		$page_heading = 'Pengajuan Penerbitan SKL';
		$heading_class = 'fal fa-file-invoice';

		$npwp_company = $user->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);
		$verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first() ?? new AjuVerifTanam();
		$verifProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->first() ?? new AjuVerifProduksi();
		$userDocs = UserDocs::where('no_ijin', $commitment->no_ijin)->first() ?? new UserDocs();
		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get() ?? new Pks();
		$lokasis = Lokasi::where('no_ijin', $commitment->no_ijin)->get() ?? new Lokasi();

		//ringkasan umum
		$company = $user->data_user->company_name;
		$noIjin = $commitment->no_ijin;
		$periode = $commitment->periodetahun;

		//ringkasan pengajuan verifikasi tanam
		$avtDate = $verifTanam->created_at;
		$avtVerifAt = $verifTanam->verif_at;
		$avtStatus = $verifTanam->status;
		$avtMetode = $verifTanam->metode;
		$avtNote = $verifTanam->note;

		//ringkasan pengajuan verifikasi produksi
		$avpDate = $verifProduksi->created_at;
		$avpVerifAt = $verifProduksi->verif_at;
		$avpStatus = $verifProduksi->status;
		$avpMetode = $verifProduksi->metode;
		$avpNote = $verifProduksi->note;

		//ringkasan kewajiban dan realisasi
		$wajibTanam = $commitment->luas_wajib_tanam;
		$wajibProduksi = $commitment->volume_produksi;
		$realisasiTanam = $lokasis->sum('luas_tanam');
		$realisasiProduksi = $lokasis->sum('volume');
		$hasGeoloc = $lokasis->where('polygon', '!=', null)->count();

		//ringkasan kemitraan
		$countPoktan = $pks->count();
		$countPks = $pks->where('berkas_pks', '!=', null)->count();
		$countAnggota = $lokasis->count();

		return view('admin.pengajuan.verifSkl.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'company', 'noIjin', 'periode', 'wajibTanam', 'wajibProduksi', 'realisasiTanam', 'realisasiProduksi', 'hasGeoloc', 'countPoktan', 'countPks', 'countAnggota', 'avtDate', 'avtVerifAt', 'avtStatus', 'avtMetode', 'avtNote', 'avpDate', 'avpVerifAt', 'avpStatus', 'avpMetode', 'avpNote', 'userDocs'));
	}

	public function ajuVerifSklStore($id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)
			->findOrFail($id);

		$verifTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)->first();
		$verifProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)->first();
		$userDoc = UserDocs::where('no_ijin', $commitment->no_ijin)->first();
		$pks = Pks::where('no_ijin', $commitment->no_ijin)->get();

		//data validasi
		$lokasis = Lokasi::where('no_ijin', $commitment->no_ijin)->get();
		$wajibTanam = $commitment->luas_wajib_tanam;
		$wajibProduksi = $commitment->volume_produksi;
		$realisasiTanam = $lokasis->sum('luas_tanam');
		$realisasiProduksi = $lokasis->sum('volume');

		// Validasi berkas
		if ($userDoc === null) {
			$errorMessage = 'Anda belum memiliki kelengkapan dokumen untuk diperiksa.';
		} elseif ($userDoc->sptjm === null) {
			$errorMessage = 'Surat Pertanggungjawaban Mutlak tidak ditemukan.';
		} elseif ($userDoc->spskl === null) {
			$errorMessage = 'Surat Pengajuan Penerbitan SKL tidak ditemukan.';
		} elseif ($userDoc->rta === null) {
			$errorMessage = 'Form Realisasi Tanam tidak ditemukan.';
		} elseif ($userDoc->rpo === null) {
			$errorMessage = 'Form Realisasi Produksi tidak ditemukan.';
		} elseif ($userDoc->sphproduksi === null) {
			$errorMessage = 'Dokumen SPH-SBS (Tanam dan Produksi) tidak ditemukan.';
		} elseif ($userDoc->formLa === null) {
			$errorMessage = 'Dokumen Laporan Akhir tidak ditemukan.';
			// } elseif ($realisasiTanam < $wajibTanam) {
			// 	$errorMessage = 'Realisasi Luas Tanam yang dilaporkan tidak memenuhi syarat.';
		} elseif ($verifTanam === null || $verifTanam->status !== '4') {
			$errorMessage = 'Hasil Verifikasi tahap tanam tidak memenuhi syarat.';
		} elseif ($realisasiProduksi < $wajibProduksi) {
			$errorMessage = 'Realisasi Produksi yang dilaporkan tidak memenuhi syarat.';
		} elseif ($verifProduksi === null || $verifProduksi->status !== '4') {
			$errorMessage = 'Hasil Verifikasi tahap Produksi tidak memenuhi syarat.';
		}

		$optionalMessage = 'Pengajuan Surat Keterangan Lunas untuk RIPH No ' . $commitment->no_ijin . ' tidak dapat dilakukan. Ajukan kembali setelah Anda melengkapi syarat-syarat yang diperlukan.';

		if (isset($errorMessage)) {
			return redirect()->route('admin.task.commitment')->withErrors($errorMessage . $optionalMessage);
		}

		AjuVerifSkl::updateOrCreate(
			[
				'npwp' => $commitment->npwp,
				'commitment_id' => $commitment->id,
				'no_ijin' => $commitment->no_ijin,
			],
			[
				'status' => '1',
			]
		);
		return redirect()->route('admin.task.commitment')
			->with('success', 'Surat Keterangan Lunas berhasil diajukan.');
	}



	//ke bawah kemungkinan di hapus

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
