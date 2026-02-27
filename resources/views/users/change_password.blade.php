@extends('layouts.app')
@section('title','Change Password')
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
{!! Form::model($users, ['route' => ['change_password_update'], 'method' => 'post', 'style'=>'width: 100%;padding: 10px;']) !!}
<div class="card "  style="">
	<div class="card-header" style="padding:0.7rem 1.7rem;background:#f5f7f9;">
		<span style="font-weight: 500;font-size: 15px;line-height: 2;">Change Password</span>
		<button type="submit" class="btn btn-dark btn-sm blue-black" style="width: 70px;float:right;margin-left:10px">Save</button>
	</div>
	<div class="card-body">
			@include('flash::message')
			@include('adminlte-templates::common.errors')                                
			<div class="row">

					<div class="col-md-6 form-group mb-3 has-feedback{{ $errors->has('old_password') ? ' has-error' : '' }}">
						{!! Form::label('old_password', 'Old Password:') !!}
						<!-- {!! Form::text('password', null, ['class' => 'form-control','type' => 'password']) !!} -->
						<input type="password" class="form-control" name="old_password" placeholder="Password">
						@if ($errors->has('old_password'))
							<span class="help-block">
							<strong>{{ $errors->first('old_password') }}</strong>
							</span>
						@endif
					</div>                                

					<div class="col-md-6 form-group mb-3 has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
						{!! Form::label('password', 'New Password:') !!}
						<!-- {!! Form::text('password', null, ['class' => 'form-control','type' => 'password']) !!} -->
						<input type="password" class="form-control" name="password" placeholder="Password">
						@if ($errors->has('password'))
							<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
							</span>
						@endif
					</div>
					<div class="col-md-6 form-group mb-3 has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
						{!! Form::label('password', 'Confirm Password:') !!}
						<input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password">
						@if ($errors->has('password_confirmation'))
							<span class="help-block">
							<strong>{{ $errors->first('password_confirmation') }}</strong>
							</span>
						@endif
					</div>



					<script>
						$(document).ready(function () {
							//$("select option:first-child").remove();
							//$(".select2").select2();
						});
					</script>



			</div>

	</div>
	<div class="card-footer">
	</div>
</div>
{!! Form::close() !!}

@endsection

