@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@can('commitment_show')
@include('partials.sysalert')
    {{-- {{ dd($data_poktan) }} --}}
	<ul class="nav nav-tabs" role="tablist">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#panel-5" role="tab" aria-selected="true">Realisasi Perjanjian Kerjasama/PKS</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#panel-6" role="tab" aria-selected="true">Unggah Berkas (wajib)</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade active show" id="panel-5" role="tabpanel" aria-labelledby="panel-5">
			<div class="panel" id="panel-5">
				<div class="panel-container show">
					<div class="panel-content">
						<table id="tblPks" class="table table-sm table-bordered table-hover table-striped w-100">
							<thead>
								<tr>
									<th>No. Perjanjian</th>
									<th>Poktan Mitra</th>
									<th>Anggota</th>
									<th>Luas/Target</th>
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
											{{$pks->luas_rencana}} ha / {{$pks->luas_rencana*6}} ton
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
													echo '
													<button type="button" class="btn btn-icon btn-xs btn-danger" data-toggle="modal" data-target="#modalId' . $pks->id . '"><i class="fal fa-cassette-tape" data-toggle="tooltip" data-original-title="Lengkapi data"></i></button>';
												} else {
													echo '
													<button type="button" class="btn btn-icon btn-xs btn-success" data-toggle="modal" data-target="#modalId' . $pks->id . '"><i class="fal fa-file-check" data-toggle="tooltip" data-original-title="Data PKS lengkap. Klik jika ingin mengubah data"></i></button>
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
										<div class="modal fade" id="modalId{{ $pks->id }}" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
											<div class="modal-dialog modal-dialog-right" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<span class="modal-title">
															<h4 class="fw-500">Data PKS</h4>
															<span>Data Perjanjian Kerjasama dengan Mitra Kelompok Tani</span>
														</span>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<form action=" {{route('admin.task.pks.update', $pks->id)}} " method="POST" enctype="multipart/form-data">
														@csrf
														@method('PUT')
														<div class="modal-body">
															<div class="row d-flex">
																<div class="col-md-12 mb-3">
																	<label class="form-label">Unggah Berkas PKS (Perjanjian Kerjasama)</label>
																	<div class="input-group">
																		<div class="input-group-prepend">
																			<a href="">
																			<span class="input-group-text" id="inputGroupPrepend3">PKS</span>
																			</a>
																		</div>
																		<div class="custom-file">
																			<input type="file" class="custom-file-input" id="berkas_pks" name="berkas_pks"
																			value="{{old('berkas_pks', $pks->berkas_pks)}}">
																			<label class="custom-file-label" for="berkas_pks">
																				{{ $pks->berkas_pks ? $pks->berkas_pks : 'Pilih file...' }}
																			</label>
																		</div>
																	</div>
																	<span class="help-block">
																		@php
																			$npwp = str_replace(['.', '-'], '', $npwp);
																		@endphp
																		@if($pks->berkas_pks)
																			<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$pks->berkas_pks) }}" target="_blank">
																				Lihat berkas yang telah diunggah
																			</a>
																		@else
																		Unggah hasil pindai berkas Perjanjian dalam bentuk pdf.
																		@endif
																	</span>
																</div>
																<div class="col-md-12 mb-3">
																	<div class="form-group">
																		<label class="form-label" for="no_perjanjian">Nomor Perjanjian</label>
																		<div class="input-group">
																			<div class="input-group-prepend">
																				<span class="input-group-text" id="no_perjanjian">123</span>
																			</div>
																			<input type="text" class="form-control " id="no_perjanjian" name="no_perjanjian"
																				value="{{old('no_perjanjian', $pks->no_perjanjian)}}"
																				required>
																		</div>
																		<div class="help-block">
																			Nomor Pejanjian Kerjasama dengan Poktan Mitra.
																		</div>
																	</div>
																</div>
																<div class="col-md-6 mb-3">
																	<div class="form-group">
																		<label class="form-label">Tanggal perjanjian</label>
																		<div class="input-daterange input-group" id="tgl_perjanjian_start" name="tgl_perjanjian_start">
																			<div class="input-group-prepend">
																				<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
																			</div>
																			<input type="date" name="tgl_perjanjian_start" id="tgl_perjanjian_start"
																				class="form-control " placeholder="tanggal mulai perjanjian"
																				value="{{old('tgl_perjanjian_start', $pks->tgl_perjanjian_start)}}" required
																				aria-describedby="helpId">
																		</div>
																		<div class="help-block">
																			Pilih Tanggal perjanjian ditandatangani.
																		</div>
																	</div>
																</div>
																<div class="col-md-6 mb-3">
																	<div class="form-group">
																		<label class="form-label">Tanggal berakhir perjanjian</label>
																		<div class="input-daterange input-group" id="tgl_perjanjian_end" name="tgl_perjanjian_end">
																			<div class="input-group-prepend">
																				<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
																			</div>
																			<input type="date" name="tgl_perjanjian_end" id="tgl_perjanjian_end"
																				class="form-control " placeholder="tanggal akhir perjanjian"
																				value="{{old('tgl_perjanjian_end', $pks->tgl_perjanjian_end)}}" required
																				aria-describedby="helpId">
																		</div>
																		<div class="help-block">
																			Pilih Tanggal berakhirnya perjanjian.
																		</div>
																	</div>
																</div>
																<div class="col-md-12 mb-3" hidden>
																	<div class="form-group">
																		<label class="form-label" for="simpleinputInvalid">Luas Rencana (ha)</label>
																		<div class="input-group">
																			<div class="input-group-prepend">
																				<span class="input-group-text" id="inputGroupPrepend3"><i class="fal fa-ruler"></i></span>
																			</div>
																			<input type="" class="form-control " name="luas_rencana" id="luas_rencana"
																				value="{{old('luas_rencana', $pks->sum_luaslahan)}}" step="0.01" readonly>
																		</div>
																		<div class="help-block">
																			Jumlah Luas total sesuai dokumen perjanjian.
																		</div>
																	</div>
																</div>
																<div class="col-md-12 mb-3">
																	<div class="form-group">
																		<label class="form-label" for="varietas_tanam">Varietas Tanam</label>
																		<div class="input-group">
																			<select class="form-control custom-select" name="varietas_tanam" id="select2-varietas" required>
																				<option value="" hidden></option>
																				@foreach($varietass as $varietas)
																					<option value="{{ $varietas->id }}"{{ old('varietas_tanam', $pks->varietas_tanam) == $varietas->id ? ' selected' : '' }}>
																						{{ $varietas->nama_varietas }}
																					</option>
																				@endforeach
																			</select>
																		</div>
																		<div class="help-block">
																			Varietas ditanam sesuai dokumen perjanjian.
																		</div>
																	</div>
																</div>
																<div class="col-md-12 mb-3">
																	<div class="form-group">
																		<label class="form-label" for="periode">Periode Tanam</label>
																		<div class="input-group">
																			<div class="input-group-prepend">
																				<span class="input-group-text" id=""><i class="fal fa-calendar-week"></i></span>
																			</div>
																			<input type="text" name="periode_tanam" id="periode_tanam"
																				class="form-control " placeholder="misal: Jan-Feb" aria-describedby="helpId"
																				value="{{old('periode_tanam', $pks->periode_tanam)}}" required>
																		</div>
																		<div class="help-block">
																			Periode tanam sesuai dokumen perjanjian.
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
															<button class="btn btn-primary btn-sm" type="submit"
																@if ($disabled) disabled @endif>
																<i class="fal fa-save mr-1"></i>Simpan
															</button>
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
			</div>
		</div>
		<div class="tab-pane fade" id="panel-6" role="tabpanel" aria-labelledby="panel-6">
			<div class="panel" id="panel-6">
				<form action="{{route('admin.task.commitment.realisasi.storeUserDocs', $commitment->id)}}" method="post" enctype="multipart/form-data" id="docsUpload">
					@csrf
					<div class="panel-container show">
						<div class="panel-tag fade show">
							<div class="d-flex align-items-center">
								<i class="fal fa-info-circle mr-1"></i>
								{{-- <div class="alert-icon">
									<span class="icon-stack icon-stack-sm"> --}}
										{{-- <i class="base-7 icon-stack-3x color-fusion-200"></i>
										<i class="base-7 icon-stack-2x color-fusion-500"></i> --}}
									{{-- </span>
								</div> --}}
								<div class="flex-1">
									<small>Berkas-berkas di bawah ini diperlukan untuk verifikasi. Anda dapat melengkapi berkas ini sebelum dilaksanakan verifikasi atau penerbitan Surat Keterangan Lunas (SKL).</small>
								</div>
							</div>
						</div>
						<div class="panel-content">
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="sptjm">Form SPTJM<span class="text-danger">**</span></label>
								<div class="col-sm-10">
									<div class="custom-file input-group">
										<input type="file" class="custom-file-input" name="sptjm" id="sptjm" value="{{ old('sptjm', optional($docs)->sptjm) }}">
										<label class="custom-file-label" for="sptjm">{{ $docs ? ($docs->sptjm ? $docs->sptjm : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->sptjm)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->sptjm) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<small><i class="fa fa-info-circle mr-1"></i>Surat Pertanggungjawaban Mutlak. Diperlukan sebagai syarat pengajuan verifikasi dan penerbitan SKL.</small>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="formLa">Form LA<span class="text-danger">*</span></label>
								<div class="col-sm-10">
									<div class="custom-file input-group">
										<input type="file" class="custom-file-input" name="formLa" id="formLa" value="{{ old('formLa', optional($docs)->formLa) }}">
										<label class="custom-file-label" for="formLa">{{ $docs ? ($docs->formLa ? $docs->formLa : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->formLa)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->formLa) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<span><i class="fa fa-info-circle mr-1"></i>Form laporan Akhir. Diperlukan sebagai Syarat Penerbitan SKL.</small>
										@endif
									</span>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label" for="spskl">Pengajuan SKL<span class="text-danger">*</span></label>
								<div class="col-sm-10">
									<div class="custom-file input-group">
										<input type="file" class="custom-file-input" name="spskl" id="spskl" value="{{ old('spskl', optional($docs)->spskl) }}">
										<label class="custom-file-label" for="spskl">{{ $docs ? ($docs->spskl ? $docs->spskl : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
									</div>
									<span class="help-block">
										@if($docs && $docs->spskl)
											<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->spskl) }}" target="_blank">
												Lihat Dokumen diunggah.
											</a>
										@else
										<span><i class="fa fa-info-circle mr-1"></i>Surat Pengajuan Penerbitan SKL. Diperlukan sebagai Syarat Penerbitan SKL.</small>
										@endif
									</span>
								</div>
							</div><hr>
							<div class="card-deck">
								<div class="card">
									<div class="panel-hdr thead-themed">
										<h2>Dokumen Tanam</h2>
									</div>
									<div class="card-body">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="spvt">Pengajuan Verifikasi Tanam<span class="text-danger">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="spvt" id="spvt" value="{{ old('spvt', optional($docs)->spvt) }}">
													<label class="custom-file-label" for="spvt">{{ $docs ? ($docs->spvt ? $docs->spvt : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->spvt)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->spvt) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Surat Pengajuan Verifikasi Tanam. Diperlukan untuk verifikasi realisasi tanam.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="rta">Form RTA<span class="text-danger">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="rta" id="rta" value="{{ old('rta', optional($docs)->rta) }}">
													<label class="custom-file-label" for="rta">{{ $docs ? ($docs->rta ? $docs->rta : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->rta)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->rta) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Form laporan realisasi tanam. Diperlukan saat verifikasi realisasi tanam.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="sphtanam">SPH-SBS Tanam<span class="text-info">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="sphtanam" id="sphtanam" value="{{ old('sphtanam', optional($docs)->sphtanam) }}">
													<label class="custom-file-label" for="sphtanam">{{ $docs ? ($docs->sphtanam ? $docs->sphtanam : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->sphtanam)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->sphtanam) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Keterangan Realisasi Tanam tercatat di SPH-SBS. Diperlukan saat verifikasi realisasi tanam.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="spdst">Surat Pengantar dari Dinas<span class="text-info">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="spdst" id="spdst" value="{{ old('spdst', optional($docs)->spdst) }}">
													<label class="custom-file-label" for="spdst">{{ $docs ? ($docs->spdst ? $docs->spdst : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->spdst)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->spdst) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Surat Pengantar/Keterangan Dinas Telah Selesai Tanam.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="logbooktanam">Logbook<span class="text-info">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="logbooktanam" id="logbooktanam" value="{{ old('logbooktanam', optional($docs)->logbooktanam) }}">
													<label class="custom-file-label" for="logbooktanam">{{ $docs ? ($docs->logbooktanam ? $docs->logbooktanam : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->logbooktanam)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->logbooktanam) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Logbook (s.d tanam).</small>
													@endif
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="card">
									<div class="panel-hdr thead-themed">
										<h2>Dokumen Produksi</h2>
									</div>
									<div class="card-body">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="spvp">Pengajuan Verifikasi Produksi<span class="text-danger">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="spvp" id="spvp" value="{{ old('spvp', optional($docs)->spvp) }}">
													<label class="custom-file-label" for="spvp">{{ $docs ? ($docs->spvp ? $docs->spvp : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->spvp)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->spvp) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Surat Pengajuan Verifikasi Produksi. Diperlukan untuk verifikasi realisasi produksi.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="rpo">Form RPO<span class="text-danger">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="rpo" id="rpo" value="{{ old('rpo', optional($docs)->rpo) }}">
													<label class="custom-file-label" for="rpo">{{ $docs ? ($docs->rpo ? $docs->rpo : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->rpo)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->rpo) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Form laporan realisasi produksi. Diperlukan untuk verifikasi realisasi produksi.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="sphproduksi">SPH-SBS Produksi<span class="text-danger">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="sphproduksi" id="sphproduksi" value="{{ old('sphproduksi', optional($docs)->sphproduksi) }}">
													<label class="custom-file-label" for="sphproduksi">{{ $docs ? ($docs->sphproduksi ? $docs->sphproduksi : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->sphproduksi)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->sphproduksi) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Keterangan realisasi produksi tercatat di SPH-SBS. Diperlukan sebagai Syarat Penerbitan SKL.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="spdsp">Surat Pengantar dari Dinas<span class="text-info">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="spdsp" id="spdsp" value="{{ old('spdsp', optional($docs)->spdsp) }}">
													<label class="custom-file-label" for="spdsp">{{ $docs ? ($docs->spdsp ? $docs->spdsp : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->spdsp)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->spdsp) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Surat Pengantar/Keteragan dari Dinas telah selesai produksi.</small>
													@endif
												</span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-4 col-form-label" for="logbookproduksi">LogBook<span class="text-info">*</span></label>
											<div class="col-sm-8">
												<div class="custom-file input-group">
													<input type="file" class="custom-file-input" name="logbookproduksi" id="logbookproduksi" value="{{ old('logbookproduksi', optional($docs)->logbookproduksi) }}">
													<label class="custom-file-label" for="logbookproduksi">{{ $docs ? ($docs->logbookproduksi ? $docs->logbookproduksi : 'Pilih berkas...') : 'Pilih berkas...' }}</label>
												</div>
												<span class="help-block">
													@if($docs && $docs->logbookproduksi)
														<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$docs->logbookproduksi) }}" target="_blank">
															Lihat Dokumen diunggah.
														</a>
													@else
													<span><i class="fa fa-info-circle mr-1"></i>Logbook (s.d produksi).</small>
													@endif
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer row d-flex justify-content-between align-items-center small">
						<div class="col-md-6">
							Keterangan: <br>
							<span class="text-danger">**</span> : Berkas Utama (Wajib diunggah sebelum verifikasi.); <br>
							<span class="text-danger">*</span> : Berkas Utama (Wajib. Dapat diunggah setelah verifikasi.); <br>
							<span class="text-info">*</span> : Berkas Pendukung (Opsional).
						</div>
						<div class="col-md-6 text-right">
							<button class="btn btn-primary btn-sm waves-effect waves-themed" type="submit">
								<i class="fal fa-cloud-upload mr-1"></i>Unggah
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	{{-- <div class="col-md-3">
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
										<span class="icon-stack-1x text-white">1</span>
									</span>
								</div>
								<div class="col-12 text-center">
									<span class="fw-700">Sudah Diajukan.</span>
								</div>
							</div><hr>
							<p class="small help-block">Anda telah mengajukan verifikasi tanam dan segera kami proses.<br> Selama Masa Tunggu, Anda tidak dapat: menambah, mengubah, dan atau menghapus data terkait.
							</p>
						@break
						@case(2)
							<div class="row">
								<div class="col-12 text-center mb-1">
									<span class="icon-stack icon-stack-sm">
										<i class="base-7 icon-stack-3x color-info-500"></i>
										<span class="icon-stack-1x text-white">2</>
									</span>
								</div>
								<div class="col-12 text-center">
									<span class="fw-700">Tahap Pemeriksaan Berkas</span>
								</div>
							</div><hr>
							<p class="small help-block">Pemeriksaan berkas dan kelengkapan administratif telah selesai.</p>
						@break
						@case(3)
							<div class="row">
								<div class="col-12 text-center mb-1">
									<span class="icon-stack icon-stack-sm">
										<i class="base-7 icon-stack-3x color-info-500"></i>
										<span class="icon-stack-1x text-white">3</>
									</span>
								</div>
								<div class="col-12 text-center">
									<span class="fw-700">Tahap Pemeriksaan PKS</span>
								</div>
							</div><hr>
							<p class="small help-block">Pemeriksaan berkas-berkas Perjanjian Kerjasama dengan Mitra Kelompok Tani.</p>
						@break
						@case(4)
							<div class="row">
								<div class="col-12 text-center mb-1">
									<span class="icon-stack icon-stack-sm">
										<i class="base-7 icon-stack-3x color-success-500"></i>
										<span class="icon-stack-1x text-white">4</span>
									</span>
								</div>
								<div class="col-12 text-center">
									<span class="fw-700">Verifikasi Tanam Selesai</span>
								</div>
							</div><hr>
							<p class="small help-block">Verifikasi Tanam SELESAI diproses.<br> Anda sudah dapat mengajukan verifikasi produksi.</p><hr>
							<a href="{{ route('admin.task.commitment.avp', $commitment->id) }}"
								class="btn btn-xs btn-danger d-block">
								Ajukan Review dan Verifikasi Produksi
							</a>
						@break
						@case(5)
							<div class="row">
								<div class="col-12 text-center mb-1">
									<span class="icon-stack icon-stack-sm">
										<i class="base-7 icon-stack-3x color-success-300"></i>
										<span class="icon-stack-1x text-white">5</span>
									</span>
								</div>
								<div class="col-12 text-center">
									<span class="fw-700">Sudah Diajukan.</span>
								</div>
							</div><hr>
							<p class="small help-block">Anda telah mengajukan verifikasi produksi dan segera kami proses.<br> Selama Masa Tunggu, Anda tidak dapat: menambah, mengubah, dan atau menghapus data terkait.
							</p>
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
										<span class="icon-stack-1x text-white fw-500">!</span>
									</span>
								</div>
								<div class="col-12 text-center">
									<span class="fw-700 small">Anda belum mengajukan verifikasi</span>
								</div>
							</div><hr>
							<a href="{{ route('admin.task.commitment.avt', $commitment->id) }}"
								class="btn btn-xs btn-danger d-block">
								Ajukan Verifikasi Tanam
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
									<h6 class="fw-500 my-0">Mulsa:&nbsp;</h6>
									<h6 class="fw-500 my-0">{{ number_format($commitment->mulsa,2,',', '.') }} kg</h6>
								</div>
							</div>
						</li>
						<li class="list-group-item d-flex justify-content-between">
							<div>
								<span class="text-muted">Bagi Hasil (%)</span>
								<h6 class="fw-500 my-0">
									{{$commitment->poktan_share}} : {{$commitment->importir_share}}
								</h6>

							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div> --}}
	{{-- <div class="panel" id="panel-7">
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
	</div> --}}


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
