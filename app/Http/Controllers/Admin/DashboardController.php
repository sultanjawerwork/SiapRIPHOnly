<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\CommitmentBackdate;
use App\Models\AnggotaMitra;
use App\Models\PullRiph;
use App\Models\Lokasi;
use App\Models\RiphAdmin;

class DashboardController extends Controller
{
	public function index()
	{

		$roleaccess = Auth::user()->roleaccess;
		if ($roleaccess == 1) {

			if (Auth::user()->roles[0]->title == 'Admin' || Auth::user()->roles[0]->title == 'Pejabat') {
				$module_name = 'Dashboard';
				$page_title = 'Monitoring Realisasi';
				$page_heading = 'Monitoring';
				$heading_class = 'fal fa-analytics';

				$periodeTahuns = RiphAdmin::all()->groupBy('periode');
				$riph_admin = RiphAdmin::orderBy('periode', 'DESC')->get();

				return view('admin.dashboard.indexadmin', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'riph_admin', 'periodeTahuns'));
			}
			if (Auth::user()->roles[0]->title == 'Verifikator') {
				$module_name = 'Dashboard';
				$page_title = 'Monitoring Verifikasi';
				$page_heading = 'Monitoring';
				$heading_class = 'fal fa-chart-bar';
				return view('admin.dashboard.indexverifikator', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
			}
		}
		if (($roleaccess == 2)) {
			$module_name = 'Dashboard';
			$page_title = 'Ringkasan Data';
			$page_heading = 'Dashboard';
			$heading_class = 'fal fa-tachometer';
			return view('admin.dashboard.indexuser', compact('module_name', 'page_title', 'page_heading', 'heading_class'));
		}
		if (($roleaccess == 3)) {
			$module_name = 'Dashboard';
			$page_title = 'Ringkasan Data';
			$page_heading = 'Dashboard';
			$page_desc = 'Pemantauan dan Analisa Kinerja Realisasi Komitmen';
			$heading_class = 'fal fa-tachometer';


			$periodeTahuns = CommitmentBackdate::all()->groupBy('periodetahun');


			return view('v2.dashboard.data', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'page_desc', 'periodeTahuns'));
		}
	}

	public function map()
	{
		$module_name = 'Dashboard';
		$page_title = 'Pemetaan';
		$page_heading = 'Pemetaan';
		$page_desc = 'Peta Lahan Realisasi Wajib Tanam-Produksi';
		$heading_class = 'fal fa-map-marked-alt';

		$anggotaMitras = Lokasi::with([
			'pks' => function ($query) {
				$query->with('commitment');
			},
			'pks',
			'masteranggota'
		])->get();

		$periodeTahuns = PullRiph::all()->groupBy('periodetahun');


		return view('admin.dashboard.map', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'anggotaMitras', 'page_desc', 'periodeTahuns'));
	}

	public function monitoring(Request $request)
	{
		$module_name = 'Dashboard';
		$page_title = 'Monitoring Realisasi';
		$page_heading = 'Monitoring';
		$heading_class = 'fal fa-analytics mr-1';

		$periodeTahuns = PullRiph::all()->groupBy('periodetahun');

		return view('admin.dashboard.monitoring', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'periodeTahuns'));
	}
}
