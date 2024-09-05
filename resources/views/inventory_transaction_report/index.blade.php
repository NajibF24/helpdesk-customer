@extends('layouts.app')
@section('title', 'Inventory Report')
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
    <ul class="nav nav-pills p-4">
        <li class="nav-item">
          <a class="nav-link {{ request()->type == 'goods_issue' ? 'active' : '' }}" href="{{url('/inventory_transaction_report')}}?type=goods_issue">Goods Issue</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->type == 'goods_receive' ? 'active' : '' }}" href="{{url('/inventory_transaction_report')}}?type=goods_receive">Goods Receive</a>
        </li>
    </ul>
    
    <div class="card my-3 ms-5 me-5">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="status">Statuses</label><br>
                    <select name="status" id="status" class="select2 w-100" multiple>
                        @foreach ($inventoryTypes->where('transaction_type', request()->type) as $item)
                            <option value="{{ $item->id }}">{{ $item->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="material_group_id">Material Group</label><br>
                    <select name="material_group_id" id="select-material-group" class="select2 w-100" multiple>
                        @foreach ($materialGroups as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="material_code_id">Material Code</label><br>
                    <select name="material_code_id" id="select-material-code" class="select2 w-100" multiple>
                        @foreach ($materialCodes as $item)
                            <option value="{{ $item->id }}">{{ $item->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="warehouse_id">Warehouses</label><br>
                    <select name="warehouse_id" id="select-warehouse" class="select2 w-100" multiple>
                        @foreach ($warehouses as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="button-group">
                <button class="btn btn-primary" id="apply-filter">Apply</button>
                <button class="btn btn-success" id="export">Export</button>
            </div>
        </div>
    </div>
    
    @include('flash::message')
    
    <div class="table-responsive" style="">
        @include('flash::message')

        <table class="table table-striped table-responsive table-bordered" id="main-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Transaction No</th>
                    <th>Serial Number</th>
                    <th>Material Code</th>
                    <th>Material Code Name</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Group</th>
                    <th>PO Number</th>
                    <th>UoM</th>
                    <th>Material Tag</th>
                    <th>Brand</th>
                    <th>Warehouse</th>
                    <th>Store Location</th>
                    <th>Material Remarks</th>
                    <th>Update Date</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready( function () {
    initDatatable(`?status=&material_group=&material_code=&warehouse=&type={{request()->type}}`);

    $('#apply-filter').click(function() {
        const status = $('#status').val().join(',')
        const materialGroup = $('#select-material-group').val().join(',')
        const materialCode = $('#select-material-code').val().join(',')
        const warehouse = $('#select-warehouse').val().join(',')

        initDatatable(`?status=${status}&material_group=${materialGroup}&material_code=${materialCode}&warehouse=${warehouse}&type={{request()->type}}`);
    })

    $('#export').click(function() {
        const status = $('#status').val().join(',')
        const materialGroup = $('#select-material-group').val().join(',')
        const materialCode = $('#select-material-code').val().join(',')
        const warehouse = $('#select-warehouse').val().join(',')

        window.location.href = `{{route('inventory_transaction_report.export')}}?status=${status}&material_group=${materialGroup}&material_code=${materialCode}&warehouse=${warehouse}&type={{request()->type}}`
    })
 });

function initDatatable(filters = '') {
    $('#main-table').DataTable({
        processing: true,
        serverSide: true,
        bDestroy: true,
        ajax: "{{ route('inventory_transaction_report.index') }}"+filters,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'transaction_no', name: 'transaction_no' },
            { data: 'serial_no', name: 'serial_no' },
            { data: 'material_code.code', name: 'material_code.code', searchable: false, orderable: false },
            { data: 'material_code.name', name: 'material_code.name', searchable: false, orderable: false },
            { data: 'qty', name: 'qty', render: (data,type,row) => '1' },
            { data: 'status', name: 'status' },
            { data: 'material_code.material_group.name', name: 'material_code.material_group.name', searchable: false, orderable: false },
            { data: 'po_no', name: 'po_no' },
            { data: 'material_code.uom', name: 'material_code.uom', render: (data, type, row) => row.material_code.uom_label, searchable: false, orderable: false },
            { data: 'material_tag2', name: 'material_tag2' },
            { data: 'brand_name', name: 'brand_name' },
            { data: 'store_location.warehouse.name', name: 'store_location.warehouse.name', searchable: false, orderable: false, defaultContent: '' },
            { data: 'store_location.name', name: 'store_location.name', searchable: false, orderable: false, defaultContent: '' },
            { data: 'material_remarks', name: 'material_remarks', searchable: false, orderable: false },
            { data: 'updated_at', name: 'updated_at' },
        ]
	})
 }
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
