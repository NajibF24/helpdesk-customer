@extends('layouts.app')

@section('content')
<!--begin::Container-->
<div class="container">
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
                        <li class="nav-item d-flex col flex-grow-1 flex-shrink-0 mr-3 mb-3 mb-lg-0">
                            <a class="nav-link border-nabati py-10 d-flex flex-grow-1 rounded flex-column align-items-center" href="#tab_forms_widget_1">
                                <span class="nav-icon py-2 w-auto">
                                    <span class="svg-icon svg-icon-3x">
                                        <!--begin::Svg Icon | path:{{URL('/')}}/assets/media/svg/icons/Home/Library.svg-->
                                        <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <!-- Generator: Sketch 50.2 (55047) - http://www.bohemiancoding.com/sketch -->
                                            <title>Stockholm-icons / Home / Lamp1</title>
                                            <desc>Created with Sketch.</desc>
                                            <defs></defs>
                                            <g id="Stockholm-icons-/-Home-/-Lamp2" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect id="bound" x="0" y="0" width="50" height="50"></rect>
                                                <path d="M12,2 C12.5522847,2 13,2.44771525 13,3 L13,11 C13,11.5522847 12.5522847,12 12,12 C11.4477153,12 11,11.5522847 11,11 L11,3 C11,2.44771525 11.4477153,2 12,2 Z" id="Combined-Shape" fill="#000000" opacity="0.3"></path>
                                                <path d="M8.12601749,19 L15.8739825,19 C15.4299397,20.7252272 13.8638394,22 12,22 C10.1361606,22 8.57006028,20.7252272 8.12601749,19 Z" id="Combined-Shape" fill="#000000" opacity="0.3"></path>
                                                <path d="M12,8 L12,8 C16.9705627,8 21,12.0294373 21,17 L3,17 L3,17 C3,12.0294373 7.02943725,8 12,8 Z" id="Rectangle-98" fill="#000000"></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </span>
                                <span class="nav-text font-size-lg py-2 font-weight-bolder text-center">Browse Help Article</span>
                                <span class="nav-text font-size-lg py-2 text-center">Look Up FAQ to Fix Issues on Your Own</span>
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item d-flex col flex-grow-1 flex-shrink-0 mr-3 mb-3 mb-lg-0">
                            <a class="nav-link border-nabati py-10 d-flex flex-grow-1 rounded flex-column align-items-center" href="#tab_forms_widget_2">
                                <span class="nav-icon py-2 w-auto">
                                    <span class="svg-icon svg-icon-3x">
                                        <!--begin::Svg Icon | path:{{URL('/')}}/assets/media/svg/icons/Layout/Layout-4-blocks.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="50" height="50" />
                                                <rect fill="#000000" x="4" y="4" width="7" height="7" rx="1.5" />
                                                <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#000000" opacity="0.3" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </span>
                                <span class="nav-text font-size-lg py-2 font-weight-bolder text-center">Report an Issue</span>
                                <span class="nav-text font-size-lg py-2 text-center">Having Trouble ? Contact The Support Team</span>
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item d-flex col flex-grow-1 flex-shrink-0 mr-3 mb-3 mb-lg-0">
                            <a class="nav-link border-nabati py-10 d-flex flex-grow-1 rounded flex-column align-items-center" href="{{URL('/')}}/request-service">
                                <span class="nav-icon py-2 w-auto">
                                    <span class="svg-icon svg-icon-3x">
                                        <!--begin::Svg Icon | path:{{URL('/')}}/assets/media/svg/icons/Media/Movie-Lane2.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g id="Stockholm-icons-/-Shopping-/-Cart1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect id="bound" x="0" y="0" width="50" height="50"></rect>
                                                <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" id="Combined-Shape" fill="#000000" opacity="0.3"></path>
                                                <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" id="Combined-Shape" fill="#000000"></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </span>
                                <span class="nav-text font-size-lg py-2 font-weight-bolder text-center">Request a Service</span>
                                <span class="nav-text font-size-lg py-2 text-center">Raise a Request for a new device, software or service</span>
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item d-flex col flex-grow-1 flex-shrink-0 mr-3 mb-3 mb-lg-0">
                            <a class="nav-link border-nabati py-10 d-flex flex-grow-1 rounded flex-column align-items-center" href="#tab_forms_widget_4">
                                <span class="nav-icon py-2 w-auto">
                                    <span class="svg-icon svg-icon-3x">
                                        <!--begin::Svg Icon | path:{{URL('/')}}/assets/media/svg/icons/Media/Equalizer.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="50" height="50" />
                                                <rect fill="#000000" opacity="0.3" x="13" y="4" width="3" height="16" rx="1.5" />
                                                <rect fill="#000000" x="8" y="9" width="3" height="11" rx="1.5" />
                                                <rect fill="#000000" x="18" y="11" width="3" height="9" rx="1.5" />
                                                <rect fill="#000000" x="3" y="13" width="3" height="7" rx="1.5" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </span>
                                <span class="nav-text font-size-lg py-2 font-weight-bolder text-center">Approve Request</span>
                                <span class="nav-text font-size-lg py-2 text-center">View all requests awaiting my approval</span>
                            </a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="nav-item d-flex col flex-grow-1 flex-shrink-0 mr-3 mb-3 mb-lg-0">
                            <a class="nav-link border-nabati py-10 d-flex flex-grow-1 rounded flex-column align-items-center" href="{{URL('/')}}/ticket-monitoring">
                                <span class="nav-icon py-2 w-auto">
                                    <span class="svg-icon svg-icon-3x">
                                        <!--begin::Svg Icon | path:{{URL('/')}}/assets/media/svg/icons/General/Shield-check.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g id="Stockholm-icons-/-Shopping-/-Ticket" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect id="bound" x="0" y="0" width="50" height="50"></rect>
                                                <path d="M3,10.0500091 L3,8 C3,7.44771525 3.44771525,7 4,7 L9,7 L9,9 C9,9.55228475 9.44771525,10 10,10 C10.5522847,10 11,9.55228475 11,9 L11,7 L21,7 C21.5522847,7 22,7.44771525 22,8 L22,10.0500091 C20.8588798,10.2816442 20,11.290521 20,12.5 C20,13.709479 20.8588798,14.7183558 22,14.9499909 L22,17 C22,17.5522847 21.5522847,18 21,18 L11,18 L11,16 C11,15.4477153 10.5522847,15 10,15 C9.44771525,15 9,15.4477153 9,16 L9,18 L4,18 C3.44771525,18 3,17.5522847 3,17 L3,14.9499909 C4.14112016,14.7183558 5,13.709479 5,12.5 C5,11.290521 4.14112016,10.2816442 3,10.0500091 Z M10,11 C9.44771525,11 9,11.4477153 9,12 L9,13 C9,13.5522847 9.44771525,14 10,14 C10.5522847,14 11,13.5522847 11,13 L11,12 C11,11.4477153 10.5522847,11 10,11 Z" id="Combined-Shape-Copy" fill="#000000" opacity="0.3" transform="translate(12.500000, 12.500000) rotate(-45.000000) translate(-12.500000, -12.500000) "></path>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                </span>
                                <span class="nav-text font-size-lg py-2 font-weight-bolder text-center">Your Ticket</span>
                                <span class="nav-text font-size-lg py-2 text-center">Monitoring your submit ticket</span>
                            </a>
                        </li>
                        <!--end::Item-->
                    </ul>
                    <!--end::Nav Tabs-->
                    <!--begin::Nav Content-->
                    <div class="tab-content m-0 p-0">
                        <div class="tab-pane active" id="forms_widget_tab_1" role="tabpanel"></div>
                        <div class="tab-pane" id="forms_widget_tab_2" role="tabpanel"></div>
                        <div class="tab-pane" id="forms_widget_tab_3" role="tabpanel"></div>
                        <div class="tab-pane" id="forms_widget_tab_4" role="tabpanel"></div>
                        <div class="tab-pane" id="forms_widget_tab_6" role="tabpanel"></div>
                    </div>
                    <!--end::Nav Content-->
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
