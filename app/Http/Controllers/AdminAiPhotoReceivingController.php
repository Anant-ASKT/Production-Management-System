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
            ->leftJoin('auto_itemname_master as itemname', 'itemname.sno', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.sno', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.sno', '=', 'spec.gender');

        if ($status !== 'all') {
            if ($status === 'approved') {
                $query->whereIn('eps.status', ['approved', 'approved_need_version']);
            } else {
                $query->where('eps.status', $status);
            }
        }

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('itemname.itemname', 'like', "%{$search}%")
                  ->orWhere('ae.first_name', 'like', "%{$search}%")
                  ->orWhere('ae.last_name', 'like', "%{$search}%")
                  ->orWhere('spec.sku', 'like', "%{$search}%");
            });
        }

        $submissions = $query->select(
            'eps.*',
            'itemname.itemname as product_name',
            'colour.colourname as color',
            'spec.sku',
            'ae.first_name as enhancer_first_name',
            'ae.last_name as enhancer_last_name',
            'sup.name as supplier_name'
        )
        ->orderBy('eps.created_at', 'desc')
        ->paginate(20);

        return view('admin.ai_photo_enhancing.receiving', compact('submissions'));
    }

    public function show($id)
    {
        $submission = DB::table('enhanced_product_submissions as eps')
            ->join('auto_designer_specification_master as spec', 'eps.specification_id', '=', 'spec.sno')
            ->join('ai_photo_enhancers as ae', 'eps.ai_photo_enhancer_id', '=', 'ae.sno')
            ->leftJoin('suppliers as sup', 'spec.supplier_id', '=', 'sup.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.sno', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.sno', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.sno', '=', 'spec.gender')
            ->where('eps.sno', $id)
            ->select(
                'eps.*',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'gender.name as gender_text',
                'spec.sku',
                'spec.barcode',
                'spec.clientreference',
                'ae.first_name as enhancer_first_name',
                'ae.last_name as enhancer_last_name',
                'sup.name as supplier_name'
            )
            ->first();

        if (!$submission) {
            abort(404);
        }

        // Fetch all other historical submissions for this same original image by this enhancer (both rejected or approved)
        $history = DB::table('enhanced_product_submissions')
            ->where('specification_id', $submission->specification_id)
            ->where('ai_photo_enhancer_id', $submission->ai_photo_enhancer_id)
            ->where('original_image_path', $submission->original_image_path)
            ->where('sno', '!=', $submission->sno)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.ai_photo_enhancing.receiving_detail', compact('submission', 'history'));
    }

    public function approve($id)
    {
        $submission = DB::table('enhanced_product_submissions')->where('sno', $id)->first();
        if (!$submission) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            // Update status in submissions table
            DB::table('enhanced_product_submissions')
                ->where('sno', $id)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now()
                ]);

            // Save to approved_enhanced_images table
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
                'updated_at'           => now()
            ]);

            DB::commit();
            return redirect()->route('admin.ai-photo-enhancing.receiving')->with('success', 'Enhanced image approved successfully.');
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
            // Update status in submissions table
            DB::table('enhanced_product_submissions')
                ->where('sno', $id)
                ->update([
                    'status' => 'approved_need_version',
                    'updated_at' => now()
                ]);

            // Save to approved_enhanced_images table
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
                'updated_at'           => now()
            ]);

            DB::commit();
            return redirect()->route('admin.ai-photo-enhancing.receiving')->with('success', 'Enhanced image approved but requested new version.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error approving enhancement: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_feedback' => 'required|string|max:1000'
        ]);

        $submission = DB::table('enhanced_product_submissions')->where('sno', $id)->first();
        if (!$submission) {
            abort(404);
        }

        DB::table('enhanced_product_submissions')
            ->where('sno', $id)
            ->update([
                'status' => 'rejected',
                'admin_feedback' => $request->input('admin_feedback'),
                'updated_at' => now()
            ]);

        return redirect()->route('admin.ai-photo-enhancing.receiving')->with('success', 'Enhanced image rejected and feedback sent back.');
    }
}
