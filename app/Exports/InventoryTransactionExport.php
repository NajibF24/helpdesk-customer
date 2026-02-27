<?php

namespace App\Exports;

use App\Models\GoodsDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryTransactionExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $index = 0;
    protected $payload = [];

    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function query()
    {
        $status = $this->payload['status'];
        $materialGroup = $this->payload['material_group'];
        $materialCode = $this->payload['material_code'];
        $warehouse = $this->payload['warehouse'];
        $type = $this->payload['type'];

        $data = GoodsDetail::with(
            'material',
            'materialCode.materialGroup',
            'storeLocation.warehouse',
            'goodsIssue.inventoryType',
            'goodsReceive.inventoryType'
        )
        ->whereNotNull($type.'_id')
        ->when($type == 'goods_receive' && $status != '', function($q) use($status){
            $q->whereHas('goodsReceive', function($query) use($status){
                $query->whereIn('inventory_type_id', explode(',', $status));
            });
        })
        ->when($type == 'goods_issue' && $status != '', function($q) use($status){
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

        return $data;
    }

    public function map($row): array
    {
        return [
            ++$this->index,
            $this->payload['type'] == 'goods_receive' ? @$row->goodsReceive->code : @$row->goodsIssue->code,
            $row->material ? @$row->material->serial_number : $row->serial_number,
            $row->materialCode->code,
            $row->materialCode->name,
            '1',
            $this->payload['type'] == 'goods_receive' ? @$row->goodsReceive->inventoryType->label : @$row->goodsIssue->inventoryType->label,
            @$row->materialCode->materialGroup->name,
            $row->material ? $row->material->po_number : $row->po_number,
            $row->materialCode->uom_label,
            $row->material ? @$row->material->material_tag : $row->material_tag,
            $row->material ? @$row->material->brand->name : @$row->brand->name,
            @$row->storeLocation->warehouse->name,
            @$row->storeLocation->name,
            $row->material ? $row->material->description : $row->description,
            $row->updated_at
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Transaction No',
            'Serial No',
            'Material Code',
            'Material Code Name',
            'Qty',
            'Status',
            'Group',
            'Po No',
            'UoM',
            'Material Tag',
            'Brand',
            'Warehouse',
            'Store Location',
            'Material Description',
            'Last Updated Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row 
            1    => ['font' => ['bold' => true]],
        ];
    }
}