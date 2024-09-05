<style>
    .v-align-middle{
        vertical-align: middle !important;
    }
</style>
<div class="card mb-8">
    <div class="card-body">
        <h4><strong>Requested Material</strong></h4>
        <div class="row">
            <div class="col-12 table-responsive">
                <table class="table table-bordered mt-2" id="dynamic-table">
                    <thead>
                        <tr>
                            <th class="v-align-middle" style="background: #3599FE">No.</th>
                            @foreach (\App\Models\GoodsIssue::mapMaterialListColumn($detail->inventoryType->title) as $item)
                                <th class="text-center" style="vertical-align: middle;background:#3599FE">{{ ucwords(str_replace('_', ' ', $item)) }}</th>
                            @endforeach
                            <th class="bg-warning v-align-middle">Assigned Serial Number</th>
                            <th class="bg-warning v-align-middle">Store Location</th>
                            <th class="bg-warning v-align-middle">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detail->details->sortBy('id') as $key => $item)
                            <tr>
                                <td class="v-align-middle">{{ $loop->iteration }}</td>
                                <td class="v-align-middle">{{ @$item->materialCode->code }}</td>
                                <td class="v-align-middle">{{ @$item->materialCode->name }}</td>
                                <td class="v-align-middle">1</td>
                                <td class="v-align-middle">{{ @$item->materialCode->materialGroup->name }}</td>

                                @if (in_array($detail->inventoryType->title, ['borrow', 'deploy', 'disposal', 'transfer']))
                                <td class="v-align-middle">{{ @$item->pic->name }}</td>
                                @endif

                                @if (in_array($detail->inventoryType->title, ['borrow', 'in_repair', 'disposal', 'transfer']))
                                <td class="v-align-middle"><small>{{ date('d M Y', strtotime($item->start_date)).' - '.date('d M Y', strtotime($item->end_date)) }}</small></td>
                                @endif

                                {{-- @if (in_array($detail->inventoryType->title, ['in_repair', 'disposal', 'transfer']))
                                <td class="v-align-middle">{{ $item->supplier }}</td>
                                @endif

                                @if (in_array($detail->inventoryType->title, ['deploy', 'in_repair', 'disposal', 'transfer']))
                                <td class="v-align-middle">{{ $item->amount }}</td>
                                @endif --}}

                                <td class="v-align-middle">{{ $item->remarks }}</td>
                                <td class="v-align-middle">
                                    <a href="javascript:void(0)" class="{{ $item->material ? 'show-material' : ''}}" data-material_id="{{$item->material_id}}">{{ $item->material->serial_number ?? '-' }}</a>
                                </td>
                                <td class="v-align-middle">{{ $item->material->storeLocation->name ?? '-' }}</td>

                                <td class="v-align-middle">
                                    @if ($is_alr_first_support_custom)
                                    <button class="btn btn-sm btn-primary assign-btn " data-material_code_id="{{ $item->material_code_id }}" data-detail_id="{{ $item->id }}"  data-toggle="modal" data-target="#materialListModal"><i class="fas fa-shipping-fast"></i> Assign</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@push('modal')
@include('goods_issue.material-detail-modal')

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
            <table class="table table-bordered mt-2" style="width: 100%" id="material-list-table">
                <thead>
                    <tr>
                        <th>Material Code</th>
                        <th>Material Name</th>
                        <th>Specification</th>
                        <th>Serial No.</th>
                        <th>Group</th>
                        <th>Status</th>
                        <th>Store Location</th>
                        <th>Warehouse</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
      </div>
    </div>
</div>

<script>

    $('.assign-btn').click(function() {
        const materialCodeId = $(this).data('material_code_id')
        const goodsIssueDetailId = $(this).data('detail_id')

        $('#material-list-table').DataTable({
            processing: true,
            serverSide: true,
            bDestroy: true,
            ajax: "{{ route($module.'.material-list', 'MATERIAL_CODE_ID') }}?warehouse_ids={{json_encode($warehouse_ids)}}".replace('MATERIAL_CODE_ID', materialCodeId),
            columns: [
                { data: 'material_code.code', name: 'material_code.code', sortable: false, searchable: false },
                { data: 'material_code.name', name: 'material_code.name', sortable: false, searchable: false },
                { data: 'specification', name: 'specification'  },
                { data: 'serial_number', name: 'serial_number',render: (data, type, row) => {
                    return `<strong
                                style="cursor: pointer"
                                onclick="assignMaterial(${row.id}, ${goodsIssueDetailId})"
                            >
                                ${row.serial_number}
                            </strong>`
                } },
                { data: 'material_code.material_group.name', name: 'material_code.material_group.name', searchable: false, sortable: false },
                { data: 'status', name: 'status'  },
                { data: 'store_location.name', name: 'store_location.name', searchable: false, sortable: false, defaultContent: ''  },
                { data: 'store_location.warehouse.name', name: 'store_location.warehouse.name', searchable: false, sortable: false, defaultContent: ''  },
            ],
            language: {
                emptyTable: "No Material available"
            }
        });


    })

    function assignMaterial(materialId, goodsIssueDetailId) {
        const url = '{{ route("goods_issue.assign_material", ["MATERIAL_ID", "GI_DETAIL_ID"]) }}'
            .replace('GI_DETAIL_ID', goodsIssueDetailId)
            .replace('MATERIAL_ID', materialId)

        $.ajax({
            url: url,
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: (res) => {
                $('#materialListModal').modal('hide')
                Swal.fire(res.success ? "Updated" : "Failed", res.message, res.success ? "success" : "error")
                setTimeout(() => {
                    window.location.reload()
                }, 1200);
            },
            error: () => alert('Something went wrong')
        });

    }
 </script>
@endpush
