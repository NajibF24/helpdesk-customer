<?php

namespace App\Http\Controllers;

use App\Models\GoodsDetail;
use App\Models\InventoryType;
use App\Models\Material;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rules\File;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;

class InventoryListController extends Controller
{
    protected $title;
    protected $module;
    protected $model;

    public function __construct(Material $model)
    {
        $this->title = 'Inventory List';
        $this->module = 'inventory_list';
        $this->model = $model;

        View::share('title', $this->title);
        View::share('module', $this->module);
    }

    public function index(Request $request) {
        if($request->ajax()) {
            $data = Material::with('material_code.material_group', 'store_location', 'inventory_type');

            return DataTables::of($data)
                ->addColumn('action', $this->module.'.actions')
                ->rawColumns(['action'])
                ->make();
        }

        return view($this->module.'.index');
    }

    public function edit(Request $request, $id) {
        accessv('goods_receive', 'edit');

        if($request->ajax()) {
            $data = GoodsDetail::where('material_id', $id)
                ->with('pic', 'goodsIssue.goodsReceive.contactRequestor', 'goodsIssue.contactRequestor', 'goodsReceive.contactRequestor');
            return DataTables::of($data)
                ->addColumn('category', function($row) {
                    return !empty($row->goodsReceive) ? 'Receive' : 'Issue';
                })
                ->addColumn('document_no', function($row) {
                    $documentNo = !empty($row->goodsReceive) ? $row->goodsReceive->code : $row->goodsIssue->code;
                    $url = !empty($row->goodsReceive) ? route('goods_receive.show', $row->goodsReceive->id) : route('goods_issue.show', $row->goodsIssue->id);
                    return "<a href='". $url ."'>$documentNo</a>";
                })
                ->addColumn('date', function($row) {
                    return !empty($row->goodsReceive) ? date('d F Y', strtotime($row->goodsReceive->created_at)) : date('d F Y', strtotime($row->goodsIssue->created_at));
                })
                ->addColumn('request_by', function($row) {
                    return !empty($row->goodsReceive) ? $row->goodsReceive->contactRequestor->name : $row->goodsIssue->contactRequestor->name;
                })
                ->rawColumns(['document_no'])
                ->make(true);
        }

        $warehouses = Warehouse::all(['name', 'id'])->pluck('name', 'id')->toArray();
        $inventoryTypes = InventoryType::get(['id', 'title'])
            ->pluck('label', 'id')
            ->toArray();

        $detail = $this->model->with('storeLocation', 'materialCode.materialGroup', 'brand')->find($id);

        return view($this->module.'.edit', compact('detail', 'warehouses', 'inventoryTypes'));
    }

    public function update(Request $request, $id) {
        accessv('goods_receive', 'edit');

        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'document' => ['nullable', File::types(['csv', 'xls', 'jpg', 'png', 'docx', 'pdf', 'xlsx'])],
                'inventory_type_id' => 'required',
                'store_location_id' => 'required',
                'remarks' => 'nullable',
            ]);

            if($validator->fails()) {
                return redirect(route($this->module.'.edit', $id))->withErrors($validator)->withInput();
            }

            $detail = $this->model->find($id);

            $data = $validator->safe()->toArray();

            if($request->hasFile('document'))
			{
                // if($detail->image && Storage::exists(asset($detail->image))) Storage::delete(asset($detail->image));
                if($detail->document) Storage::delete(asset($detail->document));

                $file = $request->file('document');
				$filename = izrand(5).'-'.$file->getClientOriginalName();
				handleUpload($file,$filename);
			    $data['document'] = "uploads/$filename";
			}

            $detail->update($data);
            
            DB::commit();

			Flash::success('Data has been successfully saved');
            return redirect(route($this->module.'.index'));
        } catch (\Throwable $th) {
            DB::rollBack();

            if(config('app.env') == 'local') dd($th->getMessage());

            return redirect(route($this->module.'.index'))->with('error', 'Something went wrong');
        }
    }
}
