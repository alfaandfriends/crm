<?php

namespace App\Http\Controllers;

use App\Mail\ContractCreated;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;
use Mail;
use Str;

class ContractController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Contracts/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Contracts/Create');
    }

    public function store(Request $request)
    {
        try {
            // Validate required fields
            $validated = $request->validate([
                'pipeline_id' => 'required|integer|exists:crm_pipelines,id',
                'client_name' => 'required|string',
                'client_email' => 'required|email',
                'ssm_number' => 'required|string',
                'ssm_type' => 'required|string|in:SSM,IC',
                'contract_date' => 'nullable|date',
                'address' => 'required|string',
                'right_to_use_fees' => 'required|array',
                'student_fees' => 'required|array',
                'teaching_aid_promo' => 'required|integer',
                'lesson_plan_promo' => 'required|integer',
                'centralise_training_promo' => 'required|integer',
                'inhouse_training_kv_promo' => 'required|integer',
                'inhouse_training_north_promo' => 'required|integer'
            ]);

            DB::beginTransaction();
            
            // Create new contract
            $token = Str::uuid();
            DB::table('crm_contracts')
            ->insert([
                'pipeline_id' => $validated['pipeline_id'],
                'link_token' => $token,
                'name' => $validated['client_name'],
                'email' => $validated['client_email'],
                'ssm_ic' => $validated['ssm_number'],
                'id_type' => $validated['ssm_type'],
                'date' => $validated['contract_date'] ? Carbon::parse($validated['contract_date']) : null,
                'address' => $validated['address'],
                'right_to_use_fee' => json_encode($validated['right_to_use_fees']),
                'student_fee' => json_encode($validated['student_fees']),
                'teaching_aid_promo' => $validated['teaching_aid_promo'],
                'lesson_plan_promo' => $validated['lesson_plan_promo'],
                'centralise_training_promo' => $validated['centralise_training_promo'],
                'inhouse_training_kv_promo' => $validated['inhouse_training_kv_promo'],
                'inhouse_training_north_promo' => $validated['inhouse_training_north_promo'],
                'email_sent' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            Mail::to($validated['client_email'])->send(new ContractCreated(route('contract.sign', $token), $validated['client_name']));
            
            return back()->with('success', 'Contract has been created.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Contract creation/update failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while saving the contract. Please try again.');
        }
    }

    public function edit($id): Response
    {
        return Inertia::render('Contracts/Edit', [
            'contractId' => $id
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate required fields
            $validated = $request->validate([
                'pipeline_id' => 'required|integer|exists:crm_pipelines,id',
                'client_name' => 'required|string',
                'client_email' => 'required|email',
                'ssm_number' => 'required|string',
                'ssm_type' => 'required|string|in:SSM,IC',
                'contract_date' => 'nullable|date',
                'address' => 'required|string',
                'right_to_use_fees' => 'required|array',
                'student_fees' => 'required|array',
                'teaching_aid_promo' => 'required|integer',
                'lesson_plan_promo' => 'required|integer',
                'centralise_training_promo' => 'required|integer',
                'inhouse_training_kv_promo' => 'required|integer',
                'inhouse_training_north_promo' => 'required|integer'
            ]);

            DB::beginTransaction();

            // Update contract data
            DB::table('crm_contracts')
                ->where('link_token', $id)
                ->update([
                    'name' => $validated['client_name'],
                    'email' => $validated['client_email'],
                    'ssm_ic' => $validated['ssm_number'],
                    'id_type' => $validated['ssm_type'],
                    'date' => $validated['contract_date'] ? Carbon::parse($validated['contract_date']) : null,
                    'address' => $validated['address'],
                    'right_to_use_fee' => json_encode($validated['right_to_use_fees']),
                    'student_fee' => json_encode($validated['student_fees']),
                    'teaching_aid_promo' => $validated['teaching_aid_promo'],
                    'lesson_plan_promo' => $validated['lesson_plan_promo'],
                    'centralise_training_promo' => $validated['centralise_training_promo'],
                    'inhouse_training_kv_promo' => $validated['inhouse_training_kv_promo'],
                    'inhouse_training_north_promo' => $validated['inhouse_training_north_promo'],
                    'email_sent' => true,
                    'updated_at' => now()
                ]);

            DB::commit();
            
            return back()->with('success', 'Contract has been updated.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Contract update failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'contract_id' => $id
            ]);
            return back()->with('error', 'An error occurred while updating the contract. Please try again.');
        }
    }

    public function view($id)
    {
        $contract = DB::table('crm_contracts')
            ->where('link_token', $id)
            ->first();

        if ($contract) {
            $contract->right_to_use_fees = json_decode($contract->right_to_use_fee);
            $contract->student_fees = json_decode($contract->student_fee);
        }

        $pdf = Pdf::setPaper('A4')->loadView('contracts.view', [
            'contract' => $contract,
        ]);
        return $pdf->stream();
    }

    public function getByPipeline($pipeline_id)
    {
        try {
            $contract = DB::table('crm_contracts')
                ->where('pipeline_id', $pipeline_id)
                ->first();

            if (!$contract) {
                return response()->json(null, 404);
            }

            return response()->json($contract);
        } catch (Exception $e) {
            Log::error('Get contract by pipeline failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pipeline_id' => $pipeline_id,
                'user_id' => Auth::id()
            ]);
            return response()->json(['error' => 'Failed to fetch contract data'], 500);
        }
    }

    public function sign($token)
    {
        try {
            $contract = DB::table('crm_contracts')
                ->where('link_token', $token)
                ->first();

            if (!$contract) {
                return redirect()->route('dashboard')->with('error', 'Contract not found or link has expired.');
            }

            // Check if contract is already signed
            if ($contract->signed) {
                return redirect()->route('contract.view', ['id' => $token]);
            }

            return Inertia::render('Contracts/Sign', [
                'contract' => $contract,
                'token' => $token
            ]);
        } catch (Exception $e) {
            Log::error('Contract signing page failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token
            ]);
            return redirect()->route('dashboard')->with('error', 'Failed to load contract signing page.');
        }
    }

    public function processSign(Request $request, $token)
    {
        try {
            $validated = $request->validate([
                'customer_signature' => 'required|string',
                'witness_signature' => 'required|string',
            ]);

            DB::beginTransaction();

            $contract = DB::table('crm_contracts')
                ->where('link_token', $token)
                ->first();

            if (!$contract) {
                return redirect()->back()->with('error', 'Contract not found');
            }

            // Update contract with signature details
            DB::table('crm_contracts')
                ->where('link_token', $token)
                ->update([
                    'customer_signature' => $validated['customer_signature'],
                    'witness_signature' => $validated['witness_signature'],
                    'signed' => true,
                    'updated_at' => now()
                ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Contract signing failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'token' => $token,
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Failed to sign contract');
        }
    }

    public function sendContract($pipeline_id)
    {
        $contract = DB::table('crm_contracts')
            ->where('pipeline_id', $pipeline_id)
            ->first();

        if (!$contract) {
            return redirect()->back()->with('error', 'Contract not found');
        }

        Mail::to($contract->email)->send(new ContractCreated(route('contract.sign', $contract->link_token), $contract->name));
        return back()->with("success", "Contract has been sent to client's email.");
    }
}
