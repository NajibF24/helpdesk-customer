@extends('layouts.app')

@section('content')
<style>
.table-responsive tr {
	cursor: pointer;
}
#table_ticket {
	margin-top: 0 !important;
}
#kt_wrapper {
	background: #f0f7fd;
}
.label.label-inline.label-lg {
    padding: 0.4rem 0.75rem;
    height: auto;
}
.subheader {
	display:block !important;
	margin-left: -6px;
}
tbody tr td:last-child {
  display:none !important;
}
</style>

<style>
.content {
	padding-top:0 !important;
}
table.dataTable thead th, table.dataTable thead td {
    border-bottom: none !important;
}
th,td {
	vertical-align: middle !important;
}
.table th {
	line-height: 2 !important;
}
.table-responsive {
    min-height: .01%;
    overflow-x: scroll;
}
.table {
	background:#fff;
	
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: #ffffff;
}
thead {
	background:#f5f7f9;
}
tbody tr:first-child td {
	border-top:1px solid #cfd7df !important;
}
tr th {
	border-top:1px solid #cfd7df !important;
}
tbody tr td {
	border-right:none !important
}
tr {
	border-right:1px solid #cfd7df !important;
	border-left:1px solid #cfd7df !important;
}
tr td:first-child,tr th:first-child {
	border-left:1px solid #cfd7df !important;
}
tr th {
	border-right:1px solid #cfd7df !important;
	white-space:nowrap;
}
/*
.dataTables_wrapper .row:first-child {
	display:none;
}
*/
.custom-select {
    border-radius: 0.3rem;
}
div.dataTables_wrapper div.dataTables_filter input {
    max-width: 190px;
}
.dataTables_wrapper .dataTable {
    width: unset !important;
}
tr td {
	white-space:nowrap;
}
</style>
<div class="card card-custom gutter-b" style="min-width: 100%;box-shadow: none;margin-top: 0px;">
    <!--begin::Form-->
    {!! Form::open(['route' => ['report.store']]) !!}
        <div class="card-body" style="background: #f3f6f9;">
            <div class="form-group row">
                <div class="col-lg-4">
                    <label>Date</label>
                    <div class='input-group' id='kt_daterangepicker_2'>
                        <input type='text' class="form-control" name="date" readonly="readonly" value="{{$date}}"/>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                    </div>
                    <span class="form-text text-muted">Select a date range</span>
                </div>
                <?php
                $status_distinct = DB::table('ticket')
                ->select(DB::raw('distinct(status)'))
                ->get();

                $type_distinct = DB::table('ticket')
                ->select(DB::raw('distinct(finalclass)'))
                ->where('finalclass', '!=', 'problem_request')
                ->get();
                ?>
                <div class="col-lg-4">
                    <label>Status</label>
                    <select class="form-control select2" id="kt_select2_status" name="status[]" multiple="multiple">
                        @foreach($status_distinct as $status)
                        <option value="{{$status->status}}" {{in_array($status->status, $statuses) ? "selected" : "" }}>{{$status->status}}</option>
                        @endforeach
                    </select>
                    <span class="form-text text-muted">Select Statuses</span>
                </div>
                <div class="col-lg-4">
                    <label>Type</label>
                    <select class="form-control select2" id="kt_select2_type" name="type[]" multiple="multiple">
                        @foreach($type_distinct as $type)
                        <option value="{{$type->finalclass}}" {{in_array($type->finalclass, $types) ? "selected" : "" }}>{{str_replace('Management', 'Request', ucwords(str_replace("_", " ", $type->finalclass)))}}</option>
                        @endforeach
                    </select>
                    <span class="form-text text-muted">Select Types</span>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-4">
                    <button type="submit" class="btn btn-primary mr-2">Apply</button>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
    <!--end::Form-->
	<div class="card-body" style="background: #f3f6f9;padding: 0.2rem 0.7rem;">    
        @include('flash::message')

        <div class="table-responsive pl-3 pr-3" style="overflow-x:scroll">
            <style>
            th {
                    font-weight:600 !important;
            }
            </style>
            <!--begin: Datatable-->
            <table class="table table-striped table-bordered" id="table_ticket">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Ticket Number</th>
                        <th>Request Type</th>
                        <th>Request Name</th>
                        <th>Title</th>
                        <th>Service</th>
                        <th>Service Category</th>
                        <th>Status</th>
                        <th>Related Ticket</th>
                        <th>Request Date</th>
                        <th>Request By</th>
                        <th style="display:none">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; 
                    
                    ?>
                    @foreach ($tickets as $ticket)
                    <tr>
                        <td>{{ $no }}</td>
                        <td>{{ $ticket->ref }}</td>
                        <td><?php if ($ticket->finalclass == "service_request") {
                                    echo "Service";
                                } elseif ($ticket->finalclass == "problem_request") {
                                    echo "Problem";
                                } else {
                                    echo "Incident";
                                }
                                ?></td>
                        <td><?php
                                $service = DB::table('service')->where('id', $ticket->service_id)->first();
                            ?>{{ $service->name  ?? '-'}}
                        </td>
                        <td>{{ $ticket->title }}</td>
                        <td><?php
                                $service = DB::table('service')->where('id', $ticket->service_id)->first();
                            ?>{{ $service->name  ?? '-'}}</td>
                        <td><?php
                                $category = DB::table('service_category')->where('id', $ticket->servicesubcategory_id)->first();
                            ?>{{ isset($category->name) ? $category->name : '-' }}</td>
                        <td><?= statusHtml($ticket->status) ?></td>
                        <td>{{ $ticket->related_ticket ?? '-' }}</td>
                        <td>{{ date('d M Y', strtotime($ticket->created_at)) }}</td>
                        <td><?php
                                $name = DB::table('users')->where('id', $ticket->created_by)->first();
                            ?>{{ $name->name }}</td>
                        <td style="display:none">{{URL('/')}}/approve-request/{{ $ticket->token }}</td>
                    </tr>
                    <?php $no++;?>
                    @endforeach
                </tbody>
            </table>
            <!--end: Datatable-->
        </div>
	</div>
</div>

<script>
$(document).ready( function () {
    $('#table_ticket').DataTable({
        dom: 'lBfrtip',
        buttons: [
            { extend: 'excel', text: 'Export Data', exportOptions: {columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ]} }
        ],
        fixedHeader:true,
        scrollX: true,
    });
});
</script>
@endsection
