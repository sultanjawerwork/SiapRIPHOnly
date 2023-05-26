@extends('layouts.admin')
@section('styles')
<link rel="stylesheet" media="screen, print" href="{{ asset('css/miscellaneous/lightgallery/lightgallery.bundle.css') }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1ea90fk4RXPswzkOJzd17W3EZx_KNB1M&libraries=drawing,geometry"></script>
{{-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> --}}

@endsection
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
@can('commitment_show')
	<div class="row">
		<div class="col-md-12">
			<div class="panel" id="panel-1">
				<div class="panel-hdr">
					<h2>
						Data <span class="fw-300">
							<i>Geolokasi</i>
						</span>
					</h2>
					<div class="panel-toolbar">
						@include('partials.globaltoolbar')
					</div>
				</div>
				<div class="panel-container show">
					<div class="panel-content card-header">
						<div class="row">
							<div class="form-group col-md-12">
								<label class="form-label" for="gmap">
									Pilih lokasi dan Buat Peta Polygon bidang lahan dari lokasi yang dipilih
									<sup class="text-danger"> *</sup>
								</label>
								<div id="myMap" style="height: 500px; width: 100%;"></div>
							</div>
						</div>
					</div>
					<div class="panel-content card-header">
						<form id="location-search-form">
							<div class="form-group mb-5" title="Cari lokasi yang diinginkan">
								<div class="input-group bg-white shadow-inset-2">
									<div class="input-group-prepend">
										<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
											<i class="fal fa-search"></i>
										</span>
									</div>
									<input id="searchBox" placeholder="cari lokasi..."
										class="form-control border-left-0 bg-transparent pl-0" >
									<div class="input-group-append">
										<button class="btn btn-default waves-effect waves-themed"
											type="submit">Search</button>
									</div>
								</div>
								<span class="help-block">Cari lokasi di peta</span>
							</div>
						</form>
						<div class="row d-flex flex-row justify-content-between">
							<div class="col-md-6">
								<div class="form-group">
									<div class="input-group bg-white shadow-inset-2">
										<div class="input-group-prepend">
											<span class="input-group-text bg-transparent border-right-0 py-1 px-3 text-success">
												<i class="fal fa-upload"></i>
											</span>
										</div>
										<div class="custom-file">
											<input type="file" id="kml_file" placeholder="ambil berkas KML..." onchange="kml_parser()"
												class="custom-file-input border-left-0 bg-transparent pl-0" >
											<label class="custom-file-label text-muted" for="inputGroupFile01">ambil berkas KML...</label>
										</div>
									</div>
									<span class="help-block">Unggah berkas KML</span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<div class="input-group bg-grey shadow-inset-2">
										<div class="input-group-prepend">
											<span class="input-group-text border-right-0 py-1 px-3 text-success">
												<i class="fal fa-globe"></i>
											</span>
										</div>
										<input id="mapId" name="mapId" placeholder="contoh: 1cwFsptUJ7EdW1IoHxFB_VRHsD10TEJ0" class="form-control" disabled>
										<div class="input-group-append">
											
											<button class="btn btn-default waves-effect waves-themed" disabled
												onclick="link_parser()">Open</button>
										</div>
									</div>
									<span class="help-block">Fitur masih dalam pengembangan.</span>
								</div>
							</div>
						</div>
					</div>
					<form action="{{route('admin.task.lokasi.tanam.update', $anggota->anggota_id)}}"
						method="POST" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="form_action" value="form1">
						<div class="panel-content">
							<div class="row">
								<div class="form-group col-md-3">
									<label>Nama Lokasi <sup class="text-danger"> **</sup></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fal fa-map-signs"></i></span>
										</div>
										<input type="text" value="{{ old('nama_lokasi', $anggota->nama_lokasi) }}"
											name="nama_lokasi" id="nama_lokasi"
											class="font-weight-bold form-control form-control-sm bg-white" />
									</div>
									<span class="help-block">berikan Nama/ID untuk lokasi ini.</span>
								</div>
								<div class="form-group col-md-3">
									<label>Latitude <sup class="text-info"> *</sup></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fal fa-map-marker"></i></span>
										</div>
										<input type="text" value="{{ old('latitude', $anggota->latitude) }}"
											name="latitude" id="latitude" readonly
											class="font-weight-bold form-control form-control-sm bg-white" />
									</div>
									<span class="help-block">Koordinat Lintang lokasi</span>
								</div>
								<div class="form-group col-md-3">
									<label>Longitude <sup class="text-info"> *</sup></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fal fa-map-marker-alt"></i></span>
										</div>
										<input type="text" value="{{ old('longitude', $anggota->longitude) }}"
											name="longitude" id="longitude"
											class="font-weight-bold form-control form-control-sm bg-white" />
									</div>
									<span class="help-block">Koordinat Bujur lokasi</span>
								</div>
								<div class="form-group col-3">
									<label>Altitude (mdpl) <sup class="text-danger"> **</sup></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fal fa-ruler-vertical"></i></span>
										</div>
										<input type="text" value="{{ old('altitude', $anggota->altitude) }}"
											name="altitude" id="altitude"
											class="font-weight-bold form-control form-control-sm bg-white" />
									</div>
									<span class="help-block">Ketinggian lokasi lahan (rerata ketinggain dpl)</span>
								</div>
								<div class="form-group col-md-7">
									<label>Polygon<sup class="text-info"> *</sup></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fal fa-draw-polygon"></i></span>
										</div>
										<input type="text" value="{{ old('polygon', $anggota->polygon) }}"
										name="polygon" id="polygon" readonly
										class="font-weight-bold form-control form-control-sm bg-white" />
									</div>
									<span class="help-block">Kurva bidang lahan yang ditanami.</span>
								</div>
								<div class="form-group col-md-5">
									<label>Luas Perkiraan (ha)<sup class="text-info"> *</sup></label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fal fa-ruler-combined"></i></span>
										</div>
										<input type="text" value="{{ old('luas_kira', $anggota->luas_kira) }}"
											name="luas_kira" id="luas_kira" readonly
											class="font-weight-bold form-control form-control-sm bg-white" />
									</div>
									<span class="help-block">Luas bidang diukur oleh sistem.</span>
								</div>
							</div>
							<div class="mt-5">
								<span class="small mr-3"><span class="text-info mr-1"> *</span>: Autogenerate by System</span>
								<span class="small"><span class="text-danger mr-1"> **</span>: Wajib diisi</span>
							</div>
						</div>
						<div class="card-footer">
							<div class="justify-content-end">
								<a href="{{route('admin.task.pks.anggotas', $pks->id)}}"
									class="btn btn-sm btn-info" role="button">
									<i class="fa fa-door-open mr-1"></i>Kembali
								</a>
								
								<button class="btn btn-sm btn-primary" role="button" type="submit"
									@if ($disabled) disabled @endif>
									<i class="fa fa-save mr-1"></i>Simpan
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		@php
			$npwp = str_replace(['.', '-'], '', $npwp_company);
		@endphp
		<div class="col-md-6">
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>Realisasi Wajib Tanam</h2>
					<div class="panel-toolbar">
						<button class="btn btn-xs btn-info mr-1" type="button"
							data-toggle="modal" data-target="#modalTanam"
							@if ($disabled) disabled @endif>
							<i class="fas fa-upload"></i> Dokumentasi
						</button>
					</div>
				</div>
				<div class="panel-container show">
					<form action="{{route('admin.task.lokasi.tanam.update', $anggota->anggota_id)}}"
						method="POST" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="form_action" value="form2">
						<div class="panel-content">
							<div class="row mb-3">
								<div class="col-md-12">
									<div class="row">
										<div class="form-group col-md-12">
											<label class="form-label" for="tgl_tanam">Tanggal Tanam</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="{{ old('tgl_tanam', $anggota->tgl_tanam) }}"
													name="tgl_tanam" id="tgl_tanam"
													class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal mulai penanaman.</span>
										</div>
										<div class="form-group col-md-12">
											<label for="luas_tanam">Luas Bidang (ha)<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-ruler-combined"></i></span>
												</div>
												<input type="number" value="{{ old('luas_tanam', $anggota->luas_tanam) }}"
													name="luas_tanam" id="luas_tanam" step="0.01"
													class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Luas area lahan diukur mandiri.</span>
										</div>
									</div>
								</div>
							</div>
							<div>
								<label class="form-label" for="tgl_prod">Dokumentasi Tanam</label>
								<div class="d-flex align-items-center flex-row">
									<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggota->tanam_doc) }}"
										data-sub-html="{{ $anggota->tanam_doc }}" title="{{ $anggota->tanam_doc }}" target="_blank">
										<div class="d-flex align-items-center">
										   <i class="far fa-file-alt fa-5x mr-3"></i>
										</div>
									 </a>
									<div id="js-galleryTanam">
										<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggota->tanam_pict) }}"
											data-sub-html="{{$anggota->tanam_pict}}" title="{{$anggota->tanam_pict}}">
											<img class="img-responsive"
											src="{{ url('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggota->tanam_pict) }}"
											alt="{{$anggota->tanam_pict}}">
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<div class="justify-content-end">
								<a href="{{route('admin.task.pks.anggotas', $pks->id)}}"
									class="btn btn-sm btn-info" role="button">
									<i class="fa fa-door-open mr-1"></i>Kembali
								</a>
								<button class="btn btn-sm btn-primary" role="button" type="submit" @if ($disabled) disabled @endif>
									<i class="fa fa-save mr-1"></i>Simpan
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div class="col-md-6">
			<div class="panel" id="panel-2">
				<div class="panel-hdr">
					<h2>Realisasi Wajib Produksi</h2>
					<div class="panel-toolbar">
						<button class="btn btn-xs btn-info mr-1" type="button"
							data-toggle="modal" data-target="#modalProduksi"
							@if ($disabled) disabled @endif>
							<i class="fas fa-upload"></i> Dokumentasi
						</button>
					</div>
				</div>
				<div class="panel-container show">
					<form action="{{route('admin.task.lokasi.tanam.update', $anggota->anggota_id)}}"
						method="POST" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="form_action" value="form4">
						<div class="panel-content">
							<div class="row mb-3">
								<div class="col-md-12">
									<div class="row">
										<div class="form-group col-md-12">
											<label class="form-label" for="tgl_panen">Tanggal Panen</label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-calendar-day"></i></span>
												</div>
												<input type="date" value="{{ old('tgl_panen', $anggota->tgl_panen) }}"
													name="tgl_panen" id="tgl_panen"
													class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Tanggal mulai penanaman.</span>
										</div>
										<div class="form-group col-md-12">
											<label for="luas_tanam">Volume Produksi (ton)<sup class="text-danger"> *</sup></label>
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text"><i class="fal fa-ruler-combined"></i></span>
												</div>
												<input type="number" value="{{ old('volume', $anggota->volume) }}"
													name="volume" id="volume" step="0.01"
													class="font-weight-bold form-control form-control-sm bg-white" />
											</div>
											<span class="help-block">Luas area lahan diukur mandiri.</span>
										</div>
									</div>
								</div>
							</div>
							<div>
								<label class="form-label" for="tgl_prod">Dokumentasi Produksi</label>
								<div class="d-flex align-items-center flex-row">
									<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggota->panen_doc) }}"
										data-sub-html="{{$anggota->panen_doc}}" title="{{$anggota->panen_doc}}"
										target="_blank">
										<div class="d-flex align-items-center">
										<i class="far fa-file-alt fa-5x mr-3"></i>
										</div>
									</a>
									<div id="js-galleryProduksi">
										<a href="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggota->panen_pict) }}"
											data-sub-html="{{$anggota->panen_pict}}" title="{{$anggota->panen_pict}}">
											<img class="img-responsive"
											src="{{ asset('storage/uploads/'.$npwp.'/'.$commitment->periodetahun.'/'.$anggota->panen_pict) }}">
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<div class="justify-content-end">
								<a href="{{route('admin.task.pks.anggotas', $pks->id)}}"
									class="btn btn-sm btn-info" role="button">
									<i class="fa fa-door-open mr-1"></i>Kembali
								</a>
								<button class="btn btn-sm btn-primary" role="button" type="submit" @if ($disabled) disabled @endif>
									<i class="fa fa-save mr-1"></i>Simpan
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	{{-- modal upload berkas-foto tanam --}}
	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalTanam">
		<div class="modal-dialog modal-dialog-right">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title h4"><i class="subheader-icon fas fa-upload text-info"></i> Unggah Berkas Pendukung</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
				<div class="alert alert-danger border-0 mb-0">
					<div class="d-flex align-item-center">
						<div class="alert-icon">
							<div class="icon-stack icon-stack-sm mr-3 flex-shrink-0" style="font-size: 20px;">
								<i class="base base-7 icon-stack-3x opacity-100 color-danger-400"></i>
								<i class="base base-7 icon-stack-2x opacity-100 color-danger-800"></i>
								<i class="fa fa-exclamation icon-stack-1x opacity-100 color-white"></i>
							</div>
						</div>
						<div class="flex-1">
							<span><span class="fw-700">PERHATIAN!. </span> Unggahan ini akan menggantikan berkas unggahan terdahulu.</span>
						</div>
					</div>
				</div>
				<form action="{{route('admin.task.lokasi.tanam.update', $anggota->anggota_id)}}"
					method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<input type="hidden" name="form_action" value="form3">
						<div class="form-group">
							<label class="form-label">Dokumen Pendukung</label>
							<div class="custom-file input-group">
								<input type="file" class="custom-file-input" id="customControlValidation7"
									name="tanam_doc" id="tanam_doc">
								<label class="custom-file-label" for="customControlValidation7">Choose file...</label>
							</div>
							<span class="help-block">Unggah Dokumen Pendukung. Ekstensi pdf ukuran maks 4mb.</span>
						</div>
						<div class="form-group">
							<label class="form-label">Dokumentasi Tanam</label>
							<div class="custom-file input-group">
								<input type="file" class="custom-file-input" id="customControlValidation7"
									name="tanam_pict" id="tanam_pict">
								<label class="custom-file-label" for="customControlValidation7">Choose file...</label>
							</div>
							<span class="help-block">Unggah Dokumentasi Tanam. Ekstensi jpg ukuran maks 4mb.</span>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-sm btn-info waves-effect waves-themed" type="submit" @if ($disabled) disabled @endif><i class="fal fa-save mr-1"></i>Unggah</button>
						<button class="btn btn-sm btn-warning waves-effect waves-themed"><i class="fal fa-undo mr-1"></i>Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- modal upload berkas foto produksi --}}
	<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalProduksi">
		<div class="modal-dialog modal-dialog-right">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title h4"><i class="subheader-icon fas fa-upload text-info"></i> Unggah Berkas Pendukung</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
				</div>
				<div class="alert alert-danger border-0 mb-0">
					<div class="d-flex align-item-center">
						<div class="alert-icon">
							<div class="icon-stack icon-stack-sm mr-3 flex-shrink-0" style="font-size: 20px;">
								<i class="base base-7 icon-stack-3x opacity-100 color-danger-400"></i>
								<i class="base base-7 icon-stack-2x opacity-100 color-danger-800"></i>
								<i class="fa fa-exclamation icon-stack-1x opacity-100 color-white"></i>
							</div>
						</div>
						<div class="flex-1">
							<span><span class="fw-700">PERHATIAN!. </span> Unggahan ini akan menggantikan berkas unggahan terdahulu.</span>
						</div>
					</div>
				</div>
				<form action="{{route('admin.task.lokasi.tanam.update', $anggota->anggota_id)}}"
					method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<input type="hidden" name="form_action" value="form5">
						<div class="form-group">
							<label class="form-label">Dokumen Pendukung</label>
							<div class="custom-file input-group">
								<input type="file" class="custom-file-input" id="customControlValidation7"
									value="{{ old('panen_doc', $anggota->panen_doc) }}"
									name="panen_doc" id="panen_doc">
								<label class="custom-file-label" for="customControlValidation7">Choose file...</label>
							</div>
							<span class="help-block">Unggah Dokumen Pendukung. Ekstensi pdf ukuran maks 4mb.</span>
						</div>
						<div class="form-group">
							<label class="form-label">Dokumentasi Produksi</label>
							<div class="custom-file input-group">
								<input type="file" class="custom-file-input" id="customControlValidation7"
									value="{{ old('panen_pict', $anggota->panen_pict) }}"
									name="panen_pict" id="panen_pict">
								<label class="custom-file-label" for="customControlValidation7">Choose file...</label>
							</div>
							<span class="help-block">Unggah Dokumentasi Panen. Ekstensi jpg ukuran maks 4mb.</span>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-sm btn-info waves-effect waves-themed" type="submit" @if ($disabled) disabled @endif><i class="fal fa-save mr-1"></i>Unggah</button>
						<button class="btn btn-sm btn-warning waves-effect waves-themed"><i class="fal fa-undo mr-1"></i>Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@endcan

