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
        $results  =   DB::table('crm_case_status')
                        ->join('crm_pipeline_case_status', 'crm_case_status.id', '=', 'crm_pipeline_case_status.case_status_id')
                        ->join('crm_pipelines', 'crm_pipeline_case_status.pipeline_id', '=', 'crm_pipelines.id')
                        ->join('crm_pipeline_programs', 'crm_pipeline_programs.pipeline_id', '=', 'crm_pipelines.id')
                        ->join('crm_programs', 'crm_pipeline_programs.program_id', '=', 'crm_programs.id')
                        ->select(
                            'crm_programs.name as program_name', 
                            'crm_case_status.name as case_status_name',
                            DB::raw('COALESCE(SUM(crm_pipeline_programs.student_numbers), 0) as total_students')  // Use COALESCE to set 0 for nulls
                        )
                        ->groupBy('crm_case_status.id', 'crm_programs.id')
                        ->where('crm_case_status.show_in_dashboard', true)
                        ->get();

        $programs   =   DB::table('crm_programs')->get();
        $case_status   =   DB::table('crm_case_status')->where('show_in_dashboard', true)->get();

        $output = [];

        foreach ($results as $row) {
            $info['total_students'] =  $row->total_students;
            $output[$row->program_name][$row->case_status_name] = $info;
        }
        dd($results);
        
        // Reindex the array if needed (optional)
        $info = array_values($info);
        dd($info);
        return Inertia::render('Dashboard',[
            'programs'      =>  $programs,
            'case_status'   =>  $case_status,
        ]);
    }
}
