<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAiPhotoReceivingController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $status = $request->input('status', 'pending');

        $query = DB::table('enhanced_product_submissions as eps')
            ->join('auto_designer_specification_master as spec', 'eps.specification_id', '=', 'spec.sno')
            ->join('ai_photo_enhancers as ae', 'eps.ai_photo_enhancer_id', '=', 'ae.sno')
            ->leftJoin('suppliers as sup', 'spec.supplier_id', '=', 'sup.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'spec.gender')
            ->select(
                'eps.specification_id',
                'spec.sku',
                'spec.barcode',
                'spec.clientreference',
                'spec.img_path',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'gender.name as gender_text',
                DB::raw('MAX(ae.first_name) as enhancer_first_name'),
                DB::raw('MAX(ae.last_name) as enhancer_last_name'),
                DB::raw('MAX(ae.sno) as ai_photo_enhancer_id'),
                DB::raw('COUNT(eps.sno) as total_images'),
                DB::raw("SUM(CASE WHEN eps.status = 'pending' THEN 1 ELSE 0 END) as pending_count"),
                DB::raw("SUM(CASE WHEN eps.status = 'approved' THEN 1 ELSE 0 END) as approved_count"),
                DB::raw("SUM(CASE WHEN eps.status = 'approved_need_version' THEN 1 ELSE 0 END) as need_version_count"),
                DB::raw("SUM(CASE WHEN eps.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count"),
                DB::raw('MAX(eps.created_at) as latest_submission_date'),
                DB::raw("COALESCE(MAX(sup.name), (SELECT s_vsw.name FROM vendor_stock_web vsw JOIN suppliers s_vsw ON s_vsw.sno = vsw.vendor_id WHERE (vsw.item_id = spec.id OR vsw.item_id = spec.sno OR vsw.batch_no = spec.sku) ORDER BY vsw.sno DESC LIMIT 1)) as supplier_name")
            )
            ->groupBy(
                'eps.specification_id',
                'spec.sku',
                'spec.barcode',
                'spec.clientreference',
                'spec.img_path',
                'itemname.itemname',
                'colour.colourname',
                'gender.name',
                'spec.id',
                'spec.sno'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('itemname.itemname', 'like', "%{$search}%")
                  ->orWhere('ae.first_name', 'like', "%{$search}%")
                  ->orWhere('ae.last_name', 'like', "%{$search}%")
                  ->orWhere('spec.sku', 'like', "%{$search}%")
                  ->orWhere('spec.barcode', 'like', "%{$search}%");
            });
        }

        if ($status === 'pending') {
            $query->having(DB::raw("SUM(CASE WHEN eps.status = 'pending' THEN 1 ELSE 0 END)"), '>', 0);
        } elseif ($status === 'approved') {
            $query->having(DB::raw("SUM(CASE WHEN eps.status = 'approved' THEN 1 ELSE 0 END)"), '>', 0)
                  ->having(DB::raw("SUM(CASE WHEN eps.status = 'pending' THEN 1 ELSE 0 END)"), '=', 0);
        } elseif ($status === 'rejected') {
            $query->having(DB::raw("SUM(CASE WHEN eps.status = 'rejected' THEN 1 ELSE 0 END)"), '>', 0);
        } elseif ($status === 'need_version') {
            $query->having(DB::raw("SUM(CASE WHEN eps.status = 'approved_need_version' THEN 1 ELSE 0 END)"), '>', 0);
        }

        $products = $query->orderBy(DB::raw('MAX(eps.created_at)'), 'desc')->paginate(15);

        // Calculate counts for badges
        $counts = [
            'pending' => DB::table('enhanced_product_submissions')
                ->where('status', 'pending')
                ->distinct('specification_id')
                ->count('specification_id'),
            'approved' => DB::table('enhanced_product_submissions')
                ->where('status', 'approved')
                ->distinct('specification_id')
                ->count('specification_id'),
            'rejected' => DB::table('enhanced_product_submissions')
                ->where('status', 'rejected')
                ->distinct('specification_id')
                ->count('specification_id'),
            'total' => DB::table('enhanced_product_submissions')
                ->distinct('specification_id')
                ->count('specification_id'),
        ];

        return view('admin.ai_photo_enhancing.receiving', compact('products', 'counts', 'status', 'search'));
    }

    public function show($id)
    {
        // $id may be specification_id or legacy submission sno
        $spec = DB::table('auto_designer_specification_master as spec')
            ->leftJoin('suppliers as sup', 'spec.supplier_id', '=', 'sup.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'spec.gender')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'spec.item_type')
            ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'spec.designer_name')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'spec.composition')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'spec.sizes')
            ->leftJoin('auto_embellishment_master as embellishment', 'embellishment.id', '=', 'spec.embellishment')
            ->leftJoin('auto_manufacturing_process_master as manufacturing', 'manufacturing.id', '=', 'spec.manufacturing_process')
            ->where('spec.sno', $id)
            ->select(
                'spec.*',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'gender.name as gender_text',
                'itemtype.itemtype as item_type_text',
                'designer.designername as designer_name_text',
                'composition.composition_details as composition_text',
                'size.size as size_text',
                'embellishment.embellishmentname as embellishment_text',
                'manufacturing.manufacturing_process as manufacturing_process_text',
                DB::raw("COALESCE(sup.name, (SELECT s_vsw.name FROM vendor_stock_web vsw JOIN suppliers s_vsw ON s_vsw.sno = vsw.vendor_id WHERE (vsw.item_id = spec.id OR vsw.item_id = spec.sno OR vsw.batch_no = spec.sku) ORDER BY vsw.sno DESC LIMIT 1)) as supplier_name")
            )
            ->first();

        // If not found by spec.sno, check if it was submission sno
        if (!$spec) {
            $legacySub = DB::table('enhanced_product_submissions')->where('sno', $id)->first();
            if ($legacySub) {
                return redirect()->route('admin.ai-photo-enhancing.receiving.show', $legacySub->specification_id);
            }
            abort(404, 'Product specification not found.');
        }

        // Resolve original images (Main + Sub)
        $subImagesList = [];

        // Check if supplier uploaded raw images for this SKU or barcode
        $supplierProd = DB::table('supplier_products')
            ->where(function($q) use ($spec) {
                if (!empty($spec->sku)) $q->where('product_sku', $spec->sku);
                if (!empty($spec->barcode)) $q->orWhere('product_sku', $spec->barcode);
            })
            ->whereNotNull('main_image')
            ->where('main_image', '!=', '')
            ->orderByDesc('sno')
            ->first();

        if ($supplierProd) {
            if (!empty($supplierProd->main_image)) {
                $spec->img_path = $supplierProd->main_image;
            }
            if (!empty($supplierProd->sub_images)) {
                $supplierSubs = json_decode($supplierProd->sub_images, true);
                if (is_array($supplierSubs) && !empty($supplierSubs)) {
                    $subImagesList = array_merge($supplierSubs, $subImagesList);
                }
            }
        }
        if (!empty($spec->subimg_path)) {
            $decoded = json_decode($spec->subimg_path, true);
            if (is_array($decoded)) {
                $subImagesList = array_merge($subImagesList, $decoded);
            } else {
                $subImagesList[] = $spec->subimg_path;
            }
        }

        if (empty($subImagesList) && !empty($spec->barcode)) {
            $siblingSubImgs = DB::table('auto_designer_specification_master')
                ->where('barcode', $spec->barcode)
                ->whereNotNull('subimg_path')
                ->where('subimg_path', '!=', '')
                ->where('subimg_path', '!=', '[]')
                ->pluck('subimg_path');

            foreach ($siblingSubImgs as $subJson) {
                $decoded = json_decode($subJson, true);
                if (is_array($decoded)) {
                    $subImagesList = array_merge($subImagesList, $decoded);
                } else {
                    $subImagesList[] = $subJson;
                }
            }
        }

        if (empty($subImagesList) && !empty($spec->barcode)) {
            $subImgDir = public_path('ItemsDesigner_Masterwithbarcode/' . $spec->barcode . '/SubImgs');
            if (is_dir($subImgDir)) {
                $files = array_diff(scandir($subImgDir), ['.', '..']);
                foreach ($files as $file) {
                    $subImagesList[] = 'ItemsDesigner_Masterwithbarcode/' . $spec->barcode . '/SubImgs/' . $file;
                }
            }
        }

        $spec->resolved_sub_images = array_values(array_unique(array_filter($subImagesList)));

        // Fetch all enhanced submissions for this specification
        $submissions = DB::table('enhanced_product_submissions as eps')
            ->join('ai_photo_enhancers as ae', 'eps.ai_photo_enhancer_id', '=', 'ae.sno')
            ->where('eps.specification_id', $spec->sno)
            ->select(
                'eps.*',
                'ae.first_name as enhancer_first_name',
                'ae.last_name as enhancer_last_name'
            )
            ->orderBy('eps.created_at', 'desc')
            ->get();

        $assignment = DB::table('ai_photo_enhancer_assignments')
            ->where('specification_id', $spec->sno)
            ->first();

        return view('admin.ai_photo_enhancing.receiving_detail', compact('spec', 'submissions', 'assignment'));
    }

    public function reviewBatch(Request $request, $id)
    {
        $specId = (int) $id;
        $allSubmissions = DB::table('enhanced_product_submissions')
            ->where('specification_id', $specId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($allSubmissions->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'No photos found for this product.']);
        }

        $reviews = $request->input('reviews', []);
        if (empty($reviews) || !is_array($reviews)) {
            return redirect()->back()->withInput()->withErrors(['error' => 'No reviews submitted.']);
        }

        // VALIDATION: Ensure every single submitted photo has a status selected!
        $unselectedPhotos = [];
        foreach ($allSubmissions as $index => $sub) {
            $selectedStatus = $reviews[$sub->sno]['status'] ?? null;
            if (empty($selectedStatus) || !in_array($selectedStatus, ['approved', 'approved_need_version', 'rejected'])) {
                $unselectedPhotos[] = 'Photo #' . ($index + 1);
            }
        }

        if (!empty($unselectedPhotos)) {
            $msg = 'Please choose a status (Approved, Need More, or Reject) for all photos before saving. Missing decision for: ' . implode(', ', $unselectedPhotos) . '.';
            return redirect()->back()->withInput()->withErrors(['error' => $msg]);
        }

        $mainComment = $request->filled('main_comment') ? trim($request->input('main_comment')) : null;

        DB::beginTransaction();
        try {
            // Ensure only 1 approved photo is 'main'
            $mainFound = false;
            foreach ($reviews as $subId => &$rData) {
                if (($rData['status'] ?? '') === 'approved') {
                    if (($rData['image_type'] ?? '') === 'main') {
                        if (!$mainFound) {
                            $mainFound = true;
                        } else {
                            $rData['image_type'] = 'sub';
                        }
                    }
                }
            }
            unset($rData);

            // If approved photos exist but none selected as 'main', set first approved to 'main'
            if (!$mainFound) {
                foreach ($reviews as $subId => &$rData) {
                    if (($rData['status'] ?? '') === 'approved') {
                        $rData['image_type'] = 'main';
                        break;
                    }
                }
                unset($rData);
            }

            foreach ($reviews as $subId => $reviewData) {
                $status = $reviewData['status'] ?? 'pending';
                $feedback = isset($reviewData['feedback']) ? trim($reviewData['feedback']) : null;
                $imageType = $reviewData['image_type'] ?? 'sub';
                if (!in_array($imageType, ['main', 'sub', 'extra'])) {
                    $imageType = 'sub';
                }

                $submission = DB::table('enhanced_product_submissions')->where('sno', $subId)->first();
                if (!$submission) {
                    continue;
                }

                DB::table('enhanced_product_submissions')
                    ->where('sno', $subId)
                    ->update([
                        'status'         => $status,
                        'image_type'     => $imageType,
                        'admin_feedback' => $feedback,
                        'updated_at'     => now(),
                    ]);

                // Sync with approved_enhanced_images table
                if ($status === 'approved' || $status === 'approved_need_version') {
                    $existing = DB::table('approved_enhanced_images')
                        ->where('enhanced_image_path', $submission->enhanced_image_path)
                        ->first();

                    if ($existing) {
                        DB::table('approved_enhanced_images')
                            ->where('sno', $existing->sno)
                            ->update([
                                'status'     => $status,
                                'image_type' => $imageType,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('approved_enhanced_images')->insert([
                            'countryid'            => $submission->countryid,
                            'companyid'            => $submission->companyid,
                            'subcompanyid'         => $submission->subcompanyid,
                            'projectid'            => $submission->projectid,
                            'subprojectid'         => $submission->subprojectid,
                            'specification_id'     => $submission->specification_id,
                            'ai_photo_enhancer_id' => $submission->ai_photo_enhancer_id,
                            'original_image_path'  => $submission->original_image_path,
                            'enhanced_image_path'  => $submission->enhanced_image_path,
                            'image_type'           => $imageType,
                            'status'               => $status,
                            'created_at'           => now(),
                            'updated_at'           => now(),
                        ]);
                    }
                } elseif ($status === 'rejected') {
                    DB::table('approved_enhanced_images')
                        ->where('enhanced_image_path', $submission->enhanced_image_path)
                        ->delete();
                }
            }

            // Recalculate and update overall assignment status
            $specId = (int) $id;
            $allSubmissions = DB::table('enhanced_product_submissions')->where('specification_id', $specId)->get();
            // Recalculate status from fresh DB submissions
            $freshSubmissions = DB::table('enhanced_product_submissions')->where('specification_id', $specId)->get();
            $pendingCount = $freshSubmissions->where('status', 'pending')->count();
            $approvedCount = $freshSubmissions->where('status', 'approved')->count();
            $rejectedCount = $freshSubmissions->where('status', 'rejected')->count();
            $needVersionCount = $freshSubmissions->where('status', 'approved_need_version')->count();

            $newAssignmentStatus = 'in_review';
            if ($pendingCount === 0) {
                if ($approvedCount > 0 && $rejectedCount === 0 && $needVersionCount === 0) {
                    $newAssignmentStatus = 'approved';
                } elseif ($rejectedCount > 0 || $needVersionCount > 0) {
                    $newAssignmentStatus = 'revision_requested';
                }
            }

            DB::table('ai_photo_enhancer_assignments')
                ->where('specification_id', $specId)
                ->update([
                    'status'        => $newAssignmentStatus,
                    'admin_comment' => $mainComment,
                    'updated_at'    => now(),
                ]);

            DB::commit();
            return redirect()->back()->with('success', 'Product photo enhancement review updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error updating reviews: ' . $e->getMessage()]);
        }
    }

    public function approve($id)
    {
        $submission = DB::table('enhanced_product_submissions')->where('sno', $id)->first();
        if (!$submission) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            DB::table('enhanced_product_submissions')
                ->where('sno', $id)
                ->update([
                    'status'     => 'approved',
                    'updated_at' => now(),
                ]);

            $existing = DB::table('approved_enhanced_images')
                ->where('enhanced_image_path', $submission->enhanced_image_path)
                ->first();

            if ($existing) {
                DB::table('approved_enhanced_images')->where('sno', $existing->sno)->update([
                    'status'     => 'approved',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('approved_enhanced_images')->insert([
                    'countryid'            => $submission->countryid,
                    'companyid'            => $submission->companyid,
                    'subcompanyid'         => $submission->subcompanyid,
                    'projectid'            => $submission->projectid,
                    'subprojectid'         => $submission->subprojectid,
                    'specification_id'     => $submission->specification_id,
                    'ai_photo_enhancer_id' => $submission->ai_photo_enhancer_id,
                    'original_image_path'  => $submission->original_image_path,
                    'enhanced_image_path'  => $submission->enhanced_image_path,
                    'image_type'           => $submission->image_type,
                    'status'               => 'approved',
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Image approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error approving enhancement: ' . $e->getMessage()]);
        }
    }

    public function approveNeedVersion($id)
    {
        $submission = DB::table('enhanced_product_submissions')->where('sno', $id)->first();
        if (!$submission) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            DB::table('enhanced_product_submissions')
                ->where('sno', $id)
                ->update([
                    'status'     => 'approved_need_version',
                    'updated_at' => now(),
                ]);

            $existing = DB::table('approved_enhanced_images')
                ->where('enhanced_image_path', $submission->enhanced_image_path)
                ->first();

            if ($existing) {
                DB::table('approved_enhanced_images')->where('sno', $existing->sno)->update([
                    'status'     => 'approved_need_version',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('approved_enhanced_images')->insert([
                    'countryid'            => $submission->countryid,
                    'companyid'            => $submission->companyid,
                    'subcompanyid'         => $submission->subcompanyid,
                    'projectid'            => $submission->projectid,
                    'subprojectid'         => $submission->subprojectid,
                    'specification_id'     => $submission->specification_id,
                    'ai_photo_enhancer_id' => $submission->ai_photo_enhancer_id,
                    'original_image_path'  => $submission->original_image_path,
                    'enhanced_image_path'  => $submission->enhanced_image_path,
                    'image_type'           => $submission->image_type,
                    'status'               => 'approved_need_version',
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Image marked as Approved (Need Version).');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error approving enhancement: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_feedback' => 'required|string|max:1000',
        ]);

        $submission = DB::table('enhanced_product_submissions')->where('sno', $id)->first();
        if (!$submission) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            DB::table('enhanced_product_submissions')
                ->where('sno', $id)
                ->update([
                    'status'         => 'rejected',
                    'admin_feedback' => $request->input('admin_feedback'),
                    'updated_at'     => now(),
                ]);

            DB::table('approved_enhanced_images')
                ->where('enhanced_image_path', $submission->enhanced_image_path)
                ->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Enhanced image rejected and feedback sent.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error rejecting enhancement: ' . $e->getMessage()]);
        }
    }
}
