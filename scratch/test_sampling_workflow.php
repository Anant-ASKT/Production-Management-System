<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Sampling\SamplingCompany;
use App\Models\Sampling\SamplingUser;
use App\Models\Sampling\SamplingDivision;
use App\Models\Sampling\SamplingProject;
use App\Models\Sampling\SamplingBatch;
use App\Models\Sampling\SamplingSample;
use App\Models\Sampling\SamplingBom;
use App\Models\Sampling\SamplingOperation;
use App\Models\Sampling\SamplingSpecification;
use App\Models\Sampling\SamplingMeasurement;
use App\Models\Sampling\SamplingPattern;
use App\Models\Sampling\SamplingFinalImage;
use App\Models\Sampling\SamplingProductionNote;
use App\Models\Sampling\SamplingStorageLocation;
use App\Models\Sampling\SamplingPhysicalStorage;
use App\Models\Sampling\SamplingCosting;
use App\Services\Sampling\FreezeChecklistService;
use App\Services\Sampling\SampleFreezeService;
use Illuminate\Support\Facades\Hash;

echo "=== STARTING SAMPLING MODULE VERIFICATION TEST ===\n";

// 1. Create or Find Test Company
$company = SamplingCompany::firstOrCreate(
    ['code' => 'TEST-SMP-001'],
    [
        'name' => 'Kashmir Artisan Craft Studio',
        'contact_person' => 'Farooq Ahmed',
        'email' => 'studio@kashmircraft.com',
        'phone' => '+91 99060 12345',
        'status' => 'active'
    ]
);
echo "1. Company Verified: [{$company->code}] {$company->name} (ID: {$company->id})\n";

// 2. Create Division
$division = SamplingDivision::firstOrCreate(
    ['sampling_company_id' => $company->id, 'name' => 'Knit'],
    ['status' => 'active']
);
echo "2. Division Verified: {$division->name} (ID: {$division->id})\n";

// 3. Create User
$user = SamplingUser::firstOrCreate(
    ['email' => 'artisan@kashmircraft.com'],
    [
        'sampling_company_id' => $company->id,
        'name' => 'Ghulam Hassan',
        'password' => Hash::make('secret123'),
        'role' => 'sampling_manager',
        'division_id' => $division->id,
        'status' => 'active'
    ]
);
echo "3. User Verified: {$user->name} ({$user->email})\n";

// 4. Create Project & Batch
$project = SamplingProject::firstOrCreate(
    ['sampling_company_id' => $company->id, 'project_code' => 'PRJ-2026-TEST'],
    [
        'project_name' => 'Autumn Winter Pure Cashmere 2026',
        'start_date' => now()->toDateString(),
        'status' => 'open',
        'created_by' => $user->id
    ]
);

$batch = SamplingBatch::firstOrCreate(
    ['sampling_project_id' => $project->id, 'batch_number' => 'B01'],
    [
        'batch_name' => 'Hand Knitted Sweaters Batch 1',
        'start_date' => now()->toDateString(),
        'status' => 'open',
        'created_by' => $user->id
    ]
);
echo "4. Project & Batch Verified: {$project->project_code} -> {$batch->batch_number}\n";

// 5. Create Sample
$sample = SamplingSample::firstOrCreate(
    ['sample_code' => 'SAM-2026-TEST-B01-S01'],
    [
        'sampling_company_id' => $company->id,
        'sampling_project_id' => $project->id,
        'sampling_batch_id' => $batch->id,
        'style_name' => 'Chunky Cashmere Cable Sweater',
        'overall_division_id' => $division->id,
        'assigned_person_id' => $user->id,
        'approval_status' => 'approved', // Pre-approve for freeze testing
        'approval_date' => now()->toDateString(),
        'approved_by' => $user->id,
        'is_frozen' => false,
        'priority' => 'high',
        'created_by' => $user->id
    ]
);
echo "5. Sample Verified: {$sample->sample_code} (Approved: {$sample->approval_status})\n";

// 6. Add BOM (Test formula: gross_quantity = net * (1 + wastage/100), material_cost = gross * rate)
SamplingBom::where('sample_id', $sample->id)->delete();
$bom = SamplingBom::create([
    'sample_id' => $sample->id,
    'material_category' => 'Yarn',
    'description' => '100% Cashmere 2/28 Natural Grey',
    'unit_of_measure' => 'kg',
    'net_quantity' => 0.5000,
    'wastage_percentage' => 10.00, // 10%
    'cost_rate' => 8000.0000,
]);
echo "6. BOM Formula Check: Net: {$bom->net_quantity}, Gross: {$bom->gross_quantity} (Expected: 0.55), Cost: ₹{$bom->material_cost} (Expected: 4400.00)\n";
assert($bom->gross_quantity == 0.5500, 'BOM gross quantity calculation mismatch!');
assert($bom->material_cost == 4400.0000, 'BOM material cost calculation mismatch!');

