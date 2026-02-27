@extends('layouts.app')

@section('content')

<?php
use App\Helpers\TicketStatusHelper;
use Illuminate\Support\Facades\Auth;
 ?>

<style>
.note-editable {
	min-height:100px !important;
}
</style>
<style>
#kt_wrapper {
	background: #f0f7fd;
}
</style>


<?php

$email = "";

?>
<!--begin::Container-->
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row d-flex">
        <div class="col-xl-8">
			@include('goods_receive.content_left')
        </div>
        <div class="col-xl-4 mt-8 mt-md-0">

            <div class="card card-custom gutter-b h-full">
                <div class="card-header align-items-center border-0 mt-4">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="font-weight-bolder text-dark">Goods Receive Status</span>
                    </h3>
                </div>
                <div class="card-body pt-2">
                    <div class="detail-item">
                        <span style="font-weight: 600" class="text-dark-75 text-hover-primary font-size-lg mb-1">Status</span>
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
                            {{ucwords(str_replace('_', ' ', $detail->status))}} 
                        </p>
                        <br>
                    </div>
                    <div class="detail-item">
                        <span style="font-weight: 600" class="text-dark-75 text-hover-primary font-size-lg mb-1">Request No</span>
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
                            {{ucwords($detail->code)}}
                        </p>
                        <br>
                    </div>
                    <div class="detail-item">
                        <span style="font-weight: 600" class="text-dark-75 text-hover-primary font-size-lg mb-1">Service Catalog</span>
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
                        {{ucwords(@$detail->requestManagement->service->name)}} 
                        </p>
                        <br>
                    </div>
                    <div class="detail-item">
                        <span style="font-weight: 600" class="text-dark-75 text-hover-primary font-size-lg mb-1">Next Approver</span>
                        <p class="text-dark-50 m-0 pt-2 font-weight-normal">
                        {{$detail->nextApprover ? $detail->nextApprover->name : '-'}}
                        </p>
                    </div>
                </div>
            </div>

			@include('goods_receive.case_journey_content')

        </div>

        <div class="col-12 mt-8">
            @if ($detail->goods_issue)
			    @include('goods_receive.material_list')
            @else
                @include('goods_receive.material_list_2')
            @endif
            <div class="messages">
                @include('goods_issue.comments', ['type' => 'goods_receive'])
            </div>
        </div>
    </div>
    <!--end::Row-->
</div>
<!--end::Container-->

<link href="{{URL('/')}}/vendor/summernote/summernote.min.css" rel="stylesheet">
<script src="{{URL('/')}}/vendor/summernote/summernote.min.js"></script>
@endsection
