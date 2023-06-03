@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
@include('partials.sysalert')
@can('master_riph_access')
<div class="row">
	<div class="col-md-12">
		<div id="panel-1" class="panel">
			<div class="panel-container show">
				<div class="panel-content">
					<table id="riphList" class="table table-sm table-bordered table-hover table-striped w-100">
						<thead>
							<tr>
								<th>Periode</th>
								<th>Tanggal Update</th>
								<th>Total Vol. RIPH</th>
								<th>Beban Tanam (ha)</th>
								<th>Beban Produksi (ton)</th>
								<th>Jumlah Importir</th>
								<th>Tindakan</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($riph_admin as $riph )
								<tr>
									<td>{{ $riph->periode }}</td>
									<td>{{ $riph->updated_at->format('d/m/Y') }}</td>
									<td>{{ number_format($riph->v_pengajuan_import, 0, ',', '.') }}</td>
									<td>{{ number_format($riph->v_beban_tanam, 0, ',', '.') }}</td>
									<td>{{ number_format($riph->v_beban_produksi, 0, ',', '.') }}</td>
									<td>{{ number_format($riph->jumlah_importir, 0, ',', '.') }}</td>
									<td>
										<a class="btn btn-xs btn-primary btn-icon waves-effect waves-themed" href="/admin/riphAdmin/{{ $riph->id }}/edit" data-toggle="tooltip" data-offset="0,10" data-original-title="Ubah Data"><i class="fal fa-edit"></i></a>
										<a class="btn btn-xs btn-danger btn-icon waves-effect waves-themed" href="" data-toggle="tooltip" data-offset="0,10" data-original-title="Hapus Data"><i class="fal fa-trash"></i></a>
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
<!-- @parent -->
<!-- start script for this page -->
@section('scripts')

<script>
	$(document).ready(function()
	{
		// initialize tblPenangkar
		$('#riphList').dataTable(
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

					text: '<i class="fal fa-plus mr-1"></i>Tambah Data',
					titleAttr: 'Tambah Data',
					className: 'btn btn-info btn-xs ml-2',
					action: function(e, dt, node, config) {
						window.location.href = '{{ route('admin.riphAdmin.create') }}';
					}
				}
			]
		});

	});
</script>
@endsection