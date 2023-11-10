<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Pks;
use App\Models\Lokasi;
use App\Models\MasterAnggota;
use App\Models\PullRiph;
use App\Models\MasterKecamatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\SimeviTrait;
use App\Models\DataRealisasi;
use App\Models\FotoProduksi;
use App\Models\FotoTanam;
use App\Models\Saprodi;
use App\Models\Varietas;
use Dflydev\DotAccessData\Data;
use Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Storage;

class PksController extends Controller
{
	use SimeviTrait;

	public function index(Request $request)
	{
		abort_if(Gate::denies('pks_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		if ($request->ajax()) {
			$npwp = (Auth::user()::find(Auth::user()->id)->data_user->npwp_company ?? null);

			if (!auth()->user()->isAdmin) {
				$query = Pks::where('npwp', $npwp)->select(sprintf('%s.*', (new Pks())->table));
			} else
				$query = Pks::query()->select(sprintf('%s.*', (new Pks())->table));


			$table = Datatables::of($query);

			$table->addColumn('placeholder', '&nbsp;');
			$table->addColumn('actions', '&nbsp;');

			$table->editColumn('actions', function ($row) {
				$viewGate = 'pks_show';
				$deleteGate = 'pks_delete';
				$editGate = 'pks_edit';
				$crudRoutePart = 'task.pks';

				return view('partials.datatablesActions', compact(
					'viewGate',
					'editGate',
					'deleteGate',
					'crudRoutePart',
					'row'
				));
			});

			$table->editColumn('id', function ($row) {
				return $row->id ? $row->id : '';
			});
			$table->editColumn('npwp', function ($row) {
				return $row->npwp ? $row->npwp : '';
			});
			$table->editColumn('no_riph', function ($row) {
				return $row->no_riph ? $row->no_riph : '';
			});
			$table->editColumn('no_perjanjian', function ($row) {
				return $row->no_perjanjian ? $row->no_perjanjian : '';
			});
			$table->editColumn('tgl_perjanjian_start', function ($row) {
				return $row->tgl_perjanjian_start ? date('d/m/Y', strtotime($row->tgl_perjanjian_start)) : '';
			});
			$table->editColumn('tgl_perjanjian_end', function ($row) {
				return $row->tgl_perjanjian_end ? date('d/m/Y', strtotime($row->tgl_perjanjian_end)) : '';
			});
			$table->editColumn('jumlah_anggota', function ($row) {
				return $row->jumlah_anggota ? $row->jumlah_anggota : 0;
			});
			$table->editColumn('luas_rencana', function ($row) {
				return $row->luas_rencana ? $row->luas_rencana : 0;
			});
			$table->editColumn('varietas_tanam', function ($row) {
				return $row->varietas_tanam ? $row->varietas_tanam : '';
			});
			$table->editColumn('luas_wajib_tanam', function ($row) {
				return $row->periode_tanam ?  $row->periode_tanam : '';
			});
			$table->editColumn('provinsi', function ($row) {
				return $row->provinsi ? $row->provinsi : '';
			});
			$table->editColumn('kabupaten', function ($row) {
				return $row->kabupaten ? $row->kabupaten : '';
			});
			$table->editColumn('kecamatan', function ($row) {
				return $row->kecamatan ? $row->kecamatan : '';
			});
			$table->editColumn('desa', function ($row) {
				return $row->provinsi ? $row->provinsi : '';
			});
			$table->editColumn('berkas_pks', function ($row) {
				return $row->berkas_pks ? $row->berkas_pks : '';
			});

			$table->rawColumns(['actions', 'placeholder']);

			return $table->make(true);
		}
		$module_name = 'Proses RIPH';
		$page_title = 'Daftar PKS';
		$page_heading = 'Daftar PKS ';
		$heading_class = 'fal fa-ballot-check';
		return view('admin.pks.index', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}

	public function create($id)
	{
		$npwp = (Auth::user()::find(Auth::user()->id)->data_user->npwp_company ?? null);

		$nomor = Str::substr($no_riph, 0, 4) . '/' . Str::substr($no_riph, 4, 2) . '.' . Str::substr($no_riph, 6, 3) . '/' .
			Str::substr($no_riph, 9, 1) . '/' . Str::substr($no_riph, 10, 2) . '/' . Str::substr($no_riph, 12, 4);

		$query = 'select g.nama_kelompok, g.id_kecamatan, g.id_kelurahan , count(p.nama_petani) as jum_petani, round(SUM(p.luas_lahan),2) as luas from poktans p, group_tanis g where p.npwp = "' . $npwp . '"' . ' and p.id_poktan=g.id_poktan and g.no_riph= "' . $nomor . '" and g.id_poktan = "' . $poktan . '" GROUP BY g.nama_kelompok';


		$poktans = DB::select(DB::raw($query));
		// dd($poktans);
		foreach ($poktans as $poktan) {
			$access_token = $this->getAPIAccessToken(config('app.simevi_user'), config('app.simevi_pwd'));
			$datakecamatan = $this->getAPIKecamatan($access_token, $poktan->id_kecamatan);
			if ($datakecamatan['data'][0]) {
				$kec = $datakecamatan['data'][0]['nm_kec'];
				$poktan->kecamatan = $kec;
			}
			$datakelurahan = $this->getAPIDesa($access_token, $poktan->id_kelurahan);
			if ($datakelurahan['data'][0]) {
				$desa = $datakelurahan['data'][0]['nm_desa'];
				$poktan->kelurahan = $desa;
			}
		}

		// dd($poktans);
		$module_name = 'Proses RIPH';
		$page_title = 'Kelompok Tani';
		$page_heading = 'Buat PKS ';
		$heading_class = 'fal fa-ballot-check';
		return view('admin.pks.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'poktans'));
	}

	public function store(Request $request)
	{
		//
	}

	public function show(Pks $pks)
	{
		//
	}

	public function edit($id)
	{
		$module_name = 'Realisasi';
		$page_title = 'Kelompok Tani';
		$page_heading = 'Data Perjanjian';
		$heading_class = 'fal fa-file-invoice';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$pks = Pks::withCount('lokasi')
			->where('npwp', $npwpCompany)
			->findOrFail($id);

		$sumLuasLahan = $pks->masterpoktan->anggota->sum('luas_lahan');

		$pks->sum_luaslahan = $sumLuasLahan;

		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();

		$commitmentStatus = $commitment->status;
		$commitmentId = $commitment->id;

		$varietass = Varietas::all();

		// dd($commitmentId);

		if (empty($commitmentStatus) || $commitmentStatus == 3 || $commitmentStatus == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}
		return redirect()->back()->with('success', 'Berkas berhasil diunggah.');
		// return view('admin.pks.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pks', 'disabled', 'commitmentId', 'npwpCompany', 'commitment', 'varietass'));
	}

	public function update(Request $request, $id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$pks = Pks::findOrFail($id);
		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)
			->first();
		// dd($commitment);
		$filenpwp = str_replace(['.', '-'], '', $npwp_company);
		$pks->no_perjanjian = $request->input('no_perjanjian');
		$pks->tgl_perjanjian_start = $request->input('tgl_perjanjian_start');
		$pks->tgl_perjanjian_end = $request->input('tgl_perjanjian_end');
		$pks->luas_rencana = $request->input('luas_rencana');
		$pks->varietas_tanam = $request->input('varietas_tanam');
		$pks->periode_tanam = $request->input('periode_tanam');
		if ($request->hasFile('berkas_pks')) {
			$file = $request->file('berkas_pks');
			if ($file->getClientOriginalExtension() === 'pdf') {
				$filename = 'pks_' . $pks->poktan_id . '.' . $file->getClientOriginalExtension();
				$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
				$pks->berkas_pks = $filename;
			} else {
				return redirect()->back()->with('error', 'Berkas harus memiliki ekstensi .pdf.');
			}
		}
		$pks->save();

		return redirect()->route('admin.task.commitment.realisasi', $commitment->id)->with('message', "Data berhasil disimpan.");
	}

	public function anggotas($id)
	{
		$module_name = 'Realisasi';
		$page_title = 'Daftar Petani Mitra';
		$page_heading = 'Daftar Petani Mitra';
		$heading_class = 'fal fa-user-hard-hat';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$pks = Pks::where('npwp', $npwpCompany)
			->findOrFail($id);
		$commitment = PullRiph::where('npwp', $npwpCompany)
			->where('no_ijin', $pks->no_ijin)
			->first();
		$anggotas = MasterAnggota::where('npwp', $npwpCompany)
			->where('poktan_id', $pks->poktan_id)
			->get();
		$lokasis = Lokasi::where('npwp', $npwpCompany)
			->where('no_ijin', $pks->no_ijin)
			->where('poktan_id', $pks->poktan_id)
			->get();

		$sumLuas = $lokasis->sum('luas_tanam');
		$sumProduksi = $lokasis->sum('volume');
		// dd($sumLuas, $sumProduksi);

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}

		return view('admin.pks.anggotas', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'pks', 'commitment', 'anggotas', 'lokasis', 'disabled', 'sumLuas', 'sumProduksi'));
	}

