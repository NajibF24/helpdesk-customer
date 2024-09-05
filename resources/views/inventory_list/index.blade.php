@extends('layouts.app')
@section('title', 'List '.ucfirst($module))
@section('content')

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

</style>

<div class="container-fluid">
    @include('flash::message')

    <div class="table-responsive" style="">
    <?php
        $identifier =  rand(pow(10, 12-1), pow(10, 12)-1);
        $id_table = "table-".$identifier;
    ?>
    <style>
    th {
            font-weight:600 !important;
    }
    </style>

        @include('flash::message')

        <table class="table table-striped table-bordered" id="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Serial Number</th>
                    <th>Material Code</th>
                    <th>Material Code Name</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Group</th>
                    <th>UoM</th>
                    <th>Store Location</th>
                    <th>Update Date</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready( function () {
    $('#main-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route($module.'.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'serial_number', name: 'serial_number' },
            { data: 'material_code.code', name: 'material_code.code', defaultContent: '' },
            { data: 'material_code.name', name: 'material_code.name', defaultContent: '' },
            { data: 'qty', name: 'qty' },
            { data: 'inventory_type.label', name: 'label_status', defaultContent: '',
                render: (data,type,row) => row.inventory_type ? row.inventory_type.label : '-'
            },
            { data: 'material_code.material_group.name', name: 'material_code.material_group.name', defaultContent: '' },
            { data: 'material_code.uom', name: 'material_code.uom', render: (data, type, row) => row.material_code.uom_label, defaultContent: '' },
            { data: 'store_location.name', name: 'store_location.name', defaultContent: '' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action', orderable: false, sortable: false },
        ]
	});
 });
</script>
@endsection
<style>
tfoot input {
	width:100%;
	border-radius: 2px;
	border: 1px solid #e2e7f1;
	box-shadow: none;

}
tfoot input:focus {

  border: 1px solid #e2e7f1;
}
</style>
