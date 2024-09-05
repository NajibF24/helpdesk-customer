@extends('layouts.app')
@section('title','Add '.$module)
@section('content')
<style>
.ui-timepicker-standard {
	z-index: 100000 !important;
}
</style>
<style>
#kt_subheader {
	display:none;
}	
#kt_content {
	padding-top: 0;
	margin-top: -45px;
    margin-left: 5px;
}
</style>
<div class="container-fluid my-5">
	{!! Form::open(['route' => $module.'.store', 'enctype' => 'multipart/form-data', 'id' => 'inv-management-form']) !!}
	<div class="card "  style="">
		<div class="card-header" style="padding:0.7rem 1.7rem;background:#f5f7f9;">
			<span style="font-weight: 500;font-size: 15px;line-height: 2;">{{ucfirst($title)}}</span>
			<button type="button" class="btn btn-dark btn-sm blue-black" style="width: 70px;float:right;margin-left:10px" id="btn-submit">Create</button>
			{{-- <button class="btn btn-warning btn-sm" style="width: 70px;float:right;margin-left:10px" id="btn-submit-draft">Draft</button> --}}
			<a href="{!! route($module.'.index') !!}" class="btn btn-sm btn-outline-dark btn-white-line"  style="width: 70px;float:right;margin-left:10px">Cancel</a>
		</div>
		<div class="card-body">

				@include('flash::message')
				@include('adminlte-templates::common.errors')
				{{-- @include('crudmodal.menutab') --}}
				
				<div class="row content-tab-home mt-4">

				@include($module.'.fields')

				</div>
		</div>
	</div>
	<div class="card mt-4">
		<div class="card-body">
			@include($module.'.material-items', compact('users'))
		</div>
	</div>
</div>
{!! Form::close() !!}
@endsection