	public function listLokasi($pksId, $anggotaId)
	{
		$module_name = 'Realisasi';
		$page_title = 'Daftar Lokasi Tanam';
		$page_heading = 'Daftar Lokasi Tanam per Anggota';
		$heading_class = 'fal fa-map-marked';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$pks = Pks::find($pksId);
		$anggota = Lokasi::find($anggotaId);
		$listLokasi = DataRealisasi::where('lokasi_id', $anggotaId)->get();
		// dd($anggota->datarealisasi->sum('luas_lahan'));


		return view('admin.pks.listLokasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'pks', 'anggota', 'listLokasi'));
	}

	public function addLokasiTanam($pksId, $anggotaId)
	{
		$module_name = 'Realisasi';
		$page_title = 'Realisasi Tanam-Produksi';
		$page_heading = 'Realisasi Tanam-Produksi';
		$heading_class = 'fal fa-farm';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$pks = Pks::findOrFail($pksId);
		$anggota = Lokasi::findOrFail($anggotaId);

		return view('admin.pks.addLokasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'pks', 'anggota'));
	}

	public function storeLokasiTanam(Request $request)
	{
		$npwpCompany = Auth::user()->data_user->npwp_company;

		$pksId = $request->input('pks_id');
		$anggotaId = $request->input('lokasi_id');
		// dd($anggotaId);

		$newLokasi = new DataRealisasi();
		$newLokasi->npwp_company = $npwpCompany;
		$newLokasi->no_ijin = $request->input('no_ijin');
		$newLokasi->poktan_id = $request->input('poktan_id');
		$newLokasi->pks_id = $request->input('pks_id');
		$newLokasi->anggota_id = $request->input('anggota_id');
		$newLokasi->lokasi_id = $request->input('lokasi_id');

		$newLokasi->nama_lokasi = $request->input('nama_lokasi');
		$newLokasi->latitude = $request->input('latitude');
		$newLokasi->longitude = $request->input('longitude');
		$newLokasi->polygon = $request->input('polygon');
		$newLokasi->altitude = $request->input('altitude');
		$newLokasi->luas_kira = $request->input('luas_kira');

		//data tanam
		$newLokasi->mulai_tanam = $request->input('mulai_tanam');
		$newLokasi->akhir_tanam = $request->input('akhir_tanam');
		$newLokasi->luas_lahan = $request->input('luas_lahan');

		$newLokasi->save();

		// udate table lokasis
		$lokasiId = $request->input('lokasi_id');
		$updateLokasi = Lokasi::find($lokasiId);
		$dataRealisasi = DataRealisasi::where('lokasi_id', $lokasiId)->get();
		$firstTanam = $dataRealisasi->min('mulai_tanam');
		$firstProduksi = $dataRealisasi->min('mulai_panen');
		$sumLuas = $dataRealisasi->sum('luas_lahan');
		$sumVolume = $dataRealisasi->sum('volume');

		$updateLokasi->tgl_tanam = $firstTanam;
		$updateLokasi->tgl_panen = $firstProduksi;
		$updateLokasi->luas_tanam = $sumLuas;
		$updateLokasi->volume = $sumVolume;
		$countLokasi = $dataRealisasi->count();
		$updateLokasi->nama_lokasi = $countLokasi;
		$updateLokasi->save();
		// dd('Luas awal: ' . $updateLokasi->luas_tanam, 'Luas baru: ' . $sumLuas, 'Volume awal: ' . $updateLokasi->volume, 'Volume baru: ' . $sumVolume, 'Jumlah awal: ' . $updateLokasi->nama_lokasi = $countLokasi, 'Jumlah baru: ' . $updateLokasi->count);

		// dd($request->all());
		return redirect()->route('admin.task.pks.anggota.listLokasi', [$pksId, $anggotaId])->with('message', "Data berhasil disimpan.");
	}

	public function editLokasiTanam($pksId, $anggotaId, $id)
	{
		$module_name = 'Realisasi';
		$page_title = 'Realisasi Tanam';
		$page_heading = 'Realisasi Tanam dan Spasial';
		$heading_class = 'fal fa-farm';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$pks = Pks::findOrFail($pksId);
		$anggota = Lokasi::findOrFail($anggotaId);
		$lokasi = DataRealisasi::find($id);

		return view('admin.pks.editLokasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'pks', 'anggota', 'lokasi'));
	}

	public function updateLokasiTanam(Request $request, $id)
	{
		$npwpCompany = Auth::user()->data_user->npwp_company;

		$pksId = $request->input('pks_id');
		$anggotaId = $request->input('lokasi_id');
		// dd($anggotaId);

		$updateLokasi = DataRealisasi::find($id);
		$updateLokasi->npwp_company = $npwpCompany;
		$updateLokasi->no_ijin = $request->input('no_ijin');
		$updateLokasi->poktan_id = $request->input('poktan_id');
		$updateLokasi->pks_id = $request->input('pks_id');
		$updateLokasi->anggota_id = $request->input('anggota_id');
		$updateLokasi->lokasi_id = $request->input('lokasi_id');

		$updateLokasi->nama_lokasi = $request->input('nama_lokasi');
		$updateLokasi->latitude = $request->input('latitude');
		$updateLokasi->longitude = $request->input('longitude');
		$updateLokasi->polygon = $request->input('polygon');
		$updateLokasi->altitude = $request->input('altitude');
		$updateLokasi->luas_kira = $request->input('luas_kira');

		//data tanam
		$updateLokasi->mulai_tanam = $request->input('mulai_tanam');
		$updateLokasi->akhir_tanam = $request->input('akhir_tanam');
		$updateLokasi->luas_lahan = $request->input('luas_lahan');
		$updateLokasi->save();

		// udate table lokasis
		$lokasiId = $request->input('lokasi_id');
		$dataLokasi = Lokasi::find($lokasiId);
		$dataRealisasi = DataRealisasi::where('lokasi_id', $lokasiId)->get();
		$firstTanam = $dataRealisasi->min('mulai_tanam');
		$firstProduksi = $dataRealisasi->min('mulai_panen');
		$sumLuas = $dataRealisasi->sum('luas_lahan');
		$sumVolume = $dataRealisasi->sum('volume');
		$dataLokasi->tgl_tanam = $firstTanam;
		$dataLokasi->tgl_panen = $firstProduksi;
		$dataLokasi->luas_tanam = $sumLuas;
		$dataLokasi->volume = $sumVolume;
		$countLokasi = $dataRealisasi->count();
		$dataLokasi->nama_lokasi = $countLokasi;
		$dataLokasi->save();
		// dd($request->all());
		return redirect()->route('admin.task.pks.anggota.listLokasi', [$pksId, $anggotaId])->with('message', "Data berhasil disimpan.");
	}

	public function storeRealisasiProduksi(Request $request, $id)
	{
		$produksi = DataRealisasi::findOrFail($id);
		$produksi->mulai_panen = $request->input('mulai_panen');
		$produksi->akhir_panen = $request->input('akhir_panen');
		$produksi->volume = $request->input('volume');
		$produksi->save();

		// udate table lokasis
		$lokasiId = $produksi->lokasi_id;
		$updateLokasi = Lokasi::find($lokasiId);
		$dataRealisasi = DataRealisasi::where('lokasi_id', $lokasiId)->get();
		$firstTanam = $dataRealisasi->min('mulai_tanam');
		$firstProduksi = $dataRealisasi->min('mulai_panen');
		$sumLuas = $dataRealisasi->sum('luas_lahan');
		$sumVolume = $dataRealisasi->sum('volume');
		$updateLokasi->tgl_tanam = $firstTanam;
		$updateLokasi->tgl_panen = $firstProduksi;
		$updateLokasi->luas_tanam = $sumLuas;
		$updateLokasi->volume = $sumVolume;
		$countLokasi = $dataRealisasi->count();
		$updateLokasi->nama_lokasi = $countLokasi;
		$updateLokasi->save();
		// dd($request->all());
		return redirect()->back()->with('success', 'Data berhasil diperbarui.');
	}

	public function fotoLokasi($pksId, $anggotaId, $id)
	{
		$module_name = 'Realisasi';
		$page_title = 'Foto Kegiatan';
		$page_heading = 'Foto Kegiatan Realisasi';
		$heading_class = 'fal fa-images';
		$page_desc = 'Halaman unggahan Foto-foto kegiatan pelaksanaan realisasi komitmen wajib tanam-produksi.';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwpCompany);
		$pks = Pks::findOrFail($pksId);
		$anggota = Lokasi::findOrFail($anggotaId);
		$lokasi = DataRealisasi::find($id);

		$fotoTanams = FotoTanam::where('realisasi_id', $id)->get();
		$fotoProduksis = FotoProduksi::where('realisasi_id', $id)->get();

		return view('admin.pks.fotoLokasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'filenpwp', 'pks', 'anggota', 'lokasi', 'fotoTanams', 'fotoProduksis'));
	}

	public function dropZoneTanam(Request $request)
	{
		$realisasiId = $request->input('lokasiId');
		$periode = $request->input('periode');
		$npwpCompany = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwpCompany);
		$uploadedFiles = [];

		$image = $request->file('file');
		if ($request->file('file')) {
			$newFileName = 'foto_tanam_' . $realisasiId . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
			$filePath = 'uploads/' . $filenpwp . '/' . $periode . '/';
			$image->storeAs($filePath, $newFileName, 'public');

			// Setel $imagePath ke path file lengkap
			$imagePath = url('/') . '/' . $filePath . $newFileName;
		}

		FotoTanam::create([
			'realisasi_id' => $realisasiId,
			'filename' => $newFileName,
			'url' => $imagePath,
		]);

		return response()->json(['success' => 'Sukses', 'lokasi' => $realisasiId, 'files' => $uploadedFiles]);
	}

	public function dropZoneProduksi(Request $request)
	{
		$realisasiId = $request->input('lokasiId');
		$periode = $request->input('periode');
		$npwpCompany = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwpCompany);
		$uploadedFiles = [];

		$image = $request->file('file');
		if ($request->file('file')) {
			$newFileName = 'foto_produksi_' . $realisasiId . '_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
			$image->storeAs('uploads/' . $filenpwp . '/' . $periode . '/', $newFileName, 'public');
		}

		FotoProduksi::create([
			'realisasi_id' => $realisasiId,
			'filename' => $newFileName,
		]);

		return response()->json(['success' => 'Sukses', 'lokasi' => $realisasiId, 'files' => $uploadedFiles]);
	}

	public function deleteFotoTanam($id)
	{
		$foto = FotoTanam::find($id);
		$realisasiId = DataRealisasi::where('id', $foto->realisasi_id)->first();
		$commitment = PullRiph::where('no_ijin', $realisasiId->no_ijin)->first();
		$npwpCompany = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwpCompany);

		// Hapus foto dari basis data
		$foto->delete();

		// Setelah menghapus, Anda bisa mengarahkan ke halaman yang sesuai atau memberikan respons yang sesuai.
		return redirect()->back()->with('success', 'Foto berhasil dihapus.');
	}

