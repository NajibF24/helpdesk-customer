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
                <table class="table table-bordered mt-2 w-100" id="dynamic-table">
                    <thead>
                        <tr>
                            <th class="v-align-middle" style="background:#3599FE">No.</th>
                            @foreach (\App\Models\GoodsIssue::mapMaterialListColumn($detail->goods_issue->inventoryType->title) as $item)
                                <th class="text-center" style="vertical-align: middle;background:#3599FE">{{ ucwords(str_replace('_', ' ', $item)) }}</th>
                            @endforeach
                            <th class="bg-warning v-align-middle">Assigned Serial Number</th>
                            <th class="bg-warning v-align-middle">Store Location</th>
                            <th class="bg-warning v-align-middle">Condition</th>
                            <th class="bg-warning v-align-middle">Return</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $status = [
                                'issued' => 'primary',
                                'hold' => 'warning',
                                'approved' => 'success',
                            ];
                        @endphp
                        @foreach ($detail->goods_issue->details->sortBy('id') as $key => $item)
                            <tr>
                                <td class="v-align-middle">{{ $loop->iteration }}</td>
                                <td class="v-align-middle">{{ @$item->materialCode->code }}</td>
                                <td class="v-align-middle">{{ @$item->materialCode->name }}</td>
                                <td class="v-align-middle">1</td>
                                <td class="v-align-middle">{{ @$item->materialCode->materialGroup->name }}</td>

                                @if (in_array($detail->goods_issue->inventoryType->title, ['borrow', 'deploy', 'disposal', 'transfer']))
                                <td class="v-align-middle">{{ @$item->pic->name }}</td>
                                @endif

                                @if (in_array($detail->goods_issue->inventoryType->title, ['borrow', 'in_repair', 'disposal', 'transfer']))
                                <td class="v-align-middle"><small>{{ date('d M Y', strtotime($item->start_date)).' - '.date('d M Y', strtotime($item->end_date)) }}</small></td>
                                @endif

                                @if (in_array($detail->goods_issue->inventoryType->title, ['in_repair', 'disposal', 'transfer']))
                                <td class="v-align-middle">{{ $item->supplier }}</td>
                                @endif

                                @if (in_array($detail->goods_issue->inventoryType->title, ['deploy', 'in_repair', 'disposal', 'transfer']))
                                <td class="v-align-middle">{{ $item->amount }}</td>
                                @endif

                                <?php
                                    // dd($next_approver);
                                    //cek last approver
                                    $last_approver = DB::table('goods_receive_approvals')->where('goods_receive_id', $detail->id)->latest('created_at')->first();
                                    // dd($request_management);
                                    $last_approver_id = $last_approver->approval_id ?? -9234;
                                    // dd([$next_approver, Auth::user()->person, $is_alr_first_support_custom]);
                                ?>
                                <td class="v-align-middle">{{ $item->remarks }}</td>
                                <td class="v-align-middle"><a href="javascript:void(0)" class="show-material" data-material_id="{{$item->material_id}}">{{ $item->material->serial_number ?? '-' }}</a></td>
                                @if (@$next_approver->id == Auth::user()->person && $is_alr_first_support_custom)
                                <td class="v-align-middle">
                                    <select name="store_location_ids[]" class="form-control form-control-sm">
                                        <option value="" disabled>Select Store Location</option>
                                        @foreach (\App\Models\StoreLocation::get(['id', 'name']) as $storeLocation)
                                            <option value="{{$storeLocation->id}}">{{ $storeLocation->name }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td class="v-align-middle">
                                    <input type="text" name="conditions[]" class="form-control form-control-sm">
                                </td>
                                @else
                                <td class="v-align-middle">{{ $item->material->storeLocation->name ?? '-' }}</td>
                                <td class="v-align-middle">
                                    <input type="text" name="conditions[]" class="form-control form-control-sm" value="{{$item->condition}}" disabled>
                                </td>
                                @endif
                                <td class="v-align-middle text-center">
                                    @if (@$next_approver->id == Auth::user()->person && $is_alr_first_support_custom)
                                        <div class="custom-control custom-switch" style="transform: scale(1.5);">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch{{ $key }}" name="issue_detail_ids[]" value="{{ $item->id ?? '-' }}">
                                            <label class="custom-control-label" for="customSwitch{{ $key }}"></label>
                                        </div>
                                    @else
                                        <div class="custom-control custom-switch" style="transform: scale(1.5);">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch{{ $key }}" name="issue_detail_ids[]" {{in_array($item->status_return, ['issued', 'approved', 'hold'])  ? 'checked' : ''}} disabled>
                                            <label class="custom-control-label" for="customSwitch{{ $key }}"></label>
                                        </div>
                                        {{-- <span class="badge badge-{{ @$status[$item->status_return] }}">{{ ucwords($item->status_return) }}</span> --}}
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
            <table class="table table-bordered mt-2 table-responsive" id="material-list-table">
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
            ajax: "{{ route($module.'.material-list', 'MATERIAL_CODE_ID') }}".replace('MATERIAL_CODE_ID', materialCodeId),
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
                { data: 'store_location.name', name: 'store_location.name', searchable: false, sortable: false, render: (data,type,row) => row.store_location?.name ?? ''  },
                { data: 'store_location.warehouse.name', name: 'store_location.warehouse.name', searchable: false, sortable: false, render: (data,type,row) => row.store_location?.warehouse?.name ?? ''  },
            ],
            language: {
                emptyTable: "No Material available in this Material Code"
            }
        });


    })

    function assignMaterial(materialId, goodsIssueDetailId) {
        const url = '{{ route("goods_receive.assign_material", ["MATERIAL_ID", "GI_DETAIL_ID"]) }}'
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
                Swal.fire("Updated", res.message, "success")
                setTimeout(() => {
                    window.location.reload()
                }, 800);
            },
            error: () => alert('Something went wrong')
        });

    }
 </script>
@endpush
