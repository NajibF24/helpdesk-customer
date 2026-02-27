<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller {

    public function index(Request $request)
    {
        $title = "Report";
		$breadcumb = [
			[
				'name' => 'Report',
				'url' => 'report'
			]
		];
        
		$tickets = DB::table('ticket')
			->orderBy('id','desc')
			->where('created_by', Auth::user()->id);

		if ($request->query('date')) {
			$date = explode(",", str_replace(" / ", ",", $request->query('date')));
			$tickets = $tickets->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$date[0], $date[1]]);
		}

		if ($request->query('status')) {
			$tickets = $tickets->whereIn('status', $request->query('status'));
		}

		if ($request->query('type')) {
			$tickets = $tickets->whereIn('finalclass', $request->query('type'));
		}

		$tickets = $tickets->get();

        return view('report.index')
            ->with('tickets', $tickets)
            ->with('title', $title)
            ->with('breadcumb', $breadcumb)
			->with('date', $request->query('date') ?? '')
			->with('statuses', $request->query('status') ?? [])
			->with('types', $request->query('type') ?? []);
    }

	public function store(Request $request)
    {
        $input = $request->all();

        return redirect()->route('report.index', ['date' => $input['date'], 'status' => $input['status'] ?? '', 'type' => $input['type'] ?? '']);
    }
}
