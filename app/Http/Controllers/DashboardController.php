<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Centre;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\ValueObjects\Messages\UserMessage;  
use Prism\Prism\ValueObjects\Messages\AssistantMessage;

class DashboardController extends Controller
{
    protected $openAI;
    protected $model = 'gpt-4o-mini';

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

    public function handleAIPrompt(Request $request)
    {
        try {
            $prompt = $request->input('prompt');
            $conversationHistory = $request->input('conversation_history', []);
            
            if (empty($prompt)) {
                return response()->json([
                    'error' => 'Prompt is required'
                ], 400);
            }

            $messages = [];
            foreach ($conversationHistory as $message) {
                if ($message['role'] === 'user') {
                    $messages[] = new UserMessage($message['content']);
                } else if ($message['role'] === 'assistant') {
                    $messages[] = new AssistantMessage($message['content']);
                }
            }
            // Add the current prompt
            $messages[] = new UserMessage($prompt);

            $queryBuilder = $this->generateSQL($prompt, $messages);

            $extractedData = simplexml_load_string($queryBuilder->text);

            if($extractedData->query){
                $queryResult = DB::select($extractedData->query);
                $formattedResults = json_encode($queryResult, JSON_PRETTY_PRINT);
            }

            if($extractedData->response){
                $response = $extractedData->response;
            }

            if($extractedData->reject){
                return response()->json([
                    'response' => (string) $extractedData->reject[0],
                ]);
            }

            $output = Prism::text()
                ->using(Provider::OpenAI, $this->model)
                ->withSystemPrompt('
                    You are a information assistant. Response to user using understandable language.

                    Data: ' . ($formattedResults ?? '') . ' //ignore this if no data
                    If there are multiple similar data, suggest or ask user to specify which one they want to know.

                    Response: ' . ($response ?? '') . ' //ignore this if no response
                ')
                ->withMessages($messages)
                ->asText();

            return response()->json([
                'response' => $output->text,
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Prompt Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to process your request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateSQL(string $naturalLanguageQuery)
    {
        $prompt = "
        You are a pipeline information assistant. Help users with pipeline-related queries.

        Database schema:
        wpvt_users (id, display_name, user_email) - sales person details
        crm_pipelines (id, assignee_user_id, date_start, lead_source_id, school_name, school_type_id, branch_numbers, school_address, principal_name, principal_phone_number, pic_name, pic_phone_number, pic_position_id) - pipeline details that sales person is assigned to
        
        Key relationships:
        - assignee_user_id -> wpvt_users.id
        - lead_source_id -> crm_lead_sources.id
        - school_type_id -> crm_school_types.id
        - pic_position_id -> crm_pic_positions.id

        Rules:
        - Use LIKE for string searches
        - Join related tables
        - Use exact table names
        - Don't expose table/column names or IDs
        - Only return pipeline-related data
        - Include user names
        - No markdown or code blocks
        - Wrap SQL in <query> tags
        - Wrap responses in <response> tags
        - Wrap rejections in <reject> tags
        - All tags within <root> tags

        User query: \"{$naturalLanguageQuery}\"";

        $response = Prism::text()
            ->using(Provider::OpenAI, $this->model)
            ->withPrompt($prompt)
            ->asText();

        return $response;
    }
}
