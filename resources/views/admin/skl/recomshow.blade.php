@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
<div class="row">
	<div class="col-12">
		<div class="text-center">
			<i class="fal fa-badge-check fa-3x subheader-icon"></i>
			<h2 class="display-5 d-block l-h-n m-0 fw-500 mb-2">{{$importir->name}}</h2>
			<div class="row justify-content-center">
				<p class="lead">{{$importir->company_name}}</p>
			</div>
		</div>
		<div class="panel" id="panel-1">
			<div class="panel-container card-header show">
				<div class="row d-flex justify-content-between">
					<div class="form-group col-md-4">
						<label class="form-label" for="no_hs">Nomor SKL</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
									<i class="fal fa-file-invoice"></i>
								</span>
							</div>
							<input type="text" class="form-control form-control-sm bg-white" id="no_skl" value="{{$pengajuan->skl->no_skl}}" disabled="">
						</div>
						<span class="help-block">Nomor Surat Keterangan Lunas Wajib Tanam-Produksi.</span>
					</div>
					<div class="form-group col-md-2 col-sm-6">
						<label class="form-label" for="tgl_akhir">Tanggal Diajukan</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
									<i class="fal fa-calendar-day"></i>
								</span>
							</div>
							<input type="text" class="form-control form-control-sm bg-white" id="tgl_akhir" value="{{ date('d-m-Y', strtotime($pengajuan->skl->created_at)) }}" disabled="">
						</div>
						<span class="help-block">Tanggal pengajuan SKL.</span>
					</div>
					<div class="form-group col-md-4">
						<label class="form-label" for="no_ijin">Nomor RIPH</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
									<i class="fal fa-file-invoice"></i>
								</span>
							</div>
							<input type="text" class="form-control form-control-sm bg-white" id="no_ijin" value="{{$pengajuan->no_ijin}}" disabled="">
						</div>
						<span class="help-block">Nomor Ijin RIPH.</span>
					</div>
					<div class="form-group col-md-2 col-sm-6">
						<label class="form-label" for="tgl_ijin">Tanggal Ijin</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text">
									<i class="fal fa-calendar-day"></i>
								</span>
							</div>
							<input type="text" class="form-control form-control-sm bg-white" id="tgl_ijin" value="{{ date('d-m-Y', strtotime($pengajuan->commitment->tgl_ijin)) }}" disabled="">
						</div>
						<span class="help-block">Tanggal mulai berlaku RIPH.</span>
					</div>
				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<table class="table table-striped table-bordered w-100" id="mainCheck">
						<thead>
							<th class="text-muted text-uppercase">Data</th>
							<th class="text-muted text-uppercase">Kewajiban</th>
							<th class="text-muted text-uppercase">Realisasi</th>
							<th class="text-muted text-uppercase">Status</th>
						</thead>
						<tbody>
							<tr>
								<td class="text-muted">
									<span class="fw-700 h6">Wajib Tanam</span><br>
									<span class="help-block">Komitmen wajib tanam yang telah dipenuhi hingga saat ini</span>
								</td>
								<td class="text-right">
									{{ number_format($wajib_tanam, 2) }} ha
								</td>
								<td class="text-right">
									{{ number_format($luas_verif, 2) }} ha
								</td>
								<td>
									@if ($luas_verif >= $wajib_tanam)
										<i class="fas fa-check text-success"></i>
										<i>Terpenuhi</i>
									@else
										<i class="fas fa-times text-danger"></i>
										<i>Tidak Terpenuhi</i>
									@endif
								</td>
							</tr>
							<tr>
								<td class="text-muted">
									<span class="fw-700 h6">Wajib produksi</span><br>
									<span class="help-block">Komitmen wajib tanam yang telah dipenuhi hingga saat ini</span>
								</td>
								<td class="text-right">
									{{ number_format($wajib_produksi, 2) }} ha
								</td>
								<td class="text-right">
									{{ number_format($volume_verif, 2) }} ha
								</td>
								<td>
									@if ($volume_verif >= $wajib_produksi)
										<i class="fas fa-check text-success"></i>
										<i>Terpenuhi</i>
									@else
										<i class="fas fa-times text-danger"></i>
										<i>Tidak Terpenuhi</i>
									@endif
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-header show">
				<div class="d-flex justify-content-between align-item-center">
					<div class="form-group row">
					</div>
					<div>
						<form action="{{route('verification.skl.recomendations.store', $skl->id)}}" method="post" onsubmit="return confirm('Anda setuju untuk menerbitkan Surat Keterangan Lunas untuk RIPH terkait?')">
							<a class="btn btn-sm btn-danger" href="{{route('verification.skl.recomendations')}}" role="button"><i class="fal fa-times text-align-center mr-1"></i> Batalkan</a>
							@csrf
							@method('PUT')
							<button class="btn btn-sm btn-primary" type="submit">
								<i class="fas fa-upload text-align-center mr-1"></i> Terbitkan
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
@parent
@endsection