// 7. Add Operation
SamplingOperation::where('sample_id', $sample->id)->delete();
$op = SamplingOperation::create([
    'sample_id' => $sample->id,
    'sequence_number' => 1,
    'operation_name' => 'Hand Knitting Front and Back',
    'division_id' => $division->id,
    'estimated_time_minutes' => 120, // 2 hours
    'labour_rate_per_hour' => 300.00,
]);
echo "7. Operation Check: Time: {$op->estimated_time_minutes} min, Labour Cost: ₹{$op->estimated_labour_cost} (Expected: 600.00)\n";
assert($op->estimated_labour_cost == 600.00, 'Labour cost calculation mismatch!');

// 8. Add Technical Final Photo
SamplingFinalImage::where('sample_id', $sample->id)->delete();
$img = SamplingFinalImage::create([
    'sample_id' => $sample->id,
    'image_type' => 'front',
    'file_path' => 'uploads/sampling/photos/test_front.jpg',
    'caption' => 'Front cable technical view',
    'uploaded_by' => $user->id
]);

// 9. Add Mandatory Production Note (Written + Audio)
SamplingProductionNote::where('sample_id', $sample->id)->delete();
$pNote = SamplingProductionNote::create([
    'sample_id' => $sample->id,
    'sequence' => 1,
    'subject' => 'Critical Cashmere Tension & Wash Behavior',
    'division_id' => $division->id,
    'written_note' => 'Do not hard steam rib cuffs before linking. Use luke warm soak only; cashmere will relax by 4% in width.',
    'voice_audio_path' => 'uploads/sampling/notes/voice_test.webm',
    'audio_duration_seconds' => 35,
    'created_by' => $user->id
]);
echo "9. Production Note Check: Subject: '{$pNote->subject}', Audio Duration: {$pNote->audio_duration_seconds}s\n";

// 10. Storage Location & Physical Storage
$storageLoc = SamplingStorageLocation::firstOrCreate(
    ['sampling_company_id' => $company->id, 'studio_name' => 'Srinagar Studio', 'room' => 'Archive Room A'],
    ['rack' => 'Rack 3', 'shelf' => 'Shelf B', 'box' => 'Box 09', 'status' => 'active']
);

SamplingPhysicalStorage::where('sample_id', $sample->id)->delete();
$phys = SamplingPhysicalStorage::create([
    'sample_id' => $sample->id,
    'storage_location_id' => $storageLoc->id,
    'date_stored' => now()->toDateString(),
    'stored_by' => $user->id,
    'sample_condition' => 'Approved Master Reference',
    'quantity' => 1
]);

// 11. Evaluate Freeze Readiness Checklist
$checklistService = new FreezeChecklistService();
$eval = $checklistService->evaluate($sample);
echo "11. Freeze Checklist Evaluation: Passed {$eval['passed_count']}/{$eval['total_count']} checks. Can Freeze: " . ($eval['can_freeze'] ? 'YES' : 'NO') . "\n";
assert($eval['can_freeze'] === true, 'Sample should be eligible to freeze!');

// 12. Freeze Sample (Generates Revision 0)
$freezeService = new SampleFreezeService($checklistService);
$revision0 = $freezeService->freeze($sample, $user->id, 'Initial Frozen Production Master');
$sample->refresh();

echo "12. Freeze Verification: Sample is_frozen = " . ($sample->is_frozen ? 'TRUE' : 'FALSE') . ", Revision Code = {$revision0->revision_code}\n";
assert($sample->is_frozen === true, 'Sample should be marked as frozen!');
assert($sample->current_revision_id === $revision0->id, 'Current revision ID not matching!');

// Verify child items are stamped with revision ID
$stampedBomCount = SamplingBom::where('sampling_revision_id', $revision0->id)->count();
$stampedOpCount = SamplingOperation::where('sampling_revision_id', $revision0->id)->count();
$stampedNoteCount = SamplingProductionNote::where('sampling_revision_id', $revision0->id)->count();
echo "    Stamped items for Revision 0: BOMs: {$stampedBomCount}, Ops: {$stampedOpCount}, Production Notes: {$stampedNoteCount}\n";
assert($stampedBomCount >= 1 && $stampedOpCount >= 1 && $stampedNoteCount >= 1, 'Child records were not stamped with revision ID!');

// 13. Test Revision Cloning (Create Next Draft Revision)
echo "13. Testing Next Draft Revision creation (e.g. Preparing for Rev 1)...\n";
$freezeService->createNextDraftRevision($sample, 'Adjusted stitch tension based on production trial');
$sample->refresh();

echo "    Sample unlocked for editing: is_frozen = " . ($sample->is_frozen ? 'TRUE' : 'FALSE') . "\n";
assert($sample->is_frozen === false, 'Sample should be unlocked into draft mode!');

// Verify that Revision 0 records still exist and are untouched
$origRevBomCount = SamplingBom::where('sampling_revision_id', $revision0->id)->count();
$newDraftBomCount = SamplingBom::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->count();
echo "    Revision 0 BOM count (preserved): {$origRevBomCount}, New Working Draft BOM count: {$newDraftBomCount}\n";
assert($origRevBomCount >= 1, 'Revision 0 BOMs were lost!');
assert($newDraftBomCount >= 1, 'New Draft BOMs were not cloned!');

echo "\n=== ALL 13 TEST CASES PASSED SUCCESSFULLY! ===\n";