@endsection

<!-- start script for this page -->
@section('scripts')
<script src="{{ asset('js/miscellaneous/lightgallery/lightgallery.bundle.js') }}"></script>
@parent
<script src="{{ asset('js/gmap/map.js') }}"></script>
<script src="{{ asset('js/gmap/location-search.js') }}"></script>
<script src="{{ asset('js/gmap/kml_parser.js') }}"></script>
<script src="{{ asset('js/gmap/link_parser.js') }}"></script>

<script>
	window.addEventListener('load', function() {
		initMap();
	});
</script>

{{-- <script src="https://cdn.rawgit.com/geocodezip/geoXML3/master/geoxml3.js"></script> --}}

<!-- gallery Tanam -->
	<script>
		$(document).ready(function() {
			var $initScope = $('#js-galleryTanam');
			if ($initScope.length) {
				$initScope.justifiedGallery({
					border: -1,
					rowHeight: 75,
					margins: 8,
					waitThumbnailsLoad: true,
					randomize: false,
				}).on('jg.complete', function() {
					$initScope.lightGallery({
						thumbnail: true,
						animateThumb: true,
						showThumbByDefault: true,
					});
				});
			};
			$initScope.on('onAfterOpen.lg', function(event) {
				$('body').addClass("overflow-hidden");
			});
			$initScope.on('onCloseAfter.lg', function(event) {
				$('body').removeClass("overflow-hidden");
			});
		});
	</script>
<!-- gallery Tanam -->
<!-- gallery Produksi -->
	<script>
		$(document).ready(function() {
			var $initScope = $('#js-galleryProduksi');
			if ($initScope.length) {
				$initScope.justifiedGallery({
					border: -1,
					rowHeight: 75,
					margins: 8,
					waitThumbnailsLoad: true,
					randomize: false,
				}).on('jg.complete', function() {
					$initScope.lightGallery({
						thumbnail: true,
						animateThumb: true,
						showThumbByDefault: true,
					});
				});
			};
			$initScope.on('onAfterOpen.lg', function(event) {
				$('body').addClass("overflow-hidden");
			});
			$initScope.on('onCloseAfter.lg', function(event) {
				$('body').removeClass("overflow-hidden");
			});
		});
	</script>
<!-- gallery Produksi -->
@endsection