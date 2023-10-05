@extends('layouts.admin')
@section('styles')

@endsection
@section('content')
	@include('partials.subheader')
	@include('partials.sysalert')
		<div class="row">
			<div class="col-md-7">
				<div class="panel" id="panel-1">
					<div class="panel-hdr">
						<h2>
							Data <span class="fw-300">
								<i>Isian</i>
							</span>
						</h2>
						<div class="panel-toolbar">
							{{-- @include('partials.globaltoolbar') --}}
						</div>
					</div>
					<div class="panel-container">
						<div class="panel-content">
							<div class="form-group">
								<label for="">Nama Pejabat</label>
								<input type="text" name="nama_pejabat" id="nama_pejabat" class="form-control" placeholder="Nama Pejabat" aria-describedby="helpId">
								<small id="helpId" class="text-muted">Isi dengan nama pejabat penandatangan</small>
							</div>
							<div class="form-group">
								<label for="">Nomor Induk Pegawai</label>
								<input type="text" name="nip" id="nip" class="form-control" placeholder="nomor induk pegawai" aria-describedby="helpId">
								<small id="helpId" class="text-muted">Isi dengan NIP (Nomor Induk Pegawai) penandatangan</small>
							</div>
							<div class="form-group">
								<label for="">Security Code Generator</label>
								<input type="text" name="secCode" id="secCode" class="form-control" placeholder="Secure Code Generator" aria-describedby="helpId" readonly required>
								<small id="helpId" class="text-muted">Isi dengan NIP (Nomor Induk Pegawai) penandatangan</small>
							  </div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<div class="panel" id="panel-1">
					<div class="panel-hdr">
						<h2>
							QRCode
							<span class="fw-300">
								<i>Image</i>
							</span>
						</h2>
						<div class="panel-toolbar">
							{{-- @include('partials.globaltoolbar') --}}
						</div>
					</div>
					<div class="panel-container">
						<div class="panel-content">

						</div>
					</div>
				</div>
			</div>
		</div>
@endsection

<!-- start script for this page -->
@section('scripts')
	@parent
@endsection
