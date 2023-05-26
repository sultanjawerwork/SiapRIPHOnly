@extends('layouts.admin')
@section('content')
	{{-- @include('partials.breadcrumb') --}}
	@include('partials.subheader')
	@can('commitment_access')
		@include('partials.sysalert')
		<div class="row">
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
										<input type="text" class="form-control form-control-sm" id="no_pengajuan"
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
										<input type="text" class="form-control form-control-sm" id="no_ijin" value="{{$verifikasi->no_ijin}}" disabled>
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
							Data Ringkasan Realisasi
						</h2>
						<div class="panel-toolbar">
							
						</div>
					</div>
					<div class="panel-container show">
						<div class="alert alert-info border-0 mb-0">
							<div class="d-flex align-item-center">
								<i class="fal fa-info-circle mr-1"></i>
								<div class="flex-1">
									<span>Berikut ini adalah data ringkasan realisasi komitmen wajib tanam-produksi.</span>
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
										<td>Produksi</td>
										<td class="text-right">
											{{ number_format($commitment->volume_riph * 0.05, 2, '.', ',') }} ton
										</td>
										<td class="text-right">
											{{number_format($total_volume,2,'.',',')}} ton
										</td>
										<td>
											@if($commitment->volume_riph * 0.05 >= $total_volume)
												<i class="fas fa-exclamation-circle mr-1 text-warning"></i><span class="text-warning fw-500">TIDAK TERPENUHI</span>
											@else
											<i class="fas fa-check mr-1 text-success"></i><span class="text-success fw-500">TERPENUHI</span>
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
							<a href="{{route('verification.data.commitmentcheck', $commitmentcheck->id)}}" class="btn btn-xs btn-primary"><i class="fal fa-search mr-1"></i>Periksa Dokumen</a>
						</div>
					</div>
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
							<table class="table table-striped table-bordered w-100" id="attchCheck">
								<thead class="card-header">
									<tr>
										<th class="text-uppercase text-muted">Form</th>
										<th class="text-uppercase text-muted">Nama Berkas</th>
										<th class="text-uppercase text-muted">Tindakan</th>
										<th class="text-uppercase text-muted">Hasil Periksa</th>
									</tr>
								</thead>
								<tbody>
									<tr class="align-items-center">
										<td>Penerbitan RIPH</td>
										<td>
											@if ($commitment->formRiph)
												<span class="text-primary">{{ $commitment->formRiph }}</span>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@if($commitment->formRiph)
												<a href="#" data-toggle="modal" data-target="#viewDocs"
													data-doc="{{ asset('storage/docs/commitmentsv2/' . $commitment->periodetahun . '/formRiph/' . $commitment->formRiph) }}">
													<i class="fas fa-search mr-1"></i>
													Lihat Dokumen
												</a>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@php
												$myform = $commitmentcheck->formRiph;
											@endphp
											@if ($myform === 'Sesuai')
												<span class="text-success">
													<i class="fas fa-check-circle mr-1"></i>Sesuai
												</span>
											@elseif ($myform === 'Tidak Sesuai')
												<span class="text-warning">
													<i class="fas fa-times-circle mr-1" data-toggle="tooltip" data-original-title="{{$verifcommit->note}}"></i>Tidak Sesuai
												</span>
											@else
												<span class="text-danger">
													<i class="fas fa-exclamation-circle mr-1"></i>Belum Diperiksa
												</span>
											@endif
										</td>
									</tr>
									<tr>
										<td>Form SPTJM</td>
										<td>
											@if ($commitment->formSptjm)
												<span class="text-primary">{{ $commitment->formSptjm }}</span>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@if ($commitment->formSptjm)
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/docs/commitmentsv2/' . $commitment->periodetahun . '/formSptjm/' . $commitment->formSptjm) }}">
													<i class="fas fa-search mr-1"></i>
													Lihat Dokumen
												</a>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@php
												$myform = $commitmentcheck->formSptjm;
											@endphp
											@if ($myform === 'Sesuai')
												<span class="text-success">
													<i class="fas fa-check-circle mr-1"></i>Sesuai
												</span>
											@elseif ($myform === 'Tidak Sesuai')
												<span class="text-warning" data-toggle="tooltip" data-original-title="{{$verifcommit->note}}">
													<i class="fas fa-times-circle mr-1"></i>Tidak Sesuai
												</span>
											@else
												<span class="text-danger">
													<i class="fas fa-exclamation-circle mr-1"></i>Belum Diperiksa
												</span>
											@endif
										</td>
									</tr>
									<tr>
										<td>Logbook</td>
										<td>
											@if ($commitment->logbook)
												<span class="text-primary">{{ $commitment->logbook }}</span>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@if ($commitment->logbook)
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/docs/commitmentsv2/' . $commitment->periodetahun . '/logbook/' . $commitment->logbook) }}">
													<i class="fas fa-search mr-1"></i>
													Lihat Dokumen
												</a>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@php
												$myform = $commitmentcheck->logbook;
											@endphp
											@if ($myform === 'Sesuai')
												<span class="text-success">
													<i class="fas fa-check-circle mr-1"></i>Sesuai
												</span>
											@elseif ($myform === 'Tidak Sesuai')
												<span class="text-warning">
													<i class="fas fa-times-circle mr-1" data-toggle="tooltip" data-original-title="{{$verifcommit->note}}"></i>Tidak Sesuai
												</span>
											@else
												<span class="text-danger">
													<i class="fas fa-exclamation-circle mr-1"></i>Belum Diperiksa
												</span>
											@endif
										</td>
									</tr>
									<tr>
										<td>Form RT</td>
										<td>
											@if ($commitment->formRt)
												<span class="text-primary">{{ $commitment->formRt }}</span>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@if ($commitment->formRt)
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/docs/commitmentsv2/' . $commitment->periodetahun . '/formRt/' . $commitment->formRt) }}">
													<i class="fas fa-search mr-1"></i>
													Lihat Dokumen
												</a>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@php
												$myform = $commitmentcheck->formRt;
											@endphp
											@if ($myform === 'Sesuai')
												<span class="text-success">
													<i class="fas fa-check-circle mr-1"></i>Sesuai
												</span>
											@elseif ($myform === 'Tidak Sesuai')
												<span class="text-warning" data-toggle="tooltip" data-original-title="{{$verifcommit->note}}">
													<i class="fas fa-times-circle mr-1"></i>Tidak Sesuai
												</span>
											@else
												<span class="text-danger">
													<i class="fas fa-exclamation-circle mr-1"></i>Belum Diperiksa
												</span>
											@endif
										</td>
									</tr>
									<tr>
										<td>Form RTA</td>
										<td>
											@if ($commitment->formRta)
												<span class="text-primary">{{ $commitment->formRta }}</span>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@if ($commitment->formRta)
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/docs/commitmentsv2/' . $commitment->periodetahun . '/formRta/' . $commitment->formRta) }}">
													<i class="fas fa-search mr-1"></i>
													Lihat Dokumen
												</a>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@php
												$myform = $commitmentcheck->formRta;
											@endphp
											@if ($myform === 'Sesuai')
												<span class="text-success">
													<i class="fas fa-check-circle mr-1"></i>Sesuai
												</span>
											@elseif ($myform === 'Tidak Sesuai')
												<span class="text-warning" data-toggle="tooltip" data-original-title="{{$verifcommit->note}}">
													<i class="fas fa-times-circle mr-1"></i>Tidak Sesuai
												</span>
											@else
												<span class="text-danger">
													<i class="fas fa-exclamation-circle mr-1"></i>Belum Diperiksa
												</span>
											@endif
										</td>
									</tr>
									<tr>
										<td>Form RPO</td>
										<td>
											@if ($commitment->formRpo)
												<span class="text-primary">{{ $commitment->formRpo }}</span>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@if ($commitment->formRpo)
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/docs/commitmentsv2/' . $commitment->periodetahun . '/formRpo/' . $commitment->formRpo) }}">
													<i class="fas fa-search mr-1"></i>
													Lihat Dokumen
												</a>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@php
												$myform = $commitmentcheck->formRpo;
											@endphp
											@if ($myform === 'Sesuai')
												<span class="text-success">
													<i class="fas fa-check-circle mr-1"></i>Sesuai
												</span>
											@elseif ($myform === 'Tidak Sesuai')
												<span class="text-warning" data-toggle="tooltip" data-original-title="{{$verifcommit->note}}">
													<i class="fas fa-times-circle mr-1"></i>Tidak Sesuai
												</span>
											@else
												<span class="text-danger">
													<i class="fas fa-exclamation-circle mr-1"></i>Belum Diperiksa
												</span>
											@endif
										</td>
									</tr>
									<tr>
										<td>Form LA</td>
										<td>
											@if ($commitment->formLa)
												<span class="text-primary">{{ $commitment->formLa }}</span>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@if ($commitment->formLa)
												<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/docs/commitmentsv2/' . $commitment->periodetahun . '/formLa/' . $commitment->formLa) }}">
													<i class="fas fa-search mr-1"></i>
													Lihat Dokumen
												</a>
											@else
												<span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Tidak Ada</span>
											@endif
										</td>
										<td>
											@php
												$myform = $commitmentcheck->formLa;
											@endphp
											@if ($myform === 'Sesuai')
												<span class="text-success">
													<i class="fas fa-check-circle mr-1"></i>Sesuai
												</span>
											@elseif ($myform === 'Tidak Sesuai')
												<span class="text-warning" data-toggle="tooltip" data-original-title="{{$verifcommit->note}}">
													<i class="fas fa-times-circle mr-1"></i>Tidak Sesuai
												</span>
											@else
												<span class="text-danger">
													<i class="fas fa-exclamation-circle mr-1"></i>Belum Diperiksa
												</span>
											@endif
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
					</div>
				</div>
				<div id="panel-4" class="panel">
					<div class="panel-hdr">
						<h2>Perjanjian Kemitraan</h2>
						<div class="panel-toolbar">
							<button class="btn btn-xs btn-primary" data-toggle="modal" data-target="#selectPks">Pilih PKS/Poktan</button>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content">
							<table class="table table-striped table-bordered w-100" id="pksCheck">
								<thead class="card-header">
									<tr>
										<th class="text-uppercase text-muted">Nomor Perjanjian</th>
										<th class="text-uppercase text-muted">Kelompok Tani</th>
										<th class="text-uppercase text-muted">Masa Berlaku</th>
										<th class="text-uppercase text-muted">Tanggal Pemeriksaan</th>
										<th class="text-uppercase text-muted">Status</th>
										<th class="text-uppercase text-muted">Tindakan</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($pkschecks as $verifpksmitra)
										<tr class="align-items-center">
											<td>{{$verifpksmitra->poktanriph->no_perjanjian}}</td>
											<td>{{$verifpksmitra->poktanriph->poktans->nama_kelompok}}</td>
											<td>
												{{$verifpksmitra->poktanriph->tgl_perjanjian_start}} s.d <br>
												{{$verifpksmitra->poktanriph->tgl_perjanjian_end}}
											</td>
											<td>{{$verifpksmitra->verif_at}}</td>
											<td class="text-center">
												@if ($verifpksmitra->status === '1')
													<span class="badge btn-xs btn-icon btn-success"
														data-toggle="tooltip" title
														data-original-title="Pemeriksaan Selesai. Catatan: {{$verifpksmitra->note}}">
														<i class="fa fa-check-circle"></i>
													</span>
												@elseif ($verifpksmitra->status === '2')
													<span class="badge btn-xs btn-icon btn-danger" data-toggle="tooltip" title
													data-original-title="Pemeriksaan Selesai, Pelaku usaha harus memperbaiki kekurangan. Catatan: {{$verifpksmitra->note}}">
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
												@if($verifpksmitra->id)
													<a href="" data-toggle="tooltip"
														data-original-title="Ubah Pemeriksaan"
														class="btn btn-xs btn-icon btn-primary mr-1">
														<i class="fal fa-edit"></i>
													</a>
												@else
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="panel-5" class="panel">
					<div class="panel-hdr">
						<h2>Data Lokasi Tanam</h2>
						<div class="panel-toolbar">
							<a href="" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#selectLokasi">Pilih Lokasi Sampling</a>
						</div>
					</div>
					<div class="panel-container show">
						<div class="panel-content">
							<table class="table table-striped table-bordered w-100" id="lokasiCheck">
								<thead>
									<th class="text-uppercase text-muted">Kelompoktani</th>
									<th class="text-uppercase text-muted">Nama Lokasi</th>
									<th class="text-uppercase text-muted">Pengelola</th>
									<th class="text-uppercase text-muted">Luas</th>
									<th class="text-uppercase text-muted">Volume</th>
									<th class="text-uppercase text-muted">Status</th>
									<th class="text-uppercase text-muted">Tindakan</th>
								</thead>
								<tbody>
									@foreach ($lokasis as $lokasi)
									<tr>
										<td>{{$lokasi->masteranggota->masterpoktan->nama_kelompok}}</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12">
				<div class="panel" id="panel-6">
					
					<div class="panel-hdr">
						<h2>BA. Verifikasi Administratif</h2>
						<div class="panel-toolbar">
							<span class="help-block">Rekam Berita Acara ini <span class="text-danger fw-500">HANYA JIKA</span> pemeriksaan seluruh data secara administratif telah selesai.</span>
						</div>
					</div>
					<div class="panel-container show">
						<form action="" method="POST" enctype="multipart/form-data">
							@csrf
							@method('PUT')
							<div class="panel-content">
								<div class="form-group">
									<label for="onlinenote">Catatan Pemeriksaan</label>
									<textarea name="onlinenote" id="onlinenote" rows="5" class="form-control form-control-sm" required>{{ old('onlinenote', $verifikasi ? $verifikasi->onlinenote : '') }}</textarea>
								</div>
								<div class="form-group">
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
								</div>
								<div class="row">
									<div class="form-group col-md-6">
										<label for="onlinestatus">Status Pemeriksaan</label>
										<select class="custom-select" name="onlinestatus" id="onlinestatus">
											<option value="" hidden>-- pilih</option>
											<option value="1" {{ old('onlinestatus', $verifikasi ? $verifikasi->onlinestatus : '') == '1' ? 'selected' : '' }}>Selesai</option>
											<option value="2" {{ old('onlinestatus', $verifikasi ? $verifikasi->onlinestatus : '') == '2' ? 'selected' : '' }}>Perbaikan Data</option>
										</select>
									</div>
									<div class="form-group col-md-6">
										<label class="">Konfirmasi</label>
										<div class="input-group">
											<input type="text" class="form-control" placeholder="ketik username Anda di sini" id="validasi" name="validasi"required>
											<div class="input-group-append">
												<button class="btn btn-danger" type="submit" onclick="return validateInput()">
													<i class="fas fa-save text-align-center mr-1"></i>Simpan
												</button>
											</div>
										</div>
										<span class="help-block">Dengan ini kami menyatakan verifikasi pada bagian ini telah SELESAI.</span>
									</div>
								</div>
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

		{{-- modal view selectPks --}}
		<div class="modal fade " id="selectPks" tabindex="-1" role="dialog" aria-labelledby="document" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">
							Pilih <span class="fw-300"><i>PKS/Kelompoktani </i></span>
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="form-label" for="pksMitra">PKS/Poktan</label>
							<div class="input-group">
								<select class="form-control" id="pksMitra" name="pksMitra" required>
									<option value="" hidden></option>
									@foreach ($pkss as $pks)
										@if (!$pkschecks->contains('poktan_id', $pks->id))
											<option value="{{$pks->id}}" data-verifikasi="{{$verifikasi->id}}" data-commitment="{{ $pks->commitment }}">
												{{$pks->no_perjanjian}} - {{$pks->masterpoktan->nama_kelompok}} <em>{{$pks->lokasi_count}} anggota</em>
											</option>
										@endif
									@endforeach
								</select>
							</div>
							<div class="help-block">
								Pilih Perjanjian.
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<a class="btn btn-warning btn-sm">Batal</a>
						<a href="" id="verifikasi-link" class="btn btn-sm btn-primary">Pilih</a>
					</div>
				</div>
			</div>
		</div>

		{{-- modal view selectlokasi --}}
		<div class="modal fade " id="selectLokasi" tabindex="-1" role="dialog" aria-labelledby="document" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">
							Pilih <span class="fw-300"><i>Lokasi</i></span>
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="form-label" for="lokasiLahan">Lokasi Lahan/Anggota</label>
							<div class="input-group">
								<select class="select2-des form-control" id="lokasiLahan" name="lokasiLahan" required>
									<option value="" hidden></option>
								</select>
							</div>
							<div class="help-block">
								Pilih Lokasi yang akan diperiksa.
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<a class="btn btn-warning btn-sm">Batal</a>
						<a href="" id="lokasi-link" class="btn btn-primary btn-sm" type="submit">Periksa</a>
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
		});
	</script>

	<script>
		$(document).ready(function() {
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

			$('#pksMitra').change(function () {
				// Get the selected value of the select element
				var selectedValue = $(this).val();

				// Get the data-verifikasi and data-commitment attributes of the selected option
				var verifikasiId = $('option:selected', this).data('verifikasi');
				var commitmentId = $('option:selected', this).data('commitment');

				// Construct the new href value with the selected value and data attributes
				var newHref = "";
				newHref = newHref.replace(':verifikasi', verifikasiId);
				newHref = newHref.replace(':commitment', commitmentId);
				newHref = newHref.replace(':id', selectedValue);

				// Update the href attribute of the link with the new href value
				$('#verifikasi-link').attr('href', newHref);
			});

			$('#lokasiLahan').change(function () {
				// Get the selected value of the select element
				var selectedValue = $(this).val();

				// Get the data attributes of the selected option
				// var verifikasi = $('#lokasiLahan option:selected').data('verifikasi');
				// var commitment = $('#lokasiLahan option:selected').data('commitment');
				// var pksmitra = $('#lokasiLahan option:selected').data('pksmitra');

				// Construct the new href value with the selected value and data attributes
				var newHref = "";
				newHref = newHref.replace(':id', selectedValue);

				// Update the href attribute of the link with the new href value
				$('#lokasi-link').attr('href', newHref);
			});

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
