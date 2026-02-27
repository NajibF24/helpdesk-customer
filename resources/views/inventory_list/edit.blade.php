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
{!! Form::open(['route' => [$module.'.update', $detail->id], 'method' => 'put', 'enctype' => 'multipart/form-data', 'disabled' => 'disabled']) !!}
<div class="container-fluid my-5">
    <div class="card">
        <div class="card-header" style="padding:0.7rem 1.7rem;background:#f5f7f9;">
            <span style="font-weight: 500;font-size: 15px;line-height: 2;">Edit {{ucfirst($title)}}</span>
            <a href="{!! route($module.'.index') !!}" class="btn btn-sm btn-outline-dark btn-white-line"  style="width: 70px;float:right;margin-left:10px">Cancel</a>
        </div>
        <div class="card-body">
    
                @include('flash::message')
                @include('adminlte-templates::common.errors')
                {{-- @include('crudmodal.menutab') --}}
                
                <div class="row content-tab-home mt-4">
    
                    <div class="col-md-6 form-group mb-3">
                        {!! label_required('Name') !!}
                        {!! Form::text('name', $detail->name, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="form-group col-sm-6">
                        {!! label_required('PO Number') !!}
                        {!! Form::text('po_number', $detail->po_number, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                                    
                    <div class="form-group col-sm-6">
                        {!! label_required('Material Code') !!}
                        {!! Form::text('material_code', @$detail->materialCode->code, ['class' => 'form-control', 'id' => 'select-material-code', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="form-group col-sm-6">
                        {!! Form::label('material_code_name', 'Material Code Name') !!}
                        {!! Form::text('material_code_name', @$detail->materialCode->name, ['class' => 'form-control', 'disabled', 'id' => 'material-code-name', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="form-group col-sm-6">
                        {!! Form::label('group', 'Group') !!}
                        {!! Form::text('group', @$detail->materialCode->materialGroup->name, ['class' => 'form-control', 'disabled', 'id' => 'group-name', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="form-group col-sm-6">
                        {!! Form::label('uom', 'Uom') !!}
                    {!! Form::text('uom', @$detail->materialCode->uom_label, ['class' => 'form-control', 'disabled', 'id' => 'uom', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="col-md-6 form-group mb-3">
                        {!! label_required('Serial Number') !!}
                        {!! Form::text('serial_number', $detail->serial_number, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="form-group col-sm-6">
                        {!! label_required('Brand') !!}
                        {!! Form::text('brand', @$detail->brand->name, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="col-md-6 form-group mb-3">
                        {!! label_required('Material Tag') !!}
                        {!! Form::text('material_tag', $detail->material_tag, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
    
                    <div class="col-md-6 form-group mb-3">
                        {!! Form::label('image', 'Image') !!}
                        <img id="image-preview" src="{{ asset($detail->image) }}" style="width: 400px"/>
                    </div>   
                                    
                    <div class="form-group col-sm-6">
                        {!! Form::label('description', 'Description') !!}
                        {!! Form::textarea('description', $detail->description, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    
                    <div class="form-group col-sm-6">
                        {!! label_required('Specification') !!}
                        {!! Form::textarea('specification', $detail->specification, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                    </div>
                    
                </div>
        </div>
        <div class="card-footer">
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-body">
            <div class="row">
                <div class="form-group col-sm-6">
                    {!! label_required('Select Status Inventory') !!}
                    {!! Form::select('inventory_type_id', ['' => 'Please select'] + $inventoryTypes, $detail->inventory_type_id, ['class' => 'select2 form-control']) !!}
                </div>
    
                <div class="form-group col-sm-6">
                    {!! label_required('Select Warehouse') !!}
                    {!! Form::select('', ['' => 'Please select'] + $warehouses, $detail->storeLocation->warehouse_id, ['class' => 'select2 form-control', 'id' => 'select-warehouse']) !!}
                </div>
    
                <div class="col-md-6 form-group">
                    {!! label_required('Qty') !!}
                    {!! Form::number('qty', $detail->qty, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                </div>
    
                <div class="form-group col-sm-6">
                    {!! label_required('Store Location') !!}
                    <select name="store_location_id" id="select-store-loc" class="select form-control">
                        @if(isset($detail))
                            <option value="{{ @$detail->store_location_id }}">{{ @$detail->storeLocation->name }}</option>
                        @endif
                    </select>
                </div>
    
                <div class="form-group col-sm-6">
                    {!! Form::label('remarks', 'Remarks') !!}
                    {!! Form::textarea('remarks', $detail->description, ['class' => 'form-control']) !!}
                </div>
    
                <div class="col-md-6 form-group mb-3">
                    {!! Form::label('document', 'Document') !!}
                    {!! Form::file('document') !!}
                    <br>
                    @if($detail->document)
                        <a href="{{asset($detail->document)}}" download>Download Document</a>
                    @endif
                </div> 
    
            </div>
    
            <a href="{!! route($module.'.index') !!}" class="btn btn-sm btn-outline-dark btn-white-line"  style="width: 70px;float:right;margin-left:10px">Cancel</a>
            <button type="submit" class="btn btn-success btn-sm" style="width: 70px;float:right;margin-left:10px">Save</button>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-body">
            <h3>History</h3>
            <br>
            <table class="table table-striped table-bordered" id="main-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Document No</th>
                        <th>Request By</th>
                        <th>Status</th>
                        <th>Period</th>
                        <th>Assigned PIC</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
{!! Form::close() !!}
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#main-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($module.'.edit', $detail->id) }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'date', name: 'date' },
                { data: 'category', name: 'category', searchable: false, orderable: false },
                { data: 'document_no', name: 'document_no', searchable: false, orderable: false },
                { data: 'request_by', name: 'request_by', searchable: false, orderable: false },
                { data: 'status_return', name: 'status_return' },
                { data: 'goods_issues.created_at', name: 'goods_issues.created_at', render: (data, type, row) => row.period },
                { data: 'pic.name', name: 'pic.name', render: (data, type, row) => row.pic?.name ?? '' },
                { data: 'remarks', name: 'remarks' },
            ]
        });

        setTimeout(function() {
            $("select").select2();
        }, 100);

        const isEdit = '{{ isset($detail) }}'

        $('#select-warehouse').change(function() {
            const warehouseId = $(this).val()

            $('#select-store-loc').select2().empty()

            $('#select-store-loc').select2({
                allowClear: true,
                placeholder: "Please Select",
                 ajax: {
                    url: "{{route('material.store-location-list', 'WAREHOUSE_ID')}}".replace('WAREHOUSE_ID', warehouseId),
                    dataType: "json",
                    delay: 250,
                    processResults: function (response) {
                        return {
                            results: $.map(response.data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                };
                            }),
                            pagination: {
                            }
                        };
                    }
                },
            })
        })

        if(isEdit == 1) {
            $('#select-material-code').change()
        }

        $('#image').change(function() {
            const file = $(this).prop('files')[0]
            if (file) {
                $('#image-preview').attr('src', URL.createObjectURL(file))
            }
        })

        // if($('#image').length) {

        // }
    });
</script>
@endsection