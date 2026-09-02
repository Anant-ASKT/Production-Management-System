<?php

namespace App\Http\Controllers\AiEnhancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignedProductController extends Controller
{
    public function index()
    {
        $user = auth('ai_enhancer')->user();
        
        $assignedProducts = \DB::table('ai_photo_enhancer_assignments as apa')
            ->join('auto_designer_specification_master as spec', 'apa.specification_id', '=', 'spec.sno')
            ->leftJoin('suppliers', 'spec.supplier_id', '=', 'suppliers.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'spec.gender')
            ->where('apa.ai_photo_enhancer_id', $user->sno)
            ->select(
                'apa.id as assignment_id',
                'apa.status as assignment_status',
                'apa.created_at as assigned_date',
                'spec.sno as spec_id',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'gender.name as age_group',
                'suppliers.name as supplier_name'
            )
            ->orderBy('apa.created_at', 'desc')
            ->get();

        return view('ai_enhancer.assigned_products.index', compact('assignedProducts'));
    }

    public function show($id)
    {
        $user = auth('ai_enhancer')->user();

        $product = \DB::table('ai_photo_enhancer_assignments as apa')
            ->join('auto_designer_specification_master as spec', 'apa.specification_id', '=', 'spec.sno')
            ->leftJoin('suppliers', 'spec.supplier_id', '=', 'suppliers.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'spec.gender')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'spec.item_type')
            ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'spec.designer_name')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'spec.composition')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'spec.sizes')
            ->leftJoin('auto_embellishment_master as embellishment', 'embellishment.id', '=', 'spec.embellishment')
            ->leftJoin('auto_manufacturing_process_master as manufacturing', 'manufacturing.id', '=', 'spec.manufacturing_process')
            ->leftJoin('auto_craftsman_master as craftsman', 'craftsman.id', '=', 'spec.craftsman')
            ->leftJoin('auto_manufacture_master as manufacture', 'manufacture.id', '=', 'spec.manufecture')
            ->leftJoin('auto_client_master as client', 'client.id', '=', 'spec.client')
            ->where('apa.id', $id)
            ->where('apa.ai_photo_enhancer_id', $user->sno)
            ->select(
                'apa.id as assignment_id',
                'apa.status as assignment_status',
                'apa.created_at as assigned_date',
                'spec.sno as spec_id',
                'spec.barcode',
                'spec.sku',
                'spec.img_path',
                'spec.subimg_path',
                'spec.supplier_id',
                'spec.clientreference',
                'spec.edatetime',
                'itemname.itemname as item_name_text',
                'colour.colourname as colour_text',
                'gender.name as gender_text',
                'itemtype.itemtype as item_type_text',
                'designer.designername as designer_name_text',
                'composition.composition_details as composition_text',
                'size.size as size_text',
                'embellishment.embellishmentname as embellishment_text',
                'manufacturing.manufacturing_process as manufacturing_process_text',
                'craftsman.name as craftsman_text',
                'manufacture.name as manufacture_text',
                'client.name as client_text',
                'suppliers.name as supplier_name'
            )
            ->first();

        if (!$product) {
            abort(404);
        }

        // Resolve sub-images with strict hierarchy to avoid duplicate raw vs moved files:
        $subImagesList = [];

        // 1. Current specification subimg_path
        if (!empty($product->subimg_path)) {
            $decoded = json_decode($product->subimg_path, true);
            if (is_array($decoded)) {
                $subImagesList = array_merge($subImagesList, $decoded);
            } else {
                $subImagesList[] = $product->subimg_path;
            }
        }

