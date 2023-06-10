@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
{{-- @include('partials.subheader') --}}
@can('dashboard_access')
<!-- Page Content -->
<div class="subheader">
	<h1 class="subheader-title">
		<i class="subheader-icon {{ ($heading_class ?? '') }}"></i><span class="fw-700 mr-2 ml-2">{{  ($page_heading ?? '') }}</span><span class="fw-300">Realisasi & Verifikasi</span>
	</h1>
	<div class="subheader-block d-lg-flex align-items-center  d-print-none">
		<div class="d-inline-flex flex-column justify-content-center ">
			<select type="text" id="periodetahun" class="form-control form-control-sm custom-select" data-toggle="tooltip" title data-original-title="pilih tahun laporan">
				<option hidden>- pilih tahun laporan</option>
				<option value="" hidden>--pilih tahun</option>
				@foreach($periodeTahuns as $periodetahun => $records)
					<option value="{{ $periodetahun }}">Tahun {{ $periodetahun }}</option>
				@endforeach
			</select>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<div class="panel rounded overflow-hidden position-relative text-white mb-g">
			<div class="card-body bg-danger-300">
				<div class="">
					<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white" data-toggle="tooltip" title data-original-title="Jumlah Perusahaan Pemegang RIPH">
						<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
						<span id="jumlah_importir">{{ number_format($riph_admin[0]->jumlah_importir, 0, ',', '.') }}</span>
						<small class="m-0 l-h-n">Perusahaan / <span id="company" class="mr-1"></span>Terdaftar</small> 
					</h3>
				</div>
			</div>
			<i class="fal fa-landmark position-absolute pos-right pos-bottom opacity-25 mb-n1 mr-n1" style="font-size:4rem"></i>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel rounded overflow-hidden position-relative text-white mb-g">
			<div class="card-body bg-warning-400">
				<div class="">
					<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white" data-toggle="tooltip" title data-original-title="Jumlah volume import pada periode ini.">
						<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
						<span id="v_pengajuan_import">{{ number_format($riph_admin[0]->v_pengajuan_import, 0, ',', '.') }}</span>
						<small class="m-0 l-h-n">Volume Import (ton)</small>
					</h3>
				</div>
			</div>
			<i class="fal fa-balance-scale position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel rounded overflow-hidden position-relative text-white mb-g">
			<div class="card-body bg-info-300">
				<div class="">
					<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white" data-toggle="tooltip" title data-original-title="Jumlah wajib tanam pada periode ini.">
						<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
						<span id="v_beban_tanam">{{ number_format($riph_admin[0]->v_beban_tanam, 0, ',', '.') }}</span>
						<small class="m-0 l-h-n">Kewajiban Tanam (ha)</small>
					</h3>
				</div>
			</div>
			<i class="fal fa-seedling position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel rounded overflow-hidden position-relative text-white mb-g">
			<div class="card-body bg-success-500">
				<div class="">
					<h3 class="display-5 d-block l-h-n m-0 fw-500 text-white" data-toggle="tooltip" title data-original-title="Jumlah wajib tanam pada periode ini.">
						<!-- nilai ini diperoleh dari jumlah seluruh pengajuan yang belum diverifikasi. where status = 1 (user) -->
						<span id="v_beban_produksi">{{ number_format($riph_admin[0]->v_beban_produksi, 0, ',', '.') }}</span>
						<small class="m-0 l-h-n">Kewajiban Produksi (ton)</small>
					</h3>
				</div>
			</div>
			<i class="fal fa-dolly position-absolute pos-right pos-bottom opacity-40 mb-n1 mr-n1" style="font-size:4rem"></i>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="panel" id="panel-2">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-seedling mr-1"></i>Wajib Tanam
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<!-- Row -->
					<div class="row mb-3 align-items-center">
						<div class="col-lg-5 col-sm-6 align-self-center text-center">
							<div class="tab-content" id="v-pills-tabContent">
								<div class="tab-pane fade show active" id="real_Tanam" role="tabpanel" aria-labelledby="real_Tanam">
									<div class="c-chart-wrapper">
										<div 
											id = "naschartTanam"
											class="js-easy-pie-chart color-success-300 position-relative d-inline-flex align-items-center justify-content-center "
											data-percent="50.00"
											data-piesize="145"
											data-linewidth="10"
											data-linecap="butt"
											data-scalelength="7"
											data-toggle="tooltip"
											title data-original-title="% dari Kewajiban"
											data-placement="bottom">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-xl">
												<span class="fs-xxl fw-500 text-dark"><span id="ltTowt">0</span><sup>%</sup></span>
												<!--<span class="display-5 fw-500 js-percent d-block text-dark">97.68</span>-->
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="verif_Tanam" role="tabpanel" aria-labelledby="verif_Tanam">
									<div class="c-chart-wrapper">
										<div
											id="naschartTanamVerif"
											class="js-easy-pie-chart color-primary-500 position-relative d-inline-flex align-items-center justify-content-center"
											data-percent=""
											data-piesize="145"
											data-linewidth="10"
											data-linecap="butt"
											data-scalelength="7"
											data-toggle="tooltip"
											title data-original-title="% dari Kewajiban"
											data-placement="bottom">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-xl">
												<span class="fs-xxl fw-500 text-dark" id="lvTowt">0<sup>%</sup></span>
												<!--<span class="display-3 fw-500 js-percent d-block text-dark">97.68</span>-->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-7 col-sm-6">
							<nav class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
								<a class="nav-link shadow-1 p-1 btn-block btn-success bg-success-500 rounded overflow-hidden position-relative text-white mb-2 waves-effect waves-themed" id="v-pills-home-tab" data-toggle="pill" href="#real_Tanam" role="tab" aria-controls="v-pills-home" aria-selected="true">
									<div class="">
										<span class="small">Realisasi</span>
										<div class="d-flex">
											<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="total_luastanam">...</h5>
											<span>ha</span>
										</div>
									</div>
									<i class="fal fa-hand-holding-seedling position-absolute pos-right pos-bottom opacity-25 mb-n1 mr-n1" style="font-size:3rem"></i>
								</a>
								<a class="nav-link  shadow-1 p-1 btn-block btn-primary bg-primary-300 rounded overflow-hidden position-relative text-white mb-2 waves-effect waves-themed" id="v-pills-profile-tab" data-toggle="pill" href="#verif_Tanam" role="tab" aria-controls="v-pills-profile" aria-selected="false">
									<div class="">
										<span class="small">Verifikasi</span>
										<div class="d-flex">
											<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="luas_verif">...</h5>
											<span>ha</span>
										</div>
									</div>
									<i class="fal fa-hand-holding-seedling position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:3rem"></i>
								</a>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel" id="panel-2">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-dolly mr-1"></i>Wajib Produksi
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<!-- Row -->
					<div class="row mb-3 align-items-center">
						<div class="col-lg-5 col-sm-6 align-self-center text-center">
							<div class="tab-content" id="v-pills-tabContent">
								<div class="tab-pane fade show active" id="real_Produksi" role="tabpanel" aria-labelledby="real_Produksi">
									<div class="c-chart-wrapper">
										<div
											id = "naschartProduksi"
											class="js-easy-pie-chart color-warning-500 position-relative d-inline-flex align-items-center justify-content-center"
											data-percent="50.00"
											data-piesize="145"
											data-linewidth="10"
											data-linecap="butt"
											data-scalelength="7"
											data-toggle="tooltip"
											title data-original-title=""
											data-placement="bottom">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-xl">
												<span class="fs-xxl fw-500 text-dark"><span id="vpTowp">0</span><sup>%</sup></span>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="verif_Produksi" role="tabpanel" aria-labelledby="v-pills-profile-tab">
									<div class="c-chart-wrapper">
										<div 
											id = "naschartProduksiVerif"
											class="js-easy-pie-chart color-warning-500 position-relative d-inline-flex align-items-center justify-content-center"
											data-percent="50.00"
											data-piesize="145"
											data-linewidth="10"
											data-linecap="butt"
											data-scalelength="7"
											data-toggle="tooltip"
											title data-original-title=""
											data-placement="bottom">
											<div class="d-flex flex-column align-items-center justify-content-center position-absolute pos-left pos-right pos-top pos-bottom fw-300 fs-xl">
												<span class="fs-xxl fw-500 text-dark"><span id="vvTowp">0</span><sup>%</sup></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-7 col-sm-6">
							<nav class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
								<a class="nav-link shadow-1 p-1 btn-block btn-warning bg-warning-300 rounded overflow-hidden position-relative text-white mb-2 waves-effect waves-themed" id="v-pills-home-tab" data-toggle="pill" href="#real_Produksi" role="tab" aria-controls="realisasiProduksi" aria-selected="true">
									<div class="">
										<span class="small">Realisasi</span>
										<div class="d-flex">
											<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="total_volume">...</h5>
											<span>ton</span>
										</div>
									</div>
									<i class="fal fa-hand-holding-seedling position-absolute pos-right pos-bottom opacity-35 mb-n1 mr-n1" style="font-size:3rem"></i>
								</a>
								<a class="nav-link  shadow-1 p-1 btn-block btn-primary bg-primary-300 rounded overflow-hidden position-relative text-white mb-2 waves-effect waves-themed" id="v-pills-profile-tab" data-toggle="pill" href="#verif_Produksi" role="tab" aria-controls="v-pills-profile" aria-selected="false">
									<div class="">
										<span class="small">Verifikasi</span>
										<div class="d-flex">
											<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="volume_verif">...</h5>
											<span>ton</span>
										</div>
									</div>
									<i class="fal fa-hand-holding-seedling position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:3rem"></i>
								</a>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
					
