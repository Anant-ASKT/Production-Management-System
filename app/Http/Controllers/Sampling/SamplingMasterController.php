<?php

namespace App\Http\Controllers\Sampling;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sampling\SamplingDivision;
use App\Models\Sampling\SamplingMeasurementPoint;
use App\Models\Sampling\SamplingStorageLocation;
use App\Models\Sampling\SamplingOperationMaster;
use App\Models\Sampling\SamplingMaterialMaster;
use App\Models\Sampling\SamplingUom;
use App\Models\Sampling\SamplingDesigner;
use App\Models\Sampling\SamplingCollection;
use App\Models\Sampling\SamplingSpecAttribute;
use App\Models\Sampling\SamplingSkillLevel;
use App\Models\Sampling\SamplingReferenceType;
use Illuminate\Support\Facades\Auth;

class SamplingMasterController extends Controller
{
    private function getCompanyId(): int
    {
        return Auth::guard('sampling')->user()->sampling_company_id;
    }

    public function index()
    {
        return redirect()->route('sampling.masters.departments.index');
    }

    // ==========================================
    // 1. DEPARTMENTS (DIVISIONS)
    // ==========================================
    public function departmentsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $departments = SamplingDivision::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.departments', compact('departments'));
    }

    // Legacy alias
    public function divisionsIndex()
    {
        return $this->departmentsIndex();
    }

    public function storeDepartment(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        SamplingDivision::create([
            'sampling_company_id' => $companyId,
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'description' => $request->description,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.departments.index')->with('success', 'Department added successfully.');
    }

    public function storeDivision(Request $request)
    {
        return $this->storeDepartment($request);
    }

    public function updateDepartment(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $department = SamplingDivision::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $department->update([
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.departments.index')->with('success', 'Department updated successfully.');
    }

    public function updateDivision(Request $request, $id)
    {
        return $this->updateDepartment($request, $id);
    }

    public function destroyDepartment($id)
    {
        $companyId = $this->getCompanyId();
        $department = SamplingDivision::where('sampling_company_id', $companyId)->findOrFail($id);
        $department->delete();

        return redirect()->route('sampling.masters.departments.index')->with('success', 'Department removed.');
    }

    public function destroyDivision($id)
    {
        return $this->destroyDepartment($id);
    }

    // ==========================================
    // 2. SKILL LEVELS MASTER
    // ==========================================
    public function skillLevelsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $skillLevels = SamplingSkillLevel::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.skill_levels', compact('skillLevels'));
    }

    public function storeSkillLevel(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'default_rate_per_hour' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        SamplingSkillLevel::create([
            'sampling_company_id' => $companyId,
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'default_rate_per_hour' => $request->default_rate_per_hour ?: 0,
            'description' => $request->description,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.skill-levels.index')->with('success', 'Skill level added successfully.');
    }

    public function updateSkillLevel(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $skill = SamplingSkillLevel::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'default_rate_per_hour' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $skill->update([
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'default_rate_per_hour' => $request->default_rate_per_hour ?: 0,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.skill-levels.index')->with('success', 'Skill level updated successfully.');
    }

    public function destroySkillLevel($id)
    {
        $companyId = $this->getCompanyId();
        $skill = SamplingSkillLevel::where('sampling_company_id', $companyId)->findOrFail($id);
        $skill->delete();

        return redirect()->route('sampling.masters.skill-levels.index')->with('success', 'Skill level removed.');
    }

    // ==========================================
    // 3. MEASUREMENT POINTS (NAAP MASTER)
    // ==========================================
    public function measurementPointsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $measurementPoints = SamplingMeasurementPoint::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.measurement_points', compact('measurementPoints'));
    }

    public function storeMeasurementPoint(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'point_name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'default_unit' => 'required|string|max:20',
        ]);

        SamplingMeasurementPoint::create([
            'sampling_company_id' => $companyId,
            'point_name' => $request->point_name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'default_unit' => $request->default_unit,
        ]);

        return redirect()->route('sampling.masters.measurement-points.index')->with('success', 'Measurement point added.');
    }

    public function updateMeasurementPoint(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $point = SamplingMeasurementPoint::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'point_name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'default_unit' => 'required|string|max:20',
        ]);

        $point->update([
            'point_name' => $request->point_name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'default_unit' => $request->default_unit,
        ]);

        return redirect()->route('sampling.masters.measurement-points.index')->with('success', 'Measurement point updated.');
    }

    public function destroyMeasurementPoint($id)
    {
        $companyId = $this->getCompanyId();
        $point = SamplingMeasurementPoint::where('sampling_company_id', $companyId)->findOrFail($id);
        $point->delete();

        return redirect()->route('sampling.masters.measurement-points.index')->with('success', 'Measurement point removed.');
    }

    // ==========================================
    // 4. STORAGE LOCATIONS
    // ==========================================
    public function storageLocationsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $storageLocations = SamplingStorageLocation::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.storage_locations', compact('storageLocations'));
    }

    public function storeStorageLocation(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'studio_name' => 'required|string|max:100',
            'room' => 'required|string|max:100',
            'rack' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'box' => 'nullable|string|max:50',
        ]);

        SamplingStorageLocation::create([
            'sampling_company_id' => $companyId,
            'studio_name' => $request->studio_name,
            'room' => $request->room,
            'rack' => $request->rack,
            'shelf' => $request->shelf,
            'box' => $request->box,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.locations.index')->with('success', 'Storage location added.');
    }

    public function updateStorageLocation(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $loc = SamplingStorageLocation::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'studio_name' => 'required|string|max:100',
            'room' => 'required|string|max:100',
            'rack' => 'nullable|string|max:50',
            'shelf' => 'nullable|string|max:50',
            'box' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $loc->update([
            'studio_name' => $request->studio_name,
            'room' => $request->room,
            'rack' => $request->rack,
            'shelf' => $request->shelf,
            'box' => $request->box,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.locations.index')->with('success', 'Storage location updated.');
    }

    public function destroyStorageLocation($id)
    {
        $companyId = $this->getCompanyId();
        $loc = SamplingStorageLocation::where('sampling_company_id', $companyId)->findOrFail($id);
        $loc->delete();

        return redirect()->route('sampling.masters.locations.index')->with('success', 'Storage location removed.');
    }

    // ==========================================
    // 5. OPERATIONS / MAKING STEPS
    // ==========================================
    public function operationsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $operations = SamplingOperationMaster::where('sampling_company_id', $companyId)->with('division')->latest()->get();
        $departments = SamplingDivision::where('sampling_company_id', $companyId)->where('status', 'active')->get();
        $skillLevels = SamplingSkillLevel::where('sampling_company_id', $companyId)->where('status', 'active')->get();

        return view('sampling.masters.operations', compact('operations', 'departments', 'skillLevels'));
    }

    public function storeOperation(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'operation_name' => 'required|string|max:191',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'skill_level' => 'required|string|max:100',
            'default_time_minutes' => 'nullable|numeric|min:0',
            'default_rate_per_hour' => 'nullable|numeric|min:0',
        ]);

        SamplingOperationMaster::create([
            'sampling_company_id' => $companyId,
            'operation_name' => $request->operation_name,
            'division_id' => $request->division_id,
            'skill_level' => $request->skill_level,
            'default_time_minutes' => $request->default_time_minutes ?: 0,
            'default_rate_per_hour' => $request->default_rate_per_hour ?: 0,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.operations.index')->with('success', 'Operation added to master.');
    }

    public function updateOperation(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $op = SamplingOperationMaster::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'operation_name' => 'required|string|max:191',
            'division_id' => 'nullable|exists:sampling_divisions,id',
            'skill_level' => 'required|string|max:100',
            'default_time_minutes' => 'nullable|numeric|min:0',
            'default_rate_per_hour' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $op->update([
            'operation_name' => $request->operation_name,
            'division_id' => $request->division_id,
            'skill_level' => $request->skill_level,
            'default_time_minutes' => $request->default_time_minutes ?: 0,
            'default_rate_per_hour' => $request->default_rate_per_hour ?: 0,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.operations.index')->with('success', 'Operation updated.');
    }

    public function destroyOperation($id)
    {
        $companyId = $this->getCompanyId();
        $op = SamplingOperationMaster::where('sampling_company_id', $companyId)->findOrFail($id);
        $op->delete();

        return redirect()->route('sampling.masters.operations.index')->with('success', 'Operation removed.');
    }

    // ==========================================
    // 6. MATERIALS & ITEMS MASTER
    // ==========================================
    public function materialsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $materials = SamplingMaterialMaster::where('sampling_company_id', $companyId)->latest()->get();
        $uoms = SamplingUom::where('sampling_company_id', $companyId)->where('status', 'active')->get();

        return view('sampling.masters.materials', compact('materials', 'uoms'));
    }

    public function storeMaterial(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'material_category' => 'required|string|max:100',
            'material_name' => 'required|string|max:191',
            'item_code' => 'nullable|string|max:50',
            'unit_of_measure' => 'required|string|max:30',
            'standard_cost' => 'nullable|numeric|min:0',
        ]);

        SamplingMaterialMaster::create([
            'sampling_company_id' => $companyId,
            'material_category' => $request->material_category,
            'material_name' => $request->material_name,
            'item_code' => $request->item_code ? strtoupper($request->item_code) : null,
            'unit_of_measure' => $request->unit_of_measure,
            'standard_cost' => $request->standard_cost ?: 0,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.materials.index')->with('success', 'Material added to catalog.');
    }

    public function updateMaterial(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $mat = SamplingMaterialMaster::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'material_category' => 'required|string|max:100',
            'material_name' => 'required|string|max:191',
            'item_code' => 'nullable|string|max:50',
            'unit_of_measure' => 'required|string|max:30',
            'standard_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $mat->update([
            'material_category' => $request->material_category,
            'material_name' => $request->material_name,
            'item_code' => $request->item_code ? strtoupper($request->item_code) : null,
            'unit_of_measure' => $request->unit_of_measure,
            'standard_cost' => $request->standard_cost ?: 0,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.materials.index')->with('success', 'Material updated.');
    }

    public function destroyMaterial($id)
    {
        $companyId = $this->getCompanyId();
        $mat = SamplingMaterialMaster::where('sampling_company_id', $companyId)->findOrFail($id);
        $mat->delete();

        return redirect()->route('sampling.masters.materials.index')->with('success', 'Material removed.');
    }

    // ==========================================
    // 7. UNITS OF MEASURE (UOM)
    // ==========================================
    public function uomsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $uoms = SamplingUom::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.uoms', compact('uoms'));
    }

    public function storeUom(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:30',
            'type' => 'nullable|string|max:50',
        ]);

        SamplingUom::create([
            'sampling_company_id' => $companyId,
            'name' => $request->name,
            'symbol' => $request->symbol,
            'type' => $request->type,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.uoms.index')->with('success', 'Unit of measure added.');
    }

    public function updateUom(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $uom = SamplingUom::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'symbol' => 'required|string|max:30',
            'type' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $uom->update([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'type' => $request->type,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.uoms.index')->with('success', 'Unit of measure updated.');
    }

    public function destroyUom($id)
    {
        $companyId = $this->getCompanyId();
        $uom = SamplingUom::where('sampling_company_id', $companyId)->findOrFail($id);
        $uom->delete();

        return redirect()->route('sampling.masters.uoms.index')->with('success', 'Unit of measure removed.');
    }

    // ==========================================
    // 8. DESIGNERS MASTER
    // ==========================================
    public function designersIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $designers = SamplingDesigner::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.designers', compact('designers'));
    }

    public function storeDesigner(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:100',
        ]);

        SamplingDesigner::create([
            'sampling_company_id' => $companyId,
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'email' => $request->email,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.designers.index')->with('success', 'Designer added.');
    }

    public function updateDesigner(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $des = SamplingDesigner::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $des->update([
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'email' => $request->email,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.designers.index')->with('success', 'Designer updated.');
    }

    public function destroyDesigner($id)
    {
        $companyId = $this->getCompanyId();
        $des = SamplingDesigner::where('sampling_company_id', $companyId)->findOrFail($id);
        $des->delete();

        return redirect()->route('sampling.masters.designers.index')->with('success', 'Designer removed.');
    }

    // ==========================================
    // 9. COLLECTIONS & SEASONS MASTER
    // ==========================================
    public function collectionsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $collections = SamplingCollection::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.collections', compact('collections'));
    }

    public function storeCollection(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'season' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        SamplingCollection::create([
            'sampling_company_id' => $companyId,
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'season' => $request->season,
            'year' => $request->year,
            'description' => $request->description,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.collections.index')->with('success', 'Collection added.');
    }

    public function updateCollection(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $coll = SamplingCollection::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:50',
            'season' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $coll->update([
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'season' => $request->season,
            'year' => $request->year,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.collections.index')->with('success', 'Collection updated.');
    }

    public function destroyCollection($id)
    {
        $companyId = $this->getCompanyId();
        $coll = SamplingCollection::where('sampling_company_id', $companyId)->findOrFail($id);
        $coll->delete();

        return redirect()->route('sampling.masters.collections.index')->with('success', 'Collection removed.');
    }

    // ==========================================
    // 10. TECHNICAL SPEC ATTRIBUTES
    // ==========================================
    public function specsIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $specAttributes = SamplingSpecAttribute::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.specs', compact('specAttributes'));
    }

    public function storeSpecAttribute(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'attribute_name' => 'required|string|max:100',
            'field_type' => 'required|string|max:30',
            'unit' => 'nullable|string|max:30',
            'is_required' => 'nullable|boolean',
        ]);

        SamplingSpecAttribute::create([
            'sampling_company_id' => $companyId,
            'attribute_name' => $request->attribute_name,
            'field_type' => $request->field_type,
            'unit' => $request->unit,
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()->route('sampling.masters.specs.index')->with('success', 'Tech spec attribute added.');
    }

    public function updateSpecAttribute(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $attr = SamplingSpecAttribute::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'attribute_name' => 'required|string|max:100',
            'field_type' => 'required|string|max:30',
            'unit' => 'nullable|string|max:30',
            'is_required' => 'nullable|boolean',
        ]);

        $attr->update([
            'attribute_name' => $request->attribute_name,
            'field_type' => $request->field_type,
            'unit' => $request->unit,
            'is_required' => $request->boolean('is_required'),
        ]);

        return redirect()->route('sampling.masters.specs.index')->with('success', 'Tech spec attribute updated.');
    }

    public function destroySpecAttribute($id)
    {
        $companyId = $this->getCompanyId();
        $attr = SamplingSpecAttribute::where('sampling_company_id', $companyId)->findOrFail($id);
        $attr->delete();

        return redirect()->route('sampling.masters.specs.index')->with('success', 'Tech spec attribute removed.');
    }

    // ==========================================
    // 11. SKETCH & SWATCH REFERENCE TYPES
    // ==========================================
    public function referenceTypesIndex()
    {
        $companyId = $this->getCompanyId();
        $this->ensureDefaultMastersExist($companyId);
        $referenceTypes = SamplingReferenceType::where('sampling_company_id', $companyId)->latest()->get();

        return view('sampling.masters.reference_types', compact('referenceTypes'));
    }

    public function storeReferenceType(Request $request)
    {
        $companyId = $this->getCompanyId();

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        SamplingReferenceType::create([
            'sampling_company_id' => $companyId,
            'name' => $request->name,
            'code' => $request->code ? strtolower(str_replace(' ', '_', $request->code)) : strtolower(str_replace(' ', '_', $request->name)),
            'description' => $request->description,
            'status' => 'active',
        ]);

        return redirect()->route('sampling.masters.reference-types.index')->with('success', 'Sketch / Swatch type added.');
    }

    public function updateReferenceType(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $refType = SamplingReferenceType::where('sampling_company_id', $companyId)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $refType->update([
            'name' => $request->name,
            'code' => $request->code ? strtolower(str_replace(' ', '_', $request->code)) : $refType->code,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route('sampling.masters.reference-types.index')->with('success', 'Sketch / Swatch type updated.');
    }

    public function destroyReferenceType($id)
    {
        $companyId = $this->getCompanyId();
        $refType = SamplingReferenceType::where('sampling_company_id', $companyId)->findOrFail($id);
        $refType->delete();

        return redirect()->route('sampling.masters.reference-types.index')->with('success', 'Sketch / Swatch type removed.');
    }

    // ==========================================
    // HELPER: Auto-populate initial default masters
    // ==========================================
    private function ensureDefaultMastersExist(int $companyId): void
    {
        // 1. Departments (Divisions)
        if (SamplingDivision::where('sampling_company_id', $companyId)->count() === 0) {
            $defaultDivisions = [
                ['name' => 'Knitwear & Knitting', 'code' => 'KNT', 'description' => 'Flat knitting, hand knitting & linking'],
                ['name' => 'Stitching & Tailoring', 'code' => 'STC', 'description' => 'Garment tailoring, seam joining & assembly'],
                ['name' => 'Hand Embroidery', 'code' => 'EMB', 'description' => 'Artisan hand needlework, aari, zardozi & thread work'],
                ['name' => 'Leather Workshop', 'code' => 'LTR', 'description' => 'Leather cutting, skiving & goods fabrication'],
                ['name' => 'Dyeing & Washing', 'code' => 'DYE', 'description' => 'Colour development, yarn/fabric dyeing & soft washing'],
                ['name' => 'Finishing & QC', 'code' => 'FIN', 'description' => 'Steam pressing, final inspection & presentation packaging'],
            ];
            foreach ($defaultDivisions as $div) {
                SamplingDivision::create(array_merge($div, [
                    'sampling_company_id' => $companyId,
                    'status' => 'active',
                ]));
            }
        }

        // 2. Skill Levels
        if (SamplingSkillLevel::where('sampling_company_id', $companyId)->count() === 0) {
            $defaultSkills = [
                ['name' => 'Master Artisan', 'code' => 'MST', 'default_rate_per_hour' => 150.00, 'description' => 'Top-tier master craftsman with deep technical and design expertise'],
                ['name' => 'Senior Artisan', 'code' => 'SNR', 'default_rate_per_hour' => 130.00, 'description' => 'Experienced artisan capable of intricate execution without supervision'],
                ['name' => 'Skilled', 'code' => 'SKL', 'default_rate_per_hour' => 110.00, 'description' => 'Skilled operator for precision stitching, knitting, or linking'],
                ['name' => 'Semi-Skilled', 'code' => 'SSK', 'default_rate_per_hour' => 80.00, 'description' => 'Performs preparatory, finishing, washing, or helper tasks'],
                ['name' => 'Trainee / Apprentice', 'code' => 'TRN', 'default_rate_per_hour' => 50.00, 'description' => 'Apprentice artisan undergoing craft training'],
            ];
            foreach ($defaultSkills as $sk) {
                SamplingSkillLevel::create(array_merge($sk, [
                    'sampling_company_id' => $companyId,
                    'status' => 'active',
                ]));
            }
        }

        // 3. Units of Measure
        if (SamplingUom::where('sampling_company_id', $companyId)->count() === 0) {
            $defaultUoms = [
                ['name' => 'Pieces', 'symbol' => 'pcs', 'type' => 'Quantity'],
                ['name' => 'Kilograms', 'symbol' => 'kg', 'type' => 'Weight'],
                ['name' => 'Grams', 'symbol' => 'g', 'type' => 'Weight'],
                ['name' => 'Metres', 'symbol' => 'm', 'type' => 'Length'],
                ['name' => 'Yards', 'symbol' => 'yd', 'type' => 'Length'],
                ['name' => 'Inches', 'symbol' => 'in', 'type' => 'Length'],
                ['name' => 'Centimetres', 'symbol' => 'cm', 'type' => 'Length'],
                ['name' => 'Cones', 'symbol' => 'cone', 'type' => 'Quantity'],
                ['name' => 'Sets', 'symbol' => 'set', 'type' => 'Quantity'],
            ];
            foreach ($defaultUoms as $u) {
                SamplingUom::create(array_merge($u, [
                    'sampling_company_id' => $companyId,
                    'status' => 'active',
                ]));
            }
        }

        // 4. Measurement Points
        if (SamplingMeasurementPoint::where('sampling_company_id', $companyId)->count() === 0) {
            $defaultPoints = [
                ['point_name' => 'Chest Width (1" below armhole)', 'code' => 'CHEST', 'default_unit' => 'cm'],
                ['point_name' => 'Total Body Length', 'code' => 'LENGTH', 'default_unit' => 'cm'],
                ['point_name' => 'Across Shoulder', 'code' => 'SHOULDER', 'default_unit' => 'cm'],
                ['point_name' => 'Sleeve Length', 'code' => 'SLEEVE', 'default_unit' => 'cm'],
                ['point_name' => 'Bottom Hem Width', 'code' => 'HEM', 'default_unit' => 'cm'],
                ['point_name' => 'Neck Opening / Width', 'code' => 'NECK', 'default_unit' => 'cm'],
                ['point_name' => 'Armhole Depth', 'code' => 'ARMHOLE', 'default_unit' => 'cm'],
            ];
            foreach ($defaultPoints as $pt) {
                SamplingMeasurementPoint::create(array_merge($pt, [
                    'sampling_company_id' => $companyId,
                ]));
            }
        }

        // 5. Operations
        if (SamplingOperationMaster::where('sampling_company_id', $companyId)->count() === 0) {
            $knitDiv = SamplingDivision::where('sampling_company_id', $companyId)->where('code', 'KNT')->first();
            $stcDiv = SamplingDivision::where('sampling_company_id', $companyId)->where('code', 'STC')->first();
            $finDiv = SamplingDivision::where('sampling_company_id', $companyId)->where('code', 'FIN')->first();

            $defaultOps = [
                ['operation_name' => 'Hand Flat Machine Knitting', 'division_id' => $knitDiv?->id, 'skill_level' => 'Master Artisan', 'default_time_minutes' => 60, 'default_rate_per_hour' => 150],
                ['operation_name' => 'Collar & Panel Linking', 'division_id' => $knitDiv?->id, 'skill_level' => 'Skilled', 'default_time_minutes' => 30, 'default_rate_per_hour' => 120],
                ['operation_name' => 'Pattern Cutting & Preparation', 'division_id' => $stcDiv?->id, 'skill_level' => 'Skilled', 'default_time_minutes' => 25, 'default_rate_per_hour' => 100],
                ['operation_name' => 'Garment Assembly Stitching', 'division_id' => $stcDiv?->id, 'skill_level' => 'Skilled', 'default_time_minutes' => 45, 'default_rate_per_hour' => 110],
                ['operation_name' => 'Soft Wash & Drying', 'division_id' => $finDiv?->id, 'skill_level' => 'Semi-Skilled', 'default_time_minutes' => 20, 'default_rate_per_hour' => 80],
                ['operation_name' => 'Steam Pressing & Measurement Check', 'division_id' => $finDiv?->id, 'skill_level' => 'Skilled', 'default_time_minutes' => 15, 'default_rate_per_hour' => 90],
            ];
            foreach ($defaultOps as $op) {
                SamplingOperationMaster::create(array_merge($op, [
                    'sampling_company_id' => $companyId,
                    'status' => 'active',
                ]));
            }
        }

        // 6. Tech Spec Attributes
        if (SamplingSpecAttribute::where('sampling_company_id', $companyId)->count() === 0) {
            $defaultAttrs = [
                ['attribute_name' => 'Machine Gauge', 'field_type' => 'text', 'unit' => 'GG', 'is_required' => true],
                ['attribute_name' => 'Yarn Ply / Count', 'field_type' => 'text', 'unit' => 'Ply', 'is_required' => true],
                ['attribute_name' => 'Stitches Per Inch (SPI)', 'field_type' => 'number', 'unit' => 'SPI', 'is_required' => false],
                ['attribute_name' => 'Garment Weight (Target GSM)', 'field_type' => 'number', 'unit' => 'GSM', 'is_required' => false],
                ['attribute_name' => 'Wash Shrinkage Tolerance', 'field_type' => 'text', 'unit' => '%', 'is_required' => false],
            ];
            foreach ($defaultAttrs as $attr) {
                SamplingSpecAttribute::create(array_merge($attr, [
                    'sampling_company_id' => $companyId,
                ]));
            }
        }

        // 7. Sketch & Swatch Types
        if (SamplingReferenceType::where('sampling_company_id', $companyId)->count() === 0) {
            $defaultRefTypes = [
                ['name' => 'Sketch / Drawing', 'code' => 'sketch', 'description' => 'Hand sketches, technical flats, CAD illustrations'],
                ['name' => 'Fabric Swatch', 'code' => 'fabric_ref', 'description' => 'Fabric texture scans, knit swatches, weave references'],
                ['name' => 'Photo Reference', 'code' => 'photograph', 'description' => 'Inspiration photos, garment fit pictures, archive styling'],
                ['name' => 'External Link', 'code' => 'web_url', 'description' => 'Pinterest boards, Figma files, digital lookbooks, web links'],
            ];
            foreach ($defaultRefTypes as $rt) {
                SamplingReferenceType::create(array_merge($rt, [
                    'sampling_company_id' => $companyId,
                    'status' => 'active',
                ]));
            }
        }
    }
}