	public function deleteFotoProduksi($id)
	{
		$foto = FotoProduksi::find($id);
		$realisasiId = DataRealisasi::where('id', $foto->realisasi_id)->first();
		$commitment = PullRiph::where('no_ijin', $realisasiId->no_ijin)->first();
		$npwpCompany = Auth::user()->data_user->npwp_company;
		$filenpwp = str_replace(['.', '-'], '', $npwpCompany);

		// Hapus foto dari basis data
		$foto->delete();

		// Setelah menghapus, Anda bisa mengarahkan ke halaman yang sesuai atau memberikan respons yang sesuai.
		return redirect()->back()->with('success', 'Foto berhasil dihapus.');
	}

	public function deleteLokasiTanam($id)
	{
		$lokasi = DataRealisasi::find($id);
		FotoTanam::where('realisasi_id', $id)->delete();
		FotoProduksi::where('realisasi_id', $id)->delete();

		// udate table lokasis
		$anggotaId = $lokasi->lokasi_id;
		$updateAnggota = Lokasi::find($anggotaId);

		$dataRealisasi = DataRealisasi::where('lokasi_id', $anggotaId)->where('id', '!=', $id)->get();
		$firstTanam = $dataRealisasi->min('mulai_tanam');
		$firstProduksi = $dataRealisasi->min('mulai_panen');
		$sumLuas = $dataRealisasi->sum('luas_lahan');
		$sumVolume = $dataRealisasi->sum('volume');
		$updateAnggota->tgl_tanam = $firstTanam;
		$updateAnggota->tgl_panen = $firstProduksi;
		$updateAnggota->luas_tanam = $sumLuas;
		$updateAnggota->volume = $sumVolume;
		$countLokasi = $dataRealisasi->count();
		$updateAnggota->nama_lokasi = $countLokasi;
		$updateAnggota->save();
		$lokasi->delete();

		return redirect()->route('admin.task.pks.anggota.listLokasi', [$updateAnggota->id, $anggotaId])->with('message', "Data berhasil dihapus.");
	}

	public function saprodi($id)
	{
		$module_name = 'Realisasi';
		$page_title = 'Daftar Saprodi';
		$page_heading = 'Daftar Bantuan Saprodi';
		$heading_class = 'fal fa-gifts';

		$npwpCompany = Auth::user()->data_user->npwp_company;
		$pks = Pks::where('npwp', $npwpCompany)
			->findOrFail($id);
		$saprodis = Saprodi::where('pks_id', $pks->id)->get();
		$commitment = PullRiph::where('no_ijin', $pks->no_ijin)->first();

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}

		return view('admin.pks.saprodi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwpCompany', 'pks', 'saprodis', 'disabled'));
	}

	public function destroy(Pks $pks)
	{
		//
	}
}
