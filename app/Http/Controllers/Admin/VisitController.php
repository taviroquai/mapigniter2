<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Visit;
use Session;
use DB;

class VisitController extends BaseController
{
    
    /**
     * Get visits index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/visits');
	}
    
    /**
     * Get all visits
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function json()
	{
        $visits = Visit::with('content', 'user')
                ->addSelect(DB::raw('visits.*, count(id) as visits'))
                ->groupBy('http_url', 'user_id', 'content_id')
                ->orderBy('created_at', 'desc')
                ->get();
		return response()->json(['data' => $visits]);
	}
    
    /**
     * Get visits flot data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function visitsTotalsJson($date_start, $date_end)
    {
        Session::put('visits_start', $date_start);
        Session::put('visits_end', $date_end);
        $sql = "
            SELECT x, sum(y) as y FROM (
                SELECT date(created_at) as x, count(id) as y
                FROM visits
                WHERE date(created_at) >= ? and date(created_at) <= ?
                GROUP BY visits.created_at
            ) t1
            GROUP BY x
            ORDER BY date(x)
        ";
        $totals = DB::select(DB::raw($sql),  [$date_start, $date_end]);
        $data = [];
        foreach ($totals as $item) {
            $data[] = [strtotime($item->x).'000', $item->y];
        }
        return response()->json(['data' => $data]);
    }

}
