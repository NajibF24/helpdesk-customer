@extends('layouts.app')

@section('content')

<!--begin::Container-->
<div class="container-fluid">
    <!--begin::Row-->
    <div class="row">
        <div class="col-xl-12">
            <!--begin::Nav Panel Widget 1-->
            <div class="card card-custom gutter-b card-stretch">
                <!--begin::Body-->
                <div class="card-body p-0">
                    <!--begin::Nav Tabs-->
                    <ul class="dashboard-tabs nav nav-pills nav-danger row row-paddingless m-0 p-0 flex-column flex-sm-row" role="tablist">
                        <!--begin::Item-->
                        @foreach ($organizations as $key => $organization)
                        
                        <li class="nav-item d-flex col flex-grow-1 flex-shrink-0 mr-3 mb-3 mb-lg-0">
                            <a class="nav-link border-nabati py-10 d-flex flex-grow-1 rounded flex-column align-items-center" href="{{URL('/')}}/request-service/service-catalog/{{ $organization->id }}">
                                <span class="nav-icon py-2 w-auto">
                                    <span class="svg-icon svg-icon-3x">
                                        <!--begin::Svg Icon | path:{{URL('/')}}/assets/media/svg/icons/Home/Library.svg-->
                                        <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
                                            <title>Stockholm-icons / Home / Lamp1</title>
                                            <desc>Created with Sketch.</desc>
                                            <defs></defs>
                                            <g id="Stockholm-icons-/-Home-/-Lamp2" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect id="bound" x="0" y="0" width="24" height="24"></rect>
                                                <path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,11 C13,11.5522847 12.5522847,12 12,12 C11.4477153,12 11,11.5522847 11,11 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" id="Combined-Shape" fill="#000000" opacity="0.3"></path>
                                                <path d="M8.12601749,19 L15.8739825,19 C15.4299397,20.7252272 13.8638394,22 12,22 C10.1361606,22 8.57006028,20.7252272 8.12601749,19 Z" id="Combined-Shape" fill="#000000" opacity="0.3"></path>
                                                <path d="M12,8 L12,8 C16.9705627,8 21,12.0294373 21,17 L3,17 L3,17 C3,12.0294373 7.02943725,8 12,8 Z" id="Rectangle-98" fill="#000000"></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </span>
                                <span class="nav-text font-size-lg py-2 font-weight-bold text-center">{{ $organization->name }}</span>
                            </a>
                        </li>
                        @endforeach
                        <!--end::Item-->
                    </ul>
                    <!--end::Nav Tabs-->
                </div>
                <!--end::Body-->
            </div>
            <!--begin::Nav Panel Widget 1-->
        </div>
    </div>
    <!--end::Row-->
</div>
<!--end::Container-->

@endsection