<div class="row">
	<!-- Tabel Verifikasi
		Nilai Tabel chart ini diperoleh dari kueri data verifikasi dengan status mulai dari 0 s. d 5. Temporary tabel sesuai dengan tampilan pada layar html.
		Setiap status merupakan pintasan cepat ke halaman terkait.
	-->
	<div class="col-md-12">
		<div class="panel" id="panel-2">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-ballot-check mr-1"></i>Daftar <span class="fw-300"><i>Verifikasi</i></span>
				</h2>
				<div class="panel-toolbar">
					@include('layouts.globaltoolbar')
				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<div class="row d-flex">
						<div class="col-md-3">
							<div class="shadow-1 p-2 bg-primary-100 rounded overflow-hidden position-relative text-white mb-2">
								{{-- where status != empty, seluruh data pengajuan yang memiliki status (1 s.d 7) --}}
								<div data-toggle="tooltip" title data-original-title="Jumlah Pengajuan Verifikasi Wajib Tanam-Produksi">
									<div class="d-flex">
										<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="ajucount">0</h5>
										<span>RIPH</span>
									</div>
									<h5 class="d-block l-h-n m-0 fw-500">
										{{-- sum wajib tanam --}}
										0.00 ha
									</h5>
									<span class="small">Pengajuan</span>
								</div>
								<i class="fal fa-download position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
							</div>
						</div>
						<div class="col-md-3">
							{{-- where onlinestatus = 1 and onfarmstatus = empty, seluruh pengajuan yang memiliki onlinestatus dengan nilai 1 (sesuai) namun tidak memiliki tidak memiliki onfarmstatus (belum diperiksa) --}}
							<div class="shadow-1 p-2 bg-primary-200 rounded overflow-hidden position-relative text-white mb-2">
								<div data-toggle="tooltip" title data-original-title="Jumlah RIPH yang sedang diverifikasi">
									<div class="d-flex">
										<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="proccesscount">0</h5>
										<span>RIPH</span>
									</div>
									<h5 class="d-block l-h-n m-0 fw-500">
										{{-- sum wajib tanam --}}
										0.00 ha
									</h5>
									<span class="small">Dalam Proses</span>
								</div>
								<i class="fal fa-hourglass position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
							</div>
						</div>
						<div class="col-md-3">
							{{-- where onfarmstatus != empty, ini berarti data telah diperiksa dan survey lokasi telah dilakukan. di tabel ini, status sesuai atau tidak diabaikan. --}}
							<div class="shadow-1 p-2 bg-primary-300 rounded overflow-hidden position-relative text-white mb-2">
								<div data-toggle="tooltip" title data-original-title="Jumlah RIPH yang telah diverifikasi">
									<div class="d-flex">
										<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="verifiedcount">0</h5>
										<span>RIPH</span>
									</div>
									<h5 class="d-block l-h-n m-0 fw-500">
										{{-- sum wajib tanam --}}
										0.00 ha
									</h5>
									<span class="small">Terverifikasi</span>
								</div>
								<i class="fal fa-check-circle position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
							</div>
						</div>
						<div class="col-md-3">
							<div class="shadow-1 p-2 bg-primary-500 rounded overflow-hidden position-relative text-white mb-2">
								{{-- where status = '7' --}}
								<div data-toggle="tooltip" title data-original-title="Jumlah RIPH Lunas Wajib Tanam-Produksi">
									<div class="d-flex">
										<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="lunascount">0</h5>
										<span>RIPH</span>
									</div>
									<div class="d-flex">
										<div class="d-flex">
											<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="sumLunasTanam">0</h5>
											<span>ha  |  </span>
										</div>
										<div class="d-flex ml-1">
											<h5 class="d-block l-h-n m-0 fw-500 mr-1" id="sumLunasProduksi">0</h5>
											<span>ton</span>
										</div>
									</div>
									<span class="small">LUNAS</span>
								</div>
								<i class="fal fa-award position-absolute pos-right pos-bottom opacity-30 mb-n1 mr-n1" style="font-size:3rem"></i>
							</div>
						</div>
					</div><hr>
					<table class="table table-bordered table-hover table-sm w-100" id="verifprogress">
						<thead>
							<th>Nomor Pengajuan</th>
							<th>Nama Perusahaan</th>
							<th>Nomor RIPH</th>
							<th>Data</th>
							<th>Lapangan</th>
							<th>Lunas</th>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
	<!-- End Page Content -->

