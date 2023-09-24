@extends('layouts.admin')
@section ('styles')
<style>
td {
	vertical-align: middle !important;
}
</style>
@endsection
@section('content')
	{{-- @include('partials.breadcrumb') --}}
	@include('partials.subheader')
	@can('online_access')
		@include('partials.sysalert')
		<div class="row" id="contentToPrint">
			@php
				$npwp = str_replace(['.', '-'], '', $commitment->npwp);
			@endphp
			<div class="col-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content">
							<div class="row d-flex justify-content-between">
								<div class="form-group col-md-4">
									<label class="form-label" for="no_pengajuan">Nomor Pengajuan</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="no_pengajuan" name="no_pengajuan"
											value="{{$verifikasi->no_pengajuan}}" disabled>
									</div>
									<span class="help-block">Nomor Pengajuan Verifikasi.</span>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label" for="no_ijin">Nomor RIPH</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-file-invoice"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="no_ijin" value="{{$verifikasi->no_ijin}}" name="no_ijin" disabled>
									</div>
									<span class="help-block">Nomor Ijin RIPH.</span>
								</div>
								<div class="form-group col-md-4">
									<label class="form-label" for="created_at">Tanggal Pengajuan</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fal fa-calendar-day"></i>
											</span>
										</div>
										<input type="text" class="form-control form-control-sm" id="created_at" name="created_at"
											value="{{$verifikasi->created_at}}" disabled>
									</div>
									<span class="help-block">Tanggal Pengajuan</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#panel-2" role="tab" aria-selected="true">Ringkasan Data Realisasi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-3" role="tab" aria-selected="true">Kelengkapan Berkas</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-4" role="tab" aria-selected="true">Perjanjian Kemitraan</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-5" role="tab" aria-selected="true">Data Lokasi Tanam</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-danger" data-toggle="tab" href="#panel-6" role="tab" aria-selected="true">Hasil Pemeriksaan</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade active show" id="panel-2" role="tabpanel" aria-labelledby="panel-2">
						<div id="panel-2" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah data ringkasan realisasi komitmen wajib tanam-produksi yang telah dilaporkan oleh Pelaku Usaha.</small>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="dataRiph">
										<thead class="thead-themed text-muted text-uppercase">
											<th>Data</th>
											<th>Kewajiban</th>
											<th>Realisasi</th>
											<th>Status</th>
										</thead>
										<tbody>
											<tr>
												<td>Tanam</td>
												<td class="text-right">
													{{ number_format($commitment->volume_riph * 0.05/6, 2, '.', ',') }} ha
												</td>
												<td class="text-right">
													{{number_format($total_luastanam, 2,'.',',')}} ha
												</td>
												<td>
													@if($commitment->volume_riph * 0.05/6 >= $total_luastanam)
														<span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>TIDAK TERPENUHI</span>
													@else
													<span class="text-success"><i class="fas fa-check mr-1"></i>TERPENUHI</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>Kelompok Tani dan PKS</td>
												<td class="text-right">
													{{$countPoktan}} Poktan
												</td>
												<td class="text-right">
													{{$countPks}} PKS
												</td>
												<td>
													@if($countPks < $countPoktan)
														<i class="fas fa-exclamation-circle mr-1 text-warning"></i><span class="text-warning fw-500">TIDAK SESUAI</span>
													@else
														<i class="fas fa-check mr-1 text-success"></i><span class="text-success fw-500">SESUAI</span>
													@endif
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-3" role="tabpanel" aria-labelledby="panel-3">
						<div id="panel-3" class="panel">
							<form method="post"
								action="{{route('verification.tanam.checkBerkas', $verifikasi->id)}}">
								@csrf
								<div class="panel-container show">
									<div class="panel-tag fade show">
										<div class="d-flex align-items-center">
											<i class="fal fa-info-circle mr-1"></i>
											<div class="flex-1">
												<small>Berikut ini adalah berkas-berkas kelengkapan yang diunggah oleh Pelaku Usaha.</small>
											</div>
										</div>
									</div>
									<div class="panel-content">
										<table class="table table-striped table-bordered table-sm w-100" id="attchCheck">
											<thead class="thead-themed text-uppercase text-muted">
												<tr>
													<th>Form</th>
													<th>Nama Berkas</th>
													<th>Tindakan</th>
													<th>Hasil Periksa</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Surat Pertanggungjawaban Mutlak</td>
													<td>{{$userDocs->sptjm}}</td>
													<td>
														@if ($userDocs->sptjm)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjm) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="sptjmcheck" id="sptjmcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sptjmcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->sptjmcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Surat Pengajuan Verifikasi Tanam</td>
													<td>{{$userDocs->spvt}}</td>
													<td>
														@if ($userDocs->spvt)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvt) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="spvtcheck" id="spvtcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->spvtcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->spvtcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Form Realisasi Tanam</td>
													<td>{{$userDocs->rta}}</td>
													<td>
														@if ($userDocs->rta)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="rtacheck" id="rtacheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->rtacheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->rtacheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Form SPH-SBS</td>
													<td>{{$userDocs->sphtanam}}</td>
													<td>
														@if ($userDocs->sphtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="sphtanamcheck" id="sphtanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphtanamcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->sphtanamcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Pengantar Dinas telah selesai Tanam</td>
													<td>{{$userDocs->spdst}}</td>
													<td>
														@if ($userDocs->spdst)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spdst) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="spdstcheck" id="spdstcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->spdstcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->spdstcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															<option value="" {{ $userDocs->logbookcheck == '' ? 'selected' : '' }}>Tidak ada</option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Logbook (s.d tanam)</td>
													<td>{{$userDocs->logbooktanam}}</td>
													<td>
														@if ($userDocs->logbooktanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbooktanam) }}">
																<i class="fas fa-search mr-1"></i>
																Lihat Dokumen
															</a>
														@endif
													</td>
													<td>
														<select class="form-control form-control-sm" name="logbookcheck" id="logbookcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->logbookcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->logbookcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															<option value="" {{ $userDocs->logbookcheck == '' ? 'selected' : '' }}>Tidak ada</option>
														</select>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="card-footer d-flex justify-content-between align-items-center">
									<div class="help-block col-md-7">
									</div>
									<div class="col-md text-right">
										<button type="submit" class="btn btn-primary btn-sm">
											Selesai
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-4" role="tabpanel" aria-labelledby="panel-4">
						<div id="panel-4" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah data Perjanjian Kerjasama. Anda dapat memeriksa dan menetapkan hasil pemeriksaan.</small>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="pksCheck">
										<thead class="thead-themed">
											<tr>
												<th class="text-uppercase text-muted">Nomor Perjanjian</th>
												<th class="text-uppercase text-muted">Kelompok Tani</th>
												<th class="text-uppercase text-muted">Masa Berlaku</th>
												<th class="text-uppercase text-muted">Status</th>
												<th class="text-uppercase text-muted">Tindakan</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($pkss as $pks)
												<tr>
													<td>{{$pks->no_perjanjian}}</td>
													<td>{{$pks->masterpoktan->nama_kelompok}}</td>
													<td class="text-center">
														{{$pks->tgl_perjanjian_start}} s.d
														{{$pks->tgl_perjanjian_end}}
													</td>
													<td class="text-center">
														{{$pks->status}}
													</td>
													<td class="text-center">
														<a href="{{route('verification.tanam.check.pks', ['noIjin' => $noIjin, 'poktan_id' => $pks->poktan_id]) }}" class="btn btn-icon @if($pks->status) btn-success @else btn-warning @endif btn-xs" data-toggle="tooltip" data-original-title="Lihat/Periksa berkas dan data.">
															<i class="fal fa-search"></i>
														</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>

							<form action="{{route('verification.tanam.checkPksSelesai', $verifikasi->id)}}" method="post">
								@csrf
								@method('PUT')
								<div class="card-footer d-flex alignt-items-center justify-content-between">
									<div>
									</div>
									<div>
										<button type="submit" class="btn btn-primary btn-sm">
											simpan
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-5" role="tabpanel" aria-labelledby="panel-5">
						<div id="panel-5" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah data lokasi tanam-produksi. Anda dapat melihat dan memeriksa titik lokasi dan polygon lahan.</small>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="dataTable">
										<thead class="thead-themed text-uppercase text-muted">
											<th>Kelompoktani</th>
											<th>Nama Lokasi</th>
											<th>Pengelola</th>
											<th>Luas</th>
											<th>Tindakan</th>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-6" role="tabpanel" aria-labelledby="panel-6">
						<div class="panel" id="panel-6">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Setelah selesai memeriksa secara menyeluruh, Anda harus menetapkan hasil pemeriksaan yang dilakukan pada bagian ini.</small>
										</div>
									</div>
								</div>
								<form action="{{route('verification.tanam.storeCheck', $verifikasi->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									@method('PUT')
									<div class="panel-content">
										<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
										<input type="text" name="no_pengajuan" value="{{$verifikasi->no_pengajuan}}" hidden>
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
										<div class="row d-flex justify-content-between">
											<div class="form-group col-md-12">
												<label for="note">Catatan Pemeriksaan <sup class="text-danger"> *</sup></label>
												<textarea name="note" id="note" rows="3" class="form-control form-control-sm">{{ old('note', $verifikasi ? $verifikasi->note : '') }}</textarea>
											</div>
											<div class="form-group col-md-6">
												<label class="">Nota Dinas<sup class="text-danger"> *</sup></label>
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="ndhprt" id="ndhprt" value="{{ old('ndhprt', $verifikasi ? $verifikasi->ndhprt : '') }}">
													<label class="custom-file-label" for="ndhprt">{{ old('ndhprt', $verifikasi ? $verifikasi->ndhprt : 'pilih berkas') }}</label>
												</div>
												@if ($verifikasi->ndhprt)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->ndhprt) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Nota Dinas.
													</a>
												@else
													<span class="help-block fw-500">Nota Dinas Hasil Pemeriksaan Realisasi Tanam. <span class="text-danger">(wajib)</span></span>
												@endif
											</div>
											<div class="form-group col-md-6">
												<label class="">Berita Acara<sup class="text-danger">*</sup></label>
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="batanam" id="batanam" value="{{ old('batanam', $verifikasi ? $verifikasi->batanam : '') }}">
													<label class="custom-file-label" for="batanam">{{ old('batanam', $verifikasi ? $verifikasi->batanam : 'pilih berkas') }}</label>
												</div>
												@if ($verifikasi->batanam)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->batanam) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Berita Acara.
													</a>
												@else
													<span class="help-block">Berita Acara Pemeriksaan Realisasi Tanam. <span class="text-danger"></span></span>
												@endif
											</div>
											<div class="form-group col-md-3">
												<label for="">Metode Pemeriksaan<sup class="text-danger"> *</sup></label>
												<select name="metode" id="metode" class="form-control custom-select" required>
													<option value="" hidden>-- pilih metode --</option>
													<option value="Lapangan" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Lapangan' ? 'selected' : '' }}>Lapangan</option>
													<option value="Wawancara" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Wawancara' ? 'selected' : '' }}>Wawancara</option>
												</select>
												<small id="helpId" class="text-muted">Pilih metode pemeriksaan</small>
											</div>
											<div class="form-group col-md-3">
												<label for="">Kesimpulan Pemeriksaan<sup class="text-danger"> *</sup></label>
												<select name="status" id="status" class="form-control custom-select" required>
													<option value="" hidden>-- pilih status --</option>
													<option value="4" {{ old('status', $verifikasi ? $verifikasi->status : '') == '4' ? 'selected' : '' }}>Sesuai</option>
													<option value="5" {{ old('status', $verifikasi ? $verifikasi->status : '') == '5' ? 'selected' : '' }}>Tidak Sesuai/Perbaikan</option>
												</select>
												<small id="helpId" class="text-muted">Pilih hasil pemeriksaan</small>
											</div>
											<div class="form-group col-md-6">
												<label class="">Dengan ini kami menyatakan verifikasi tanam telah SELESAI dilaksanakan.</label>
												<div class="input-group">
													<input type="text" class="form-control" placeholder="ketik username Anda di sini" id="validasi" name="validasi"required>
													<div class="input-group-append">
														<button class="btn btn-danger" type="submit" onclick="return validateInput()" id="btnSubmit">
															<i class="fas fa-save text-align-center mr-1"></i>Simpan
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		{{-- modal view doc --}}
		<div class="modal fade" id="viewDocs" tabindex="-1" role="dialog" aria-labelledby="document" aria-hidden="true">
			<div class="modal-dialog modal-dialog-right" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">
							Berkas <span class="fw-300"><i>lampiran </i></span>
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body embed-responsive embed-responsive-16by9">
						<iframe class="embed-responsive-item" src="" width="100%"  frameborder="0"></iframe>
					</div>
				</div>
			</div>
		</div>
	@endcan
