<div class="row">
    <div class="col-12">
        <button class="btn btn-dark btn-sm blue-black" type="button" data-toggle="modal" data-target="#materialListModal">Add Material</button>
        <table class="table table-bordered mt-2 table-responsive" id="dynamic-table">
            <thead>
                <tr>
                    <th>Material Code</th>
                    <th>Material Name</th>
                    <th>Qty</th>
                    <th>Group</th>
                    <th>PIC</th>
                    <th>Period</th>
                    <th>Remarks</th>
                    <th>Supplier</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="material-list-body"></tbody>
        </table>
    </div>
</div>


@push('modal')
<div class="custom-modal fade" id="materialListModal" tabindex="-1" aria-labelledby="materialListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="min-width: 1000px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="materialListModalLabel">Material</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered mt-2 table-responsive" id="material-list-table">
                <thead>
                    <tr>
                        <th>Material Code</th>
                        <th>Material Name</th>
                        <th>Description</th>
                        <th>Group</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
      </div>
    </div>
</div>

<script>

    $('#material-list-table').DataTable({
         processing: true,
         serverSide: true,
         ajax: "{{ route($module.'.material-code-list') }}",
         columns: [
             { data: 'code', name: 'code', sortable: false },
             { data: 'name', name: 'name', render: (data, type, row) => {
                return `<strong
                            style="cursor: pointer"
                            id="material_${row.id}"
                            data-material_code="${row.code}"
                            data-name="${row.name}"
                            data-group="${row.material_group.name}"
                            onclick="handleDynamicForm(${row.id})"
                        >
                            ${row.name}
                        </strong>`
             } },
             { data: 'description', name: 'description' },
             { data: 'material_group.name', name: 'material_group.name', searchable: false, sortable: false },
         ]
     });

    function handleDynamicForm(materialCodeId) {

        const materialCode = $('#material_'+materialCodeId).data('material_code')
        const materialName = $('#material_'+materialCodeId).data('name')
        const materialGroupName = $('#material_'+materialCodeId).data('group')

        // if($('#material_row_'+materialCodeId).length == 0) {
            $('#dynamic-table>tbody').append(`
                <tr id="material_row_${materialCodeId}">
                    <td>
                        ${materialCode}
                        <input type="hidden" name="material_code_id[]" value="${materialCodeId}">
                    </td>
                    <td>${materialName}</td>
                    <td>1</td>
                    <td>${materialGroupName}</td>
                    <td width="200">
                        <select class="form-control select2 form-control-sm material_pic" name="user_id[]">
                            <option selected disabled>Select PIC</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" class="date-range form-control form-control-sm material_period" name="dates[]"></td>
                    <td><textarea class="form-control" name="remarks[]"></textarea></td>
                    <td><input type="text" class="form-control form-control-sm material_supplier" name="supplier[]"/></td>
                    <td><input type="number" class="form-control form-control-sm material_amount" name="amount[]"/></td>
                    <td><button type="button" class="btn btn-sm"><i class="fa fa-trash" style="color: red" onclick="deleteRow(${materialCodeId})"><i/></button></td>
                </tr>
            `)

            $('.select2').select2()

            $('.date-range').daterangepicker({
			   buttonClasses: ' btn',
			   applyClass: 'btn-primary',
			   cancelClass: 'btn-secondary'
			  }, function(start, end, label) {
					$('.date-range').val( start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
			  });

              $('#materialListModal').modal('hide')

              $('#select-type').change()
        // }
    }

    function deleteRow(materialCodeId) {
        $('#material_row_'+materialCodeId).remove()
    }
 </script>
@endpush
