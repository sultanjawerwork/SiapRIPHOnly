@extends('layouts.admin')
@section ('styles')
<style>
	td {
		vertical-align: middle !important;
	}
		/* Remove outer border from the entire DataTable */
		.dataTables_wrapper {
		border: none;
	}

	/* Remove cell borders within the DataTable */
	table.dataTable td,
	table.dataTable th {
		border: none;
	}

	/* Remove the header border */
	table.dataTable thead th {
		border-bottom: none;
	}

	/* Remove the footer border (if applicable) */
	table.dataTable tfoot th {
		border-top: none;
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
						<a class="nav-link active" data-toggle="tab" href="#panel-2" role="tab" aria-selected="true">Ringkasan Pengajuan</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#panel-3" role="tab" aria-selected="true">Pemeriksaan Berkas</a>
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
							<div class="panel-container">
								<div class="panel-content">
									<table class="table table-hover table-sm w-100" style="border: none; border-top:none; border-bottom:none;" id="dataSummary">
										<thead>
											<th  style="width: 32%"> </th>
											<th style="width: 1%"> </th>
											<th> </th>
										</thead>
										<tbody>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Umum
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-muted">Perusahaan</td>
												<td>:</td>
												<td class="fw-500" id="company">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Nomor Ijin (RIPH)</td>
												<td>:</td>
												<td class="fw-500" id="noIjin">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Periode</td>
												<td>:</td>
												<td class="fw-500" id="periode">
												</td>
											</tr>
											<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Kewajiban dan Realisasi
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-muted">Luas Wajib Tanam</td>
												<td>:</td>
												<td class="fw-500" id="wajibTanam">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Realisasi Tanam</td>
												<td>:</td>
												<td class="fw-500" id="realisasiTanam">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Lokasi Tanam/Geolokasi</td>
												<td>:</td>
												<td class="fw-500" id="hasGeoloc">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Volume Wajib Produksi</td>
												<td>:</td>
												<td class="fw-500" id="wajibProduksi">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Realisasi Tanam</td>
												<td>:</td>
												<td class="fw-500" id="realisasiProduksi">
												</td>
											</tr>
											<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Kemitraan
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Kelompok Tani Mitra</td>
												<td>:</td>
												<td class="fw-500" id="countPoktan">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Anggota Kelompok Tani Mitra</td>
												<td>:</td>
												<td class="fw-500" id="countAnggota">
												</td>
											</tr>
											<tr>
												<td class="text-muted">Jumlah Perjanjian (PKS) diunggah</td>
												<td>:</td>
												<td class="fw-500" id="countPks">
												</td>
											</tr>
											<tr class="bg-primary-50" style="height: 20px; opacity: 0.15">
												<td></td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500 h6">
													Ringkasan Verifikasi Sebelumnya
												</td>
												<td></td>
												<td></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500">A. TAHAP TANAM</td>
												<td>:</td>
												<td class="fw-500" id=""></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Nota Dinas Verifikasi Tanam</td>
												<td>:</td>
												<td class="fw-500" id="ndhprt"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Berita Acara Pemeriksaan Tanam</td>
												<td>:</td>
												<td class="fw-500" id="batanam"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avtDate"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avtVerifAt"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Metode Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avtMetode"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Catatan Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avtNote"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Hasil Pemeriksaan (Status)</td>
												<td>:</td>
												<td class="fw-500" id="avtStatus"></td>
											</tr>
											<tr>
												<td class="text-uppercase fw-500">B. TAHAP PRODUKSI</td>
												<td>:</td>
												<td class="fw-500" id=""></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Nota Dinas Verifikasi Produksi</td>
												<td>:</td>
												<td class="fw-500" id="ndhprp"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Berita Acara Pemeriksaan Produksi</td>
												<td>:</td>
												<td class="fw-500" id="baproduksi"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avpDate"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Tanggal Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avpVerifAt"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Metode Pengajuan</td>
												<td>:</td>
												<td class="fw-500" id="avpMetode"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Catatan Pemeriksaan</td>
												<td>:</td>
												<td class="fw-500" id="avpNote"></td>
											</tr>
											<tr>
												<td class="text-muted pl-4">Hasil Pemeriksaan (Status)</td>
												<td>:</td>
												<td class="fw-500" id="avpStatus"></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					{{-- <div class="tab-pane fade active show" id="panel-2" role="tabpanel" aria-labelledby="panel-2">
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
													{{ number_format($commitment->luas_wajib_tanam, 2, '.', ',') }} ha
												</td>
												<td class="text-right">
													{{number_format($total_luastanam, 2,'.',',')}} ha
												</td>
												<td>
													@if($total_luastanam < $commitment->luas_wajib_tanam)
														<span class="text-warning"><i class="fas fa-exclamation-circle mr-1"></i>TIDAK TERPENUHI</span>
													@else
													<span class="text-success"><i class="fas fa-check mr-1"></i>TERPENUHI</span>
													@endif
												</td>
											</tr>
											<tr>
												<td>Produksi</td>
												<td class="text-right">
													{{ number_format($commitment->volume_produksi, 2, '.', ',') }} ton
												</td>
												<td class="text-right">
													{{number_format($total_volume, 2,'.',',')}} ton
												</td>
												<td>
													@if($total_volume < $commitment->volume_produksi)
														<span class="text-danger fw-500"><i class="fas fa-times-circle mr-1"></i>TIDAK TERPENUHI</span>
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
					</div> --}}
					<div class="tab-pane fade" id="panel-3" role="tabpanel" aria-labelledby="panel-3">
						<div id="panel-3" class="panel">
							<form method="post"
								action="{{route('verification.skl.checkBerkas', $verifikasi->id)}}">
								@csrf
								<div class="panel-container">
									<div class="panel-tag fade show">
										<div class="d-flex align-items-center">
											<i class="fal fa-info-circle mr-1"></i>
											<div class="flex-1">
												<small>Anda dapat melakukan pemeriksaan terhadap berkas-berkas di bawah ini.</small><br>
											</div>
										</div>
										@if (empty($verifTanam->status))
											<div class="d-flex align-items-center text-danger">
												<i class="fa fa-exclamation-circle fa-1x mr-1"></i>
												<div class="flex-1">
													<small>Pelaku usaha belum/tidak mengajukan verifikasi tanam</small><br>
												</div>
											</div>
										@endif
									</div>
									<div class="panel-content">
										<table class="table table-bordered table-hover table-striped table-sm w-100" style="vertical-align: middle" id="attCheck">
											<thead class="thead-themed">
												<th class="text-uppercase text-muted">Dokumen</th>
												<th class="text-uppercase text-muted">Sifat</th>
												<th class="text-uppercase text-muted">Status</th>
											</thead>
											<tbody>
												<tr>
													<td>
														@if ($userDocs->sptjm)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sptjm) }}">
																Surat Pertanggungjawaban Mutlak
															</a>
														@else
															<span>Surat Pertanggungjawaban Mutlak</span>
														@endif
													</td>
													<td class="text-center">
														WAJIB
														@if (empty($userDocs->sptjmcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td>
														@if ($userDocs->sptjm)
															<select required class="form-control form-control-sm" name="sptjmcheck" id="sptjmcheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->sptjmcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->sptjmcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->spvt)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $userDocs->spvt ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvt) : '' }}">
																Surat Pengajuan Verifikasi Tanam
															</a>
														@else
															<span>Surat Pengajuan Verifikasi Tanam</span>
														@endif
													</td>
													<td class="text-center">
														WAJIB
														@if (empty($userDocs->spvtcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td>
														@if ($userDocs->spvt)
															<select required class="form-control form-control-sm" name="spvtcheck" id="spvtcheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->spvtcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->spvtcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->spvp)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $userDocs->spvp ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spvp) : '' }}">
																Surat Pengajuan Verifikasi Produksi
															</a>
														@else
															<span>Surat Pengajuan Verifikasi Produksi</span>
														@endif
													</td>
													<td class="text-center">
														WAJIB
														@if (empty($userDocs->spvpcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td>
														@if ($userDocs->spvp)
															<select required class="form-control form-control-sm" name="spvpcheck" id="spvpcheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->spvpcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->spvpcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->spskl)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ $userDocs->spvp ? asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spskl) : '' }}">
																Surat Pengajuan Keterangan Lunas
															</a>
														@else
															<span>Surat Pengajuan Keterangan Lunas</span>
														@endif
													</td>
													<td class="text-center">
														WAJIB
														@if (empty($userDocs->spsklcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td>
														@if ($userDocs->spvp)
															<select required class="form-control form-control-sm" name="spsklcheck" id="spsklcheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->spsklcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->spsklcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->rta)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rta) }}">
																Form Realisasi Tanam
															</a>
														@else
															<span>Form Realisasi Tanam</span>
														@endif
													</td>
													<td class="text-center">
														WAJIB
														@if (empty($userDocs->rtacheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td>
														@if ($userDocs->rta)
															<select required class="form-control form-control-sm" name="rtacheck" id="rtacheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->rtacheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->rtacheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->rpo)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->rpo) }}">
																Form Realisasi Produksi
															</a>
														@else
															<span>Form Realisasi Produksi</span>
														@endif
													</td>
													<td class="text-center">
														WAJIB
														@if (empty($userDocs->rpocheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td>
														@if ($userDocs->rpo)
															<select required class="form-control form-control-sm" name="rpocheck" id="rpocheck">
																<option value="">- Pilih status -</option>
																<option value="sesuai" {{ $userDocs->rpocheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
																<option value="perbaiki" {{ $userDocs->rpocheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
															</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->sphtanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphtanam) }}">
																SPH-SBS (Tanam)
															</a>
														@else
															<span>SPH-SBS (Tanam)</span>
														@endif
													</td>
													<td class="text-center">
														Pendukung
														@if (empty($userDocs->sphtanamcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphtanam)
														<select required class="form-control form-control-sm" name="sphtanamcheck" id="sphtanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphtanamcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->sphtanamcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->sphproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphproduksi) }}">
																SPH-SBS (Produksi)
															</a>
														@else
															<span>SPH-SBS (Produksi)</span>
														@endif
													</td>
													<td class="text-center">
														Pendukung
														@if (empty($userDocs->sphproduksicheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphproduksi)
														<select required class="form-control form-control-sm" name="sphproduksicheck" id="sphproduksicheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphproduksicheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->sphproduksicheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->spdst)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spdst) }}">
																Surat Keterangan/Pengantar Dinas Telah Selesai Tanam
															</a>
														@else
															<span>Surat Keterangan/Pengantar Dinas Telah Selesai Tanam</span>
														@endif
													</td>
													<td class="text-center">
														Pendukung
														@if (empty($userDocs->spdstcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->spdst)
														<select required class="form-control form-control-sm" name="spdstcheck" id="spdstcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->spdstcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->spdstcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->spdsp)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->spdsp) }}">
																Surat Keterangan/Pengantar Dinas Telah Selesai Produksi
															</a>
														@else
															<span>Surat Keterangan/Pengantar Dinas Telah Selesai Produksi</span>
														@endif
													</td>
													<td class="text-center">
														Pendukung
														@if (empty($userDocs->spdspcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->spdsp)
														<select required class="form-control form-control-sm" name="spdspcheck" id="spdspcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->spdspcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->spdspcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->logbooktanam)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbooktanam) }}">
																Logbook Tanam
															</a>
														@else
															<span>Logbook Tanam</span>
														@endif
													</td>
													<td class="text-center">
														Pendukung
														@if (empty($userDocs->logbooktanamcheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->logbooktanam)
														<select required class="form-control form-control-sm" name="logbooktanamcheck" id="logbooktanamcheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->logbooktanamcheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->logbooktanamcheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->logbookproduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->logbookproduksi) }}">
																Logbook Produksi
															</a>
														@else
															<span>Logbook Produksi</span>
														@endif
													</td>
													<td class="text-center">
														Pendukung
														@if (empty($userDocs->logbookproduksicheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->logbookproduksi)
														<select required class="form-control form-control-sm" name="logbookproduksicheck" id="logbookproduksicheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->logbookproduksicheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->logbookproduksicheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
												<tr>
													<td>
														@if ($userDocs->formLa)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->formLa) }}">
																Laporan Akhir
															</a>
														@else
															<span>Laporan Akhir</span>
														@endif
													</td>
													<td class="text-center">
														Wajib
														@if (empty($userDocs->formLacheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->formLa)
														<select required class="form-control form-control-sm" name="formLacheck" id="formLacheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->formLacheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->formLacheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
														</select>
														@else
															<span class="text-danger">
																-- tidak dilampirkan --
															</span>
														@endif
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="card-footer d-flex alignt-items-center justify-content-between">
									<div>
										<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup> :Belum dilakukan pemeriksaan.
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
					<div class="tab-pane fade" id="panel-4" role="tabpanel" aria-labelledby="panel-4">
						<div id="panel-4" class="panel">
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Berikut ini adalah data Perjanjian Kerjasama. Anda dapat memeriksa ulang dan menetapkan hasil pemeriksaan.</small>
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
													<td>
														{{$pks->tgl_perjanjian_start}} s.d
														{{$pks->tgl_perjanjian_end}}
													</td>
													<td>
														{{$pks->status}}
													</td>
													<td class="text-center">
														<a href="{{route('verification.skl.check.pks', ['noIjin' => $noIjin, 'poktan_id' => $pks->poktan_id]) }}" class="btn btn-icon @if($pks->status) btn-success @else btn-warning @endif btn-xs" data-toggle="tooltip" data-original-title="Lihat/Periksa berkas dan data.">
															<i class="fal fa-search"></i>
														</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<form action="{{route('verification.skl.checkPksSelesai', $verifikasi->id)}}" method="post" id="pksChecked">
								@csrf
								<div class="card-footer d-flex alignt-items-center justify-content-between">
									<div>
									</div>
									<div>
										<button type="submit" class="btn btn-primary btn-sm" id="submitWarning">
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
										<thead>
											<th class="text-uppercase text-muted">Kelompoktani</th>
											<th class="text-uppercase text-muted">Nama Lokasi</th>
											<th class="text-uppercase text-muted">Pengelola</th>
											<th class="text-uppercase text-muted">Luas</th>
											<th class="text-uppercase text-muted">Volume</th>
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
							<div class="panel-container show">
								<div class="panel-tag fade show">
									<div class="d-flex align-items-center">
										<i class="fal fa-info-circle mr-1"></i>
										<div class="flex-1">
											<small>Setelah selesai memeriksa secara menyeluruh, Anda harus menetapkan hasil pemeriksaan yang dilakukan pada bagian ini.</small>
										</div>
									</div>
								</div>
								<form action="{{route('verification.skl.storeCheck', $verifikasi->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									<div class="panel-content">
										<input type="hidden" name="no_ijin" value="{{$verifikasi->no_ijin}}">
										<input type="hidden" name="no_pengajuan" value="{{$verifikasi->no_pengajuan}}">
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
										<div class="row d-flex justify-content-between">
											<div class="form-group col-md-12">
												<label for="note">Catatan Pemeriksaan <sup class="text-danger"> *</sup></label>
												<textarea name="note" id="note" rows="3" class="form-control form-control-sm" required>{{ old('note', $verifikasi ? $verifikasi->note : '') }}</textarea>
											</div>
											<div class="form-group col-md-6">
												<label class="">Nota Dinas<sup class="text-danger"> *</sup></label>
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="ndhpskl" id="ndhpskl" value="{{ old('ndhpskl', optional($verifikasi)->ndhpskl) }}">
													<label class="custom-file-label" for="ndhpskl">{{ old('ndhpskl', $verifikasi ? $verifikasi->ndhpskl : 'Pilih berkas') }}</label>
												</div>
												@if ($verifikasi->ndhpskl)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->ndhpskl) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Nota Dinas.
													</a>
												@else
													<span class="help-block">Nota Dinas Hasil Pemeriksaan. <span class="text-danger">(wajib)</span></span>
												@endif
											</div>
											<div class="form-group col-md-6">
												<label class="">Berita Acara<sup class="text-danger">*</sup></label>
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="baskls" id="baskls" value="{{ old('baskls', optional($verifikasi)->baskls) }}">
													<label class="custom-file-label" for="baskls">{{ old('baskls', $verifikasi ? $verifikasi->baskls : 'Pilih berkas') }}</label>
												</div>
												@if ($verifikasi->baskls)
													<a href="#" class="help-block" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$verifikasi->baskls) }}">
														<i class="fas fa-search mr-1"></i>
														Lihat Berita Acara.
													</a>
												@else
													<span class="help-block">Berita Acara Hasil Pemeriksaan <span class="text-danger">(wajib)</span></span>
												@endif
											</div>
											<div class="form-group col-md-3">
												<label for="">Metode Pemeriksaan<sup class="text-danger"> *</sup></label>
												<select name="metode" id="metode" class="form-control custom-select" required>
													<option value="" hidden>-- pilih metode --</option>
													<option value="Lapangan" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Lapangan' ? 'selected' : '' }}>Lapangan</option>
													<option value="Lapangan" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Wawancara' ? 'selected' : '' }}>Wawancara</option>
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
												<label class="">Dengan ini kami menyatakan verifikasi produksi telah SELESAI dilaksanakan.</label>
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

			$('#dataSummary').DataTable({
				responsive: true,
				"ordering": false,
				lengthChange: false,
				pageLength: -1,
				dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'><'col-sm-12 col-md-7'>>",
				buttons: [
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel"></i>',
						titleAttr: 'Ekspor data ke MS. Excel',
						className: 'btn-outline-success btn-xs btn-icon ml-3 mr-1'
					},
					{
						extend: 'print',
						text: '<i class="fa fa-print"></i>',
						titleAttr: 'Cetak halaman data.',
						className: 'btn-outline-primary btn-xs btn-icon mr-1'
					},
					{
						text: '<i class="fal fa-external-link"></i>',
						titleAttr: 'Lihat Detail',
						className: 'btn btn-icon btn-outline-info btn-xs',
						action: function () {
							// Replace 'to_somewhere' with your actual route and $key->id with the parameter value
							window.location.href = '{{ route('verification.produksi.check', $verifikasi->id) }}';
						}
					}
				],
			});

			$.ajax({
				url: '{{ route("verification.data.summary", $verifikasi->id) }}',
				type: 'GET',
				dataType: 'json',
				success: function (data) {
					$("#company").text(data.company);
					$("#npwp").text(data.npwp);
					$("#noIjin").text(data.noIjin);
					$("#countPoktan").text(data.countPoktan + ' Kelompok');
					$("#countPks").text(data.countPks + ' berkas');
					$("#countAnggota").text(data.countAnggota + ' anggota');
					$("#avtMetode").text(data.avtMetode);
					$("#avtNote").text(data.avtNote);

					var avtDate = data.avtDate ? new Date(data.avtDate) : null;
					var avtVerifAt = data.avtVerifAt ? new Date(data.avtVerifAt) : null;
					var avpDate = data.avpDate ? new Date(data.avpDate) : null;
					var avpVerifAt = data.avpVerifAt ? new Date(data.avpVerifAt) : null;

					// Menetapkan teks sesuai dengan kondisi
					var options = { day: 'numeric', month: 'long', year: 'numeric' };
					$("#avtDate").text(avtDate ? avtDate.toLocaleDateString(undefined, options) : '');
					$("#avtVerifAt").text(avtVerifAt ? avtVerifAt.toLocaleDateString(undefined, options) : '');
					$("#avpDate").text(avpDate ? avpDate.toLocaleDateString(undefined, options) : '');
					$("#avpVerifAt").text(avpVerifAt ? avpVerifAt.toLocaleDateString(undefined, options) : '');


					$("#avpMetode").text(data.avpMetode);
					$("#avpNote").text(data.avpNote);

					var formattedPeriode = 'Tahun ' + (data.periode);
					$("#periode").text(formattedPeriode);

					var formattedWajibTanam = parseFloat(data.wajibTanam).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' ha';
					var formattedRealisasiTanam = parseFloat(data.realisasiTanam).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' ha';
					var formattedWajibProduksi = parseFloat(data.wajibProduksi).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' ton';
					var formattedRealisasiProduksi = parseFloat(data.realisasiProduksi).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' ton';
					var formattedHasGeoLoc = parseFloat(data.hasGeoloc).toFixed(0).replace(/\d(?=(\d{3})+\.)/g, '$&,') + ' titik';

					$("#ndhprt").html(data.ndhprt ? '<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.ndhprtLink + '">' + data.ndhprt + '</a>' : '');

					$("#batanam").html(data.batanam ? '<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.batanamLink + '">' + data.batanam + '</a>' : '');

					$("#ndhprp").html('<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.ndhprpLink + '">' + data.ndhprp + '</a>');
					$("#baproduksi").html('<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="' + data.baproduksiLink + '">' + data.baproduksi + '</a>');

					$("#wajibTanam").text(formattedWajibTanam);
					$("#realisasiTanam").text(formattedRealisasiTanam);
					$("#wajibProduksi").text(formattedWajibProduksi);
					$("#realisasiProduksi").text(formattedRealisasiProduksi);
					$("#hasGeoloc").text(formattedHasGeoLoc);

					if (typeof data.avtStatus === 'undefined' || data.avtStatus === null){
						$("#avtStatus").text('Tidak ada status').addClass("text-warning text-uppercase fw-500").append('<i class="fas fa-exclamation-circle ml-1"></i>');
					}
					else if (data.avtStatus === '1' || data.avtStatus === '2' || data.avtStatus === '3') {
						$("#avtStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					} else if (data.avtStatus === '4') {
						$("#avtStatus").text('Memenuhi Syarat').addClass("text-success text-uppercase fw-500").append('<i class="fas fa-check ml-1"></i>');
					} else {
						// Handle case when avtStatus doesn't match any of the above conditions
						$("#avtStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					}

					if (data.avpStatus === '1' || data.avpStatus === '2' || data.avpStatus === '3') {
						$("#avpStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					} else if (data.avpStatus === '4') {
						$("#avpStatus").text('Memenuhi Syarat').addClass("text-success text-uppercase fw-500").append('<i class="fas fa-check ml-1"></i>');
					} else {
						// Handle case when avpStatus doesn't match any of the above conditions
						$("#avpStatus").text('Belum memenuhi syarat').addClass("text-danger text-uppercase fw-500").append('<i class="fas fa-times ml-1"></i>');
					}

					if (parseFloat(data.wajibTanam) > parseFloat(data.realisasiTanam)) {
						$("#realisasiTanam").removeClass("text-success").addClass("text-warning").append('<i class="fa fa-exclamation-circle ml-1"></i>');
					} else {
						$("#realisasiTanam").removeClass("text-warning").addClass("text-success").append('<i class="fas fa-check ml-1"></i>');
					}

					if (parseFloat(data.wajibProduksi) > parseFloat(data.realisasiProduksi)) {
						$("#realisasiProduksi").removeClass("text-success").addClass("text-danger").append('<i class="fas fa-exclamation-circle ml-1"></i>');
					} else {
						$("#realisasiProduksi").removeClass("text-danger").addClass("text-success").append('<i class="fas fa-check ml-1"></i>');
					}
				},
				error: function (xhr, status, error) {
					console.error(xhr.responseText);
				}
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
								var volProduksi = lokasi.volume;
								var formatter = new Intl.NumberFormat('en-GB', {
									style: 'decimal',
									minimumFractionDigits: 2,
									maximumFractionDigits: 2,
								});
								var totalLuas = formatter.format(luasTanam);
								var totalProduksi = formatter.format(volProduksi);

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
								tableData.row.add([poktan, namaLokasi, anggota, totalLuas, totalProduksi, actionBtn]).draw(false);
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
		document.getElementById('submitWarning').addEventListener('click', function () {
			if (confirm('Setiap PKS (yang memiliki berkas lampiran) yang belum Anda periksa akan dinyatakan sebagai "SESUAI". Apakah Anda yakin ingin menyimpan data ini?')) {
				// Lanjutkan dengan submit form jika konfirmasi OK
				document.getElementById('myForm').submit();
			} else {
				// Tidak melakukan apa-apa jika pengguna membatalkan
			}
		});
	</script>

	<script>
		function validateInput1() {
			// get the input value and the current username from the page
			var inputVal = document.getElementById('validasi1').value;
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
