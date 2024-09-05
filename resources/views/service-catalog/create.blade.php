@extends('layouts.app')

@section('content')
<style>
#kt_wrapper {
	background: #f0f7fd;
}
</style>
<!--begin::Container-->
<div class="container-fluid">
    <!--begin::Card-->
    <div class="card card-custom gutter-b example example-compact">
        <div class="card-header">
            <h3 class="card-title">Create Service {{ $service->name }}</h3>
        </div>
        <!--begin::Form-->
        {!! Form::open(['route' => ['service-catalog.store'], 'files' => true, 'id' => 'form_submit']) !!}
			@include('service_incident_form')
        {!! Form::close() !!}
        <!--end::Form-->
    </div>
    <!--end::Card-->
</div>
<!--end::Container-->
@include('service_incident_script')

@endsection
