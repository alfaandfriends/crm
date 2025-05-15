<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CopyPipelineProgress extends Command
{
    protected $signature = 'pipeline:copy-progress';
    protected $description = 'Copy pipeline progress with No Progress remark on 28th of each month';

    public function handle()
    {
        try {
            DB::beginTransaction();

            // Get all active pipelines
            $pipelines = DB::table('crm_pipelines')->get();

            foreach ($pipelines as $pipeline) {
                // Get the latest progress for this pipeline
                $latest_progress = DB::table('crm_pipeline_progress')
                    ->where('pipeline_id', $pipeline->id)
                    ->latest()
                    ->first();

                if ($latest_progress) {
                    // Create new progress entry
                    $progress_id = DB::table('crm_pipeline_progress')->insertGetId([
                        'pipeline_id' => $pipeline->id,
                        'case_status_id' => $latest_progress->case_status_id,
                        'date' => Carbon::now(),
                        'remark' => 'No Progress',
                        'created_by' => 1 // System user ID
                    ]);

                    // Copy the programs from latest progress
                    $programs = DB::table('crm_pipeline_programs')
                        ->where('progress_id', $latest_progress->id)
                        ->get();

                    foreach ($programs as $program) {
                        DB::table('crm_pipeline_programs')->insert([
                            'progress_id' => $progress_id,
                            'program_id' => $program->program_id,
                            'student_numbers' => $program->student_numbers,
                            'created_by' => 1 // System user ID
                        ]);
                    }
                }
            }

            DB::commit();
            $this->info('Pipeline progress copied successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to copy pipeline progress: ' . $e->getMessage());
        }
    }
} 