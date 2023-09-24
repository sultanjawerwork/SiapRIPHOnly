@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
	{{-- @can('old_skl_access') --}}
		@include('partials.sysalert')
		<div class="row">
			<div class="col-lg-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content">
							<div class="table">
								<table id="completeds" class="table table-sm table-bordered table-hover table-striped w-100">
									<thead>
										<tr>
											<th>No SKL</th>
											@if (Auth::user()->roleaccess === 1)
												<th>Perusahaan</th>
											@endif
											<th>Periode </th>
											<th>Nomor RIPH</th>
											<th>Tanggal Terbit</th>
											<th>Tanggal diunggah</th>
											<th>Tindakan</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($completeds as $completed)
											<tr>
												<td>{{$completed->no_skl}}</td>
												@if (Auth::user()->roleaccess === 1)
													<td>{{$completed->datauser->company_name}}</td>
												@endif
												<td>{{$completed->periodetahun}}</td>
												<td>{{$completed->no_ijin}}</td>
												<td class="text-center">{{ date('d-m-Y', strtotime($completed->published_date)) }}</td>
												<td class="text-center">{{ date('d-m-Y', strtotime($completed->created_at)) }}</td>
												<td class="text-center d-flex justify-content-center">
													{{-- @can('old_skl_show') --}}
														{{-- @if (Auth::user()->roles[0]->title === 'User') --}}
															<a href="{{$completed->url}}" class="btn btn-icon btn-success btn-xs mr-1" title="Lihat SKL">
																<i class="fal fa-file-certificate"></i>
															</a>
														{{-- @endif --}}
													{{-- @endcan --}}
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
		</div>
	{{-- @endcan --}}
@endsection

@section('scripts')
@parent

<script>
	$(document).ready(function()
	{
		// initialize tblPenangkar
		$('#completeds').dataTable(
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

