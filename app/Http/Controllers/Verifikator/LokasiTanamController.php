<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\AjuVerifTanam;
use App\Models\DataRealisasi;
use App\Models\FotoProduksi;
use App\Models\FotoTanam;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Gate;
use Illuminate\Support\Facades\DB;

use App\Models\Lokasi;
use App\Models\MasterAnggota;
use App\Models\MasterPoktan;
use App\Models\Pks;
use App\Models\PullRiph;

class LokasiTanamController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($noIjin)
	{
		$no_ijin = substr_replace($noIjin, '/', 4, 0);
		$no_ijin = substr_replace($no_ijin, '.', 7, 0);
		$no_ijin = substr_replace($no_ijin, '/', 11, 0);
		$no_ijin = substr_replace($no_ijin, '/', 13, 0);
		$no_ijin = substr_replace($no_ijin, '/', 16, 0);
		$commitment = PullRiph::where('no_ijin', $no_ijin)->first();

		$query = Lokasi::where('no_ijin', $no_ijin)
			// ->where('polygon', '!=', null) //uncomment this line to include the condition
			->with('masterkelompok', 'masteranggota')
			->select('id', 'npwp', 'no_ijin', 'poktan_id', 'anggota_id', 'nama_lokasi', 'luas_tanam', 'volume');

		$lokasis = $query->get()->map(function ($lokasi) {
			$poktan = MasterPoktan::where('id', $lokasi->poktan_id)->value('nama_kelompok');
			$anggota = MasterAnggota::where('id', $lokasi->anggota_id)->value('nama_petani');
			$noIjin = str_replace(['/', '.', '-'], '', $lokasi->no_ijin);

			//ubah route halaman ke daftar lokasi dari setiap petani
			$showRoute = route('verification.listLokasibyPetani', [
				'noIjin' => $noIjin,
				'lokasiId' => $lokasi->id
			]);
			return [
				// 'idPks'			=> $lokasi->id,
				// 'npwp'			=> $lokasi->npwp,
				// 'no_ijin'		=> $lokasi->no_ijin,
				// 'poktan_id'		=> $lokasi->poktan_id,
				'poktan'		=> $poktan,
				'anggota'		=> $anggota,
				'nama_lokasi'	=> $lokasi->nama_lokasi,
				'luas_tanam'	=> $lokasi->luas_tanam,
				'volume'		=> $lokasi->volume,
				'show'			=> $showRoute,
			];
		});
		$data = [
			'lokasis' => $lokasis,
		];
		return response()->json($data);
	}

	public function listLokasibyPetani($noIjin, $lokasiId)
	{
		$module_name = 'Verifikasi Data';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		$no_ijin = substr_replace($noIjin, '/', 4, 0);
		$no_ijin = substr_replace($no_ijin, '.', 7, 0);
		$no_ijin = substr_replace($no_ijin, '/', 11, 0);
		$no_ijin = substr_replace($no_ijin, '/', 13, 0);
		$no_ijin = substr_replace($no_ijin, '/', 16, 0);

		$ajuVerifTanam = AjuVerifTanam::where('no_ijin', $no_ijin)->firstOrFail()->id;
		$lokasi = Lokasi::find($lokasiId);
		$dataRealisasi = DataRealisasi::where('no_ijin', $no_ijin)
			->where('lokasi_id', $lokasiId)
			->get();

		$pks = $lokasi->pks->no_perjanjian;
		$poktan = $lokasi->masterkelompok->nama_kelompok;
		$anggota = $lokasi->masteranggota->nama_petani;
		$luasAwal = $lokasi->masteranggota->luas_lahan;

		// dd($pks, $poktan, $anggota, $luasAwal, $dataRealisasi);
		return view('admin.verifikasi.listLokasi', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'pks', 'poktan', 'anggota', 'luasAwal', 'dataRealisasi', 'ajuVerifTanam'));
	}

	public function showLocation($id)
	{
		$module_name = 'Verifikasi Data';
		$page_title = 'Verifikasi Data Lokasi';
		$page_heading = 'Pemeriksaan Data Tanam dan Produksi';
		$heading_class = 'fal fa-ballot-check';

		$lokasi = DataRealisasi::findOrFail($id);
		$noIjin = $lokasi->no_ijin;
		$realNpwp = $lokasi->npwp_company;
		$pks = $lokasi->pks->no_perjanjian;

		$fotoTanams = FotoTanam::where('realisasi_id', $id)->get();
		$fotoProduksis = FotoProduksi::where('realisasi_id', $id)->get();

		return view('admin.verifikasi.locationcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'noIjin', 'realNpwp', 'pks', 'fotoTanams', 'fotoProduksis'));
	}

	//dibuang


	public function show($noIjin, $anggota_id)
	{
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

		$anggotamitra = $lokasi;
		// dd($anggotamitra);
		return view('admin.verifikasi.locationcheck', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'lokasi', 'pks', 'commitment', 'anggotamitra'));
	}
}
