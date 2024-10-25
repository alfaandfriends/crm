<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Centre;
use Auth;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PipelineController extends Controller
{
    public function index(Request $request)
    {
        $pipelines  =   DB::table('crm_pipelines')
                            ->join('crm_school_types', 'crm_pipelines.school_type_id', '=', 'crm_school_types.id')
                            ->when(request('search'), function($query, $search) {
                                $query->where('crm_pipelines.school_name', 'LIKE', "%".$search."%");
                            })
                            ->when(!Auth::user()->hasRole('admin'), function($query, $search) {
                                $query->where('crm_pipelines.assignee_user_id', Auth::user()->ID);
                            })
                            ->select(
                                'crm_pipelines.id',
                                'crm_pipelines.school_name', 
                                'crm_school_types.name as school_type', 
                                'crm_pipelines.principal_name', 
                                'crm_pipelines.principal_phone_number', 
                                'crm_pipelines.school_address',)
                            ->paginate(10);
        
        return Inertia::render('Pipelines/Index',[
            'filter'    => request()->all('search'),
            'pipelines' =>  $pipelines
        ]);
    }

    public function create()
    {
        $lead_sources           =   DB::table('crm_lead_sources')->get();
        $school_types           =   DB::table('crm_school_types')->get();
        $pic_positions          =   DB::table('crm_pic_positions')->get();
        $case_status            =   DB::table('crm_case_status')->get();
        $programs               =   DB::table('crm_programs')->get();
        
        return Inertia::render('Pipelines/Create',[
            'lead_sources'          => $lead_sources,
            'school_types'          => $school_types,
            'pic_positions'         => $pic_positions,
            'case_status'           => $case_status,
            'programs'              => $programs
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'assign_to'                 => 'required',
            'school_type'               => 'required',
            'school_name'               => 'required|unique:crm_pipelines,school_name',
            'address'                   => 'required',
            'principal_name'            => 'required',
            'principal_phone_number'    => 'required',
        ],[
            'assign_to.required'                => 'The assignee field is required.',
            'school_type.required'              => 'The school type field is required.',
            'school_name.required'              => 'The school name field is required.',
            'school_name.unique'                => 'The school name already exists.',
            'address.required'                  => 'The school address field is required.',
            'principal_name.required'           => 'The principal field is required.',
            'principal_phone_number.required'   => 'The principal phone number field is required.',
        ]);
        
        try {
            DB::beginTransaction();

            if($request->file('quotation')){
                $quotation_file         =   $request->file('quotation');
                $quotation_file_name    =   date('Ymd_His').'_'.$quotation_file->getClientOriginalName();
                $quotation_path         =   $quotation_file->storeAs('pipelines', $quotation_file_name, 'public');
                $quotation_url          =   Storage::url($quotation_path);
            }

            if($request->file('contract')){
                $contract_file          =   $request->file('contract');
                $contract_file_name     =   date('Ymd_His').'_'.$contract_file->getClientOriginalName();
                $contract_path          =   $contract_file->storeAs('pipelines', $contract_file_name, 'public');
                $contract_url           =   Storage::url($contract_path);
            }

            $pipeline_id    =   DB::table('crm_pipelines')->insertGetId([
                'assignee_user_id'          =>  $request->assign_to,
                'date_start'                =>  Carbon::parse($request->date_start),
                'lead_source_id'            =>  $request->lead_source,
                'school_name'               =>  $request->school_name,
                'school_type_id'            =>  $request->school_type,
                'branch_numbers'            =>  $request->branch_number,
                'school_address'            =>  $request->address,
                'principal_name'            =>  $request->principal_name,
                'principal_phone_number'    =>  $request->principal_phone_number,
                'pic_name'                  =>  $request->pic_name,
                'pic_phone_number'          =>  $request->pic_phone_number,
                'pic_position_id'           =>  $request->pic_position,
                'pic_email'                 =>  $request->pic_email,
                'quotation_file_name'       =>  $quotation_file_name ?? '',
                'quotation_file_path'       =>  $quotation_url ?? '',
                'contract_file_name'        =>  $contract_file_name ?? '',
                'contract_file_path'        =>  $contract_url ?? '',
                'created_by'                =>  Auth::user()->ID
            ]);

            if(count($request->progress_status)){
                foreach($request->progress_status as $progress_status){
                    $progress_id = DB::table('crm_pipeline_progress')->insertGetId([
                        'pipeline_id'       =>  $pipeline_id,
                        'case_status_id'    =>  $progress_status['case_status'],
                        'date'              =>  Carbon::parse($progress_status['date']),
                        'remark'            =>  $progress_status['remark'],
                        'created_by'        =>  Auth::user()->ID
                    ]);

                    foreach($progress_status['signed_up_programs'] as $program){
                        DB::table('crm_pipeline_programs')->insert([
                            'progress_id'       =>  $progress_id,
                            'program_id'        =>  $program['id'],
                            'student_numbers'   =>  $program['student_numbers'],
                            'created_by'        =>  Auth::user()->ID
                        ]);
                    }
                }
            }
                
            DB::commit();
        
            return redirect()->route('pipelines')->with(['type' => 'success', 'message' => 'Data has been added']);
        } catch (Exception $e) {
            Log::error('Insert pipeline failed: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->route('pipelines')->with(['type' => 'error', 'message' => 'An error has occured']);
        }
        
    }

    public function edit($id)
    {
        $pipeline_info      =   DB::table('crm_pipelines')
                                    ->join('wpvt_users', 'crm_pipelines.assignee_user_id', '=', 'wpvt_users.id')
                                    ->select('crm_pipelines.*', 'wpvt_users.display_name as assignee_name')
                                    ->where('crm_pipelines.id', $id)
                                    ->first();

        $progress_status    =   DB::table('crm_pipeline_programs')
                                    ->join('crm_pipeline_progress', 'crm_pipeline_programs.progress_id', '=', 'crm_pipeline_progress.id')
                                    ->join('crm_case_status', 'crm_pipeline_progress.case_status_id', '=', 'crm_case_status.id')
                                    ->join('crm_programs', 'crm_pipeline_programs.program_id', '=', 'crm_programs.id')
                                    ->select(
                                        'crm_pipeline_progress.id as progress_id', 
                                        'crm_pipeline_progress.case_status_id as case_status', 
                                        'crm_case_status.name as case_status_name', 
                                        'crm_pipeline_progress.date as date', 
                                        'crm_pipeline_progress.remark as remark', 
                                        'crm_pipeline_programs.program_id as program_id', 
                                        'crm_programs.name as program_name', 
                                        'crm_pipeline_programs.student_numbers as student_numbers', 
                                    )
                                    ->where('crm_pipeline_progress.pipeline_id', $id)
                                    ->get();

        $collection = collect($progress_status);

        $pipeline_progress = $collection->groupBy('progress_id')->map(function ($items, $index) {
            $firstItem = $items->first();
        
            $signedUpPrograms = $items->map(function ($item) {
                return [
                    'id' => $item->program_id,
                    'name' => $item->program_name,
                    'student_numbers' => $item->student_numbers
                ];
            });
        
            return [
                'progress_id' => $firstItem->progress_id,
                'case_status' => $firstItem->case_status,
                'case_status_name' => $firstItem->case_status_name,
                'date' => $firstItem->date,
                'remark' => $firstItem->remark,
                'signed_up_programs' => $signedUpPrograms->all(),
            ];
        });
        
        $pipeline_progress = $pipeline_progress->values();

        $lead_sources   =   DB::table('crm_lead_sources')->get();
        $school_types   =   DB::table('crm_school_types')->get();
        $pic_positions  =   DB::table('crm_pic_positions')->get();
        $case_status    =   DB::table('crm_case_status')->get();
        $programs       =   DB::table('crm_programs')->get();

        return Inertia::render('Pipelines/Edit',[
            'pipeline_info'     => $pipeline_info,
            'pipeline_progress' => $pipeline_progress,
            'lead_sources'      => $lead_sources,
            'school_types'      => $school_types,
            'pic_positions'     => $pic_positions,
            'case_status'       => $case_status,
            'programs'          => $programs
        ]);
    }

    public function update(Request $request){
        $request->validate([
            'assign_to'                 => 'required',
            'school_type'               => 'required',
            'school_name'               => 'required|unique:crm_pipelines,school_name,' . $request->pipeline_id . ',id',
            'address'                   => 'required',
            'principal_name'            => 'required',
            'principal_phone_number'    => 'required',
        ],[
            'assign_to.required'                => 'The assignee field is required.',
            'school_type.required'              => 'The school type field is required.',
            'school_name.required'              => 'The school name field is required.',
            'school_name.unique'                => 'The school name already exists.',
            'address.required'                  => 'The school address field is required.',
            'principal_name.required'           => 'The principal field is required.',
            'principal_phone_number.required'   => 'The principal phone number field is required.',
        ]);

        try {
            DB::beginTransaction();

            $pipeline_info  =   DB::table('crm_pipelines')->where('id', $request->pipeline_id)->select('quotation_file_name', 'quotation_file_path', 'contract_file_name', 'contract_file_path')->first();
            
            $quotation_file_name    =   $pipeline_info->quotation_file_name;
            $quotation_url          =   $pipeline_info->quotation_file_path;
            $contract_file_name     =   $pipeline_info->contract_file_name;
            $contract_url           =   $pipeline_info->contract_file_path;

            if($request->delete_quotation){
                if (Storage::exists('pipelines/'.$pipeline_info->quotation_file_name)) {
                    Storage::delete('pipelines/'.$pipeline_info->quotation_file_name);
                }
                $quotation_file_name    =   '';
            }

            if($request->file('quotation')){
                $quotation_file         =   $request->file('quotation');
                $quotation_file_name    =   date('Ymd_His').'_'.$quotation_file->getClientOriginalName();
                $quotation_path         =   $quotation_file->storeAs('pipelines', $quotation_file_name);
                $quotation_url          =   Storage::url($quotation_path);
            }

            if($request->delete_contract){
                if (Storage::exists('pipelines/'.$pipeline_info->contract_file_name)) {
                    Storage::delete('pipelines/'.$pipeline_info->contract_file_name);
                }
                $contract_file_name     =   '';
            }

            if($request->file('contract')){
                $contract_file          =   $request->file('contract');
                $contract_file_name     =   date('Ymd_His').'_'.$contract_file->getClientOriginalName();
                $contract_path          =   $contract_file->storeAs('pipelines', $contract_file_name);
                $contract_url           =   Storage::url($contract_path);
            }

            DB::table('crm_pipelines')->where('id', $request->pipeline_id)->update([
                'assignee_user_id'          =>  $request->assign_to,
                'date_start'                =>  Carbon::parse($request->date_start),
                'lead_source_id'            =>  $request->lead_source,
                'school_name'               =>  $request->school_name,
                'school_type_id'            =>  $request->school_type,
                'branch_numbers'            =>  $request->branch_number,
                'school_address'            =>  $request->address,
                'principal_name'            =>  $request->principal_name,
                'principal_phone_number'    =>  $request->principal_phone_number,
                'pic_name'                  =>  $request->pic_name,
                'pic_phone_number'          =>  $request->pic_phone_number,
                'pic_position_id'           =>  $request->pic_position,
                'pic_email'                 =>  $request->pic_email,
                'quotation_file_name'       =>  $quotation_file_name,
                'quotation_file_path'       =>  $quotation_url,
                'contract_file_name'        =>  $contract_file_name,
                'contract_file_path'        =>  $contract_url,
                'created_by'                =>  Auth::user()->ID
            ]);

            DB::table('crm_pipeline_progress')->where('pipeline_id', $request->pipeline_id)->delete();
            if(count($request->progress_status)){
                foreach($request->progress_status as $progress_status){
                    $progress_id = DB::table('crm_pipeline_progress')->insertGetId([
                        'pipeline_id'       =>  $request->pipeline_id,
                        'case_status_id'    =>  $progress_status['case_status'],
                        'date'              =>  Carbon::parse($progress_status['date']),
                        'remark'            =>  $progress_status['remark'],
                        'created_by'        =>  Auth::user()->ID
                    ]);

                    foreach($progress_status['signed_up_programs'] as $program){
                        DB::table('crm_pipeline_programs')->insert([
                            'progress_id'       =>  $progress_id,
                            'program_id'        =>  $program['id'],
                            'student_numbers'   =>  $program['student_numbers'],
                            'created_by'        =>  Auth::user()->ID
                        ]);
                    }
                }
            }

            DB::commit();
        
            return redirect()->route('pipelines')->with(['type' => 'success', 'message' => 'Data has been saved.']);
        } catch (Exception $e) {
            Log::error('Insert pipeline failed: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->route('pipelines')->with(['type' => 'error', 'message' => 'An error has occured']);
        }
    }

    public function destroy($id){
        DB::table('crm_pipelines')->where('id', $id)->delete();
        DB::table('crm_pipeline_progress')->where('pipeline_id', $id)->delete();

        return redirect()->route('pipelines')->with(['type' => 'success', 'message' => 'Data has been deleted.']);
    }
}
