@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
@can('pks_create')
	@php
		$npwp = str_replace(['.', '-'], '', $npwpCompany);
	@endphp
	<div class="row">
		<div class="col">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>
						Data <span class="fw-300"><i>Informasi</i></span>
					</h2>
					<div class="panel-toolbar">
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content row d-flex">
						<div class="form-group col-md-4">
							<label for="">No. RIPH</label>
							<input disabled class="form-control form-control-sm fw-500 text-primary"
							placeholder="" aria-describedby="helpId"
							value="{{$commitment->no_ijin}}">
						</div>
						<div class="form-group col-md-4">
							<label for="">No. Perjanjian</label>
							<input disabled class="form-control form-control-sm fw-500 text-primary"
							placeholder="" aria-describedby="helpId"
							value="{{$pks->no_perjanjian}}">
						</div>
						<div class="form-group col-md-4">
							<label for="">Kelompoktani</label>
							<input disabled class="form-control form-control-sm fw-500 text-primary"
							placeholder="" aria-describedby="helpId"
							value="{{$pks->masterpoktan->nama_kelompok}}">
						</div>
					</div>
				</div>
			</div>
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>
						Daftar <span class="fw-300"><i>Realisasi Lokasi dan Pelaksana</i></span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content">
						<!-- datatable start -->
						<table id="tblLokasi" class="table table-bordered table-hover table-sm table-striped w-100">
							<thead class="thead-themed">
								<th>Petani Pelaksana</th>
								<th>Nama Lokasi</th>
								<th>Luas</th>
								<th>Tanggal Tanam</th>
								<th>Panen</th>
								<th>Tanggal Panen</th>
								<th>Tindakan</th>
							</thead>
							<tbody>
								@foreach ($lokasis as $lokasi)
								<tr>
									<td>
										<div class="d-flex justify-content-between px-2">
											<span>
												{{$lokasi->masteranggota->nama_petani}} -
												{{$lokasi->masteranggota->ktp_petani}}
											</span>
											{{-- <span class="small">
												@php
													$firstGroup = [
														$lokasi->latitude,
														$lokasi->longitude,
														$lokasi->polygon,
														$lokasi->nama_lokasi,
														$lokasi->altitude,
													];
													$isFirstGroupIncomplete = array_reduce($firstGroup, function ($carry, $item) {
														return $carry || empty($item);
													}, false);

													$secondGroup = [
														$lokasi->tgl_tanam,
														$lokasi->luas_tanam,
														$lokasi->tanam_doc,
														$lokasi->tanam_pict,
													];
													$isSecondGroupIncomplete = array_reduce($secondGroup, function ($carry, $item) {
														return $carry || empty($item);
													}, false);

													$thirdGroup = [
														$lokasi->tgl_panen,
														$lokasi->volume,
														$lokasi->panen_doc,
														$lokasi->panen_pict,
													];
													$isThirdGroupIncomplete = array_reduce($thirdGroup, function ($carry, $item) {
														return $carry || empty($item);
													}, false);
												@endphp
												@if ($isFirstGroupIncomplete)
													<i class="fal fa-map-marker-slash text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Geolokasi belum lengkap!"></i>
												@else
													<i class="fal fa-map-marker-check text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Geolokasi belum lengkap!"></i>
												@endif
												@if ($isSecondGroupIncomplete)
													<i class="fal fa-seedling text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Tanam belum lengkap!"></i>
												@endif
												@if ($isThirdGroupIncomplete)
													<i class="fal fa-balance-scale text-danger fw-bold"
													data-toggle="tooltip" data-original-title="Data Produksi belum lengkap!"></i>
												@endif
											</span> --}}
										</div>
									</td>
									<td>{{$lokasi->nama_lokasi}}</td>
									<td class="text-right">{{$lokasi->luas_tanam}} ha</td>
									<td class="text-center">{{$lokasi->tgl_tanam}}</td>
									<td class="text-right">{{$lokasi->volume}} ton</td>
									<td class="text-center">{{$lokasi->tgl_panen}}</td>
									<td  class="text-center">
										<a href="{{route('admin.task.lokasi.tanam', $lokasi->anggota_id)}}"
											title="Data Geolokasi dan Realisasi Tanam" class="btn btn-xs btn-icon btn-primary"
											data-toggle="tooltip" >
											<i class="fal fa-map"></i>
										</a>
										@if (!empty ($lokasi->polygon))
										<button type="button" class="btn btn-xs btn-icon btn-success" data-toggle="modal" data-target="#modelTanam-{{$lokasi->id}}" title="Data Tanam" id="btnModalTanam">
											<i class="fal fa-seedling"></i>
										</button>
										@endif
										@if (!empty ($lokasi->tgl_tanam) || $lokasi->tgl_panen)
											<button type="button" class="btn btn-xs btn-icon btn-warning" data-toggle="modal" data-target="#modelProduksi-{{$lokasi->id}}" title="Data Produksi" id="btnModalProduksi">
												<i class="fal fa-dolly"></i>
											</button>
										@endif
									</td>
									<!-- Modal -->
									<div class="modal fade" id="modelTanam-{{$lokasi->id}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h4 class="modal-title text-left">
														<div class="row">
															<span class="col-12 fw-500">Data Tanam</span>
															<span class="col-12 small">Luas lahan realisasi tanam.</span>
														</div>
													</h4>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<form action="{{route('admin.task.lokasi.tanam.store', $lokasi->anggota_id)}}" method="POST" enctype="multipart/form-data">
													@csrf
													@method('PUT')
													<div class="modal-body">
														<input type="hidden" value="{{$lokasi->anggota_id}}">
														<div class="card" style="width:100%">
															{{-- <div class="card-header fw-500">Realisasi Wajib Tanam</div> --}}
															@if($lokasi->tanam_pict)
																<img src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$lokasi->tanam_pict) }}" class="card-img-top" alt="Foto Tanam">
															@else
																<img src="{{ url('img/posts_img/1619.svg') }}" class="card-img-top" alt="Foto Tanam">
															@endif
															<div class="card-body">
																<div class="row">
																	<div class="form-group col-md-12">
																		<label class="form-label" for="tgl_tanam">Tanggal Tanam<sup class="text-danger"> *</sup></label>
																		<div class="input-group">
																			<div class="input-group-prepend">
																				<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
																			</div>
																			<input type="date" value="{{ old('tgl_tanam', $lokasi->tgl_tanam) }}"
																				name="tgl_tanam" id="tgl_tanam"
																				class="font-weight-bold form-control form-control-sm bg-white" />
																		</div>
																		<span class="help-block">Tanggal mulai penanaman.</span>
																	</div>
																	<div class="form-group col-md-12">
																		<label class="form-label" for="luas_tanam">Luas Bidang (ha)<sup class="text-danger"> *</sup></label>
																		<div class="input-group">
																			<div class="input-group-prepend">
																				<span class="input-group-text"><i class="fal fa-ruler-combined"></i></span>
																			</div>
																			<input type="number" value="{{ old('luas_tanam', $lokasi->luas_tanam) }}"
																				name="luas_tanam" id="luas_tanam" step="0.01"
																				class="font-weight-bold form-control form-control-sm bg-white" />
																		</div>
																		<span class="help-block">Luas area lahan diukur mandiri.</span>
																	</div>
																	<div class="form-group col-md-12">
																		<label class="form-label">Dokumen Pendukung<span class="text-danger">*</span></label>
																		<div class="custom-file input-group">
																			<input type="file" class="custom-file-input" name="tanam_doc" id="tanam_doc" value="{{ old('tanam_doc', $lokasi->tanam_doc) }}">
																			<label class="custom-file-label" for="tanam_doc">{{ $lokasi->tanam_doc ? $lokasi->tanam_doc : 'Pilih berkas...' }}</label>
																		</div>
																		<span class="help-block">
																			@if($lokasi->tanam_doc)
																				<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$lokasi->tanam_doc) }}" target="_blank">
																					Lihat Dokumen Pendukung diunggah.
																				</a>
																			@else
																				Unggah Dokumen Pendukung. Ekstensi pdf ukuran maks 4mb.
																			@endif
																		</span>
																	</div>
																	<div class="form-group col-md-12">
																		<label class="form-label">Dokumentasi Tanam<sup class="text-danger"> *</sup></label>
																		<div class="custom-file input-group">
																			<input type="file" class="custom-file-input" id="customControlValidation7"
																				name="tanam_pict" id="tanam_pict">
																			<label class="custom-file-label" for="tanam_pict">{{ $lokasi->tanam_pict ? $lokasi->tanam_pict : 'Pilih berkas...' }}</label>
																		</div>
																		<span class="help-block">Unggah Dokumentasi Tanam. Ekstensi jpg ukuran maks 4mb.</span>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
														<button type="submit" class="btn btn-primary">Save</button>
													</div>
												</form>
											</div>
										</div>
									</div>
									<div class="modal fade" id="modelProduksi-{{$lokasi->id}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h4 class="modal-title text-left">
														<div class="row">
															<span class="col-12 fw-500">Data Produksi</span>
															<span class="col-12 small">Jumlah total produksi pada lokasi/lahan ini.</span>
														</div>
													</h4>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<form action="{{route('admin.task.lokasi.produksi.store', $lokasi->anggota_id)}}" method="POST" enctype="multipart/form-data">
													@csrf
													@method('PUT')
													<div class="modal-body">
														<input type="hidden" value="{{$lokasi->anggota_id}}">
														<div class="card" style="width:100%">
															{{-- <div class="card-header fw-500">Realisasi Wajib Produksi</div> --}}
															@if($lokasi->tanam_pict)
																<img src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$lokasi->panen_pict) }}" class="card-img-top" alt="Foto Tanam">
															@else
																<img src="{{ url('img/posts_img/1619.svg') }}" class="card-img-top" alt="Foto Tanam">
															@endif
															<div class="card-body">
																<div class="row">
																	<div class="form-group col-md">
																		<label class="form-label" for="tgl_panen">Tanggal Panen<sup class="text-danger"> *</sup></label>
																		<div class="input-group">
																			<div class="input-group-prepend">
																				<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
																			</div>
																			<input type="date" value="{{ old('tgl_panen', $lokasi->tgl_panen) }}"
																				name="tgl_panen" id="tgl_panen"
																				class="font-weight-bold form-control form-control-sm bg-white" />
																		</div>
																		<span class="help-block">Tanggal awal dilakukan panen.</span>
																	</div>
																	<div class="form-group col-md">
																		<label class="form-label" for="luas_tanam">Volume Produksi (ton)<sup class="text-danger"> *</sup></label>
																		<div class="input-group">
																			<div class="input-group-prepend">
																				<span class="input-group-text"><i class="fal fa-ruler-combined"></i></span>
																			</div>
																			<input type="number" value="{{ old('volume', $lokasi->volume) }}"
																				name="volume" id="volume" step="0.01"
																				class="font-weight-bold form-control form-control-sm bg-white" />
																		</div>
																		<span class="help-block">Luas area lahan diukur mandiri.</span>
																	</div>
																	<div class="form-group col-md-12">
																		<label class="form-label">Dokumen Pendukung<sup class="text-danger"> *</sup></label>
																		<div class="custom-file input-group">
																			<input type="file" class="custom-file-input" id="customControlValidation7"
																				value="{{ old('panen_doc', $lokasi->panen_doc) }}"
																				name="panen_doc" id="panen_doc">
																			<label class="custom-file-label" for="panen_doc">{{ $lokasi->panen_doc ? $lokasi->panen_doc : 'Pilih berkas...' }}</label>
																		</div>
																		<span class="help-block">
																			@if($lokasi->panen_doc)
																				<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$lokasi->panen_doc) }}" target="_blank">
																					Lihat Dokumen Pendukung diunggah.
																				</a>
																			@else
																				Unggah Dokumen Pendukung. Ekstensi pdf ukuran maks 4mb.
																			@endif
																		</span>
																	</div>
																	<div class="form-group col-md-12">
																		<label class="form-label">Dokumentasi Produksi<sup class="text-danger"> *</sup></label>
																		<div class="custom-file input-group">
																			<input type="file" class="custom-file-input" id="customControlValidation7"
																				value="{{ old('panen_pict', $lokasi->panen_pict) }}"
																				name="panen_pict" id="panen_pict">
																				<label class="custom-file-label" for="panen_pict">{{ $lokasi->panen_pict ? $lokasi->panen_pict : 'Pilih berkas...' }}</label>
																		</div>
																		<span class="help-block">Unggah Dokumentasi Panen. Ekstensi jpg ukuran maks 4mb.</span>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
														<button type="submit" class="btn btn-primary">Save</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</tr>
								@endforeach
							</tbody>
							<tfoot class="thead-themed">
								<tr>
									<th></th>
									<th class="text-right">Total Luas</th>
									<th class="text-right">{{$sumLuas}} ha</th>
									<th class="text-right">Total Produksi</th>
									<th class="text-right">{{$sumProduksi}} ton</th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
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
		$('#tblLokasi').dataTable(
		{
			responsive: true,
			pageLength:10,
			lengthChange: true,
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'fl><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
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

{{-- {{ route('admin.task.commitments.pksmitra', $commitment->id) }} --}}
