@extends('layouts.index')
@section('title',ucwords(str_replace("_"," ",$type)))
@section('content')

<div id="page-content" class="p20 clearfix" style="padding-bottom:100px !important">
        <div class="panel panel-default">
            <div class="page-title clearfix">
                <h1> Lihat {{ucwords(str_replace("_"," ",$type))}}   </h1>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                                
                                @include('adminlte-templates::common.errors')
					
					
						@foreach($column as $c)
							@if ($c['type_data'] == '\String' || $c['type_data'] == 'Editor'|| $c['type_data'] == '\Integer')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									<p>{!! $berkas->{$c['field_name']} !!}</p>
								</div>
							@elseif ($c['type_data'] == '\Date')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									<p>{!! $berkas->{$c['field_name']} !!}</p>
								</div>
							@elseif ($c['type_data'] == '\Text')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									<p>{!! $berkas->{$c['field_name']} !!}</p>
								</div>
							@elseif ($c['type_data'] == 'select')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									<p>{!! $berkas->{$c['field_name']} !!}</p>
								</div>
							@elseif ($c['type_data'] == 'upload multiple file')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									@if(!empty($row->{$c['field_name']}))
										@php
										{{
											$list_file = explode(", ",$row->{$c['field_name']});
											foreach($list_file as $f){
												if(is_image($f)){
													echo '<p><a target="_blank" href="'.url('upload/'.$f).'"><img src="'.url('upload/'.$f).'" style="width:200px"/></a></p>';
												} else {
													echo '<p><a target="_blank" href="'.url('upload/'.$f).'">'.$f.'</a></p>';
												}
											}
										}}
										@endphp
									@endif
								</div>
							@elseif ($c['type_data'] == 'upload file')
								<div class="form-group col-sm-6">
									{!! Form::label($c['field_name'], ucwords(str_replace("_"," ",$c['field_name'])).':') !!}
									@if(!empty($row->{$c['field_name']}))
										@php
										{{
											$f = $row->{$c['field_name']};
											if(is_image($f)){
												echo '<p><a target="_blank" href="'.url('upload/'.$f).'"><img src="'.url('upload/'.$f).'" style="width:200px"/></a></p>';
											} else {
												echo '<p><a class="btn btn-success" target="_blank" href="'.url('upload/'.$f).'">'.$f.' (Download)</a></p>';
											}
										}}
										@endphp
									@endif
								</div>
							@elseif ($c['type_data'] == 'Pilih Daftar Anggota')
								<div class="col-sm-12">
								<table class="table table-bordered">
								  <thead>
									<tr>
									  <th scope="col">ID</th>
									  <th scope="col">NIK</th>
									  <th scope="col">Nama</th>
									</tr>
								  </thead>
								  <tbody class="content-anggota">
									@if(!empty($c['daftar_user_array']))
										@foreach($c['daftar_user_array'] as $k)
											<tr class="row-{{$k->penduduk}}" ><th scope="row">{{$k->penduduk}}</th><td>{{$k->nik}}</td><td>{{$k->name}}</td></tr>
										@endforeach
									@endif
								  </tbody>
								  <tfoot class="foot-empty">
									@if(empty($c['daftar_user_array']))
										<tr>
										  <td colspan="5"><i>Saat ini belum ada data</i></td>
										</tr>
									@endif
								  </tfoot>
								</table>
								</div>
							@else
							
							@endif

						@endforeach
						<a href="{!! route('list', [$type]) !!}" class="btn btn-primary">Back</a>
					</div>
                </div>
            </div>
        </div>
</div>
@endsection
