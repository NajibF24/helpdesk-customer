<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = current_schema() AND table_type = \'BASE TABLE\'');

        $data = array();
        
        foreach ($tables as $table) {
            foreach ($table as $value) {
                // dd($value)
                $query = DB::table($value);
                if ($value == 'ticket') {
					$data['ticket'] = $query->select(DB::raw('ticket.*, \'ticket\' as nama_table'))
                    ->leftJoin('ticket_approval', 'ticket.id', '=', 'ticket_approval.ticket_id')
                    ->where('title', 'ilike', '%' . $request->query('q') . '%')
                    ->where(function ($query) use ($request) {
                        $query->whereRaw('next_approval_id = ' . Auth::user()->person . ' OR ticket.created_by = ' . Auth::user()->id . ' OR ticket_approval.approval_id = ' . Auth::user()->person . '');
                    })
                    ->orWhere(DB::raw('CAST(ticket.id AS TEXT)'), $request->query('q'))
                    ->groupBy('ticket.id')
                    ->get();

                    if (count($data['ticket']) == 0) {
                        unset($data['ticket']);
                    }
                }

                if ($value == 'ticket_draft') {
					$data['ticket_draft'] = $query->select(DB::raw('*, \'ticket_draft\' as nama_table'))
                    ->where('title', 'ilike', '%' . $request->query('q') . '%')
                    ->where(function ($query) use ($request) {
                        $query->where('created_by', Auth::user()->id);
                    })
                    ->orWhere(DB::raw('CAST(id AS TEXT)'), $request->query('q'))
                    ->get();


                    if (count($data['ticket_draft']) == 0) {
                        unset($data['ticket_draft']);
                    }
                }

                if ($value == 'faq') {
					$data['faq'] = $query->select(DB::raw('*, \'faq\' as nama_table'))
                    ->where('title', 'ilike', '%' . $request->query('q') . '%')
                    ->where(function($query) use ($request) {
                        $query->orWhere(DB::raw('CAST(id AS TEXT)'), $request->query('q'));
                    })
                    ->get();

                    if (count($data['faq']) == 0) {
                        unset($data['faq']);
                    }
                }
            }
        }

        return view('search')->with('data', $data);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        return redirect()->route($input['route'] == 'faq' ? 'faq' : 'search.index', ['q' => $input['search']]);
    }
}
