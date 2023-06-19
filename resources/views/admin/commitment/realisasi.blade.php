@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@can('commitment_show')
@include('partials.sysalert')
    {{-- {{ dd($data_poktan) }} --}}
	<div class="row">
		<div class="col-md-3">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>Status Data</h2>
				</div>
				<div class="panel-container">
					<div class="panel-content">
						@switch($commitment->status)
							@case(1)
								<div class="row">
									<div class="col-12 text-center mb-1">
										<span class="icon-stack icon-stack-sm">
											<i class="base-7 icon-stack-3x color-success-50"></i>
											<i class="fal fa-hourglass icon-stack-1x text-white"></i>
										</span>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700">Sudah Diajukan.</span>
									</div>
								</div><hr>
								<p class="small help-block">Pengajuan Verifikasi Anda telah diterima dan segera diproses.<br> Selama Masa Tunggu, Anda tidak dapat: menambah, mengubah, dan atau menghapus data terkait.
								</p>
							@break
							@case(2)
								<div class="row">
									<div class="col-12 text-center mb-1">
										<span class="icon-stack icon-stack-sm">
											<i class="base-7 icon-stack-3x color-info-700"></i>
											<i class="fal fa-file-check icon-stack-1x text-white"></i>
										</span>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700">Verifikasi Data</span>
									</div>
								</div><hr>
								<p class="small help-block">Verifikasi Data SELESAI diproses.<br> Selama Masa Tunggu, Anda tidak dapat: menambah, mengubah, dan atau menghapus data terkait.
								</p>
								{{-- <span class="btn btn-xs btn-warning d-block" data-toggle="tooltip"
									data-original-title="Dalam Proses Verifikasi">
									Ajukan SKL
								</span> --}}
							@break
							@case(3)
								<div class="row">
									<div class="col-12 text-center mb-1">
										<i class="fas fa-exclamation-circle fa-2x text-warning"></i>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700">Perbaikan Data</span>
									</div>
								</div><hr>
								<p class="small help-block">Verifikasi Data SELESAI diproses. System menyatakan Anda HARUS MEMPERBAIKI DATA terlebih dahulu, lalu Ajukan Pemeriksaan Ulang
								</p><hr>
								<a href="{{ route('admin.task.submission.create', $commitment->id) }}"
									class="btn btn-xs btn-danger d-block">
									Ajukan Review dan Verifikasi Ulang
								</a>
								{{-- <a href="#" class="btn btn-xs btn-danger d-block"
									data-toggle="modal" data-target="#verifikasiModal">
									Ajukan Pemeriksaan Ulang
								</a> --}}
							@break
							@case(4)
								<div class="row">
									<div class="col-12 text-center mb-1">
										<span class="icon-stack icon-stack-sm">
											<i class="base-7 icon-stack-3x color-success-700"></i>
											<i class="fal fa-map-marker-check icon-stack-1x text-white"></i>
										</span>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700">Verifikasi Lapangan</span>
									</div>
								</div><hr>
								<p class="small help-block">Verifikasi Lapangan SELESAI diproses.<br> Surat Keterangan Lunas segera diterbitkan.
								</p>
							@break
							@case(5)
								<div class="row">
									<div class="col-12 text-center mb-1">
										<i class="fas fa-exclamation-circle fa-2x text-warning"></i>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700">Perbaikan Data</span>
									</div>
								</div><hr>
								<p class="small help-block">Pemeriksaan data Anda telah selesai dengan hasil PERBAIKAN.<br>Silahkan perbaiki data-data yang diperlukan, lalu ajukan pemeriksaan ulang.
								</p><hr>
								<a href="{{ route('admin.task.submission.create', $commitment->id) }}"
									class="btn btn-xs btn-danger d-block">
									Ajukan Review dan Verifikasi Ulang
								</a>
							@break
							@case(6)
								<div class="row">
									<div class="col-12 text-center mb-1">
										<span class="icon-stack icon-stack-sm">
											<i class="base-7 icon-stack-3x color-success-700"></i>
											<i class="fal fa-hourglass icon-stack-1x text-white"></i>
										</span>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700">Rekomendasi SKL</span>
									</div>
								</div><hr>
								<p class="small help-block">Horray! Sebentar lagi SKL diterbitkan.
								</p>
							@break
							@case(7)
								<div class="row">
									<div class="col-12 text-center mb-1">
										<span class="icon-stack icon-stack-sm">
											<i class="base-7 icon-stack-3x color-success-700"></i>
											<i class="fal fa-award icon-stack-1x text-white"></i>
										</span>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700">SKL Terbit</span>
									</div>
								</div><hr>
								<p class="small help-block">Horray! Sebentar lagi SKL diterbitkan.
								</p>
							@break
							@default
								<div class="row">
									<div class="col-12 text-center">
										<span class="icon-stack icon-stack-sm">
											<i class="base-7 icon-stack-2x color-danger-600"></i>
											<i class="fal fa-times icon-stack-1x text-white"></i>
										</span>
									</div>
									<div class="col-12 text-center">
										<span class="fw-700 small">Anda belum mengajukan verifikasi</span>
									</div>
								</div><hr>
								<a href="{{ route('admin.task.commitment.submit', $commitment->id) }}"
									class="btn btn-xs btn-danger d-block">
									Ajukan Review dan Verifikasi
								</a>
						@endswitch
					</div>
				</div>
			</div>
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>
						Data <span class="fw-300"><i>Basic</i></span>
					</h2>
					<div class="panel-toolbar">
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<ul class="list-group mb-3" style="word-break:break-word;">
							<li class="list-group-item list-group-item-action d-flex justify-content-between">
								<div>
									<span class="text-muted">Perusahaan/Lembaga</span>
									<h6 class="fw-500 my-0">{{ $commitment->user->data_user->company_name }}</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Nomor RIPH</span>
									<h6 class="fw-500 my-0">{{ $commitment->no_ijin }}</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Tanggal Terbit</span>
									<h6 class="fw-500 my-0">{{ date('d/m/Y', strtotime($commitment->tgl_ijin)) }}</h6>
								</div>
								<div>
									<span class="text-muted">Tanggal Terbit</span>
									<h6 class="fw-500 my-0">{{ date('d/m/Y', strtotime($commitment->tgl_akhir)) }}</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Volume RIPH</span>
									<h6 class="fw-500 my-0">{{ number_format($commitment->volume_riph,2,',', '.') }} ton </h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Wajib Tanam</span>
									<h6 class="fw-500 my-0">{{ number_format($commitment->volume_riph*0.05/6,2,',', '.') }} ha</h6>
								</div>
								<div>
									<span class="text-muted">Wajib Produksi</span>
									<h6 class="fw-500 my-0">{{ number_format( $commitment->volume_riph*0.05,2,',', '.') }} ton</h6>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>
						DATA <span class="fw-300"><i>Perbenihan</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<ul class="list-group mb-3">
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Kebutuhan Benih</span>
									<h6 class="fw-500 my-0"> {{ number_format($commitment->volume_riph*0.05/6*0.8,2,',', '.') }} ton</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Stok Mandiri</span>
									<h6 class="fw-500 my-0"> {{ number_format($commitment->stok_mandiri,2,',', '.') }} ton</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Beli dari Penangkar</span>
									<h6 class="fw-500 my-0"> {{ number_format($commitment->volume_riph*0.05/6*0.8-$commitment->stok_mandiri ,2,',', '.') }} ton</h6>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="panel" id="panel-3">
				<div class="panel-hdr">
					<h2>
						DATA <span class="fw-300"><i>Pengendalian</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<ul class="list-group mb-3">
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Pupuk Organik</span>
									<h6 class="fw-500 my-0"> {{ number_format($commitment->pupuk_organik,2,',', '.') }} kg</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Nitrogen Phosfor Kalium (NPK)</span>
									<h6 class="fw-500 my-0"> {{ number_format($commitment->npk,2,',', '.') }} kg</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Dolomit</span>
									<h6 class="fw-500 my-0">{{ number_format($commitment->dolomit,2,',', '.') }} kg</h6>
								</div>
							</li>
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Zwavelzure Amonium (ZA)</span>
									<h6 class="fw-500 my-0">{{ number_format($commitment->za,2,',', '.') }} kg</h6>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="panel" id="panel-4">
				<div class="panel-hdr">
					<h2>
						DATA <span class="fw-300"><i>Lainnya</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<ul class="list-group mb-3">
							<li class="list-group-item d-flex justify-content-between">
								<div>
									<span class="text-muted">Saprodi Lainnya</span>
									<div class="d-flex justify-content-between">
										{{-- @isset($pullData['riph']['wajib_tanam']['mulsa']) --}}
											<h6 class="fw-500 my-0">Mulsa:&nbsp;</h6>
											<h6 class="fw-500 my-0">{{ number_format($commitment->mulsa,2,',', '.') }} kg</h6>
										{{-- @endisset --}}
										
										
									</div>
								</div>
							</li>
							{{-- @isset($pullData['riph']['wajib_tanam']['bagi-hasil']) --}}
							<li class="list-group-item d-flex justify-content-between">
								<div>
									
									<span class="text-muted">Bagi Hasil (%)</span>
									<h6 class="fw-500 my-0">
										{{$commitment->poktan_share}} : {{$commitment->importir_share}}
									</h6>
									
								</div>
							</li>
							{{-- @endisset --}}
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel" id="panel-5">
				<div class="panel-hdr">
					<h2>
						Daftar <span class="fw-300"><i>Kelompoktani Mitra & PKS</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblPks" class="table table-sm table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>No. Perjanjian</th>
									<th>Poktan Mitra</th>
									<th>Anggota</th>
									<th>Rencana Kerja</th>
									<th>Tindakan</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($pkss as $pks)
									<tr>
										<td>{{$pks->no_perjanjian}}</td>
										<td>{{$pks->masterpoktan->nama_kelompok}}</td>
										<td class="text-right">{{$pks->lokasi_count}} org</td>
										<td class="text-right">
											{{$pks->luas_rencana}}ha / {{$pks->luas_rencana*6}}ton
										</td>
										<td>
											@php
												$emptyColumns = collect($pks->getAttributes())->except([
													'pengajuan_id','docstatus','status','note','verif_at','verif_by',
													'jumlah_anggota','provinsi_id','kabupaten_id','kecamatan_id',
													'kelurahan_id','created_at', 'updated_at', 'deleted_at'
													])->filter(function($value) {
													return empty($value);
												});

												if ($emptyColumns->count() > 0) {
													echo '<a href="'.route('admin.task.pks.edit', $pks->id).'" class="btn btn-icon btn-xs btn-danger" data-toggle="tooltip" data-original-title="Lengkapi data"><i class="fal fa-cassette-tape"></i></a>';
												} else {
													echo '
													<a href="'.route('admin.task.pks.edit', $pks->id).'" class="btn btn-icon btn-xs btn-success" data-toggle="tooltip" data-original-title="Data lengkap. Klik jika ingin mengubah data">
														<i class="fal fa-file-check"></i>
													</a>
													<a href="'.route('admin.task.pks.anggotas', $pks->id).'" class="btn btn-icon btn-xs btn-primary" data-toggle="tooltip" data-original-title="Lengkapi data realisasi wajib tanam-produksi">
														<i class="fal fa-seedling"></i>
													</a>
													<a href="'.route('admin.task.pks.saprodi', $pks->id).'" class="btn btn-icon btn-xs btn-info" data-toggle="tooltip" data-original-title="Data Bantuan Sarana Produksi Tani">
														<i class="fal fa-gifts"></i>
													</a>
													';
												}
											@endphp
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					
				</div>
			</div>
			<div class="panel" id="panel-6">
				<div class="panel-hdr">
					<h2>
						Daftar <span class="fw-300"><i>Penangkar Benih Mitra</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblPenangkar" class="table table-sm table-bordered table-hover table-striped w-100">
							<thead>
								<th>Penangkar</th>
								<th>Pimpinan</th>
								<th>Varietas</th>
								<th>Ketersediaan</th>
							</thead>
							<tbody>
								@foreach ($penangkars as $penangkar)
								<tr>
									<td>
										<a href="javascript:void()" data-toggle="modal" class="fw-500"
											data-target="#editPenangkar{{$penangkar->id}} ">
											{{$penangkar->masterpenangkar->nama_lembaga}}
										</a>
									</td>
									<td> {{$penangkar->masterpenangkar->nama_pimpinan}} </td>
									<td> {{$penangkar->varietas}} </td>
									<td> {{$penangkar->ketersediaan}} </td>
									
									{{-- Modal view Penangkar --}}
									<div class="modal fade" id="editPenangkar{{$penangkar->id}}"
										tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-dialog-right" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<div>
														<h5 class="modal-title" id="myModalLabel">Data Penangkar 
															<span class="fw-500 text-info">
																{{$penangkar->masterpenangkar->nama_lembaga}}
															</span>
														</h5>
														<small id="helpId" class="text-muted">
															Tambah Daftar Penangkar Benih berlabel
														</small>
													</div>
													<button type="button" class="close" data-dismiss="modal"
														aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="panel" id="panel-2">
														<div class="panel-hdr">
															<h2>
																Daftar <span class="fw-300"><i>Penangkar Benih Mitra</i></span>
															</h2>
															<div class="panel-toolbar">
															</div>
														</div>
														<div class="panel-container show">
															<div class="panel-content">
																<ul class="list-group list-group-flush">
																	<li class="list-group-item d-flex justify-content-between align-items-center">
																		<span class="text-muted">Nama Lembaga</span>
																		<span class="fw-500">{{$penangkar->masterpenangkar->nama_lembaga}}</span>
																	</li>
																	<li class="list-group-item d-flex justify-content-between align-items-center">
																		<span class="text-muted">Nama Pimpinan</span>
																		<span class="fw-500">{{$penangkar->masterpenangkar->nama_pimpinan}}</span>
																	</li>
																	<li class="list-group-item d-flex justify-content-between">
																		<span class="text-muted">Kontak Pimpinan</span>
																		<span class="fw-500">{{$penangkar->masterpenangkar->hp_pimpinan}}</span>
																	</li>
																	<li class="list-group-item d-flex justify-content-between row">
																		<span class="text-muted col-sm-3">Alamat</span>
																		<span class="fw-500 col-sm-9 text-right">
																			{{$penangkar->masterpenangkar->alamat}}
																		</span>
																	</li>
																</ul>
															</div>
														</div>
													</div>
													<div class="panel" id="panel-3">
														<div class="panel-hdr">
															<h2>
																Data <span class="fw-300"><i>Benih</i></span>
															</h2>
															<div class="panel-toolbar">
															</div>
														</div>
														<div class="panel-container show">
															<div class="panel-content">
																<ul class="list-group list-group-flush">
																	<li class="list-group-item d-flex justify-content-between align-items-center">
																		<span class="text-muted">Varietas</span>
																		<span class="fw-500">{{$penangkar->varietas}}</span>
																	</li>
																	<li class="list-group-item d-flex justify-content-between align-items-center">
																		<span class="text-muted">Ketersediaan</span>
																		<span class="fw-500">{{$penangkar->ketersediaan}}</span>
																	</li>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	

@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
@parent
<script>
	$(document).ready(function()
	{
		// initialize tblPenangkar
		$('#tblPenangkar').dataTable(
		{
			responsive: true,
			lengthChange: false,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
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
				},
				{
					text: '<i class="fa fa-plus mr-1"></i>Penangkar',
					titleAttr: 'Tambah Penangkar Mitra',
					className: 'btn btn-info btn-xs ml-2',
					action: function(e, dt, node, config) {
						window.location.href = '{{ route('admin.task.commitment.penangkar', $commitment->id) }}';
					}
				}
			]
		});

	});
</script>

<script>
	$(document).ready(function()
	{
		$('#tblPks').dataTable(
		{
			responsive: true,
			lengthChange: false,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
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
	});
</script>
@endsection