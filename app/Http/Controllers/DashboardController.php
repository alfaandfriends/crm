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
        $programs       =   DB::table('crm_programs')->get();
        $case_status    =   DB::table('crm_case_status')->get();

        return Inertia::render('Dashboard',[
            'programs'          =>  $programs,
            'case_status'       =>  $case_status,
        ]);
    }

    public function getMonthlyReport(Request $request){
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $result = DB::table('crm_pipeline_programs')
            ->rightJoin('crm_pipeline_progress', 'crm_pipeline_programs.progress_id', '=', 'crm_pipeline_progress.id')
            ->rightJoin('crm_pipelines', 'crm_pipeline_progress.pipeline_id', '=', 'crm_pipelines.id')
            ->select(
                'crm_pipeline_programs.program_id as program_id',
                DB::raw('SUM(crm_pipeline_programs.student_numbers) as total_student_numbers'),
                'crm_pipeline_progress.case_status_id',
                DB::raw('COUNT(DISTINCT crm_pipelines.id) as total_centres'),
                'crm_pipelines.school_name as centre_name'
            )
            ->groupBy('crm_pipeline_programs.program_id', 'crm_pipeline_progress.case_status_id', 'crm_pipelines.school_name')
            ->when(!$request->date, function ($query) use ($currentMonth, $currentYear) {
                return $query->whereMonth('crm_pipelines.date_start', $currentMonth)
                    ->whereYear('crm_pipelines.date_start', $currentYear);
            })
            ->when($request->date, function ($query) use ($request) {
                return $query->whereMonth('crm_pipelines.date_start', $request->date['month'] + 1)
                    ->whereYear('crm_pipelines.date_start', $request->date['year']);
            })
            ->when($request->sales_person, function ($query) use ($request) {
                return $query->where('crm_pipelines.assignee_user_id', $request->sales_person);
            })
            ->get();

        $monthly_report = $result->groupBy(function ($item) {
            return $item->program_id . '-' . $item->case_status_id;
        })->map(function ($items) {
            return [
                'program_id' => $items->first()->program_id,
                'case_status_id' => $items->first()->case_status_id,
                'centre_names' => $items->map(function ($item) {
                    return [
                        'name' => $item->centre_name,
                        'student_numbers' => $item->total_student_numbers
                    ];
                })->values(),
            ];
        })->values()->toArray();

        return $monthly_report;
    }

    public function getYearlyReport(Request $request)
    {
        $currentYear = Carbon::now()->year;

        $result = DB::table('crm_pipeline_programs')
            ->rightJoin('crm_pipeline_progress', 'crm_pipeline_programs.progress_id', '=', 'crm_pipeline_progress.id')
            ->rightJoin('crm_pipelines', 'crm_pipeline_progress.pipeline_id', '=', 'crm_pipelines.id')
            ->select(
                DB::raw('MONTH(crm_pipelines.date_start) as month'),
                DB::raw('SUM(crm_pipeline_programs.student_numbers) as total_student_numbers'),
                'crm_pipeline_progress.case_status_id',
                DB::raw('COUNT(DISTINCT crm_pipelines.id) as total_centres'),
                'crm_pipelines.school_name as centre_name'
            )
            ->groupBy('month', 'crm_pipeline_progress.case_status_id', 'crm_pipelines.school_name')
            ->when(!$request->date, function ($query) use ($currentYear) {
                return $query->whereYear('crm_pipelines.date_start', $currentYear);
            })
            ->when($request->date, function ($query) use ($request) {
                return $query->whereYear('crm_pipelines.date_start', $request->date);
            })
            ->when($request->sales_person, function ($query) use ($request) {
                return $query->where('crm_pipelines.assignee_user_id', $request->sales_person);
            })
            ->when($request->program, function ($query) use ($request) {
                return $query->where('crm_pipeline_programs.program_id', $request->program);
            })
            ->get();

        $yearly_report = $result->groupBy('month')->map(function ($items, $month) {
            return [
                'month' => $month,
                'case_statuses' => $items->groupBy('case_status_id')->map(function ($statusItems, $caseStatusId) {
                    return [
                        'case_status_id' => $caseStatusId,
                        'centre_names' => $statusItems->map(function ($item) {
                            return [
                                'name' => $item->centre_name,
                                'student_numbers' => $item->total_student_numbers
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->values()->toArray();

        return $yearly_report;
    }
}
