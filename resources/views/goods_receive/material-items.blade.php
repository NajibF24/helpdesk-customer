<div class="row">
    <div class="col-12 table-responsive">
        {{-- <button class="btn btn-dark btn-sm blue-black" type="button" data-toggle="modal" data-target="#materialListModal">Add Material</button> --}}
        <button class="btn btn-dark btn-sm blue-black" id="btn-add-material" type="button" data-toggle="modal" data-target="#createMaterialModal">Add New Material</button>
        <table class="table table-bordered mt-2" id="dynamic-table">
            <thead>
                <tr>
                    <th>Material Name</th>
                    <th>PO Number</th>
                    <th>Serial Number</th>
                    <th>Material Tag</th>
                    <th>Qty</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="material-list-body"></tbody>
        </table>
    </div>
</div>


@push('modal')
<div class="custom-modal fade" id="createMaterialModal" tabindex="-1" aria-labelledby="createMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="min-width: 1200px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createMaterialModalLabel">Create Material</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body row" id="material-form">
            <div class="col-md-6 form-group mb-3">
                {!! label_required('Name') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'required' => true]) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! label_required('PO Number') !!}
                {!! Form::text('po_number', null, ['class' => 'form-control', 'required' => true]) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! label_required('Select Warehouse') !!}
                {!! Form::select('', ['' => 'Please select'] + $warehouses, null, ['class' => 'select form-control', 'id' => 'select-warehouse', 'style' => 'width: 100%', 'required' => true]) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! label_required('Store Location') !!}
                <select name="store_location_id" id="select-store-loc" class="select form-control" required>
                </select>
            </div>
            
            <div class="form-group col-sm-6">
                {!! label_required('Material Code') !!}
                {!! Form::select('material_code_id', ['' => 'Please select'] + $materialCodes, null, ['class' => 'select form-control', 'id' => 'select-material-code', 'style' => 'width: 100%', 'required' => true]) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! Form::label('material_code_name', 'Material Code Name') !!}
                {!! Form::text('material_code_name', null, ['class' => 'form-control', 'disabled', 'id' => 'material-code-name']) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! Form::label('group', 'Group') !!}
                {!! Form::text('group', null, ['class' => 'form-control', 'disabled', 'id' => 'group-name']) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! Form::label('uom', 'Uom') !!}
                {!! Form::text('uom', null, ['class' => 'form-control', 'disabled', 'id' => 'uom']) !!}
            </div>
            
            <div class="col-md-6 form-group mb-3">
                {!! label_required('Serial Number') !!}
                {!! Form::text('serial_number', null, ['class' => 'form-control', 'required' => true]) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! label_required('Brand') !!}
                {!! Form::select('brand_id', ['' => 'Please select'] + $brands, null, ['class' => 'select2 form-control', 'id' => 'select-brand', 'style' => 'width: 100%', 'required' => true]) !!}
            </div>
            
            <div class="col-md-6 form-group mb-3">
                {!! label_required('Material Tag') !!}
                {!! Form::text('material_tag', null, ['class' => 'form-control', 'required' => true]) !!}
            </div>
            
            <div class="col-md-6 form-group mb-3">
                {!! label_required('Qty') !!}
                {!! Form::number('qty', null, ['class' => 'form-control', 'required' => true]) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! Form::label('material_description', 'Description') !!}
                {!! Form::textarea('material_description', null, ['class' => 'form-control']) !!}
            </div>
            
            <div class="form-group col-sm-6">
                {!! label_required('Specification') !!}
                {!! Form::textarea('specification', null, ['class' => 'form-control', 'required' => true]) !!}
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="btn-save-material">Save</button>
          </div>
      </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#select-material-code').select2({
            dropdownParent: $('#createMaterialModal')
        });

        $('#select-warehouse').select2({
            dropdownParent: $('#createMaterialModal')
        });
        $('#select-brand').select2({

            dropdownParent: $('#createMaterialModal')
        });

        let i = 1;
        $('#btn-save-material').click(function() {
            var formValid = true;
            var emptyField = '';
            // Iterate through each required field
            $("#material-form :input[required]").each(function() {
                // Check if the value is empty for text, email, textarea, and select elements
                if ($(this).hasClass("summernotez")) {
                    // For Summernote
                    var summernoteContent = $(this).summernote('code');
                    if (!summernoteContent.trim()) {
                        formValid = false;
                        emptyField = $(this).attr("name");
                        return false; // Exit the loop if any required field is empty
                    }
                } else if ($(this).hasClass("selectpicker")) {
                    // For Bootstrap Select
                    if (!$(this).val()) {
                        formValid = false;
                        emptyField = $(this).attr("name");
                        return false; // Exit the loop if any required field is empty
                    }
                } else {
                    if (!$(this).val()) {
                        formValid = false;
                        emptyField = $(this).attr("name");
                        return false; // Exit the loop if any required field is empty
                    }
                }
            });

            if (formValid) {
                const materialName = $('input[name=name]').val()
                const materialCodeId = $('#select-material-code').val()
                const brandId = $('#select-brand').val()
                const storeLocationId = $('#select-store-loc').val()
                const poNumber = $('input[name=po_number]').val()
                const serialNumber = $('input[name=serial_number]').val()
                const specification = $('textarea[name=specification]').val()
                const description = $('textarea[name=material_description]').val()
                const materialTag = $('input[name=material_tag]').val()
                const qty = $('input[name=qty]').val()

                $('#dynamic-table>tbody').append(`
                    <tr id="material-item-${i}">
                        <td>${materialName}</td>
                        <td>${poNumber}</td>
                        <td>${serialNumber}</td>
                        <td>${materialTag}</td>
                        <td>${qty}</td>
                        <td>{!! Form::file('image[]', ['accept' => "image/png, image/gif, image/jpeg"]) !!}</td>
                        <td><button type="button" class="btn btn-sm" onclick="deleteRow(${i})"><i class="fa fa-trash" style="color: red"><i/></button></td>
                        <input type="hidden" name="name[]" value="${materialName}"/>
                        <input type="hidden" name="material_code_id[]" value="${materialCodeId}"/>
                        <input type="hidden" name="serial_number[]" value="${serialNumber}"/>
                        <input type="hidden" name="brand_id[]" value="${brandId}"/>
                        <input type="hidden" name="material_tag[]" value="${materialTag}"/>
                        <input type="hidden" name="material_description[]" value="${description}"/>
                        <input type="hidden" name="specification[]" value="${specification}"/>
                        <input type="hidden" name="qty[]" value="${qty}"/>
                        <input type="hidden" name="po_number[]" value="${poNumber}"/>
                        <input type="hidden" name="store_location_id[]" value="${storeLocationId}"/>
                    </tr>
                `)

                // $('<img />',
                //     { 
                //         src: image.attr('src'), 
                //         name: 'image[]'
                //     }
                // )
                // .appendTo($(`material-item-${i}`));

                i++
                $('#createMaterialModal').modal('hide')
            } else {
                $('#createMaterialModal').modal('hide')

                Swal.fire(
                    "Failed",
                    "Please fill in the " + emptyField + " field!",
                    "error"
                )
            }
        })
    })

    // $('#material-list-table').DataTable({
    //      processing: true,
    //      serverSide: true,
    //      ajax: "{{ route($module.'.material-code-list') }}",
    //      columns: [
    //          { data: 'code', name: 'code', sortable: false },
    //          { data: 'name', name: 'name', render: (data, type, row) => {
    //             return `<strong
    //                         style="cursor: pointer"
    //                         id="material_${row.id}"
    //                         data-material_code="${row.code}"
    //                         data-name="${row.name}"
    //                         data-group="${row.material_group.name}"
    //                         onclick="handleDynamicForm(${row.id})"
    //                     >
    //                         ${row.name}
    //                     </strong>`
    //          } },
    //          { data: 'description', name: 'description' },
    //          { data: 'material_group.name', name: 'material_group.name', searchable: false, sortable: false },
    //      ]
    // });


    function deleteRow(index) {
        $('#material-item-'+index).remove()
    }

    $('#select-warehouse').change(function() {
        const warehouseId = $(this).val()

        $('#select-store-loc').select2().empty()

        $('#select-store-loc').select2({
            allowClear: true,
            dropdownParent: $('#createMaterialModal'),
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

    $('#select-material-code').change(async function() {
        const val = $(this).val()

        let data = {}
        const res = await $.getJSON(`{{url("/")}}/material/material_code/detail/${val}`)
            .done(res => {
                data = res.data
            }).fail(() => {
                alert('Something went wrong')
            })

        $('#material-code-name').val(data.name)
        $('#group-name').val(data.material_group?.name)
        $('#uom').val(data.uom_label)
    })
    // $('#btn-add-material').click(function() {
        
    // })
 </script>
@endpush
