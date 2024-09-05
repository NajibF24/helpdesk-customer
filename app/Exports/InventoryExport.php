<?php

namespace App\Exports;

use App\Models\GoodsDetail;
use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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

        $data = Material::with(
            'materialCode.materialGroup',
            'storeLocation.warehouse',
        )
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

        return $data;
    }

    public function map($row): array
    {
        return [
            ++$this->index,
            $row->serial_number,
            @$row->materialCode->code,
            @$row->materialCode->name,
            '1',
            @$row->inventoryType->label,
            @$row->materialCode->materialGroup->name,
            $row->po_number,
            @$row->materialCode->uom_label,
            $row->material_tag,
            @$row->brand->name,
            @$row->storeLocation->warehouse->name,
            @$row->storeLocation->name,
            $row->description,
            $row->updated_at
        ];
    }

    public function headings(): array
    {
        return [
            'No',
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