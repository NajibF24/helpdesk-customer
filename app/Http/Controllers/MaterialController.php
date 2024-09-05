<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\MaterialGroup;
use App\Models\Material;
use App\Models\MaterialCode;
use App\Models\StoreLocation;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Yajra\DataTables\Facades\DataTables;

class MaterialController extends Controller
{
    protected $module;
    protected $model;
    protected $title;

    public function __construct(Material $model)
    {
        $this->module = 'material';
        $this->model = $model;
        $this->title = 'Material';

        View::share('module', $this->module);
        View::share('title', $this->title);
    }

    public function index(Request $request) {
        accessv($this->module, 'list');

        if($request->ajax()) {
            $data = $this->model->with('materialCode', 'brand');

            return DataTables::of($data)
                ->addColumn('action', $this->module.'.actions')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view($this->module.'.index');
    }

    public function create() {
        accessv($this->module, 'create');

        $materialCodes = MaterialCode::all(['code', 'id'])->pluck('code', 'id')->toArray();
        $brands = Brand::all(['name', 'id'])->pluck('name', 'id')->toArray();
        $warehouses = Warehouse::all(['name', 'id'])->pluck('name', 'id')->toArray();

        return view($this->module.'.create', compact('materialCodes', 'brands', 'warehouses'));
    }

    public function store(Request $request) {
        accessv($this->module, 'create');

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), $this->validationRules());
            if($validator->fails()) {
                return redirect(route($this->module.'.create'))->withErrors($validator)->withInput();
            }

            $data = $validator->safe()->toArray();

            if($request->hasFile('image'))
			{
                $file = $request->file('image');
				$filename = izrand(5).'-'.$file->getClientOriginalName();
				handleUpload($file,$filename);
			    $data['image'] = "uploads/$filename";
			}

            $this->model->create($data);
            
            DB::commit();

			Flash::success('Data has been successfully saved');
            return redirect(route($this->module.'.index'));
        } catch (\Throwable $th) {
            DB::rollBack();

            if(config('app.env') == 'local') dd($th->getMessage());

            return redirect(route($this->module.'.index'))->with('error', 'Something went wrong');
        }
    }

    public function edit($id) {
        accessv($this->module, 'edit');

        $materialCodes = MaterialCode::all(['code', 'id'])->pluck('code', 'id')->toArray();
        $warehouses = Warehouse::all(['name', 'id'])->pluck('name', 'id')->toArray();
        $brands = Brand::all(['name', 'id'])->pluck('name', 'id')->toArray();

        $detail = $this->model->with('storeLocation')->find($id);

        return view($this->module.'.edit', compact('materialCodes', 'brands', 'detail', 'warehouses'));
    }

    public function update(Request $request, $id) {
        accessv($this->module, 'edit');

        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), $this->validationRules($id));

            if($validator->fails()) {
                return redirect(route($this->module.'.edit', $id))->withErrors($validator)->withInput();
            }

            $detail = $this->model->find($id);

            // dd(File::exists(asset($detail->image)));

            $data = $validator->safe()->toArray();

            // dd($detail);

            if($request->hasFile('image'))
			{
                // if($detail->image && Storage::exists(asset($detail->image))) Storage::delete(asset($detail->image));
                if($detail->image) Storage::delete(asset($detail->image));

                $file = $request->file('image');
				$filename = izrand(5).'-'.$file->getClientOriginalName();
				handleUpload($file,$filename);
			    $data['image'] = "uploads/$filename";
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

    public function destroy($id) {
        accessv($this->module, 'delete');

        DB::beginTransaction();
        try {
            $this->model->find($id)->delete($id);
            
            DB::commit();

			Flash::success('Data has been successfully deleted');
            return redirect(route($this->module.'.index'));
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect(route($this->module.'.index'))->with('error', 'Something went wrong');
        }
    }

    public function validationRules($id = null) {
        $validation = [
            'name' => 'required',
            'serial_number' => 'required',
            'brand_id' => 'required',
            'material_code_id' => 'required',
            'material_tag' => 'required',
            'image' => 'nullable',
            'specification' => 'required',
            'qty' => 'required',
            'description' => 'nullable',
            'po_number' => 'required',
            'store_location_id' => 'required',
        ];

        if($id) {
            // $validation['code'] = 'required|unique:materials,code,'.$id.',id,deleted_at,NULL';
        } 

        return $validation;
    }

    public function getMaterialCodeDetail($materialCodeId) {
        try {
            $data = MaterialCode::with('materialGroup')->find($materialCodeId);

            return response()->json([
                'success' => true,
                'message' => 'Success retrieve data',
                'data' => $data
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getStoreLocationList($warehouseId) {
        try {
            $data = StoreLocation::where('warehouse_id', $warehouseId)->get(['id', 'name']);

            return response()->json([
                'success' => true,
                'message' => 'Success retrieve data',
                'data' => $data
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function getMaterialDetail($materialId) {
        try {
            $data = Material::with('storeLocation.warehouse', 'brand', 'materialCode.materialGroup', 'inventoryType')->find($materialId);

            return response()->json([
                'success' => true,
                'message' => 'Success retrieve data',
                'data' => $data
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }
}
