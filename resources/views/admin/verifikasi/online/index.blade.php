@extends('layouts.admin')
@section('content')
	{{-- @include('partials.breadcrumb') --}}
	@include('partials.subheader')

	@can('commitment_access')
		@include('partials.sysalert')
		<div class="row">
			<div class="col-12">
				<div class="panel" id="panel-1">
					<div class="panel-container show">
						<div class="panel-content">
							<table id="tablePengajuan" class="table table-sm table-bordered table-striped w-100">
								<thead>
									<tr>
										<th>Pelaku Usaha</th>
										<th>No. Pengajuan</th>
										<th>No. RIPH</th>
										<th>Diajukan pada</th>
										<th>Status</th>
										<th>Tindakan</th>
									</tr>
								</thead>
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
			var pengajuanTable = $('#tablePengajuan').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: {
				url: "{{ route('verification.data') }}",
				type: "GET",
					},
				columns: [
					{ data: 'company_name', name: 'company_name' },
					{ data: 'no_pengajuan', name: 'no_pengajuan' },
					{ data: 'no_ijin', name: 'no_ijin' },
					{ data: 'created_at', name: 'created_at' },
					{ 
						data: 'status',
						name: 'status',
						createdCell: function (td, cellData, rowData, row, col) {
							$(td).addClass('text-center'); // Add the 'text-center' class to the td element
						},
						render: function (data, type, row) {
							if (data === '1') {
								return '<span class="badge btn-xs btn-icon btn-warning rounded-circle" title="Verifikasi diajukan"><i class="fa fa-download"></i></span>';
							} else if (data === '2') {
								return '<span class="badge btn-xs btn-icon btn-success rounded-circle" title="Verifikasi Selesai"><i class="fal fa-check"></i></span>';
							} else if (data === '3') {
								return '<span class="badge btn-xs btn-icon btn-danger rounded-circle" title="Dikembalikan kepada Pelaku Usaha untuk diperbaiki"><i class="fal fa-exclamation-circle"></i></span>';
							} else {
								return 'Tidak dapat mendapatkan data';
							}
						}
					},
					{
						data: null,
						createdCell: function (td, cellData, rowData, row, col) {
							$(td).addClass('text-center'); // Add the 'text-center' class to the td element
						},
						render: function(data, type, row) {
							// You can customize this as per your requirements
							var route = "{{ route('verification.data.show', ':id') }}";
							return '<a href="' + route.replace(':id', data.id) + '" class="btn btn-primary btn-xs btn-icon" data-toggle="tooltip" data-original-title="Periksa Data"><i class="fal fa-file-search"></i> </a>';
						}
					},
				],
				dom:
				"<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'>>" +
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'fl><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
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
			],
			});
		});
	</script>
@endsection