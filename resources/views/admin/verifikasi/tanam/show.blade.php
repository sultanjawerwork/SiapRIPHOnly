@extends('layouts.admin')
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
						<a class="nav-link" data-toggle="tab" href="#panel-3" role="tab" aria-selected="true">Hasil Verifikasi Tahap Tanam</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-4" role="tab" aria-selected="true">Perjanjian Kemitraan</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-5" role="tab" aria-selected="true">Data Lokasi Tanam</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-6" role="tab" aria-selected="true">Hasil Pemeriksaan</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade active show" id="panel-2" role="tabpanel" aria-labelledby="panel-2">
						<div class="panel" id="panel-2">
							{{-- <div class="panel-hdr">
								<h2>
									Ringkasan Data Realisasi
								</h2>
								<div class="panel-toolbar">

								</div>
							</div> --}}
							<div class="panel-container show">
								<div class="alert alert-info border-0 mb-0">
									<div class="d-flex align-item-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<span>Berikut ini adalah data ringkasan realisasi komitmen wajib tanam-produksi yang telah dilaporkan oleh Pelaku Usaha.</span>
										</div>
									</div>
								</div>
								<div class="panel-content">
									<table class="table table-striped table-bordered w-100" id="dataRiph">
										<thead>
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
							{{-- <div class="panel-hdr">
								<h2>Hasil Verifikasi Tahap Tanam</h2>
								<div class="panel-toolbar">
								</div>
							</div> --}}
							<div class="panel-container show">
								@if (empty($verifikasi->status))
									<div class="alert alert-danger border-0 mb-0">
										<div class="d-flex align-item-center">
											<i class="fal fa-exclamation-circle fa-1x mr-1"></i>
											<div class="flex-1">
												<span>Pelaku usaha belum/tidak mengajukan verifikasi tanam</span>
											</div>
										</div>
									</div>
								@endif
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="attchCheck">
										<thead class="thead-themed">
											<tr>
												<th class="text-uppercase text-muted">Form</th>
												<th class="text-uppercase text-muted">Berkas</th>
												<th class="text-uppercase text-muted">Hasil Periksa</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>
													@if ($verifikasi->spvt)
														<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $verifikasi->spvt ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->spvt) : '' }}">
															Surat Pengajuan Verifikasi Tanam
														</a>
													@else
														<span>Surat Pengajuan Verifikasi Produksi</span>
													@endif
												</td>
												<td>
													<span>{{$verifikasi->spvt}}</span>
												</td>
												<td>
													@if ($verifikasi->spvtcheck)
														<span>{{$verifikasi->spvtcheck}}</span>
													@else
														<span class="text-danger text-center">Tidak ada data</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>
													@if ($verifikasi->sptjm)
														<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->sptjm) }}">
															Surat Pertanggungjawaban Mutlak
														</a>
													@else
														<span>Surat Pertanggungjawaban Mutlak</span>
													@endif
												</td>
												<td>
													<span>{{$verifikasi->sptjm}}</span>
												</td>
												<td>
													@if ($verifikasi->sptjmcheck)
														<span>{{$verifikasi->sptjmcheck}}</span>
													@else
														<span class="text-danger text-center">Tidak ada data</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>
													@if ($verifikasi->rta)
														<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->rta) }}">
															Form Realisasi Tanam
														</a>
													@else
														<span>Form Realisasi Tanam</span>
													@endif
												</td>
												<td>
													<span>{{$verifikasi->rta}}</span>
												</td>
												<td>
													@if ($verifikasi->rtacheck)
														<span>{{$verifikasi->rtacheck}}</span>
													@else
														<span class="text-danger text-center">Tidak ada data</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>
													@if ($verifikasi->sphtanam)
														<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->sphtanam) }}">
															Form SPH-SBS
														</a>
													@else
														<span>Form SPH-SBS</span>
													@endif
												</td>
												<td>
													<span>{{$verifikasi->sphtanam}}</span>
												</td>
												<td>
													@if ($verifikasi->sphtanamcheck)
														<span>{{$verifikasi->sphtanamcheck}}</span>
													@else
														<span class="text-danger text-center">Tidak ada data</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>
													@if ($verifikasi->spdst)
														<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->spdst) }}">
															Pengantar Dinas telah selesai Tanam
														</a>
													@else
														<span>Pengantar Dinas telah selesai Tanam</span>
													@endif
												</td>
												<td>
													<span>{{$verifikasi->spdst}}</span>
												</td>
												<td>
													@if ($verifikasi->spdstcheck)
														<span>{{$verifikasi->spdstcheck}}</span>
													@else
														<span class="text-danger text-center">Tidak ada data</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>
													@if ($verifikasi->logbooktanam)
														<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->logbooktanam) }}">
															Logbook (s.d tanam)
														</a>
													@else
														<span>Logbook (s.d tanam)</span>
													@endif
												</td>
												<td>
													<span>{{$verifikasi->logbooktanam}}</span>
												</td>
												<td>
													@if ($verifikasi->logbookcheck)
														<span>{{$verifikasi->logbookcheck}}</span>
													@else
														<span class="text-danger text-center">Tidak ada data</span>
													@endif
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-4" role="tabpanel" aria-labelledby="panel-4">
						<div id="panel-4" class="panel">
							{{-- <div class="panel-hdr">
								<h2>Perjanjian Kemitraan</h2>
							</div> --}}
							<div class="panel-container show">
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="pksCheck">
										<thead class="thead-themed">
											<tr>
												<th class="text-uppercase text-muted">Nomor Perjanjian</th>
												<th class="text-uppercase text-muted">Kelompok Tani</th>
												<th class="text-uppercase text-muted">Masa Berlaku</th>
												<th class="text-uppercase text-muted">Status</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($pkss as $pks)
												<tr>
													<td>
														@if($pks->berkas_pks)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $pks->berkas_pks ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$pks->berkas_pks) : '' }}">
																{{$pks->no_perjanjian}}
															</a>
														@endif
													</td>
													<td>{{$pks->masterpoktan->nama_kelompok}}</td>
													<td>
														{{$pks->tgl_perjanjian_start}} s.d
														{{$pks->tgl_perjanjian_end}}
													</td>
													<td>
														@if ($pks->pkscheck->isNotEmpty())
															{{ $pks->pkscheck->first()->status }}
														@endif
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="tab-pane fade" id="panel-5" role="tabpanel" aria-labelledby="panel-5">
						<div id="panel-5" class="panel">
							{{-- <div class="panel-hdr">
								<h2>Data Lokasi Tanam</h2>
							</div> --}}
							<div class="panel-container show">
								<div class="panel-content">
									<table class="table table-striped table-bordered table-sm w-100" id="dataTable">
										<thead>
											<th class="text-uppercase text-muted">Kelompoktani</th>
											<th class="text-uppercase text-muted">Nama Lokasi</th>
											<th class="text-uppercase text-muted">Pengelola</th>
											<th class="text-uppercase text-muted">Luas</th>
											<th class="text-uppercase text-muted">Tindakan</th>
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
							<div class="panel-hdr">
								<h2>Hasil Pemeriksaan</h2>
								<div class="panel-toolbar">
									<span class="help-block">Rekam Berita Acara ini <span class="text-danger fw-500">HANYA JIKA</span> pemeriksaan seluruh data secara administratif telah selesai.</span>
								</div>
							</div>
							<div class="panel-container show">
								<form action="{{route('verification.tanam.store', $verifikasi->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									@method('PUT')
									<div class="panel-content">
										<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
										<input type="text" name="no_pengajuan" value="{{$verifikasi->no_pengajuan}}" hidden>
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
										<div class="row d-flex justify-content-between">
											<div class="col-md-6">
												<div class="form-group">
													<label for="note">Catatan Pemeriksaan</label>
													<textarea name="note" id="note" rows="13" class="form-control form-control-sm" required>{{ old('note', $verifikasi ? $verifikasi->note : '') }}</textarea>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="">Nota Dinas<sup class="text-danger"> *</sup></label>
													<div class="custom-file input-group">
														<input type="file" class="custom-file-input" name="ndhprt" id="ndhprt" value="{{ old('ndhprt', $verifikasi ? $verifikasi->ndhprt : '') }}" required>
														<label class="custom-file-label" for="batanam">{{ old('ndhprt', $verifikasi ? $verifikasi->ndhprt : 'pilih berkas') }}</label>
													</div>
													@if($verifikasi->ndhprt)
														<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $verifikasi->ndhprt ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->ndhprt) : '' }}">
															Lihat Nota Dinas.
														</a>
													@endif
													<span class="text-danger">(wajib)</span>
												</div>
												<div class="form-group">
													<label class="">Berita Acara<sup class="text-danger"></sup></label>
													<div class="custom-file input-group">
														<input type="file" class="custom-file-input" name="batanam" id="batanam" value="{{ old('batanam', $verifikasi ? $verifikasi->batanam : '') }}" required>
														<label class="custom-file-label" for="batanam">{{ old('batanam', $verifikasi ? $verifikasi->batanam : 'pilih berkas') }}</label>
													</div>
													@if($verifikasi->batanam)
														<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $verifikasi->batanam ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->batanam) : '' }}">
															Lihat Berita Acara.
														</a>
													@endif
													<span class="text-danger"></span>
												</div>
												<div class="form-group">
													<label class="">Dengan ini kami menyatakan verifikasi pada bagian ini telah SELESAI</label>
													<div class="input-group">
														<input type="text" class="form-control" placeholder="ketik username Anda di sini" id="validasi" name="validasi"required>
														<div class="input-group-append">
															<button class="btn btn-danger" type="submit" onclick="return validateInput()">
																<i class="fas fa-save text-align-center mr-1"></i>Simpan
															</button>
														</div>
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
			<div class="col-12">
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

					{ className: 'text-right', targets: [3] },
					{ className: 'text-center', targets: [4] },
				]
			});

			function updateTableData() {
				$.ajax({
					url: '{{ route("admin.lokasiTanamByCommitment", $verifikasi->commitment_id) }}',
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

			// check if the input is not empty and matches the current username
			if (inputVal !== '' && inputVal === currentUsername) {
				return true; // allow form submission
			} else {
				alert('Isi kolom Konfirmasi dengan username Anda!.');
				return false; // prevent form submission
			}
		}
	</script>
@endsection