@endcan
@endsection
@section('scripts')
@parent
	<script>
		$(document).ready(function() {
			//initialize datatable verifprogress
			$('#verifprogress').dataTable({
				responsive: true,
				lengthChange: false,
				dom:
				"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'<'select'>>>" +
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				buttons: [
					{
						extend: 'pdfHtml5',
						text: '<i class="fa fa-file-pdf"></i>',
						titleAttr: 'Generate PDF',
						className: 'btn-outline-danger btn-sm btn-icon mr-1'
					},
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-sm btn-icon mr-1'
					},
					{
						extend: 'csvHtml5',
						text: '<i class="fal fa-file-csv"></i>',
						titleAttr: 'Generate CSV',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					},
					{
						extend: 'copyHtml5',
						text: '<i class="fa fa-copy"></i>',
						titleAttr: 'Copy to clipboard',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-sm btn-icon mr-1'
					}
				]
			});

			// Create the "Status" select element and add the options
			var selectStatus = $('<select>')
				.attr('id', 'selectverifprogressStatus')
				.addClass('custom-select custom-select-sm col-3 mr-2')
				.on('change', function() {
				var status = $(this).val();
				table.column(6).search(status).draw();
				});

			$('<option>').val('').text('Semua Status').appendTo(selectStatus);
			$('<option>').val('1').text('Sudah Terbit').appendTo(selectStatus);
			$('<option>').val('2').text('Belum Terbit').appendTo(selectStatus);

			// Add the select elements before the first datatable button in the second table
			$('#verifprogress_wrapper .dt-buttons').before(selectStatus);
		});
	</script>
	<script>
		$(document).ready(function() {
			$('#periodetahun').on('change', function() {
				var periodetahun = $(this).val();
				var url = '{{ route("admin.monitoringDataByYear", ":periodetahun") }}';
				url = url.replace(':periodetahun', periodetahun);

				$.get(url, function (data) {
					$('#jumlah_importir').text(data.jumlah_importir);
					$('#v_pengajuan_import').text(formatNumber(data.v_pengajuan_import));
					$('#v_beban_tanam').text(formatNumber(data.v_beban_tanam));
					$('#v_beban_produksi').text(formatNumber(data.v_beban_produksi));
					$('#company').text(data.company);
					$('#volume_import').text(formatdecimals(data.volume_import));
					$('#total_luastanam').text(formatdecimals(data.total_luastanam));
					$('#total_volume').text(formatdecimals(data.total_volume));
					$('#luas_verif').text(formatdecimals(data.luas_verif));
					$('#volume_verif').text(formatdecimals(data.volume_verif));
					$('#ltTowt').text(formatdecimals(data.ltTowt));
					$('#vpTowp').text(formatdecimals(data.vpTowp));
					$('#lvTowt').text(formatdecimals(data.lvTowt));
					$('#vvTowp').text(formatdecimals(data.vvTowp));
					$('#ajucount').text(data.ajucount);
					$('#proccesscount').text(data.proccesscount);
					$('#verifiedcount').text(data.verifiedcount);
					$('#lunascount').text(data.lunascount);
					$('#sumLunasTanam').text(formatdecimals(data.sumLunasTanam));
					$('#sumLunasProduksi').text(formatdecimals(data.sumLunasProduksi));
					
					var ltToWt = (data.ltTowt);
					$('#naschartTanam').attr('data-percent', ltToWt);
					$('#naschartTanam').attr('data-original-title', ltToWt  + '% dari kewajiban');
					var $chartTanam = $('#naschartTanam');
					$chartTanam.data('easyPieChart').update(ltToWt);

					var vpToWp = (data.vpTowp);
					$('#naschartProduksi').attr('data-percent', vpToWp);
					$('#naschartProduksi').attr('data-original-title', vpToWp  + '% dari kewajiban');
					var $chartProduksi = $('#naschartProduksi');
					$chartProduksi.data('easyPieChart').update(vpToWp);

					var lvToWt = (data.lvTowt);
					$('#naschartTanamVerif').attr('data-percent', lvToWt);
					$('#naschartTanamVerif').attr('data-original-title', lvToWt  + '% dari kewajiban');
					var $chartTanam = $('#naschartTanamVerif');
					$chartTanam.data('easyPieChart').update(lvToWt);

					var vvToWp = (data.vvTowp);
					$('#naschartProduksiVerif').attr('data-percent', vvToWp);
					$('#naschartProduksiVerif').attr('data-original-title', vvToWp  + '% dari kewajiban');
					var $chartTanam = $('#naschartProduksiVerif');
					$chartTanam.data('easyPieChart').update(vvToWp);

					// // Build table for pengajuanv2s
					var tableBody = $("#verifprogress tbody");
					tableBody.empty(); // Clear previous table data

					$.each(data.verifikasis, function (index, verifikasi) {
						console.log('Verifikasi:', verifikasi);
						var row = $("<tr></tr>");
						var nomorPengajuan = $("<td></td>").text(verifikasi.no_pengajuan);
						var namaPerusahaan = $("<td></td>").text(verifikasi.commitment.datauser.company_name);
						var nomorRIPH = $("<td></td>").text(verifikasi.no_ijin);
						
						var dataCell = $('<td class="text-center"></td>').html(function() {
							if (!verifikasi.status) {
								return '<span class="badge badge-xs badge-warning"><i class="fal fa-exclamation-circle mr-1"></i>Belum diajukan</span>';
							} else if (verifikasi.status === '1' && !verifikasi.onlinestatus) {
								return '<span class="badge badge-xs badge-primary"><i class="fal fa-check-upload mr-1"></i>Diajukan</span>';
							} else if (verifikasi.status === '2') {
								return '<span class="badge badge-xs badge-success"><i class="fal fa-check-circle mr-1"></i>Selesai</span>';
							} else if (verifikasi.status === '3') {
								return '<span class="badge badge-xs badge-danger"><i class="fal fa-ban mr-1"></i>Tidak Sesuai</span>';
							}
						});

						var lapanganCell = $('<td class="text-center"></td>').html(function() {
							if (verifikasi.status === '2' && !verifikasi.onfarmstatus) {
								return '<span class="badge badge-xs badge-warning"><i class="fal fa-exclamation-circle mr-1"></i>Belum diperiksa</span>';
							} else if (verifikasi.status === '4') {
								return '<span class="badge badge-xs badge-success"><i class="fal fa-check-circle mr-1"></i>Selesai</span>';
							} else if (verifikasi.status === '5') {
								return '<span class="badge badge-xs badge-danger"><i class="fal fa-ban mr-1"></i>Tidak Sesuai</span>';
							}
						});

						var lunasCell = $('<td></td>').html(function() {
							if (verifikasi.status === '6') {
								return '<span class="badge badge-xs badge-primary"><i class="fal fa-file-signature mr-1"></i>Rekomendasi</span>';
							} else if (verifikasi.status === '7') {
								return '<span class="badge badge-xs badge-success"><i class="fal fa-award mr-1"></i>Lunas</span>';
							}
						});

						row.append(nomorPengajuan, namaPerusahaan, nomorRIPH, dataCell, lapanganCell, lunasCell);
						tableBody.append(row);
					});
					
				});

				
				
				function formatNumber(number) {
					return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
				}

				function formatdecimals(number) {
					var parts = number.toFixed(2).toString().split(".");
					var formattedNumber = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
					if (parts.length > 1) {
						formattedNumber += "," + parts[1];
					} else {
						formattedNumber += ",00"; // Add two decimal places if there are none
					}
					return formattedNumber;
				}
			});
		});
	</script>
@endsection