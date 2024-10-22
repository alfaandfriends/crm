<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Centre;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $results  =   DB::table('crm_pipelines')
                        ->join('crm_progress_percentage', 'crm_pipelines.progress_id', '=', 'crm_progress_percentage.id')
                        ->select(
                            'crm_progress_percentage.name as progress_percentage',
                            'crm_pipelines.assignee_user_id as user_id',
                        )
                        ->get();

        $progress_status       =   DB::table('crm_progress_percentage')->get();
        dd($results);
        return Inertia::render('Dashboard',[
            'progress_status'   =>  $progress_status,
        ]);
    }
}
