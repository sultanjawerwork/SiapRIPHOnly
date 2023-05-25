<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

use App\Models\Commitmentbackdate;
use App\Models\MasterPenangkar;
use App\Models\MasterKelompok;
use App\Models\PenangkarMitra;
use App\Models\PengajuanV2;
use App\Models\verif_commitment;
use App\Models\User;

class CommitmentBackdateController extends Controller
{
	// use SimeviTrait;

	// public $access_token = '';
	// public $data_user;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		//
		$module_name = 'Komitmen';
		$page_title = 'Daftar Komitmen';
		$page_heading = 'Daftar Komitmen';
		$heading_class = 'fa fa-file-invoice';

		$masterpenangkars = MasterPenangkar::all();
		$user = Auth::user();
		$commitments = CommitmentBackdate::where('user_id', $user->id)->get();
		// dd($user);

		return view('v2.commitment.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'user', 'commitments', 'masterpenangkars'));
	}

	public function create()
	{
		//
		$module_name = 'Komitmen';
		$page_title = 'Komitmen Baru';
		$page_heading = 'Tambah Komitmen Baru';
		$heading_class = 'fa fa-file-invoice';
		$masterpenangkars = MasterPenangkar::all();

		return view('v2.commitment.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'masterpenangkars'));
	}

	public function store(Request $request)
	{
		//
		$commitments = new CommitmentBackdate();
		$commitments->user_id = Auth::user()->id;
		$commitments->no_ijin = $request->input('no_ijin');
		$commitments->periodetahun = $request->input('periodetahun');
		$commitments->tgl_ijin = $request->input('tgl_ijin');
		$commitments->tgl_end = $request->input('tgl_end');
		$commitments->no_hs = $request->input('no_hs');
		$commitments->volume_riph = $request->input('volume_riph');
		$commitments->no_hs = $request->input('no_hs');
		$commitments->stok_mandiri = $request->input('stok_mandiri');
		$commitments->organik = $request->input('organik');
		$commitments->npk = $request->input('npk');
		$commitments->dolomit = $request->input('dolomit');
		$commitments->za = $request->input('za');
		$commitments->mulsa = $request->input('mulsa');
		$commitments->poktan_share = $request->input('poktan_share');

		//upload formRiph
		if ($request->hasFile('formRiph')) {
			$attch = $request->file('formRiph');
			$attchname = 'formRiph_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRiph', $attch, $attchname);
			$commitments->formRiph = $attchname;
		}

		//upload formSptjm
		if ($request->hasFile('formSptjm')) {
			$attch = $request->file('formSptjm');
			$attchname = 'formSptjm_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formSptjm', $attch, $attchname);
			$commitments->formSptjm = $attchname;
		}

		//upload logbook
		if ($request->hasFile('logbook')) {
			$attch = $request->file('logbook');
			$attchname = 'logbook_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'logbook', $attch, $attchname);
			$commitments->logbook = $attchname;
		}

