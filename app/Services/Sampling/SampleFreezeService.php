<?php

namespace App\Services\Sampling;

use App\Models\Sampling\SamplingSample;
use App\Models\Sampling\SamplingRevision;
use App\Models\Sampling\SamplingBom;
use App\Models\Sampling\SamplingOperation;
use App\Models\Sampling\SamplingSpecification;
use App\Models\Sampling\SamplingMeasurement;
use App\Models\Sampling\SamplingPattern;
use App\Models\Sampling\SamplingFinalImage;
use App\Models\Sampling\SamplingProductionNote;
use App\Models\Sampling\SamplingCosting;
use App\Models\Sampling\SamplingPhysicalStorage;
use Illuminate\Support\Facades\DB;
use Exception;

class SampleFreezeService
{
    protected FreezeChecklistService $checklistService;

    public function __construct(FreezeChecklistService $checklistService)
    {
        $this->checklistService = $checklistService;
    }

    /**
     * Formally freezes the sample into an immutable revision snapshot.
     */
    public function freeze(SamplingSample $sample, int $userId, ?string $changeSummary = null): SamplingRevision
    {
        $evaluation = $this->checklistService->evaluate($sample);
        if (!$evaluation['can_freeze']) {
            $failedMessages = array_map(fn($item) => $item['message'], array_filter($evaluation['checklist'], fn($item) => !$item['passed']));
            throw new Exception("Cannot freeze sample: " . implode(' ', $failedMessages));
        }

        return DB::transaction(function () use ($sample, $userId, $changeSummary, $evaluation) {
            // Determine next revision number
            $lastRevision = SamplingRevision::where('sample_id', $sample->id)->orderBy('revision_number', 'desc')->first();
            $nextRevNumber = $lastRevision ? ($lastRevision->revision_number + 1) : 0;
            $revisionCode = "{$sample->sample_code}-REV{$nextRevNumber}";

            // Calculate total direct cost if costing exists
            $costing = SamplingCosting::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->first();
            $directCost = $costing ? $costing->total_estimated_direct_cost : 0.0000;

            // 1. Create Revision Header
            $revision = SamplingRevision::create([
                'sample_id' => $sample->id,
                'revision_number' => $nextRevNumber,
                'revision_code' => $revisionCode,
                'frozen_date' => now(),
                'frozen_by' => $userId,
                'change_summary' => $changeSummary ?? ($nextRevNumber === 0 ? 'Initial Frozen Master Revision' : 'Updated Production Revision'),
                'is_active' => true,
                'checklist_snapshot_json' => $evaluation['checklist'],
                'direct_production_cost' => $directCost,
            ]);

            // Deactivate previous active revisions
            SamplingRevision::where('sample_id', $sample->id)
                ->where('id', '!=', $revision->id)
                ->update(['is_active' => false]);

            // 2. Stamp all active working draft items with this revision ID
            SamplingBom::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingOperation::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingSpecification::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingMeasurement::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingPattern::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingFinalImage::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingProductionNote::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingCosting::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);
            SamplingPhysicalStorage::where('sample_id', $sample->id)->whereNull('sampling_revision_id')->update(['sampling_revision_id' => $revision->id]);

            // 3. Mark sample as frozen
            $sample->update([
                'is_frozen' => true,
                'current_revision_id' => $revision->id,
            ]);

            return $revision;
        });
    }

    /**
     * Unlocks the sample by creating a draft clone of the existing revision
     * so modifications can be made towards a future Revision (e.g. Rev 1).
     * The existing revision remains 100% permanently frozen and unmodified!
     */
    public function createNextDraftRevision(SamplingSample $sample, string $reason): void
    {
        if (!$sample->is_frozen || !$sample->current_revision_id) {
            throw new Exception("Sample is not currently frozen.");
        }

        DB::transaction(function () use ($sample) {
            $sourceRevId = $sample->current_revision_id;

            // 1. Clone BOM items to draft (sampling_revision_id = NULL)
            $boms = SamplingBom::where('sampling_revision_id', $sourceRevId)->get();
            foreach ($boms as $bom) {
                $clone = $bom->replicate();
                $clone->sampling_revision_id = null;
                $clone->save();
            }

            // 2. Clone Operations
            $ops = SamplingOperation::where('sampling_revision_id', $sourceRevId)->get();
            foreach ($ops as $op) {
                $clone = $op->replicate();
                $clone->sampling_revision_id = null;
                $clone->save();
            }

            // 3. Clone Specifications
            $specs = SamplingSpecification::where('sampling_revision_id', $sourceRevId)->get();
            foreach ($specs as $spec) {
                $clone = $spec->replicate();
                $clone->sampling_revision_id = null;
                $clone->save();
            }

            // 4. Clone Measurements
            $measurements = SamplingMeasurement::where('sampling_revision_id', $sourceRevId)->get();
            foreach ($measurements as $m) {
                $clone = $m->replicate();
                $clone->sampling_revision_id = null;
                $clone->save();
            }

            // 5. Clone Patterns
            $patterns = SamplingPattern::where('sampling_revision_id', $sourceRevId)->get();
            foreach ($patterns as $p) {
                $clone = $p->replicate();
                $clone->sampling_revision_id = null;
                $clone->save();
            }

            // 6. Clone Final Images
            $images = SamplingFinalImage::where('sampling_revision_id', $sourceRevId)->get();
            foreach ($images as $img) {
                $clone = $img->replicate();
                $clone->sampling_revision_id = null;
                $clone->save();
            }

            // 7. Clone Production Notes & Attachments
            $notes = SamplingProductionNote::with('attachments')->where('sampling_revision_id', $sourceRevId)->get();
            foreach ($notes as $n) {
                $newNote = $n->replicate();
                $newNote->sampling_revision_id = null;
                $newNote->save();

                foreach ($n->attachments as $att) {
                    $newAtt = $att->replicate();
                    $newAtt->production_note_id = $newNote->id;
                    $newAtt->save();
                }
            }

            // 8. Clone Costing
            $costing = SamplingCosting::where('sampling_revision_id', $sourceRevId)->first();
            if ($costing) {
                $cloneCost = $costing->replicate();
                $cloneCost->sampling_revision_id = null;
                $cloneCost->save();
            }

            // 9. Clone Physical Storage
            $storage = SamplingPhysicalStorage::where('sampling_revision_id', $sourceRevId)->first();
            if ($storage) {
                $cloneStorage = $storage->replicate();
                $cloneStorage->sampling_revision_id = null;
                $cloneStorage->save();
            }

            // Switch sample to editable draft state while preserving previous revisions in history
            $sample->update([
                'is_frozen' => false,
                'current_revision_id' => null,
            ]);
        });
    }
}
