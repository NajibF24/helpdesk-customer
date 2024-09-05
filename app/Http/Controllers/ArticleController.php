<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Helpers\CommonHelper;
use PDF;

class ArticleController extends Controller
{
    public function __construct(CommonHelper $common_helper)
    {
        $this->common_helper = $common_helper;
    }

    public function index(Request $request)
    {
        $title = "FAQ & Tutorials";
        $faqcs = DB::table('faq_category')->get();
        $query = "";
        $result = $this->common_helper->parseTree($faqcs, 0);

        if ($request->query('q')) {
            $query = DB::table('faq')
                ->select('faq.*', 'faq_category.name as category_name')
                ->where('faq.title', 'ilike', '%' .$request->query('q'). '%')
                ->orWhere('faq.summary', 'ilike', '%' .$request->query('q'). '%')
                ->orWhere('faq.description', 'ilike', '%' .$request->query('q'). '%')
                ->leftJoin('faq_category', 'faq_category.id', '=', 'faq.category_id')
                ->get();
        }

        $view = [
            'title' => $title,
            'menus' => $result ?? 0,
            'faqs_search' => $query ?? "",
            'type' => $request->query('q') ? "search" : "home"
        ];

        return view('article.index')->with($view);
    }

    public function ajax_category(Request $request)
    {
        // if ($request->parent == '#')
        // {
        //     $faqcs = DB::table('faq_category')->where('parent', 0)->get();

        //     foreach($faqcs as $faqc)
        //     {
        //         $data[] = [
        //             'id' => $faqc->id,
        //             'text' => $faqc->name,
        //             'icon' => "fa fa-folder icon-lg kt-font-info",
        //             'children' => true,
        //             'type'=>'root'
        //         ];
        //     }
        // }
        // else{
        //     $faqcs = DB::table('faq_category')->where('parent', $request->parent)->get();

        //     foreach($faqcs as $faqc)
        //     {
        //         $data[] = [
        //             'id' => $faqc->id,
        //             'text' => $faqc->name,
        //             'icon' => "fa fa-folder icon-lg kt-font-info",
        //             'children' => true
        //         ];
        //     }
        // }

        $faqcs = DB::table('faq_category')->where('parent', 0)->get();

        $data[0]['id'] = 0;
        $data[0]['text'] = 'Top Level';

        $faqcs = DB::table('faq_category')->get();

        $result = $this->common_helper->parseTree($faqcs, 0);
        $data[0]['children'] = $result;

        return response()->json($data);
    }

    public function get_faq_list(Request $request)
    {
        $faq = DB::table('faq')->where('category_id', $request->category)->get();

        return response()->json($faq);
    }

    public function get_faq(Request $request)
    {
        $faq = DB::table('faq')->where('id', $request->id)->first();

        return response()->json($faq);
    }

    public function download_pdf_faq($id)
    {
        /*
        $faq = DB::table('faq')->where('id', $id)->first();
        $pdf = PDF::loadview('faq_pdf', [ 'faq' => $faq ]);

        return $pdf->download('faq.pdf');
        */
        //return view('faq_pdf')->with('faq', $faq);

        $faq = DB::table('faq')->where('id', $id)->first();

        $pdf = PDF::loadview('pdf_artikel', ['faq' => $faq]);
        // return view('pdf_artikel')->with('faq', $faq);
	    return $pdf->download('artikel.pdf');

    }
}
