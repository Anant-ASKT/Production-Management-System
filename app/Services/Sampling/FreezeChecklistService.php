<?php

namespace App\Services\Sampling;

use App\Models\Sampling\SamplingSample;

class FreezeChecklistService
{
    /**
     * Evaluates the pre-freeze readiness checklist for a given sample.
     *
     * @param SamplingSample $sample
     * @return array
     */
    public function evaluate(SamplingSample $sample): array
    {
        $checklist = [];
        $canFreeze = true;

        // 1. Approval Status: Must be Approved
        $isApproved = ($sample->approval_status === 'approved');
        $checklist[] = [
            'key' => 'approval',
            'title' => 'Design & Sample Approval',
            'passed' => $isApproved,
            'message' => $isApproved ? 'Sample is formally approved.' : 'Sample must be formally approved before freezing.',
            'tab' => 'approval',
        ];
        if (!$isApproved) $canFreeze = false;

        // 2. Bill of Materials (BOM): At least 1 material item with net_quantity > 0
        $bomCount = $sample->activeBoms()->count();
        $hasBom = ($bomCount > 0);
        $checklist[] = [
            'key' => 'bom',
            'title' => 'Bill of Materials (BOM)',
            'passed' => $hasBom,
            'message' => $hasBom ? "{$bomCount} material item(s) configured." : 'At least one material line must be added to the BOM.',
            'tab' => 'bom',
        ];
        if (!$hasBom) $canFreeze = false;

        // 3. Labour & Operations: At least 1 operation defined
        $opCount = $sample->activeOperations()->count();
        $hasOps = ($opCount > 0);
        $checklist[] = [
            'key' => 'operations',
            'title' => 'Labour & Reproduction Operations',
            'passed' => $hasOps,
            'message' => $hasOps ? "{$opCount} operation step(s) recorded." : 'Expected production operations must be documented.',
            'tab' => 'operations',
        ];
        if (!$hasOps) $canFreeze = false;

        // 4. Production Notes: MANDATORY FREEZING REQUIREMENT
        // A Sample cannot become Frozen unless at least one Production Note has been completed (written, voice, or both)
        $notesCount = $sample->activeProductionNotes()->count();
        $hasProdNotes = ($notesCount > 0);
        $checklist[] = [
            'key' => 'production_notes',
            'title' => 'Production Notes (Critical Requirement)',
            'passed' => $hasProdNotes,
            'message' => $hasProdNotes ? "{$notesCount} production note(s) recorded." : 'At least ONE Production Note (written or voice note) is mandatory to preserve institutional technique before freezing.',
            'tab' => 'production_notes',
        ];
        if (!$hasProdNotes) $canFreeze = false;

        // 5. Final Technical Photographs / Drawings: At least 1 technical photo
        $imgCount = $sample->activeFinalImages()->count();
        $hasImages = ($imgCount > 0);
        $checklist[] = [
            'key' => 'final_images',
            'title' => 'Final Technical Photographs',
            'passed' => $hasImages,
            'message' => $hasImages ? "{$imgCount} technical photo(s) uploaded." : 'At least one approved final technical photograph must be uploaded.',
            'tab' => 'photos',
        ];
        if (!$hasImages) $canFreeze = false;

        // 6. Physical Sample Storage Location: Must have location assigned
        $hasStorage = ($sample->activePhysicalStorage()->exists());
        $checklist[] = [
            'key' => 'storage',
            'title' => 'Physical Sample Archive Location',
            'passed' => $hasStorage,
            'message' => $hasStorage ? 'Physical sample storage location is archived.' : 'Room / Rack / Shelf / Box location must be assigned.',
            'tab' => 'storage',
        ];
        if (!$hasStorage) $canFreeze = false;

        // 7. Technical Specifications (advisory/optional depending on product)
        $specCount = $sample->activeSpecifications()->count();
        $checklist[] = [
            'key' => 'specifications',
            'title' => 'Technical Specifications',
            'passed' => true,
            'message' => "{$specCount} specification attribute(s) documented.",
            'tab' => 'specifications',
        ];

        // 8. Measurements (advisory/optional)
        $measureCount = $sample->activeMeasurements()->count();
        $checklist[] = [
            'key' => 'measurements',
            'title' => 'Measurement Points',
            'passed' => true,
            'message' => "{$measureCount} measurement point(s) recorded.",
            'tab' => 'measurements',
        ];

        // 9. Costing: Direct production cost calculated
        $hasCosting = ($sample->activeCosting()->exists());
        $checklist[] = [
            'key' => 'costing',
            'title' => 'Estimated Direct Costing',
            'passed' => $hasCosting,
            'message' => $hasCosting ? 'Direct production costing recorded.' : 'Direct production costing summary should be confirmed.',
            'tab' => 'costing',
        ];

        return [
            'can_freeze' => $canFreeze,
            'checklist' => $checklist,
            'passed_count' => count(array_filter($checklist, fn($i) => $i['passed'])),
            'total_count' => count($checklist),
        ];
    }
}
