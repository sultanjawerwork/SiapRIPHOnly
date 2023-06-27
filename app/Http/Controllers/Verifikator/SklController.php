<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\DataUser;
use App\Models\Skl;
use App\Models\PullRiph;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Completed;

use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SPDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;


class SklController extends Controller
{
	protected $sklid = -1;
	protected $msg = '';

	protected $fpdf; 

	//digunakan oleh Administrator/Verifikator untuk melihat daftar verifikasi yang siap direkomendasikan terbit skl.
	public function index()
	{
		abort_if(Gate::denies('verification_skl_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

		
		$module_name = 'SKL';
		$page_title = 'Daftar Rekomendasi';
		$page_heading = 'Daftar Rekomendasi & SKL';
		$heading_class = 'fa fa-file-signature';

		$recomends = Pengajuan::where('status', '>=', 4)
			->get();

		return view('admin.verifikasi.skl.index', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	//untuk pejabat melihat daftar rekomendasi penerbitan skl.
	public function recomendations()
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Daftar Rekomendasi';
		$page_heading = 'Daftar Rekomendasi Penerbitan SKL';
		$heading_class = 'fa fa-file-signature';

		$recomends = Pengajuan::where('status', '6')
			->get();
		// dd($recomends);
		return view('admin.verifikasi.skl.recomendations', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}

	//untuk pejabat melihat draft skl.
	public function draftSKL($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}
		$module_name = 'SKL';
		$page_title = 'Draft SKL';
		$page_heading = 'Preview Draft SKL';
		$heading_class = 'fa fa-file-signature';
		$user = Auth::user();

		$skl = Skl::findOrFail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = $user;
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.published', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		return view('admin.verifikasi.skl.draftSKL', compact('page_title', 'skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	}

	//dilakukan oleh verifikator/administrator
	public function recomend(Request $request)
	{
		abort_if(Gate::denies('verification_skl_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
		$user = Auth::user();
		$recomend = new Skl();
		$recomend->pengajuan_id = $request->input('pengajuan_id');
		$recomend->no_pengajuan = $request->input('no_pengajuan');
		$recomend->no_ijin = $request->input('no_ijin');
		$recomend->npwp = $request->input('npwp');
		$recomend->no_skl = $request->input('no_skl');
		$recomend->submit_by = $user->id;

		$pengajuan = Pengajuan::where('no_pengajuan', $request->input('no_pengajuan'))->first();
		$pengajuan->status = '6';

		$commitment = PullRiph::where('no_ijin', $request->input('no_ijin'))->first();
		$commitment->status = '6';

		// dd($commitment);
		$recomend->save();
		$pengajuan->save();
		$commitment->save();
		return redirect()->route('verification.skl')
			->with('success', 'Komitmen No. RIPH: ' . $commitment->no_ijin . ', berhasil diajukan untuk penerbitan SKL.');
	}

	//untuk pejabat melihat rekomendasi SKL
	public function showrecom($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'Rekomendasi Penerbitan';
		$page_heading = 'Rekomendasi SKL Terbit';
		$heading_class = 'fa fa-file-signature';

		$skl = Skl::findOrfail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$importir = DataUser::where('npwp_company', $pengajuan->npwp)->first();
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;

		return view('admin.verifikasi.skl.recomshow', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl', 'pengajuan', 'importir', 'wajib_tanam', 'luas_verif', 'wajib_produksi', 'volume_verif', 'commitment'));
	}

	//oleh pejabat bagian ini ditunda dulu sementara. diganti dengan storerecom di bawahnya
	// auto generate pdf
	public function storerecom($id)
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}
		
		$this->sklid = $id;
		
		// DB::transaction(function () {
			try {
				$skl = Skl::find($this->sklid);
				$skl->approved_by = Auth::user()->id;
				$skl->approved_at = Carbon::now();
				$skl->published_date = Carbon::now();
				// dd($skl);
				$pengajuan = Pengajuan::find($skl->pengajuan_id);
				$commitment = PullRiph::find($pengajuan->commitment_id);
				//$commitment = PullRiph::where('no_ijin', $pengajuan->no_ijin)->first();


				$pengajuan->status = '7';
				$commitment->status = '7';
				$commitment->skl = $skl->no_skl;

				$completed = new Completed();
				$completed->no_skl = $skl->no_skl;
				$completed->npwp = $skl->npwp;
				$completed->no_ijin = $skl->no_ijin;
				$completed->periodetahun = $commitment->periodetahun;
				$completed->published_date = Carbon::now();
				$completed->luas_tanam = $pengajuan->luas_verif;
				$completed->volume = $pengajuan->volume_verif;
				$completed->status = 'Lunas';

				$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
				$noIjin = str_replace(['.', '/'], '', $skl->no_ijin);
				$pejabat = User::find($skl->approved_by);

				$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
				$luas_verif = $pengajuan->luas_verif;
				$wajib_produksi = $commitment->volume_riph * 0.05;
				$volume_verif = $pengajuan->volume_verif;
				$total_luas = $commitment->lokasi->sum('luas_tanam');
				$total_volume = $commitment->lokasi->sum('volume');
				$data = [
					'Perusahaan' => $commitment->datauser->company_name,
					'No. RIPH' => $commitment->no_ijin,
					'Status' => 'LUNAS',
					'Tautan' => route('verification.skl.show', $skl->id),
				];

				// $QrCode = QrCode::size(70)->generate(json_encode($data));
				//$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);
				$dtQr = $data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan'];
				$dtPath =  storage_path('temp/') . Str::random(6) . '.png';
				// dd($dtPath);
				//QrCode::format('png')->size(200)->generate($dtQr, $dtPath);

				// $image = QrCode::format('png')
                //  ->size(200)->errorCorrection('H')
                //  ->generate($dtQr);

				// $dtPath = '/temp/img-' . time() . '.png';
				// Storage::disk('local')->put($dtPath, $image); //storage/app/public/img/qr-code/img-1557309130.png
				
				$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
				$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
				$thn = substr($skl->no_ijin, -4);

				

				$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
				$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
				$thn = substr($skl->no_ijin, -4);

				$today = Carbon::now()->isoFormat('dddd, D MMMM Y');
				// Storage::disk('public')->url('uploads/' . $filenpwp . '/' . $thn . '/' . $no_skl . '.pdf');
				$pdfUrl = 'uploads/' . $filenpwp . '/' . $thn . '/' . $no_skl . '.pdf';
				$companyName = $commitment->datauser->company_name;
				

				$this->fpdf = new SPDF('KEMENTERIAN PERTANIAN','DIREKTORAT JENDERAL HORTIKULTURA','DIREKTORAT SAYURAN DAN TANAMAN OBAT');
				$this->fpdf->SetAutoPageBreak(false);
        		$this->fpdf->SetMargins(0,0,0,0);
        		$this->fpdf->SetLineWidth(0.1);

				$this->fpdf->AddPage('P','A4', 0);

				$this->fpdf->SetXY( 8, 41 ); $this->fpdf->SetFont('Arial','',8); $this->fpdf->Cell( 60, 8, "Nomor", 0, 0, 'L');
				$this->fpdf->SetXY( 24, 41 ); $this->fpdf->SetFont('Arial','',8); $this->fpdf->Cell( 60, 8, ": ".$skl->no_skl, 0, 0, 'L');
				$this->fpdf->SetXY( 160, 41 ); $this->fpdf->SetFont('Arial','',8); $this->fpdf->Cell( 40, 8, $today, 0, 0, 'R');
				
				$this->fpdf->SetXY( 8, 48 ); $this->fpdf->SetFont('Arial','',8); $this->fpdf->Cell( 60, 8, "Lampiran", 0, 0, 'L');
				$this->fpdf->SetXY( 24, 48 ); $this->fpdf->SetFont('Arial','',8); $this->fpdf->Cell( 60, 8, ": -", 0, 0, 'L');
				
				$this->fpdf->SetXY( 8, 55 ); $this->fpdf->SetFont('Arial','',8); $this->fpdf->Cell( 60, 8, "Hal", 0, 0, 'L');
				$this->fpdf->SetXY( 24, 55 ); $this->fpdf->SetFont('Arial','',8); $this->fpdf->Cell( 60, 8, ": Keterangan Telah Melaksanakan Wajib Tanam dan Wajib Produksi", 0, 0, 'L');
				
				$this->fpdf->SetXY( 8, 74 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Kepada Yth.", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 80 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Pimpinan", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 86 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, $companyName, 0, 0, 'L');
				$this->fpdf->SetXY( 8, 92 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "di", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 98 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Tempat", 0, 0, 'L');
				
				$this->fpdf->SetXY( 8, 110 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Berdasarkan hasil evaluasi dan validasi laporan realisasi tanam dan produksi, dengan ini kami menyatakan:", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 120 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Nama Perusahaan", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 127 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Nomor RIPH", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 134 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Wajib Tanam", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 155 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Wajib Produksi", 0, 0, 'L');
				
				$this->fpdf->SetXY( 48, 120 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".$companyName, 0, 0, 'L');
				$this->fpdf->SetXY( 48, 127 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".$commitment->no_ijin, 0, 0, 'L');
				$this->fpdf->SetXY( 48, 134 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, "Beban", 0, 0, 'L');
				$this->fpdf->SetXY( 48, 141 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, "Realisasi", 0, 0, 'L');
				$this->fpdf->SetXY( 48, 148 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, "Verifikasi", 0, 0, 'L');
				$this->fpdf->SetXY( 70, 134 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".number_format($commitment->volume_riph * 0.05 / 6, 2, '.', ',')." ha.", 0, 0, 'L');
				$this->fpdf->SetXY( 70, 141 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".number_format($total_luas, 2, '.', ',')." ha.", 0, 0, 'L');
				$this->fpdf->SetXY( 70, 148 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".number_format($pengajuan->luas_verif,2,'.',',')." ha.", 0, 0, 'L');
				

				$this->fpdf->SetXY( 48, 155 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, "Beban", 0, 0, 'L');
				$this->fpdf->SetXY( 48, 162 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, "Realisasi", 0, 0, 'L');
				$this->fpdf->SetXY( 48, 169 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, "Verifikasi", 0, 0, 'L');
				$this->fpdf->SetXY( 70, 155 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".number_format($commitment->volume_riph * 0.05, 2, '.', ',')." ton.", 0, 0, 'L');
				$this->fpdf->SetXY( 70, 162 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".number_format($total_volume, 2, '.', ',')." ton.", 0, 0, 'L');
				$this->fpdf->SetXY( 70, 169 ); $this->fpdf->SetFont('Arial','B',10); $this->fpdf->Cell( 60, 8, ": ".number_format($pengajuan->volume_verif,2,'.',',')." ton.", 0, 0, 'L');
				

				$this->fpdf->SetXY( 8, 180 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Telah melaksanakan kewajiban pengembangan bawang putih di dalam negeri sebagaimana ketentuan dalam Permentan", 0, 0, 'L');
				$this->fpdf->SetXY( 8, 187 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "39 tahun 2019 dan perubahannya.", 0, 0, 'L');
				
				$this->fpdf->SetXY( 8, 198 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Atas perhatian dan kerjasama Saudara disampaikan terima kasih.", 0, 0, 'L');
				
				// -- QrCode ---
        		// $this->Image($dtPath, 8, 210 ,17,17);
				
				$this->fpdf->SetXY( 105, 215); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "Direktur,", 0, 0, 'L');
				$this->fpdf->SetXY( 105, 235); $this->fpdf->SetFont('Arial','U',10); $this->fpdf->Cell( 60, 8, $pejabat->dataadmin->nama, 0, 0, 'L');
				$this->fpdf->SetXY( 105, 240); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "NIP.".$pejabat->dataadmin->nip, 0, 0, 'L');
				
				$this->fpdf->SetXY( 10, 250 ); $this->fpdf->SetFont('Arial','U',10); $this->fpdf->Cell( 60, 8, "Tembusan", 0, 0, 'L');
				$this->fpdf->SetXY( 10, 255 ); $this->fpdf->SetFont('Arial','',10); $this->fpdf->Cell( 60, 8, "- Direktur Jenderal Hortikultura", 0, 0, 'L');
				
				
				

				$pdfData = $this->fpdf->Output('I'); // ini dibuang / diremark untuk live

				//** 
				// PENTING !!!!
				//setelah selesai ini harus dibuka supaya statusnya berubah 7
				//** 

				
				// $pdfData = $this->fpdf->Output('S');
				// Storage::disk('public')->put($pdfUrl, $pdfData);

				// $skl->file_name = $pdfUrl;

				// $pdfpublic = Storage::disk('public')->url($pdfUrl);
				// $completed->url = $pdfpublic;

				
				// $skl->save();
				// $pengajuan->save();
				// $commitment->save();
				// $completed->save();
				

				/** end remark */

				// DB::commit();
			} catch (\Exception $e) {
				// Something went wrong, rollback the transaction
				// DB::rollback();
				return $e->getMessage();
				$this->msg = 'Error! SKL Gagal diterbitkan';
			}
		// });
		if ($this->msg === '') {
			return redirect()->route('verification.skl.published', ['id' => $this->sklid]);
		} else {
			return back()->with(['message' => $this->msg]);
		}
	}

	//fungsi untuk pejabat menyetujui skl diterbitkan.
	// public function storerecom($id)
	// {
	// 	if (Auth::user()->roles[0]->title !== 'Pejabat') {
	// 		abort(403, 'Unauthorized');
	// 	}

	// 	try {
	// 		return DB::transaction(function () use ($id) {
	// 			$skl = Skl::find($id);
	// 			$skl->approved_by = Auth::user()->id;
	// 			$skl->approved_at = Carbon::now();
	// 			$skl->published_date = Carbon::now();
	// 			$pengajuan = Pengajuan::find($skl->pengajuan_id);
	// 			$commitment = PullRiph::find($pengajuan->commitment_id);

	// 			$pengajuan->status = '7';
	// 			$commitment->status = '7';
	// 			$commitment->skl = $skl->no_skl;

	// 			// dd($pengajuan);

	// 			$skl->save();
	// 			$pengajuan->save();
	// 			$commitment->save();
	// 			return redirect()->route('verification.skl.recomendations');
	// 		});
	// 	} catch (\Exception $e) {
	// 		DB::rollback();
	// 		$this->msg = 'Error! SKL Gagal diterbitkan';
	// 		return back()->with(['error' => 'An error occurred while storing the recommendation.']);
	// 	}
	// }

	//fungsi untuk administrator mencetak dokumen skl yang diterbitkan
	public function printReadySkl($id)
	{
		if (Auth::user()->roles[0]->title !== 'Admin') {
			abort(403, 'Unauthorized');
		}
		$module_name = 'SKL';
		$page_title = 'Draft SKL';
		$page_heading = 'Preview Draft SKL';
		$heading_class = 'fa fa-file-signature';

		$skl = Skl::findOrFail($id);
		if (empty($skl->approved_by)) {
			abort(403, 'Tidak dapat dicetak. Pejabat terkait belum menyetujui penerbitan SKL.');
		}
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = $skl->approved_by;
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.published', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		return view('admin.verifikasi.skl.printReadySKL', compact('page_title', 'skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	}

	//sub fungsi sklupload
	private function uploadFile($file, $filenpwp, $thn, $filename)
	{
		$path = $file->storeAs('uploads/' . $filenpwp . '/' . $thn, $filename, 'public');
		return asset('storage/' . $path);
	}

	//fungsi unggah skl oleh admin jika sudah di setujui terbit oleh pejabat.
	public function sklUpload(Request $request, $id)
	{
		if (Auth::user()->roles[0]->title !== 'Admin') {
			abort(403, 'Unauthorized');
		}
		$this->sklid = $id;
		$skl = Skl::find($this->sklid);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::find($pengajuan->commitment_id);
		$completed = new Completed();

		$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
		$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
		$noIjin = str_replace(['.', '/', '-'], '', $skl->no_ijin);
		$thn = $commitment->periodetahun;

		if ($request->hasFile('skl_upload')) {
			$file = $request->file('skl_upload');
			$filename = 'skl_' . $noIjin . '.' . $file->getClientOriginalExtension();
			$filePath = $this->uploadFile($file, $filenpwp, $thn, $filename);
			$skl->skl_upload = $filename;
			$completed->skl_upload = $filePath;
		}

		$completed->no_skl = $skl->no_skl;
		$completed->npwp = $skl->npwp;
		$completed->no_ijin = $skl->no_ijin;
		$completed->periodetahun = $commitment->periodetahun;
		$completed->published_date = Carbon::now();
		$completed->luas_tanam = $pengajuan->luas_verif;
		$completed->volume = $pengajuan->volume_verif;
		$completed->status = 'Lunas';

		$skl->save();
		$pengajuan->save();
		$commitment->save();
		$completed->save();
		// DB::transaction(function () use ($request) { // Add 'use ($request)' to access $request within the closure
		// 	try {

		// 	} catch (\Exception $e) {
		// 		// Something went wrong, rollback the transaction
		// 		DB::rollback();
		// 		$this->msg = 'Error! SKL Gagal diterbitkan';
		// 	}

		// 	if ($this->msg === '') {
		// 		return redirect()->route('verification.skl');
		// 	} else {
		// 		return redirect()->route('verification.skl')->with(['message' => $this->msg]);
		// 	}
		// });
		return redirect()->route('verification.skl')
			->with('success', 'Data Pemeriksaan berhasil disimpan');
	}

	//summary single skl
	public function show($id)
	{
		$module_name = 'SKL';
		$page_title = 'Data SKL';
		$page_heading = 'Data SKL Terbit';
		$heading_class = 'fal fa-file-certificate';

		$skl = Skl::find($id);

		return view('admin.verifikasi.skl.show', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'skl'));
	}

	//fungsi untuk melihat daftar skl yang telah terbit. sesuaikan dengan user role.
	public function completedindex()
	{
		$module_name = 'SKL';
		$page_title = 'Surat Keterangan Lunas';
		$page_heading = 'SKL Diterbitkan';
		$heading_class = 'fa fa-award';

		$roleaccess = Auth::user()->roleaccess;
		if ($roleaccess == 1) {
			$completeds = Completed::all();
		}

		if ($roleaccess == 2) {
			$user = Auth::user();
			$npwp = $user->data_user->npwp_company;
			$completeds = Completed::where('npwp', $npwp)->get();
		}

		// dd($completeds);

		return view('admin.verifikasi.skl.completed', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'completeds'));
	}

	//daftar ini digunakan oleh semua user role. mungkin di hapus
	public function publishes()
	{
		if (Auth::user()->roles[0]->title !== 'Pejabat') {
			abort(403, 'Unauthorized');
		}

		$module_name = 'SKL';
		$page_title = 'SKL Terbit';
		$page_heading = 'Daftar SKL Terbit';
		$heading_class = 'fa fa-file-certificate';

		$recomends = Pengajuan::where('status', '7')
			->get();

		return view('admin.verifikasi.skl.publishes', compact('module_name', 'page_title', 'page_heading', 'heading_class', 'recomends'));
	}
	/**
	 * Menampilkan halaman data SKL
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */

	public function published($id)
	{
		$skl = Skl::findOrfail($id);
		if (Storage::disk('public')->exists($skl->file_name)) {

			return Storage::disk('public')->response($skl->file_name);
		}
	}
	/**
	 * Menampilkan halaman SKL (print)
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function published_old($id)
	{
		$module_name = 'SKL';
		$page_title = 'Surat Keterangan Lunas';
		$page_heading = 'SKL Diterbitkan';
		$heading_class = 'fa fa-award';

		$skl = Skl::findOrfail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');
		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.show', $skl->id),
		];

		// $QrCode = QrCode::size(70)->generate(json_encode($data));
		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		// dd($commitment);
		// dompdf disini
		$filenpwp = str_replace(['.', '-'], '', $skl->npwp);
		$no_skl = str_replace(['.', '/', '-'], '', $skl->no_skl);
		$thn = substr($skl->no_ijin, -4);


		$view = view('admin.verifikasi.skl.domskl', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
		$html = mb_convert_encoding($view, 'HTML-ENTITIES', 'UTF-8');


		// dd($html);
		$pdf = app('dompdf.wrapper');
		$pdf->setPaper('A4', 'portrait');
		$pdf->loadHtml($html);
		return $pdf->stream();

		// $pdfUrl = Storage::disk('public')->put('uploads/' . $filenpwp . '/' . $thn . '/' . $no_skl . '.pdf', $pdf->output());

		// $skl->file_name = $pdfUrl;
		// $skl->save();


	}
	public function arsipskl($id)
	{
		$skl = Skl::findOrFail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.published', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		return view('admin.verifikasi.skl.sklPdf', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
	}
	public function arsipskl_baru($id)
	{
		$skl = Skl::findOrFail($id);
		$pengajuan = Pengajuan::find($skl->pengajuan_id);
		$commitment = PullRiph::where('no_ijin', $skl->no_ijin)->first();
		$pejabat = User::find($skl->approved_by);
		$wajib_tanam = $commitment->volume_riph * 0.05 / 6;
		$luas_verif = $pengajuan->luas_verif;
		$wajib_produksi = $commitment->volume_riph * 0.05;
		$volume_verif = $pengajuan->volume_verif;
		$total_luas = $commitment->lokasi->sum('luas_tanam');
		$total_volume = $commitment->lokasi->sum('volume');

		$data = [
			'Perusahaan' => $commitment->datauser->company_name,
			'No. RIPH' => $commitment->no_ijin,
			'Status' => 'LUNAS',
			'Tautan' => route('verification.skl.published', $skl->id)
		];

		$QrCode = QrCode::size(70)->generate($data['Perusahaan'] . ', ' . $data['No. RIPH'] . ', ' . $data['Status'] . ', ' . $data['Tautan']);

		$view = view('admin.verifikasi.skl.sklPdf', compact('skl', 'pengajuan', 'commitment', 'pejabat', 'QrCode', 'wajib_tanam', 'wajib_produksi', 'luas_verif', 'volume_verif', 'total_luas', 'total_volume'));
		$html = mb_convert_encoding($view, 'HTML-ENTITIES', 'UTF-8');

		$pdf = app('dompdf.wrapper');
		// $options = $pdf->getOptions();
		//  $options->set('orientation', 'landscape');
		$pdf->setPaper('A4', 'landscape');
		$pdf->loadHTML($html);

		return $pdf->download('sklpdf');
	}
	public function create()
	{
		//
	}
	public function store(Request $request)
	{
		//
	}
	public function edit($id)
	{
		//
	}
	public function update(Request $request, $id)
	{
		//
	}
	public function destroy($id)
	{
		//
	}
}
