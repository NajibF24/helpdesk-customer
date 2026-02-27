<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Exports\InventoryTransactionExport;
use App\Models\GoodsDetail;
use App\Models\InventoryType;
use App\Models\Material;
use App\Models\MaterialCode;
use App\Models\MaterialGroup;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class InventoryReportController extends Controller
{
    public function __construct()
    {
        $inventoryTypes = InventoryType::get(['id', 'title', 'transaction_type']);
        $materialGroups = MaterialGroup::get(['id', 'name']);
        $materialCodes = MaterialCode::get(['id', 'code']);
        $warehouses = Warehouse::get(['id', 'name']);

        View::share('inventoryTypes', $inventoryTypes);
        View::share('materialGroups', $materialGroups);
        View::share('materialCodes', $materialCodes);
        View::share('warehouses', $warehouses);
    }

    public function inventoryTransactionReport(Request $request) {
        accessv('inventory_report','list');

        if($request->ajax()) {
            $status = $request->status;
            $materialGroup = $request->material_group;
            $materialCode = $request->material_code;
            $warehouse = $request->warehouse;
            $type = $request->type;

           if($type == 'goods_receive') {
                $data = GoodsDetail::whereNotNull('goods_receive_id')->with(
                    'material',
                    'materialCode.materialGroup',
                    'storeLocation.warehouse',
                    'goodsIssue.inventoryType',
                    'goodsReceive.inventoryType'
                )
                ->when($status != '', function($q) use($status){
                    $q->whereHas('goodsReceive', function($query) use($status){
                        $query->whereIn('inventory_type_id', explode(',', $status));
                    });
                })
                ->when($materialGroup != '', function($q) use($materialGroup) {
                    $q->whereHas('materialCode', function($query) use($materialGroup){
                        $query->whereIn('material_group_id', explode(',', $materialGroup));
                    });
                })
                ->when($materialCode != '', function($query) use($materialCode) {
                    $query->whereIn('material_code_id', explode(',', $materialCode));
                })
                ->when($warehouse != '', function($q) use($warehouse) {
                    $q->whereHas('storeLocation', function($query) use($warehouse){
                        $query->whereIn('warehouse_id', explode(',', $warehouse));
                    });
                });

                return DataTables::of($data)
                    ->addColumn('transaction_no', function($row) {
                        return @$row->goodsReceive->code;
                    })
                    ->addColumn('serial_no', function($row) {
                        return $row->material ? @$row->material->serial_number : $row->serial_number;
                    })
                    ->addColumn('status', function($row) {
                        return @$row->goodsReceive->inventoryType->label;
                    })
                    ->addColumn('po_no', function($row) {
                        return $row->material ? $row->material->po_number : $row->po_number;
                    })
                    ->addColumn('material_tag2', function($row) {
                        return $row->material ? @$row->material->material_tag : $row->material_tag;
                    })
                    ->addColumn('brand_name', function($row) {
                        return $row->material ? @$row->material->brand->name : @$row->brand->name;
                    })
                    ->addColumn('material_remarks', function($row) {
                        return $row->material ? $row->material->description : $row->description;
                    })
                    ->make();
           } else {
                $data = GoodsDetail::whereNotNull('goods_issue_id')->with(
                    'material',
                    'materialCode.materialGroup',
                    'storeLocation.warehouse',
                    'goodsIssue.inventoryType',
                    'goodsReceive.inventoryType'
                )
                ->when($status != '', function($q) use($status){
                    $q->whereHas('goodsIssue', function($query) use($status){
                        $query->whereIn('inventory_type_id', explode(',', $status));
                    });
                })
                ->when($materialGroup != '', function($q) use($materialGroup) {
                    $q->whereHas('materialCode', function($query) use($materialGroup){
                        $query->whereIn('material_group_id', explode(',', $materialGroup));
                    });
                })
                ->when($materialCode != '', function($query) use($materialCode) {
                    $query->whereIn('material_code_id', explode(',', $materialCode));
                })
                ->when($warehouse != '', function($q) use($warehouse) {
                    $q->whereHas('storeLocation', function($query) use($warehouse){
                        $query->whereIn('warehouse_id', explode(',', $warehouse));
                    });
                });

                return DataTables::of($data)
                    ->addColumn('transaction_no', function($row) {
                        return @$row->goodsIssue->code;
                    })
                    ->addColumn('serial_no', function($row) {
                        return $row->material ? @$row->material->serial_number : $row->serial_number;
                    })
                    ->addColumn('status', function($row) {
                        return @$row->goodsIssue->inventoryType->label;
                    })
                    ->addColumn('po_no', function($row) {
                        return $row->material ? $row->material->po_number : $row->po_number;
                    })
                    ->addColumn('material_tag2', function($row) {
                        return $row->material ? @$row->material->material_tag : $row->material_tag;
                    })
                    ->addColumn('brand_name', function($row) {
                        return $row->material ? @$row->material->brand->name : @$row->brand->name;
                    })
                    ->addColumn('material_remarks', function($row) {
                        return $row->material ? $row->material->description : $row->description;
                    })
                    ->make();
           }
        }

        return view('inventory_transaction_report.index')->with('title', 'Inventory Transaction Report');
    }

    public function inventoryTransactionReportExport(Request $request) {
        return Excel::download(new InventoryTransactionExport($request->all()), 'Data Inventory '.date('Y-m-d').'.xlsx');
    }

    public function inventoryReport(Request $request) {
        accessv('inventory_report','list');

        if($request->ajax()) {
            $status = $request->status;
            $materialGroup = $request->material_group;
            $materialCode = $request->material_code;
            $warehouse = $request->warehouse;
            
            $data = Material::with('materialCode.materialGroup', 'storeLocation.warehouse', 'inventoryType', 'brand')
                ->when($status != '', function($q) use($status){
                    $q->whereIn('inventory_type_id', explode(',', $status));
                })
                ->when($materialGroup != '', function($q) use($materialGroup) {
                    $q->whereHas('materialCode', function($query) use($materialGroup){
                        $query->whereIn('material_group_id', explode(',', $materialGroup));
                    });
                })
                ->when($materialCode != '', function($query) use($materialCode) {
                    $query->whereIn('material_code_id', explode(',', $materialCode));
                })
                ->when($warehouse != '', function($q) use($warehouse) {
                    $q->whereHas('storeLocation', function($query) use($warehouse){
                        $query->whereIn('warehouse_id', explode(',', $warehouse));
                    });
                });

            return DataTables::of($data)->make();
        }

        return view('inventory_report.index')->with('title', 'Inventory Report');
    }

    public function inventoryReportExport(Request $request) {
        return Excel::download(new InventoryExport($request->all()), 'Data Inventory '.date('Y-m-d').'.xlsx');
    }

}
