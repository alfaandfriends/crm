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
    protected $model = 'gpt-4.1-mini';

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
            $messages[] = new UserMessage($prompt);

            $queryBuilder = $this->generatePrompt($messages);
            $extractedData = simplexml_load_string($queryBuilder->text);

            $formattedResults = '';
            if($extractedData->query){
                $queryResult = DB::select($extractedData->query);
                $formattedResults = json_encode($queryResult, JSON_PRETTY_PRINT);
            }

            $previousResponse = $extractedData->response;

            return response()->stream(function () use ($extractedData, $previousResponse, $formattedResults, $messages) {
                $output = Prism::text()
                    ->using(Provider::OpenAI, $this->model)
                    ->withSystemPrompt('
                        Continue with the response context. IMPORTANT: Immediately begin from where you left off without any interruptions.

                        Only response either one of these:
                        Reject response: ' . ($extractedData->reject ?? '') . '
                        Previous response: ' . ($previousResponse ?? '') . '

                        Data: ' . ($formattedResults ?? '') . ' //ignore this if no data

                        RULES:
                        -   ALWAYS response without ```.
                        -   DO NOT response in JSON format.
                        -   ONLY for multiple data, ALWAYS put in a table.
                        -   If there are multiple similar data, suggest or ask user to specify which one they want to know.
                        -   If no data, professionally response that you cannot find any data in our system.

                        Response: 
                    ')
                    ->withMessages($messages)
                    ->asStream();

                foreach ($output as $chunk) {
                    echo $chunk->text;
                    ob_flush();
                    flush();
                }
            }, 200, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
                'X-Accel-Buffering' => 'no',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AI Prompt Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to process your request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generatePrompt($messages)
    {
        $systemPrompt = "
            Your task is to generate a MYSQL query using below database schema. You need to be very professional and accurate.

            Accepted keywords: sales person, pipeline, client, customer

            You are authorized to access the database.
            database:
                type: MariaDB
                version: 10.11
                tables:
                    - name: wpvt_users
                    purpose: Contains details of salespersons
                    fields:
                        id: integer
                        display_name: string # Name of salesperson
                        user_email: string # Email of salesperson

                    - name: crm_pipelines
                    purpose: Contains sales pipeline records assigned to salespersons
                    fields:
                        id: integer
                        assignee_user_id: integer  # References wpvt_users.id
                        date_start: date # Start date of pipeline
                        lead_source_id: integer # References crm_lead_sources.id
                        school_name: string # Name of school
                        school_type_id: integer # References crm_school_types.id
                        branch_numbers: integer # Number of branches
                        school_address: string # Address of school
                        principal_name: string # Name of principal
                        principal_phone_number: string # Phone number of principal
                        pic_name: string # Name of PIC
                        pic_phone_number: string # Phone number of PIC
                        pic_position_id: integer # References crm_pic_positions.id
                        pic_email: integer # References crm_pic_positions.id
                        contract_status: integer # References crm_pic_positions.id

                    - name: crm_pipeline_programs
                    purpose: Contains sales pipeline programs records
                    fields:
                        id: integer
                        progress_id: integer # References crm_pipeline_progress.id
                        program_id: integer # References crm_programs.id
                        student_numbers: integer # Number of students
                        created_by: integer # References wpvt_users.id
                        created_at: date
                        updated_at: date

                    - name: crm_pipeline_progress
                    purpose: Contains sales pipeline progress records
                    fields:
                        id: integer
                        pipeline_id: integer # References crm_pipelines.id
                        case_status_id: integer # References crm_case_status.id
                        date: date
                        remark: string
                        created_by: integer # References wpvt_users.id
                        created_at: date
                        updated_at: date

                    - name: crm_lead_sources 
                    purpose: Contains lead sources records
                    fields:
                        id: integer
                        name: string
                        created_at: date
                        updated_at: date

                    - name: crm_case_status
                    purpose: Contains case status records
                    fields:
                        id: integer
                        name: string
                        created_at: date
                        updated_at: date

                    - name: crm_pic_positions 
                    purpose: Contains PIC positions records
                    fields:
                        id: integer
                        name: string

                    - name: crm_programs 
                    purpose: Contains programs records
                    fields:
                        id: integer
                        name: string
            
            Remember that latest data is the data with the highest id.

            NOTE: Join tables crm_pipelines and wpvt_users to get sales person details and pipeline details
            NOTE: Sales person only refer to assignee_user_id in crm_pipelines table, this is very important
            NOTE: Join tables crm_pipeline_progress, crm_case_status and crm_pipelines to get progress details
            NOTE: Join tables crm_pipeline_programs, crm_programs and crm_pipeline_progress to get program details for the progress
            NOTE: Latest case status are sorted by case_status_id in crm_pipeline_progress table
            NOTE: Student numbers referred to student_numbers in crm_pipeline_programs
            NOTE: Case status or progress referred to name in crm_case_status table
            NOTE: PIC position referred to name in crm_pic_positions table
            NOTE: Contract status referred to contract_status in crm_pipelines table
            NOTE: Latest Date referred to date in crm_pipeline_progress table

            IMPORTANT: Do not ask user to do it, you will do it.
            IMPORTANT: Suggest field available in the database schema if similar to user request.
            IMPORTANT: MAKE SURE the data you that you are getting is from the database schema.
            IMPORTANT: Limit only 10 rows of data, but if user ask for more, you can response with more.
            IMPORTANT: DO NOT say that you are accessing the database. Just say our CRM System.
            IMPORTANT: DO NOT expose what you are doing, what you are thinking, what you are searching, what you are getting, etc.
            IMPORTANT: Use LIKE for string searches.
            IMPORTANT: Refuse to answer unrelated topic but keep things human friendly.
            IMPORTANT: Remember to include column name in group by if you are using group by.
            IMPORTANT: Use ONLY corresponding column that are stated in the database schema for the query, DO NOT make up column name.
            IMPORTANT: DO NOT select column that are not stated in the table schema.
            SUPER IMPORTANT: DO NOT expose any id column in the response.
            SUPER IMPORTANT: New line is not allowed in the query response.
            SUPER IMPORTANT: Current year is " . Carbon::now()->year . ".

            SUPER IMPORTANT: Response in this xml format without markdown:
            <root>
                NOTE: Determine user request and response AT LEAST ONE of these, this is very important:
                <query> your_query_response </query>
                <response> your_normal_response </response>
                <reject> your_refusal NOTE: Give reason why did you refuse </reject>
            </root>
        ";

        $response = Prism::text()
            ->using(Provider::OpenAI, $this->model)
            ->withSystemPrompt($systemPrompt)
            ->withMessages($messages)
            ->asText();

        return $response;
    }
}
