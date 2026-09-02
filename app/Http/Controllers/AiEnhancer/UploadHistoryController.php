<?php

namespace App\Http\Controllers\AiEnhancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadHistoryController extends Controller
{
    public function index()
    {
        $user = auth('ai_enhancer')->user();

        $history = \DB::table('enhanced_product_submissions as eps')
            ->join('auto_designer_specification_master as spec', 'eps.specification_id', '=', 'spec.sno')
            ->leftJoin('suppliers', 'spec.supplier_id', '=', 'suppliers.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->where('eps.ai_photo_enhancer_id', $user->sno)
            ->select(
                'eps.*',
                'spec.sku',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'suppliers.name as supplier_name'
            )
            ->orderBy('eps.created_at', 'desc')
            ->paginate(20);

        return view('ai_enhancer.upload_history.index', compact('history'));
    }

    public function show($id)
    {
        $user = auth('ai_enhancer')->user();

        $submission = \DB::table('enhanced_product_submissions as eps')
            ->join('auto_designer_specification_master as spec', 'eps.specification_id', '=', 'spec.sno')
            ->leftJoin('suppliers', 'spec.supplier_id', '=', 'suppliers.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'spec.gender')
            ->where('eps.sno', $id)
            ->where('eps.ai_photo_enhancer_id', $user->sno)
            ->select(
                'eps.*',
                'spec.sku',
                'spec.barcode',
                'spec.clientreference',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'gender.name as gender_text',
                'suppliers.name as supplier_name'
            )
            ->first();

        if (!$submission) {
            abort(404);
        }

        // Fetch other submissions for this same original image path by this enhancer
        $history = \DB::table('enhanced_product_submissions')
            ->where('specification_id', $submission->specification_id)
            ->where('ai_photo_enhancer_id', $user->sno)
            ->where('original_image_path', $submission->original_image_path)
            ->where('sno', '!=', $submission->sno)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ai_enhancer.upload_history.show', compact('submission', 'history'));
    }
}
