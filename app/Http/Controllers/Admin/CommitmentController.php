<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PullRiph;
use App\Models\PenangkarRiph;
use App\Models\Pks;

use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\MassDestroyPullriphRequest;
use App\Http\Controllers\Traits\SimeviTrait;
use App\Models\AjuVerifProduksi;
use App\Models\AjuVerifSkl;
use App\Models\AjuVerifTanam;
use App\Models\Skl;
use App\Models\UserDocs;
use App\Models\Varietas;
use Symfony\Component\HttpFoundation\Response;

class CommitmentController extends Controller
{
	use SimeviTrait;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$module_name = 'Proses RIPH';
		$page_title = 'Daftar Komitmen';
		$page_heading = 'Daftar Komitmen';
		$heading_class = 'fal fa-ballot-check';

		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitments = PullRiph::where('npwp', $npwp_company)
			->with('skl')
			->get();
		$pksCount = 0; // Initialize with a default value
		$pksFileCount = 0; // Initialize with a default value

		if ($commitments) {
			foreach ($commitments as $commitment) {
				$sumLuas = $commitment->lokasi->sum('luas_tanam');
				$sumVolume = $commitment->lokasi->sum('volume');
				$minThresholdTanam = $commitment->luas_wajib_tanam;
				$minThresholdProd = $commitment->volume_produksi;

				$commitment->sumLuas = $sumLuas;
				$commitment->sumVolume = $sumVolume;
				$commitment->minThresholdTanam = $minThresholdTanam;
				$commitment->minThresholdProd = $minThresholdProd;
				$thesePks = Pks::where('no_ijin', $commitment->no_ijin)->get();
				$pksCount = $thesePks->count();
				$pksFileCount = $thesePks
					->whereNotNull('berkas_pks')
					->count();
				$userDocs = UserDocs::where('npwp', $npwp_company)
					->where('commitment_id', $commitment->id) // Assuming 'id' is the correct field to match commitments and userDocs
					->where('no_ijin', $commitment->no_ijin)
					->first();

				$ajuTanam = AjuVerifTanam::where('no_ijin', $commitment->no_ijin)
					->first();

				$ajuProduksi = AjuVerifProduksi::where('no_ijin', $commitment->no_ijin)
					->first();

				$ajuSkl = AjuVerifSkl::where('no_ijin', $commitment->no_ijin)
					->first();

				$skl = Skl::where('no_ijin', $commitment->no_ijin)->first();

				// Add userDocs to the commitment
				$commitment->userDocs = $userDocs;
				$commitment->ajuTanam = $ajuTanam;
				$commitment->ajuProduksi = $ajuProduksi;
				$commitment->ajuSkl = $ajuSkl;
				$commitment->skl = $skl;
			}
		}
		return view('admin.commitment.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwp_company', 'commitments', 'pksCount', 'pksFileCount'));
	}

	public function show($id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp_company)->findOrFail($id);

		$module_name = 'Komitmen';
		$page_title = 'Data Komitmen';
		$page_heading = 'Data Komitmen: ' . $commitment->no_ijin;
		$heading_class = 'fal fa-file-edit';

		return view('admin.commitment.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwp_company', 'commitment'));
	}

	public function update(Request $request, $id)
	{
		$npwp_company = Auth::user()->data_user->npwp_company;
		$noRiph = PullRiph::findOrFail($id)->no_ijin;
		$riphData = PullRiph::findOrFail($id);
		$filenpwp = str_replace(['.', '-'], '', $npwp_company);

		try {
			DB::beginTransaction();

			$commitment = PullRiph::updateOrCreate(
				[
					'npwp' => $npwp_company,
					'no_ijin' => $noRiph,
				],
				[
					'poktan_share' => $request->poktan_share,
					'importir_share' => $request->importir_share,
				]
			);
			// Handle file uploads
			if ($request->hasFile('formRiph')) {
				$file = $request->file('formRiph');
				$filename = 'formRiph_' . $file->getClientOriginalName();
				$file->storeAs('uploads/' . $filenpwp . '/' . $riphData->periodetahun, $filename, 'public');
				$commitment->formRiph = $filename;
			}

			if ($request->hasFile('formSptjm')) {
				$file = $request->file('formSptjm');
				$filename = 'formSptjm_' . $file->getClientOriginalName();
				$file->storeAs('uploads/' . $filenpwp . '/' . $riphData->periodetahun, $filename, 'public');
				$commitment->formSptjm = $filename;
			}

			if ($request->hasFile('logbook')) {
				$file = $request->file('logbook');
				$filename = 'logBook' . $file->getClientOriginalName();
				$file->storeAs('uploads/' . $filenpwp . '/' . $riphData->periodetahun, $filename, 'public');
				$commitment->logbook = $filename;
			}

			if ($request->hasFile('formRt')) {
				$file = $request->file('formRt');
				$filename = 'formRt' . $file->getClientOriginalName();
				$file->storeAs('uploads/' . $filenpwp . '/' . $riphData->periodetahun, $filename, 'public');
				$commitment->formRt = $filename;
			}

			if ($request->hasFile('formRta')) {
				$file = $request->file('formRta');
				$filename = 'formRta' . $file->getClientOriginalName();
				$file->storeAs('uploads/' . $filenpwp . '/' . $riphData->periodetahun, $filename, 'public');
				$commitment->formRta = $filename;
			}

			if ($request->hasFile('formRpo')) {
				$file = $request->file('formRpo');
				$filename = 'formRpo' . $file->getClientOriginalName();
				$file->storeAs('uploads/' . $filenpwp . '/' . $riphData->periodetahun, $filename, 'public');
				$commitment->formRpo = $filename;
			}

			if ($request->hasFile('formLa')) {
				$file = $request->file('formLa');
				$filename = 'formLa' . $file->getClientOriginalName();
				$file->storeAs('uploads/' . $filenpwp . '/' . $riphData->periodetahun, $filename, 'public');
				$commitment->formLa = $filename;
			}
			// Repeat the above code for the remaining file inputs...
			$commitment->save();
			DB::commit();

			return back()->with('success', "Data berhasil disimpan.");
		} catch (ValidationException $e) {
			DB::rollback();
			return back()->withErrors('Terjadi kesalahan saat unggah file');
		} catch (\Exception $e) {
			DB::rollback();
			// throw $e; // Uncomment this line if you want to rethrow the exception
			return back()->withErrors('Terjadi kesalahan saat unggah file');
		}
	}

	public function realisasi($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Data Realisasi';
		$page_heading = 'Realisasi Komitmen';
		$heading_class = 'fal fa-file-edit';

		$npwp = Auth::user()->data_user->npwp_company;
		$commitment = PullRiph::where('npwp', $npwp)
			->findOrFail($id);
		$pkss = Pks::withCount('lokasi')
			->where('npwp', $npwp)
			->where('no_ijin', $commitment->no_ijin)
			->get();
		foreach ($pkss as $pks) {
			// Calculate the sum of luas_lahan for this Pks record
			$luasLahanSum = $pks->masterpoktan->anggota->sum('luas_lahan');

			// Assign the sum to the Pks object
			$pks->sum_luaslahan = $luasLahanSum;
		}

		$docs = UserDocs::where('commitment_id', $id)->first();
		// dd($docs);
		$penangkars = PenangkarRiph::where('npwp', $npwp)
			->when(isset($commitment->no_ijin), function ($query) use ($commitment) {
				return $query->where('no_ijin', $commitment->no_ijin);
			}, function ($query) use ($commitment) {
				return $query->where('commitment_id', $commitment->id);
			})
			->get();
		$varietass = Varietas::all();
		$commitmentStatus = $commitment->status;
		if (empty($commitmentStatus) || $commitmentStatus == 3 || $commitmentStatus == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}
		// dd($pkss);
		return view('admin.commitment.realisasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'pkss', 'penangkars', 'docs', 'npwp', 'varietass', 'disabled'));
	}

	public function storeUserDocs(Request $request, $id)
	{
		$commitment = PullRiph::find($id);
		$realnpwp = $commitment->npwp;
		$npwp = str_replace(['.', '-'], '', $commitment->npwp);
		$realNoIjin = $commitment->no_ijin;
		$noIjin = str_replace(['/', '.'], '', $realNoIjin);
		$userFiles = [];
		try {
			DB::beginTransaction();

			$fileFields = [
				'sptjm',
				'spvt',
				'rta',
				'sphtanam',
				'spdst',
				'logbooktanam',
				'spvp',
				'rpo',
				'formLa',
				'sphproduksi',
				'spdsp',
				'logbookproduksi',
				'spskl'
				// Tambahkan field-file lainnya di sini
			];

			foreach ($fileFields as $field) {
				if ($request->hasFile($field)) {
					$file = $request->file($field);
					$file_name = $field . '_' . $noIjin . '.' . $file->getClientOriginalExtension();
					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $commitment->periodetahun, $file_name, 'public');
					$userFiles[$field] = $file_name;
				}
			}

			$data = UserDocs::updateOrCreate(
				[
					'npwp' => $realnpwp,
					'commitment_id' => $id,
					'no_ijin' => $realNoIjin
				],
				array_merge($request->all(), $userFiles) // Menggabungkan data form dan file dalam satu array
			);
			DB::commit();

			// Flash message sukses
			return redirect()->back()->with('success', 'Berkas berhasil diunggah.');
		} catch (\Exception $e) {
			// Rollback transaksi jika ada kesalahan
			DB::rollBack();

			// Flash message kesalahan
			return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggh berkas: ' . $e->getMessage());
		}
	}

	public function submission($id)
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
		// dd($total_volume);
		return view('admin.commitment.realisasi', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
	}


	public function destroy($id)
	{
		abort_if(Gate::denies('commitment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		$pullRiph = PullRiph::find($id);
		$pullRiph->delete();

		return back();
	}

	public function massDestroy(MassDestroyPullriphRequest $request)
	{
		PullRiph::whereIn('id', request('ids'))->delete();
		return response(null, Response::HTTP_NO_CONTENT);
	}

	//dihapus
	// public function store(Request $request)
	// {
	// 	$realnpwp = Auth::user()::find(Auth::user()->id)->data_user->npwp_company;

	// 	$pullRiph = PullRiph::where('npwp', $realnpwp)->first();

	// 	DB::beginTransaction();
	// 	try {
	// 		if ($pullRiph) {
	// 			$npwp = str_replace('.', '', $realnpwp);
	// 			$npwp = str_replace('-', '', $npwp);
	// 			$userFiles = [];
	// 			$userFiles += array('status' => 1);
	// 			if ($request->hasFile('formRiph')) {
	// 				if ($request->formRiph != null) {
	// 					$file = $request->file('formRiph');
	// 					$file_name = 'formRiph.' . $file->getClientOriginalExtension();
	// 					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $pullRiph->periodetahun, $file_name, 'public');
	// 					$userFiles += array('formRiph' => $file_path);
	// 				};
	// 			}
	// 			if ($request->hasFile('formSptjm')) {
	// 				if ($request->formSptjm != null) {
	// 					$file = $request->file('formSptjm');
	// 					$file_name = 'formSptjm.' . $file->getClientOriginalExtension();
	// 					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $pullRiph->periodetahun, $file_name, 'public');
	// 					$userFiles += array('formSptjm' => $file_path);
	// 				};
	// 			}
	// 			if ($request->hasFile('logBook')) {
	// 				if ($request->logBook != null) {
	// 					$file = $request->file('logBook');
	// 					$file_name = 'logBook.' . $file->getClientOriginalExtension();
	// 					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $pullRiph->periodetahun, $file_name, 'public');
	// 					$userFiles += array('logBook' => $file_path);
	// 				};
	// 			}
	// 			if ($request->hasFile('formRt')) {
	// 				if ($request->formRt != null) {
	// 					$file = $request->file('formRt');
	// 					$file_name = 'formRt.' . $file->getClientOriginalExtension();
	// 					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $pullRiph->periodetahun, $file_name, 'public');
	// 					$userFiles += array('formRt' => $file_path);
	// 				};
	// 			}
	// 			if ($request->hasFile('formRta')) {
	// 				if ($request->formRta != null) {
	// 					$file = $request->file('formRta');
	// 					$file_name = 'formRta.' . $file->getClientOriginalExtension();
	// 					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $pullRiph->periodetahun, $file_name, 'public');
	// 					$userFiles += array('formRta' => $file_path);
	// 				};
	// 			}
	// 			if ($request->hasFile('formRpo')) {
	// 				if ($request->formRpo != null) {
	// 					$file = $request->file('formRpo');
	// 					$file_name = 'formRpo.' . $file->getClientOriginalExtension();
	// 					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $pullRiph->periodetahun, $file_name, 'public');
	// 					$userFiles += array('formRpo' => $file_path);
	// 				};
	// 			}
	// 			if ($request->hasFile('formLa')) {
	// 				if ($request->formLa != null) {
	// 					$file = $request->file('formLa');
	// 					$file_name = 'formLa.' . $file->getClientOriginalExtension();
	// 					$file_path = $file->storeAs('uploads/' . $npwp . '/' . $pullRiph->periodetahun, $file_name, 'public');
	// 					$userFiles += array('formLa' => $file_path);
	// 				};
	// 			}
	// 			$pengajuan = Pengajuan::updateOrCreate(
	// 				['detail' => $pullRiph->no_ijin],
	// 				['jenis' => 1, 'status' => 1]
	// 			);
	// 			// dd($pengajuan);
	// 			$userFiles += array('no_doc' => $pengajuan->no_doc);
	// 			PullRiph::updateOrCreate(
	// 				['npwp' => $realnpwp, 'no_ijin' => $request->get('no_ijin')],
	// 				$userFiles
	// 			);
	// 		}
	// 	} catch (ValidationException $e) {
	// 		DB::rollback();
	// 		return  back()->withErrors('Terjadi kesalahan saat unggah file');
	// 	} catch (\Exception $e) {
	// 		DB::rollback();
	// 		//throw $e;
	// 		return back()->withErrors('Terjadi kesalahan saat unggah file');
	// 	}

	// 	DB::commit();
	// 	return back()->with('success', 'Sukses mengunggah file..');
	// }

	// public function show($id)
	// {
	// 	$pullRiph = PullRiph::findOrFail($id);
	// 	$pengajuan = Pengajuan::where('no_doc', $pullRiph->no_doc)->get();
	// 	$npwp = (Auth::user()::find(Auth::user()->id)->data_user->npwp_company ?? null);
	// 	$nomor = '';
	// 	if (!empty($npwp)) {
	// 		$npwp = str_replace('.', '', $npwp);
	// 		$npwp = str_replace('-', '', $npwp);
	// 		$nomor = str_replace('.', '', $pullRiph->no_ijin);
	// 		$nomor = str_replace('/', '', $nomor);
	// 		$pullData = $this->pull($npwp, $nomor);
	// 	} else
	// 		$pullData = null;


	// 	$access_token = $this->getAPIAccessToken(config('app.simevi_user'), config('app.simevi_pwd'));


	// 	$data_poktan = [];
	// 	$poktans = null;
	// 	if ($pullData) {

	// 		$query = 'select g.no_riph, g.id_kecamatan, g.nama_kelompok, g.nama_pimpinan, g.hp_pimpinan, count(p.nama_petani) as jum_petani, round(SUM(p.luas_lahan),2) as luas
	//         from poktans p, group_tanis g
	//         where p.no_riph = "' . $pullRiph->no_ijin . '"' . ' and p.id_poktan=g.id_poktan
	//         GROUP BY g.nama_kelompok';


	// 		$poktans = DB::select(DB::raw($query));

	// 		foreach ($poktans as $poktan) {
	// 			$datakecamatan = $this->getAPIKecamatan($access_token, $poktan->id_kecamatan);
	// 			$kec = $datakecamatan['data'][0]['nm_kec'];
	// 			$poktan->kecamatan = $kec;
	// 		}
	// 	}
	// 	$module_name = 'Proses RIPH';
	// 	$page_title = 'Data RIPH';
	// 	$page_heading = 'Data RIPH';
	// 	$heading_class = 'fal fa-file-invoice';
	// 	return view('admin.commitment.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'pullRiph', 'pullData', 'pengajuan', 'poktans', 'nomor'));
	// }

	// public function edit($id)
	// {
	// 	$npwp_company = Auth::user()->data_user->npwp_company;
	// 	$commitment = PullRiph::where('npwp', $npwp_company)->findOrFail($id);

	// 	$module_name = 'Komitmen';
	// 	$page_title = 'Data Komitmen';
	// 	$page_heading = 'Data Komitmen: ' . $commitment->no_ijin;
	// 	$heading_class = 'fal fa-file-edit';

	// 	return view('admin.commitment.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'npwp_company', 'commitment'));
	// }
}
