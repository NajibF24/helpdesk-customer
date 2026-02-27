
@extends('layouts.app')

@section('content')



<style>
#kt_subheader {
	display:none;
}	
#kt_content {
	padding-top: 0;
	margin-top: -45px;
    margin-left: 5px;
}
.ui-timepicker-standard {
	z-index: 100000 !important;
}
</style>
{!! Form::open(['route' => ['store', $type], 'files' => true]) !!}
<div class="card " style="">
  <div class="card-header" style="padding:0.7rem 1.7rem;background:#f5f7f9;">
		<span style="font-weight: 500;font-size: 15px;line-height: 2;">Add New</span>
		<button type="submit" class="btn btn-dark btn-sm blue-black" style="width: 70px;float:right;margin-left:10px">Create</button>
		<a href="{!! route('list', [$type]) !!}" class="btn btn-sm btn-outline-dark btn-white-line"  style="width: 70px;float:right;margin-left:10px">Cancel</a>
  </div>
  <div class="card-body">
		

		
			@include('adminlte-templates::common.errors')
			<?php 
			$table = $type;
			$organization = DB::table('organization_level')->pluck('name','name')->toArray();
			if(in_array($table,$organization)){
			?>
				<ul class="nav nav-pills nav-justified" style="margin-bottom: 10px;">
				  @foreach($organization as $org_level)
					  <li class="nav-item">
						<a class="nav-link {{$org_level==$table?'active':''}}" href="{{URL('/').'/list/'.$org_level}}">{{$org_level}}</a>
					  </li>				
				  @endforeach
				</ul>
			<?php } ?>
			
			@include('crudmodal.menutab')
					<div class="row content-tab-home mt-4">
							
					@include('crudmodal.create_fields')
					<!-- Submit Field -->
				
				
					</div>
		
	</div>

	<div class="">

	</div>
</div>
{!! Form::close() !!}

@endsection
@section('js')
	


@endsection
