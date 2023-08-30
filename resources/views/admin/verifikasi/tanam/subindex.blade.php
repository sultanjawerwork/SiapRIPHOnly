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
				<div id="panel-2" class="panel">
					<div class="panel-hdr">
						<h2>
							Ringkasan Data Realisasi
						</h2>
						<div class="panel-toolbar">

						</div>
					</div>
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
				<div id="panel-3" class="panel">
					<div class="panel-hdr">
						<h2>Kelengkapan Berkas</h2>
						<div class="panel-toolbar">
							<a href="" class="btn btn-xs btn-primary"><i class="fal fa-search mr-1"></i>Periksa Dokumen</a>
						</div>
					</div>
					<form action="{{route('verification.tanam.checkBerkas', $verifikasi->id)}}" method="post">
					@csrf
						<div class="panel-container show">
							<div class="alert alert-info border-0 mb-0">
								<div class="d-flex align-item-center">
									<i class="fal fa-info-circle mr-1"></i>
									<div class="flex-1">
										<span>Berikut ini adalah berkas-berkas kelengkapan yang diunggah oleh Pelaku Usaha.</span>
									</div>
								</div>
							</div>
							<div class="panel-content">
								<table class="table table-striped table-bordered table-sm w-100" id="attchCheck">
									<thead class="thead-themed">
										<tr>
											<th class="text-uppercase text-muted">Form</th>
											<th class="text-uppercase text-muted">Nama Berkas</th>
											<th class="text-uppercase text-muted">Tindakan</th>
											<th class="text-uppercase text-muted">Hasil Periksa</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Surat Pengajuan Verifikasi Tanam</td>
											<td>{{$verifikasi->spvt}}</td>
											<td>
												@if ($verifikasi->spvt)
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->spvt) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Dokumen
													</a>
												@endif
											</td>
											<td>
												<select class="form-control form-control-sm" name="spvtcheck" id="spvtcheck">
													<option value="">- Pilih status -</option>
													<option value="sesuai" {{ $verifikasi->spvtcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
													<option value="perbaiki" {{ $verifikasi->spvtcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
												</select>
											</td>
										</tr>
										<tr>
											<td>Surat Pertanggungjawaban Mutlak</td>
											<td>{{$verifikasi->sptjm}}</td>
											<td>
												@if ($verifikasi->sptjm)
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->sptjm) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Dokumen
													</a>
												@endif
											</td>
											<td>
												<select class="form-control form-control-sm" name="sptjmcheck" id="sptjmcheck">
													<option value="">- Pilih status -</option>
													<option value="sesuai" {{ $verifikasi->sptjmcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
													<option value="perbaiki" {{ $verifikasi->sptjmcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
												</select>
											</td>
										</tr>
										<tr>
											<td>Form Realisasi Tanam</td>
											<td>{{$verifikasi->rta}}</td>
											<td>
												@if ($verifikasi->rta)
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->rta) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Dokumen
													</a>
												@endif
											</td>
											<td>
												<select class="form-control form-control-sm" name="rtacheck" id="rtacheck">
													<option value="">- Pilih status -</option>
													<option value="sesuai" {{ $verifikasi->rtacheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
													<option value="perbaiki" {{ $verifikasi->rtacheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
												</select>
											</td>
										</tr>
										<tr>
											<td>Form SPH-SBS</td>
											<td>{{$verifikasi->sphtanam}}</td>
											<td>
												@if ($verifikasi->sphtanam)
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->sphtanam) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Dokumen
													</a>
												@endif
											</td>
											<td>
												<select class="form-control form-control-sm" name="sphtanamcheck" id="sphtanamcheck">
													<option value="">- Pilih status -</option>
													<option value="sesuai" {{ $verifikasi->sphtanamcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
													<option value="perbaiki" {{ $verifikasi->sphtanamcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
												</select>
											</td>
										</tr>
										<tr>
											<td>Pengantar Dinas telah selesai Tanam</td>
											<td>{{$verifikasi->spdst}}</td>
											<td>
												@if ($verifikasi->spdst)
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->spdst) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Dokumen
													</a>
												@endif
											</td>
											<td>
												<select class="form-control form-control-sm" name="spdstcheck" id="spdstcheck">
													<option value="">- Pilih status -</option>
													<option value="sesuai" {{ $verifikasi->spdstcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
													<option value="perbaiki" {{ $verifikasi->spdstcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
													<option value="" {{ $verifikasi->logbookcheck == '' ? 'selected' : '' }}>Tidak ada</option>
												</select>
											</td>
										</tr>
										<tr>
											<td>Logbook (s.d tanam)</td>
											<td>{{$verifikasi->logbooktanam}}</td>
											<td>
												@if ($verifikasi->logbooktanam)
													<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->logbooktanam) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Dokumen
													</a>
												@endif
											</td>
											<td>
												<select class="form-control form-control-sm" name="logbookcheck" id="logbookcheck">
													<option value="">- Pilih status -</option>
													<option value="sesuai" {{ $verifikasi->logbookcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
													<option value="perbaiki" {{ $verifikasi->logbookcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
													<option value="" {{ $verifikasi->logbookcheck == '' ? 'selected' : '' }}>Tidak ada</option>
												</select>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="card-footer d-flex justify-content-between align-items-center">
							<div class="help-block col-md-7">
								<ul class="list-group">Keterangan
									<li class="d-flex justify-content-start">
										<span class="col-3 text-danger fw-500">Belum Diperiksa</span>
										<span>Anda belum melakukan pemeriksaan terhadap berkas terkait.</span>
									</li>
									<li class="d-flex justify-content-start">
										<span class="col-3 text-success fw-500">Sesuai</span>
										<span>Jika salinan yang diunggah ADA dan SESUAI.</span>
									</li>
									<li class="d-flex justify-content-start">
										<span class="col-3 text-warning fw-500">Tidak Sesuai:</span>
										<span>Jika salinan yang diunggah TIDAK ADA atau ADA namun TIDAK SESUAI.</span>
									</li>
								</ul>
							</div>
							<div class="col-md text-right">
								<button type="submit" class="btn btn-primary btn-sm">
									simpan
								</button>
							</div>
						</div>
					</form>
				</div>
				<div id="panel-4" class="panel">
					<div class="panel-hdr">
						<h2>Perjanjian Kemitraan</h2>
					</div>
					<div class="panel-container show">
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
											<td>
												{{$pks->tgl_perjanjian_start}} s.d
												{{$pks->tgl_perjanjian_end}}
											</td>
											<td>
												@if ($pks->pkscheck->isNotEmpty())
													{{ $pks->pkscheck->first()->status }}
												@endif
											</td>
											<td>
												<a href="{{route('verification.tanam.check.pks', $pks->poktan_id)}}">
													lihat data
												</a>
											</td>
										</tr>
									@endforeach
									{{-- @foreach ($pkschecks as $pkscheck)
										<tr class="align-items-center">
											<td>{{$pkscheck->pks->no_perjanjian}}</td>
											<td>{{$pkscheck->pks->masterpoktan->nama_kelompok}}</td>
											<td>
												{{$pkscheck->pks->tgl_perjanjian_start}} s.d <br>
												{{$pkscheck->pks->tgl_perjanjian_end}}
											</td>
											<td>{{$pkscheck->verif_at}}</td>
											<td class="text-center">
												@if ($pkscheck->status === '2')
													<span class="badge btn-xs btn-icon btn-success"
														data-toggle="tooltip" title
														data-original-title="Pemeriksaan Selesai. Catatan: {{$pkscheck->note}}">
														<i class="fa fa-check-circle"></i>
													</span>
												@elseif ($pkscheck->status === '3')
													<span class="badge btn-xs btn-icon btn-danger" data-toggle="tooltip" title
													data-original-title="Pemeriksaan Selesai, Pelaku usaha harus memperbaiki kekurangan. Catatan: {{$pkscheck->note}}">
														<i class="fa fa-exclamation-circle"></i>
													</span>
												@else
													<span class="badge btn-xs btn-icon btn-warning" data-toggle="tooltip" title
													data-original-title="Pemeriksaan belum dilakukan">
														<i class="fa fa-hourglass"></i>
													</span>
												@endif
											</td>
											<td>
												@if($pkscheck->id)
													<a href="{{route('verification.data.pkscheck.edit', $pkscheck->id)}}" data-toggle="tooltip"
														data-original-title="Ubah Pemeriksaan"
														class="btn btn-xs btn-icon btn-primary mr-1">
														<i class="fal fa-edit"></i>
													</a>
												@else
												@endif
											</td>
										</tr>
									@endforeach --}}
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="panel-5" class="panel">
					<div class="panel-hdr">
						<h2>Data Lokasi Tanam</h2>
					</div>
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
			<div class="col-12">
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
											<span class="help-block">Nota Dinas Hasil Pemeriksaan Realisasi Tanam. <span class="text-danger">(wajib)</span></span>
										</div>
										<div class="form-group">
											<label class="">Berita Acara<sup class="text-danger"></sup></label>
											<div class="custom-file input-group">
												<input type="file" class="custom-file-input" name="batanam" id="batanam" value="{{ old('batanam', $verifikasi ? $verifikasi->batanam : '') }}" required>
												<label class="custom-file-label" for="batanam">{{ old('batanam', $verifikasi ? $verifikasi->batanam : 'pilih berkas') }}</label>
											</div>
											<span class="help-block">Berita Acara Pemeriksaan Realisasi Tanam. <span class="text-danger"></span></span>
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
								{{-- <div class="form-group">
									<label class="form-label">
										Berkas Berita Acara.
										@if (!empty($verifikasi->onlineattch))
										<a class="ml-1" href="" target="blank">(Lihat Berkas Berita Acara)</a>
										@endif
									</label>
									<div class="custom-file input-group">
										<input type="file" class="custom-file-input" id="customControlValidation7"
											name="onlineattch" id="onlineattch" value="">
										<label class="custom-file-label" for="customControlValidation7">
											@if (!empty($verifikasi->onlineattch))
											{{ old('onlineattch', $verifikasi ? $verifikasi->onlineattch : '') }}
											@else
											Pilih berkas...
											@endif
										</label>
									</div>
									<span class="help-block">Unggah Dokumen Pendukung. Ekstensi pdf ukuran maks 4mb.</span>
								</div> --}}
							</div>
						</form>
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

					{ className: 'text-right', targets: [3] },
					{ className: 'text-center', targets: [4] },
				]
			});

			function updateTableData() {
				$.ajax({
					// url: '{{ route("admin.lokasiTanamByCommitment", $verifikasi->commitment_id) }}',
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
