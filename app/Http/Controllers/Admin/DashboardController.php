<?php namespace App\Http\Controllers\Admin;

use Session;
use DB;

class DashboardController extends AdminController
{
    
    public function index()
	{
        $visits_start = Session::get('visits_start', date('Y-m-d', strtotime('-30 days')));
        $visits_end = Session::get('visits_end', date('Y-m-d'));
        $most_content = DB::table('visits')
            ->select(DB::raw('count(contents.id) as total, contents.id, contents.title'))
            ->join('contents', 'contents.id', '=', 'visits.content_id')
            ->whereNotNull('content_id')
            ->groupBy('contents.id')
            ->take(10)
            ->get();
        $less_content = DB::table('visits')
            ->select(DB::raw('count(contents.id) as total, contents.id, contents.title'))
            ->join('contents', 'contents.id', '=', 'visits.content_id')
            ->whereNotNull('content_id')
            ->groupBy('contents.id')
            ->take(10)
            ->get();
        
        // Set data
        $data = [
            'visits_start'  => $visits_start,
            'visits_end'    => $visits_end,
            'most_content'  => $most_content,
            'less_content'  => $less_content
        ];
		return view('admin/dashboard', $data);
	}

}
