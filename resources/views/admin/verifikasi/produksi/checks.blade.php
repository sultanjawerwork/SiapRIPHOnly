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
					</div>
					<div class="tab-pane fade" id="panel-3" role="tabpanel" aria-labelledby="panel-3">
						<div id="panel-3" class="panel">
							<form action="{{route('verification.produksi.checkBerkas', $verifikasi->id)}}" method="post">
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
															<select class="form-control form-control-sm" name="sptjmcheck" id="sptjmcheck">
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
															<span>Surat Pengajuan Verifikasi Produksi</span>
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
															<select class="form-control form-control-sm" name="spvtcheck" id="spvtcheck">
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
															<select class="form-control form-control-sm" name="spvpcheck" id="spvpcheck">
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
															<select class="form-control form-control-sm" name="rtacheck" id="rtacheck">
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
															<select class="form-control form-control-sm" name="rpocheck" id="rpocheck">
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
														<select class="form-control form-control-sm" name="sphtanamcheck" id="sphtanamcheck">
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
														@if ($userDocs->sphpoduksi)
															<a href="#" data-toggle="modal" data-target="#viewDocs" data-doc="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$userDocs->sphpoduksi) }}">
																SPH-SBS (Produksi)
															</a>
														@else
															<span>SPH-SBS (Produksi)</span>
														@endif
													</td>
													<td class="text-center">
														Pendukung
														@if (empty($userDocs->sphpoduksicheck))
															<sup class="text-danger"><i class="fa fa-exclamation-circle"></i></sup>
														@endif
													</td>
													<td class="text-center">
														@if ($userDocs->sphpoduksi)
														<select class="form-control form-control-sm" name="sphpoduksicheck" id="sphpoduksicheck">
															<option value="">- Pilih status -</option>
															<option value="sesuai" {{ $userDocs->sphpoduksicheck == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
															<option value="perbaiki" {{ $userDocs->sphpoduksicheck == 'perbaiki' ? 'selected' : '' }}>Perbaiki</option>
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
														<select class="form-control form-control-sm" name="spdstcheck" id="spdstcheck">
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
														<select class="form-control form-control-sm" name="spdspcheck" id="spdspcheck">
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
														<select class="form-control form-control-sm" name="logbooktanamcheck" id="logbooktanamcheck">
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
														<select class="form-control form-control-sm" name="logbookproduksicheck" id="logbookproduksicheck">
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
														<select class="form-control form-control-sm" name="formLacheck" id="formLacheck">
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
														@if ($pks->pksCheck)
															{{ $pks->pksCheck->status }}
														@endif
													</td>
													<td class="text-center">
														@if ($pks->pksCheck)
															<a href="javascript:void(0)" class="btn btn-icon btn-primary btn-xs" title="Edit hasil" data-toggle="modal" data-target="#modelId{{$pks->pksCheck->id}}">
																<i class="fal fa-edit"></i>
															</a>
															{{-- Modal pks check edit --}}
															<div class="modal fade" id="modelId{{$pks->pksCheck->id}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
																<div class="modal-dialog modal-dialog-right" role="document">
																	<div class="modal-content">
																		<div class="modal-header">
																			<div class="modal-title">
																				<h5>Data Perjanjian Kerjasama</h5>
																				<span>
																					Kelompok Tani:
																					<span class="fw-700">
																						{{$pks->masterpoktan->nama_kelompok}}
																					</span>
																				</span>
																			</div>
																			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																				<span aria-hidden="true">&times;</span>
																			</button>
																		</div>
																		<form action="{{route('verification.tanam.check.pks.update', $pks->pksCheck->id)}}" method="POST" enctype="multipart/form-data">
																			@csrf
																			@method('put')
																			<div class="modal-body">
																				<div id="panel-2" class="panel card">
																					<div class="panel-hdr">
																						<h2>
																							Lampiran<span class="fw-300"><i>Berkas PKS</i></span>
																						</h2>
																					</div>
																					@if ($pks->berkas_pks)
																						<div class="panel-container show card-body embed-responsive embed-responsive-16by9">
																							<iframe class="embed-responsive-item"
																								src="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$pks->berkas_pks) }}" width="100%" frameborder="0">
																							</iframe>
																						</div>
																					@else
																						<div class="panel-container show">
																							<div class="panel-content text-center">
																								<h3 class="text-danger">Tidak ada berkas dilampirkan</h2>
																							</div>
																						</div>
																					@endif
																				</div>
																				<div class="panel" id="panel-3">
																					<div class="panel-hdr">
																						<h2>
																							Data Perjanjian<span class="fw-300"><i>Kerjasama</i></span>
																						</h2>
																					</div>
																					<div class="panel-container show">
																						<div class="panel-content">
																							<ul class="list-group">
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Nomor Perjanjian</span>
																									<span class="fw-500">
																										{{$pks->no_perjanjian}}
																									</span>
																								</li>
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Kelompoktani</span>
																									<span class="fw-500">
																										{{$pks->masterpoktan->nama_kelompok}}
																									</span>
																								</li>
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Berlaku sejak</span>
																									<span class="fw-500">
																										{{$pks->tgl_perjanjian_start}}
																									</span>
																								</li>
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Berakhir pada</span>
																									<span class="fw-500">
																										{{$pks->tgl_perjanjian_end}}
																									</span>
																								</li>
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Luas Rencana</span>
																									<span class="fw-500">
																										{{$pks->luas_rencana}} ha
																									</span>
																								</li>
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Varietas</span>
																									<span class="fw-500">
																										@php
																											try {
																												$varietas = \App\Models\Varietas::findOrFail($pks->varietas_tanam);
																												$nama_varietas = $varietas->nama_varietas;
																											} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
																												// Handle the case where no record is found
																												$nama_varietas = 'tidak ada data';
																											}
																										@endphp
																										{{$nama_varietas}}
																									</span>
																								</li>
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Periode Tanam</span>
																									<span class="fw-500">
																										{{$pks->periode_tanam}}
																									</span>
																								</li>
																								<li class="list-group-item d-flex justify-content-between align-items-center">
																									<span class="text-muted">Lokasi Perjanjian</span>
																									<span class="fw-500 text-right">
																										<div class="row flex-column text-uppercase">
																											@php
																												$desa = \App\Models\MasterDesa::where('kelurahan_id', $pks->masterpoktan->id_kelurahan)->value('nama_desa');

																												$kecamatan = \App\Models\MasterKecamatan::where('kecamatan_id', $pks->masterpoktan->id_kecamatan)->value('nama_kecamatan');

																												$kabupaten = \App\Models\MasterKabupaten::where('kabupaten_id', $pks->masterpoktan->id_kabupaten)->value('nama_kab');

																												$provinsiId = substr($pks->masterpoktan->id_kabupaten, 0, 2);
																												$provinsi = \App\Models\MasterProvinsi::where('provinsi_id', $provinsiId)->value('nama');
																											@endphp
																											<span>Desa/Kel. {{$desa}} - Kec. {{$kecamatan}}</span>
																											<span>{{$kabupaten}} - {{$provinsi}}</span>
																										</div>
																									</span>
																								</li>
																							</ul>
																							<p></p>
																						</div>
																					</div>
																				</div>
																				<div class="panel" id="panel-4">
																					<div class="panel-hdr">
																						<h2>
																							Hasil<span class="fw-300"><i>Pemeriksaan</i></span>
																						</h2>
																					</div>
																					<div class="panel-container show">
																						<div class="panel-content">
																							{{-- {{$pkscheck->id}} --}}
																							<input type="text" name="pks_id" value="{{$pks->id}}" hidden>
																							<input type="text" name="poktan_id" value="{{$pks->poktan_id}}" hidden>
																							<input type="text" name="pengajuan_id" value="{{$verifikasi->id}}" hidden>
																							<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
																							<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
																							<div class="form-group">
																								<label for="">Catatan Pemeriksaan</label>
																								<textarea class="form-control form-control-sm" name="note" id="note" rows="3" required>{{$pks->pksCheck->note}}</textarea>
																								<small id="helpId" class="text-muted">Berikan catatan atau keterangan hasil pemeriksaan.</small>
																							</div>
																							<div class="form-group">
																								<label for="">Status Pemeriksaan</label>
																								<select type="text" id="status" name="status" class="form-control form-control-sm" required>
																									{{-- <option hidden value="">- pilih status periksa</option> --}}
																									<option value="Sesuai" {{ old('status', $pks->pksCheck->status) == 'Sesuai' ? 'selected' : '' }}>Sesuai</option>
																									<option value="Tidak Sesuai" {{ old('status', $pks->pksCheck->status) == 'Tidak Sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
																								</select>
																								<small id="helpId" class="text-muted">Berikan status hasil pemeriksaan.</small>
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
														@else
															<a href="javascript:void(0)" class="btn btn-icon btn-warning btn-xs" title="Periksa data" data-toggle="modal" data-target="#modelId{{$pks->poktan_id}}">
																<i class="fal fa-search"></i>
															</a>
														@endif
													</td>
													{{-- Modal pks check --}}
													<div class="modal fade" id="modelId{{$pks->poktan_id}}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
														<div class="modal-dialog modal-dialog-right" role="document">
															<div class="modal-content">
																<div class="modal-header">
																	<div class="modal-title">
																		<h5>Data Perjanjian Kerjasama</h5>
																		<span>
																			Kelompok Tani:
																			<span class="fw-700">
																				{{$pks->masterpoktan->nama_kelompok}}
																			</span>
																		</span>
																	</div>
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																		<span aria-hidden="true">&times;</span>
																	</button>
																</div>
																<form action="{{route('verification.tanam.check.pks.store', $pks->poktan_id)}}" method="POST" enctype="multipart/form-data">
																	@csrf
																	<div class="modal-body">
																		<div id="panel-2" class="panel">
																			<div class="panel-hdr">
																				<h2>
																					Lampiran<span class="fw-300"><i>Berkas PKS</i></span>
																				</h2>
																			</div>
																			@if ($pks->berkas_pks)
																				<div class="panel-container show embed-responsive embed-responsive-16by9">
																					<iframe class="embed-responsive-item"
																						src="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$pks->berkas_pks) }}" width="100%" frameborder="0">
																					</iframe>
																				</div>
																			@else
																				<div class="panel-container show">
																					<div class="panel-content text-center">
																						<h3 class="text-danger">Tidak ada berkas dilampirkan</h2>
																					</div>
																				</div>
																			@endif
																		</div>
																		<div id="panel-3" class="panel" >
																			<div class="panel-hdr">
																				<h2>
																					Data Perjanjian<span class="fw-300"><i>Kerjasama</i></span>
																				</h2>
																			</div>
																			<div class="panel-container show">
																				<div class="panel-content">
																					<ul class="list-group">
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Nomor Perjanjian</span>
																							<span class="fw-500">
																								{{$pks->no_perjanjian}}
																							</span>
																						</li>
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Kelompoktani</span>
																							<span class="fw-500">
																								{{$pks->masterpoktan->nama_kelompok}}
																							</span>
																						</li>
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Berlaku sejak</span>
																							<span class="fw-500">
																								{{$pks->tgl_perjanjian_start}}
																							</span>
																						</li>
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Berakhir pada</span>
																							<span class="fw-500">
																								{{$pks->tgl_perjanjian_end}}
																							</span>
																						</li>
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Luas Rencana</span>
																							<span class="fw-500">
																								{{$pks->luas_rencana}} ha
																							</span>
																						</li>
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Varietas</span>
																							<span class="fw-500">
																								@php
																									try {
																										$varietas = \App\Models\Varietas::findOrFail($pks->varietas_tanam);
																										$nama_varietas = $varietas->nama_varietas;
																									} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
																										// Handle the case where no record is found
																										$nama_varietas = 'tidak ada data';
																									}
																								@endphp
																								{{$nama_varietas}}
																							</span>
																						</li>
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Periode Tanam</span>
																							<span class="fw-500">
																								{{$pks->periode_tanam}}
																							</span>
																						</li>
																						<li class="list-group-item d-flex justify-content-between align-items-center">
																							<span class="text-muted">Lokasi Perjanjian</span>
																							<span class="fw-500 text-right">
																								<div class="row flex-column text-uppercase">
																									@php
																										$desa = \App\Models\MasterDesa::where('kelurahan_id', $pks->masterpoktan->id_kelurahan)->value('nama_desa');

																										$kecamatan = \App\Models\MasterKecamatan::where('kecamatan_id', $pks->masterpoktan->id_kecamatan)->value('nama_kecamatan');

																										$kabupaten = \App\Models\MasterKabupaten::where('kabupaten_id', $pks->masterpoktan->id_kabupaten)->value('nama_kab');

																										$provinsiId = substr($pks->masterpoktan->id_kabupaten, 0, 2);
																										$provinsi = \App\Models\MasterProvinsi::where('provinsi_id', $provinsiId)->value('nama');
																									@endphp
																									<span>Desa/Kel. {{$desa}} - Kec. {{$kecamatan}}</span>
																									<span>{{$kabupaten}} - {{$provinsi}}</span>
																								</div>
																							</span>
																						</li>
																					</ul>
																				</div>
																			</div>
																		</div>
																		<div class="panel" id="panel-4">
																			<div class="panel-hdr">
																				<h2>
																					Hasil<span class="fw-300"><i>Pemeriksaan</i></span>
																				</h2>
																			</div>
																			<div class="panel-container show">
																				<div class="panel-content">
																					{{-- {{$pkscheck->id}} --}}
																					<input type="text" name="pks_id" value="{{$pks->id}}" hidden>
																					<input type="text" name="poktan_id" value="{{$pks->poktan_id}}" hidden>
																					<input type="text" name="pengajuan_id" value="{{$verifikasi->id}}" hidden>
																					<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
																					<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
																					<div class="form-group">
																						<label for="">Catatan Pemeriksaan</label>
																						<textarea class="form-control form-control-sm" name="note" id="note" rows="3" required></textarea>
																						<small id="helpId" class="text-muted">Berikan catatan atau keterangan hasil pemeriksaan.</small>
																					</div>
																					<div class="form-group">
																						<label for="">Status Pemeriksaan</label>
																						<select type="text" id="status" name="status" class="form-control form-control-sm" required>
																							<option hidden value="">- pilih status periksa</option>
																							<option value="Sesuai">Sesuai</option>
																							<option value="Tidak Sesuai">Tidak Sesuai</option>
																						</select>
																						<small id="helpId" class="text-muted">Berikan status hasil pemeriksaan.</small>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="modal-footer">
																		<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fal fa-times text-align-center mr-1"></i>Batal</button>
																		<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-upload text-align-center mr-1"></i>Simpan</button>
																	</div>
																</form>
															</div>
														</div>
													</div>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
							<form action="{{route('verification.produksi.checkPksSelesai', $verifikasi->id)}}" method="post">
								@csrf
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
								<form action="{{route('verification.produksi.storeCheck', $verifikasi->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									{{-- @method('PUT') --}}
									<div class="panel-content">
										<input type="text" name="no_ijin" value="{{$verifikasi->no_ijin}}" hidden>
										<input type="text" name="no_pengajuan" value="{{$verifikasi->no_pengajuan}}" hidden>
										<input type="text" name="npwp" value="{{$verifikasi->npwp}}" hidden>
										<div class="row d-flex justify-content-between">
											<div class="form-group col-md-12">
												<label for="note">Catatan Pemeriksaan <sup class="text-danger"> *</sup></label>
												<textarea name="note" id="note" rows="3" class="form-control form-control-sm" required>{{ old('note', $verifikasi ? $verifikasi->note : '') }}</textarea>
											</div>
											<div class="form-group col-md-6">
												<label class="">Nota Dinas<sup class="text-danger"> *</sup></label>
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="ndhprp" id="ndhprp" value="{{ old('ndhprp', $verifikasi ? $verifikasi->ndhprp : '') }}" required>
													<label class="custom-file-label" for="ndhprp">{{ old('ndhprp', $verifikasi ? $verifikasi->ndhprp : 'pilih berkas') }}</label>
												</div>
												@if ($verifikasi->ndhprp)
													<span class="help-block">Nota Dinas Hasil Pemeriksaan Realisasi Tanam. <span class="text-danger">(wajib)</span></span>
												@else
													<span class="help-block">Nota Dinas Hasil Pemeriksaan Realisasi Tanam. <span class="text-danger">(wajib)</span></span>
												@endif

											</div>
											<div class="form-group col-md-6">
												<label class="">Berita Acara<sup class="text-danger">*</sup></label>
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="baproduksi" id="baproduksi" value="{{ old('baproduksi', $verifikasi ? $verifikasi->baproduksi : '') }}" required>
													<label class="custom-file-label" for="baproduksi">{{ old('baproduksi', $verifikasi ? $verifikasi->baproduksi : 'pilih berkas') }}</label>
												</div>
												<span class="help-block">Berita Acara Pemeriksaan Realisasi Tanam. <span class="text-danger"></span></span>
											</div>
											<div class="form-group col-md-3">
												<label for="">Metode Pemeriksaan<sup class="text-danger"> *</sup></label>
												<select name="metode" id="metode" class="form-control custom-select" required>
													<option value="" hidden>-- pilih metode --</option>
													<option value="Lapangan" {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Lapangan' ? 'selected' : '' }}>Lapangan</option>
													<option value="Wawancara"> {{ old('metode', $verifikasi ? $verifikasi->metode : '') == 'Wawancara' ? 'selected' : '' }}Wawancara</option>
												</select>
												<small id="helpId" class="text-muted">Pilih metode pemeriksaan</small>
											</div>
											<div class="form-group col-md-3">
												<label for="">Kesimpulan Pemeriksaan<sup class="text-danger"> *</sup></label>
												<select name="status" id="status" class="form-control custom-select" required>
													<option value="" hidden>-- pilih status --</option>
													<option value="8" {{ old('status', $verifikasi ? $verifikasi->status : '') == '8' ? 'selected' : '' }}>Sesuai</option>
													<option value="9" {{ old('status', $verifikasi ? $verifikasi->status : '') == '9' ? 'selected' : '' }}>Tidak Sesuai/Perbaikan</option>
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

					{ className: 'text-right', targets: [3] },
					{ className: 'text-center', targets: [4] },
				]
			});

			function updateTableData() {
				$.ajax({
					url: '{{ route("admin.ajuproduksi.listlokasi", $verifikasi->commitment_id) }}',
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
