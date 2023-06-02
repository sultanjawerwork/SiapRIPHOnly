@extends('layouts.admin')
@section('content')
{{-- @include('partials.breadcrumb') --}}
@include('partials.subheader')
	@can('completed_access')
		@include('partials.sysalert')
		<div class="row">
			<div class="col-lg-12">
				<div id="panel-1" class="panel">
					<div class="panel-container show">
						<div class="panel-content">
							<div class="table">
								<table id="verifList" class="table table-sm table-bordered table-hover table-striped w-100">
									<thead>
										<tr>
											<th>Nomor RIPH</th>
											<th>Perusahaan</th>
											<th>Tanggal diajukan</th>
											<th>No SKL</th>
											<th>Tanggal Terbit</th>
											<th>Tindakan</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($recomends as $recomend)
											<tr>
												<td>{{$recomend->no_ijin}}</td>
												<td>{{$recomend->datauser->company_name}}</td>
												<td>
													@if($recomend->skl?->id)
														{{$recomend->skl->created_at}}
													@else
														Belum diajukan
													@endif
												</td>
												<td>
													@if($recomend->skl?->no_skl)
														{{$recomend->skl->no_skl}}
													@else
														Belum diajukan
													@endif
												</td>
												<td>
													@if($recomend->skl?->published_date)
														{{$recomend->skl->published_date}}
													@else
														Belum terbit
													@endif
												</td>
												<td class="text-center">
													@if($recomend->skl?->published_date)
														<a href="{{route('verification.skl.published', $recomend->skl->id)}}" class="btn btn-xs btn-icon btn-success" title="SKL sudah terbit.">
															<i class="fal fa-file-certificate"></i>
														</a>
													@elseif($recomend->skl && $recomend->skl->created_at && !$recomend->skl->published_date)
														<span class="btn btn-xs btn-icon btn-info" title="Sudah direkomendasikan kepada pimpinan.">
															<i class="fal fa-check"></i>
														</span>
													@else
														<button class="btn btn-xs btn-primary" type="button" title="Rekomendasikan kepada Pimpinan" data-toggle="modal" data-target="#modal{{$recomend->id}}">
															<i class="fas fa-upload text-align-center mr-1"></i>Rekomendasi
														</button>
													@endif
												</td>
												<div class="modal fade" id="modal{{$recomend->id}}"
												tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													<div class="modal-dialog modal-dialog-center" role="document">
														<div class="modal-content">
															<div class="modal-header">
																<div>
																	<h5 class="modal-title" id="myModalLabel">Rekomendasi Penerbitan SKL</h5>
																	<small id="helpId" class="text-muted">Rekomendasikan Penerbitan SKL kepada Pimpinan/Pejabat terkait.</small>
																</div>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
																</button>
															</div>
															<form action="{{route('verification.skl.recomend')}}" method="post">
																@csrf
																<div class="modal-body">
																<input type="text" name="pengajuan_id" value="{{$recomend->id}}" hidden>
																<input type="text" name="no_pengajuan" value="{{$recomend->no_pengajuan}}" hidden>
																<input type="text" name="npwp" value="{{$recomend->npwp}}" hidden>
																<input type="text" name="no_ijin" value="{{$recomend->no_ijin}}" hidden>
																<input type="text" name="status" value="6" hidden>
																<div class="form-group">
																	<label for="">Beri Nomor Penerbitan SKL</label>
																	<input type="text" name="no_skl" id="no_skl"
																		class="form-control " placeholder="Nomor SKL"
																		aria-describedby="helpId">
																	<small id="helpId" class="text-muted">Nomor SKL yang telah dialokasikan.</small>
																</div>
																</div>
																<div class="modal-footer">
																	<button type="button" class="btn btn-warning btn-sm"
																		data-dismiss="modal">
																		<i class="fal fa-times-circle text-danger fw-500"></i> Close
																	</button>
																	<button class="btn btn-primary btn-sm" type="submit">
																		<i class="fal fa-upload"></i> Rekomendasikan
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
			</div>
		</div>
	@endcan
@endsection

@section('scripts')
@parent
<script>
	$(document).ready(function() {
		// initialize datatable
		$('#verifList').dataTable({
			pagingType: 'full_numbers',
			responsive: true,
			lengthChange: true,
			pageLength: 10,
			order: [
				[0, 'asc']
			],
			dom:
				/*	--- Layout Structure 
					--- Options
					l	-	length changing input control
					f	-	filtering input
					t	-	The table!
					i	-	Table information summary
					p	-	pagination control
					r	-	processing display element
					B	-	buttons
					R	-	ColReorder
					S	-	Select

					--- Markup
					< and >				- div element
					<"class" and >		- div with a class
					<"#id" and >		- div with an ID
					<"#id.class" and >	- div with an ID and a class

					--- Further reading
					https://datatables.net/reference/option/dom
					--------------------------------------
				*/
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
				"<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
			buttons: [
				/*{
					extend:    'colvis',
					text:      'Column Visibility',
					titleAttr: 'Col visibility',
					className: 'mr-sm-3'
				},*/
				{
					extend: 'pdfHtml5',
					text: 'PDF',
					titleAttr: 'Generate PDF',
					className: 'btn-outline-danger btn-sm mr-1'
				},
				{
					extend: 'excelHtml5',
					text: 'Excel',
					titleAttr: 'Generate Excel',
					className: 'btn-outline-success btn-sm mr-1'
				},
				{
					extend: 'csvHtml5',
					text: 'CSV',
					titleAttr: 'Generate CSV',
					className: 'btn-outline-primary btn-sm mr-1'
				},
				{
					extend: 'copyHtml5',
					text: 'Copy',
					titleAttr: 'Copy to clipboard',
					className: 'btn-outline-primary btn-sm mr-1'
				},
				{
					extend: 'print',
					text: 'Print',
					titleAttr: 'Print Table',
					className: 'btn-outline-primary btn-sm'
				}
			]
		});

	});

</script>


@endsection

