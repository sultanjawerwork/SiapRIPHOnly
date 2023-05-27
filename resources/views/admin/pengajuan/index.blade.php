@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')

@can('poktan_access')
<div class="row">
	<div class="col-12">
		<div class="panel" id="panel-1">
			<div class="panel-hdr">
				<h2>
					Kelompok Tani
				</h2>
				<div class="panel-toolbar">
					@include('partials.globaltoolbar')
				</div>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<table class="table table-hover table-striped table-bordered table-sm" id="datatable">
						<thead>
							<th>Nomor Pengajuan</th>
							<th>No. Riph</th>
							<th>Tanggal Pengajuan</th>
							<th>Tanggal Status</th>
							<th>Status Terakhir</th>
							<th>Tindakan</th>
						</thead>
						<tbody>
							@foreach ($pengajuans as $pengajuan)
							<tr>
								<td>{{$pengajuan->no_pengajuan}}</td>
								<td>{{$pengajuan->no_ijin}}</td>
								<td>{{$pengajuan->created_at}}</td>
								<td>{{$pengajuan->updated_at}}</td>
								<td class="text-center">
									@if($pengajuan->status === '1')
										<span class="badge btn-xs btn-primary" title="sudah diajukan">Diajukan</span>
									@elseif($pengajuan->status === '2')
										<span class="badge btn-xs btn-info" title="Proses pemeriksaan data">Data</span>
									@elseif($pengajuan->status === '3')
										<span class="badge btn-xs btn-danger" title="Data laporan ditolak">Ditolak</span>
									@elseif($pengajuan->status === '4')
										<span class="badge btn-xs btn-info" title="Proses Verifikasi Lapangan">Lapangan</span>
									@elseif($pengajuan->status === '5')
										<span class="badge btn-xs btn-danger" title="Data laporan ditolak">Ditolak</span>
									@elseif($pengajuan->status === '6')
										<span class="badge btn-xs btn-success" title="SKL telah terbit">SKL Terbit</span>
									@elseif($pengajuan->status === '7')
										<span class="badge btn-xs btn-success" title="SKL telah diterbitkan">SKL Terbit</span>
									@endif
								</td>
								<td class="text-center">
									<a href="{{route('admin.task.submission.show', $pengajuan->id)}}"
										class="btn btn-xs btn-icon btn-primary"
										data-toggle="tooltip" title data-original-title="Lihat data pengajuan">
										<i class="fa fa-file-invoice"></i>
									</a>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
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
  // initialize datatable
  $('#datatable').dataTable(
			{
			responsive: true,
			lengthChange: false,
			order:[],
			dom:
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'>>",
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