        // 2. If empty, check sibling records with same barcode
        if (empty($subImagesList) && !empty($product->barcode)) {
            $siblingSubImgs = \DB::table('auto_designer_specification_master')
                ->where('barcode', $product->barcode)
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

        // 3. If empty, check SubImgs folder on disk for this barcode
        if (empty($subImagesList) && !empty($product->barcode)) {
            $subImgDir = public_path('ItemsDesigner_Masterwithbarcode/' . $product->barcode . '/SubImgs');
            if (is_dir($subImgDir)) {
                $files = array_diff(scandir($subImgDir), ['.', '..']);
                foreach ($files as $file) {
                    $subImagesList[] = 'ItemsDesigner_Masterwithbarcode/' . $product->barcode . '/SubImgs/' . $file;
                }
            }
        }

        // 4. Only if still empty, fall back to supplier_products sub_images
        if (empty($subImagesList) && !empty($product->supplier_id)) {
            $supplierSubImages = \DB::table('supplier_products')
                ->where('supplier_id', $product->supplier_id)
                ->whereNotNull('sub_images')
                ->where('sub_images', '!=', '')
                ->where('sub_images', '!=', '[]')
                ->pluck('sub_images');

            foreach ($supplierSubImages as $sJson) {
                $decoded = json_decode($sJson, true);
                if (is_array($decoded)) {
                    $subImagesList = array_merge($subImagesList, $decoded);
                } elseif (is_string($sJson)) {
                    $subImagesList[] = $sJson;
                }
            }
        }

        $subImagesList = array_values(array_unique(array_filter($subImagesList)));
        $product->subimg_path = !empty($subImagesList) ? json_encode($subImagesList, JSON_UNESCAPED_SLASHES) : null;

        \DB::table('auto_designer_specification_master')
            ->where('sno', $product->spec_id)
            ->update(['subimg_path' => $product->subimg_path]);

        $submissions = \DB::table('enhanced_product_submissions')
            ->where('specification_id', $product->spec_id)
            ->where('ai_photo_enhancer_id', $user->sno)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ai_enhancer.assigned_products.show', compact('product', 'submissions'));
    }

    public function uploadEnhancedImage(Request $request)
    {
        $user = auth('ai_enhancer')->user();

        $request->validate([
            'specification_id'    => 'required|integer|exists:auto_designer_specification_master,sno',
            'original_image_path' => 'required|string',
            'image_type'          => 'required|string|in:main,sub',
            'enhanced_image'      => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
        ]);

        $specId = $request->input('specification_id');
        
        // Ensure assigned
        $assignment = \DB::table('ai_photo_enhancer_assignments')
            ->where('specification_id', $specId)
            ->where('ai_photo_enhancer_id', $user->sno)
            ->first();

        if (!$assignment) {
            return redirect()->back()->withErrors(['error' => 'You are not assigned to this product specification.']);
        }

        // Get parent spec for standard context fields
        $spec = \DB::table('auto_designer_specification_master')->where('sno', $specId)->first();
        if (!$spec) {
            return redirect()->back()->withErrors(['error' => 'Product specification not found.']);
        }

        // Handle file upload
        if ($request->hasFile('enhanced_image')) {
            $file = $request->file('enhanced_image');
            
            // Clean original filename
            $originalPath = $request->input('original_image_path');
            $originalFilename = basename($originalPath);
            if (empty($originalFilename)) {
                $originalFilename = 'product_image.jpg';
            }
            
            $filename = time() . '_' . $originalFilename;
            
            // Move file to public/enhanced_images directory
            $file->move(public_path('enhanced_images'), $filename);
            $enhancedPath = 'enhanced_images/' . $filename;

            // Insert submission
            \DB::table('enhanced_product_submissions')->insert([
                'countryid'            => $spec->countryid ?? null,
                'companyid'            => $spec->companyid ?? null,
                'subcompanyid'         => $spec->subcompanyid ?? null,
                'projectid'            => $spec->projectid ?? null,
                'subprojectid'         => $spec->subprojectid ?? null,
                'specification_id'     => $specId,
                'ai_photo_enhancer_id' => $user->sno,
                'original_image_path'  => $originalPath,
                'enhanced_image_path'  => $enhancedPath,
                'image_type'           => $request->input('image_type'),
                'status'               => 'pending',
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            return redirect()->back()->with('success', 'Enhanced image successfully uploaded and submitted for review.');
        }

        return redirect()->back()->withErrors(['error' => 'File upload failed.']);
    }
}