@endsection

@section('scripts')
	@parent
	<script>
		$(document).ready(function() {
			$('#viewDocs').on('shown.bs.modal', function (e) {
				var docUrl = $(e.relatedTarget).data('doc');
				$('iframe').attr('src', docUrl);
			});

			$(function() {
				$("#pksMitra").select2({
					placeholder: "--Pilih PKS/Poktan",
					dropdownParent:'#selectPks'
				});

				$("#lokasiLahan").select2({
					placeholder: "--Pilih lokasi",
					dropdownParent:'#selectLokasi'
				});
			});

			var tableData = $('#dataTable').DataTable({
				responsive: true,
				lengthChange: true,
				dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						titleAttr: 'Generate Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Print Table',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					}
				],
				columnDefs: [

					{ className: 'text-right', targets: [3,4] },
					{ className: 'text-center', targets: [5] },
				]
			});

			function updateTableData() {
				$.ajax({
					url: '{{ route("verification.lokasitanam", $noIjin) }}',
					type: 'GET',
					dataType: 'json',
					success: function(response) {

						tableData.clear().draw();
						if (response.lokasis.length > 0) {
							$.each(response.lokasis, function(index, lokasi) { // Update response handling

								var luasTanam = lokasi.luas_tanam;
								var formatter = new Intl.NumberFormat('en-GB', {
									style: 'decimal',
									minimumFractionDigits: 2,
									maximumFractionDigits: 2,
								});
								var LuasTanam = formatter.format(luasTanam);

								var id = lokasi.id;
								var npwp = lokasi.npwp;
								var noIjin = lokasi.no_ijin;
								var poktan = lokasi.poktan;
								var anggota = lokasi.anggota;
								var namaLokasi = lokasi.nama_lokasi;
								var actionBtn = `
									<a href="${lokasi.show}" class="btn btn-xs btn-icon btn-primary" title="Lihat detail">
										<i class="fal fa-search"></i>
									</a>
								`;
								tableData.row.add([poktan, namaLokasi, anggota,LuasTanam, actionBtn]).draw(false);
							});
						}
						tableData.draw(); // Draw the table after adding the rows
					},
					error: function(xhr, status, error) {
						console.error(xhr.responseText);
					}
				});
			}
			updateTableData();
		});
	</script>

	<script>
		function validateInput() {
			// get the input value and the current username from the page
			var inputVal = document.getElementById('validasi').value;
			var currentUsername = '{{ Auth::user()->username }}';
			var status = document.getElementById("status").value;
			var ndhprtInput = document.getElementById("ndhprt");
			var batanamInput = document.getElementById("batanam");

			// check if the input is not empty and matches the current username
			if (inputVal !== '' && inputVal === currentUsername) {
				// Jika status = 4, lakukan validasi tambahan
				if (status === "4") {
					if (ndhprtInput.files.length === 0 || batanamInput.files.length === 0) {
						alert("Nota Dinas dan Berita Acara harus diunggah jika status adalah 'Sesuai' (4).");
						return false; // Menghentikan pengiriman formulir
					}
				}
				return true; // Lanjutkan pengiriman formulir jika status adalah 'Tidak Sesuai' (5) atau kondisi lainnya
			} else {
				alert('Isi kolom Konfirmasi dengan username Anda!.');
				return false; // prevent form submission
			}
		}
	</script>
@endsection
