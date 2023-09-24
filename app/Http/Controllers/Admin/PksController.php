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
use App\Models\Saprodi;
use App\Models\Varietas;
use Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

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
			$filename = 'pks_' . $pks->poktan_id . '.' . $file->getClientOriginalExtension();
			$file->storeAs('uploads/' . $filenpwp . '/' . $commitment->periodetahun, $filename, 'public');
			$pks->berkas_pks = $filename;
		}
		$pks->save();

		return redirect()->route('admin.task.commitment.realisasi', $commitment->id)->with('message', "Data berhasil disimpan.");
	}

	public function anggotas($id)
	{
		$module_name = 'Realisasi';
		$page_title = 'Lokasi Tanam';
		$page_heading = 'Daftar Lokasi Tanam';
		$heading_class = 'fal fa-map-marked';

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