		//upload formRt
		if ($request->hasFile('formRt')) {
			$attch = $request->file('formRt');
			$attchname = 'formRt_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRt', $attch, $attchname);
			$commitments->formRt = $attchname;
		}

		//upload formRta
		if ($request->hasFile('formRta')) {
			$attch = $request->file('formRta');
			$attchname = 'formRta_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRta', $attch, $attchname);
			$commitments->formRta = $attchname;
		}

		//upload formRpo
		if ($request->hasFile('formRpo')) {
			$attch = $request->file('formRpo');
			$attchname = 'formRpo_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRpo', $attch, $attchname);
			$commitments->formRpo = $attchname;
		}

		//upload formLa
		if ($request->hasFile('formLa')) {
			$attch = $request->file('formLa');
			$attchname = 'formLa_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formLa', $attch, $attchname);
			$commitments->formLa = $attchname;
		}

		// dd($commitments);
		$commitments->save();

		return redirect()->route('admin.task.commitments.index')->with('success', 'Data Commitment Saved successfully');
	}

	public function show($id)
	{

		$module_name = 'Komitmen';
		$page_title = 'Detail';
		$page_heading = 'Detail Komitmen';
		$heading_class = 'fa fa-file-invoice';

		$commitment = CommitmentBackdate::with('user', 'pksmitra.masterkelompok', 'penangkarmitra.masterpenangkar', 'pengajuanv2')
			->where('user_id', Auth::id())
			->findOrFail($id);
		$masterkelompoks = MasterKelompok::all();
		$masterpenangkars = MasterPenangkar::all();
		$pengajuanv2 = PengajuanV2::all();
		$pksmitras = $commitment->pksmitra;
		$penangkarmitras = $commitment->penangkarmitra;

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}

		// dd();
		return view('v2.commitment.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'masterpenangkars', 'penangkarmitras', 'commitment', 'masterkelompoks', 'pksmitras', 'pengajuanv2', 'disabled'));
	}

	public function edit($id)
	{
		//
		//load all commitments for current user
		// $commitments = CommitmentBackdate::with('user')->findOrFail($id);
		$commitments = CommitmentBackdate::with('user')
			->where('user_id', Auth::id())
			->findOrFail($id);

		//load all Master Penangkar for reference in blade view
		$masterpenangkars = MasterPenangkar::all();

		//load all Penangkar Mitra for current Commitment (commitment_backdate_id)
		$penangkarmitras = PenangkarMitra::with('commitmentbackdate')->get();

		$module_name = 'Komitmen';
		$page_title = 'Ubah Data Komitmen';
		$page_heading = 'Ubah data Komitmen: ' . $commitments->no_ijin;
		$heading_class = 'fal fa-file-edit';


		return view('v2.commitment.edit', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitments', 'masterpenangkars', 'penangkarmitras'));
	}

	public function update(Request $request, $id)
	{
		//
		$commitments = CommitmentBackdate::find($id);
		$commitments->user_id = Auth::user()->id;
		$commitments->no_ijin = $request->input('no_ijin');
		$commitments->periodetahun = $request->input('periodetahun');
		$commitments->tgl_ijin = $request->input('tgl_ijin');
		$commitments->tgl_end = $request->input('tgl_end');
		$commitments->no_hs = $request->input('no_hs');
		$commitments->volume_riph = $request->input('volume_riph');
		$commitments->no_hs = $request->input('no_hs');
		$commitments->stok_mandiri = $request->input('stok_mandiri');
		$commitments->organik = $request->input('organik');
		$commitments->npk = $request->input('npk');
		$commitments->dolomit = $request->input('dolomit');
		$commitments->za = $request->input('za');
		$commitments->mulsa = $request->input('mulsa');
		$commitments->poktan_share = $request->input('poktan_share');

		//upload formRiph
		if ($request->hasFile('formRiph')) {
			$attch = $request->file('formRiph');
			$attchname = 'formRiph_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRiph', $attch, $attchname);
			$commitments->formRiph = $attchname;
		}

		//upload formSptjm
		if ($request->hasFile('formSptjm')) {
			$attch = $request->file('formSptjm');
			$attchname = 'formSptjm_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formSptjm', $attch, $attchname);
			$commitments->formSptjm = $attchname;
		}

		//upload logbook
		if ($request->hasFile('logbook')) {
			$attch = $request->file('logbook');
			$attchname = 'logbook_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'logbook', $attch, $attchname);
			$commitments->logbook = $attchname;
		}

		//upload formRt
		if ($request->hasFile('formRt')) {
			$attch = $request->file('formRt');
			$attchname = 'formRt_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRt', $attch, $attchname);
			$commitments->formRt = $attchname;
		}

		//upload formRta
		if ($request->hasFile('formRta')) {
			$attch = $request->file('formRta');
			$attchname = 'formRta_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRta', $attch, $attchname);
			$commitments->formRta = $attchname;
		}

		//upload formRpo
		if ($request->hasFile('formRpo')) {
			$attch = $request->file('formRpo');
			$attchname = 'formRpo_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formRpo', $attch, $attchname);
			$commitments->formRpo = $attchname;
		}

		//upload formLa
		if ($request->hasFile('formLa')) {
			$attch = $request->file('formLa');
			$attchname = 'formLa_' . $commitments->id . '_' . time() . '.' . $attch->getClientOriginalExtension();
			Storage::disk('public')->putFileAs('docs/commitmentsv2/' . $request->input('periodetahun') . '/' . 'formLa', $attch, $attchname);
			$commitments->formLa = $attchname;
		}

		$commitments->save();
		return redirect()->route('admin.task.commitments.index')->with('success', 'Data Commitment updated successfully');
	}

	public function read($id)
	{
		//
		//load all commitments for current user
		$commitments = CommitmentBackdate::with('user')
			->where('user_id', Auth::id())
			->findOrFail($id);

		//load all Master Penangkar for reference in blade view
		$masterpenangkars = MasterPenangkar::all();

		//load all Penangkar Mitra for current Commitment (commitment_backdate_id)
		$penangkarmitras = PenangkarMitra::with('commitmentbackdate')->get();

		$module_name = 'Komitmen';
		$page_title = 'Komitmen Detail';
		$page_heading = 'Data Komitmen: ' . $commitments->no_ijin;
		$heading_class = 'fal fa-file-invoice';


		return view('v2.commitment.read', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitments', 'masterpenangkars', 'penangkarmitras'));
	}

	public function penangkar($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Penangkar';
		$page_heading = 'Penangkar Mitra';
		$heading_class = 'fa fa-file-invoice';

		$commitment = CommitmentBackdate::with('user')
			->where('user_id', Auth::id())
			->findOrFail($id);
		$masterpenangkars = MasterPenangkar::all();
		$commitmentbackdate = CommitmentBackdate::with('penangkarmitra.masterpenangkar')
			->findOrFail($id);
		$penangkarmitras = $commitmentbackdate->penangkarmitra;

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}

		return view('v2.commitment.penangkarmitra', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'masterpenangkars', 'penangkarmitras', 'commitmentbackdate', 'disabled'));
	}

	public function pksmitra($id)
	{
		$module_name = 'Komitmen';
		$page_title = 'Kerjasama';
		$page_heading = 'Perjanjian Kerjasama';
		$heading_class = 'fa fa-file-signature';

		$commitment = CommitmentBackdate::with(['user', 'pksmitra.masterkelompok'])
			->where('user_id', Auth::id())
			->findOrFail($id);

		if (empty($commitment->status) || $commitment->status == 3 || $commitment->status == 5) {
			$disabled = false; // input di-enable
		} else {
			$disabled = true; // input di-disable
		}
		$masterkelompoks = MasterKelompok::all();
		// $commitmentbackdate = CommitmentBackdate::with('pksmitra.masterkelompok')
		// 	->findOrFail($id);
		$pksmitras = $commitment->pksmitra;

		return view('v2.commitment.pksmitra.create', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'commitment', 'masterkelompoks', 'pksmitras', 'disabled'));
	}

	public function destroy($id)
	{
		$commitments = CommitmentBackdate::withTrashed()
			->where('user_id', Auth::id())
			->findOrFail($id);
		$commitments->penangkarmitra()->delete(); //delete related object here
		$commitments->pengajuanv2()->delete(); //delete related object here
		$commitments->pksmitra()->delete();
		$commitments->delete();
		return redirect()->route('admin.task.commitments.index')->with('success', 'Data Commitment deleted successfully');
	}
}
