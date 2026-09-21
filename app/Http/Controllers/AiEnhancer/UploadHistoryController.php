<?php

namespace App\Http\Controllers\AiEnhancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UploadHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('ai_enhancer')->user();
        $search = trim($request->input('search', ''));
        $status = $request->input('status', 'all');

        $query = DB::table('enhanced_product_submissions as eps')
            ->join('auto_designer_specification_master as spec', 'eps.specification_id', '=', 'spec.sno')
            ->leftJoin('suppliers', 'spec.supplier_id', '=', 'suppliers.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'spec.gender')
            ->leftJoin('ai_photo_enhancer_assignments as apa', function ($join) use ($user) {
                $join->on('apa.specification_id', '=', 'spec.sno')
                     ->where('apa.ai_photo_enhancer_id', '=', $user->sno);
            })
            ->where('eps.ai_photo_enhancer_id', $user->sno)
            ->select(
                'eps.specification_id',
                'spec.sku',
                'spec.barcode',
                'spec.clientreference',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'gender.name as gender_text',
                DB::raw('MAX(apa.id) as assignment_id'),
                DB::raw('COUNT(eps.sno) as total_images'),
                DB::raw("SUM(CASE WHEN eps.status = 'pending' THEN 1 ELSE 0 END) as pending_count"),
                DB::raw("SUM(CASE WHEN eps.status = 'approved' THEN 1 ELSE 0 END) as approved_count"),
                DB::raw("SUM(CASE WHEN eps.status = 'approved_need_version' THEN 1 ELSE 0 END) as need_version_count"),
                DB::raw("SUM(CASE WHEN eps.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count"),
                DB::raw('MAX(eps.created_at) as latest_submission_date'),
                DB::raw("COALESCE(MAX(suppliers.name), (SELECT s_vsw.name FROM vendor_stock_web vsw JOIN suppliers s_vsw ON s_vsw.sno = vsw.vendor_id WHERE (vsw.item_id = spec.id OR vsw.item_id = spec.sno OR vsw.batch_no = spec.sku) ORDER BY vsw.sno DESC LIMIT 1)) as supplier_name")
            )
            ->groupBy(
                'eps.specification_id',
                'spec.sku',
                'spec.barcode',
                'spec.clientreference',
                'itemname.itemname',
                'colour.colourname',
                'gender.name',
                'spec.id',
                'spec.sno'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('itemname.itemname', 'like', "%{$search}%")
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

        $history = $query->orderBy(DB::raw('MAX(eps.created_at)'), 'desc')->paginate(15);

        return view('ai_enhancer.upload_history.index', compact('history', 'status', 'search'));
    }

    public function show($id)
    {
        $user = auth('ai_enhancer')->user();

        // Check if $id is specification_id
        $spec = DB::table('auto_designer_specification_master as spec')
            ->leftJoin('suppliers', 'spec.supplier_id', '=', 'suppliers.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'spec.item_name')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'spec.colour')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'spec.gender')
            ->leftJoin('ai_photo_enhancer_assignments as apa', function ($join) use ($user) {
                $join->on('apa.specification_id', '=', 'spec.sno')
                     ->where('apa.ai_photo_enhancer_id', '=', $user->sno);
            })
            ->where('spec.sno', $id)
            ->select(
                'spec.*',
                'itemname.itemname as product_name',
                'colour.colourname as color',
                'gender.name as gender_text',
                'apa.id as assignment_id',
                'apa.status as assignment_status',
                DB::raw("COALESCE(suppliers.name, (SELECT s_vsw.name FROM vendor_stock_web vsw JOIN suppliers s_vsw ON s_vsw.sno = vsw.vendor_id WHERE (vsw.item_id = spec.id OR vsw.item_id = spec.sno OR vsw.batch_no = spec.sku) ORDER BY vsw.sno DESC LIMIT 1)) as supplier_name")
            )
            ->first();

        // If not found by spec.sno, check if legacy submission sno
        if (!$spec) {
            $legacySub = DB::table('enhanced_product_submissions')
                ->where('sno', $id)
                ->where('ai_photo_enhancer_id', $user->sno)
                ->first();

            if ($legacySub) {
                return redirect()->route('ai-enhancer.upload-history.show', $legacySub->specification_id);
            }
            abort(404, 'Product not found.');
        }

        // Fetch all submissions by this enhancer for this specification
        $submissions = DB::table('enhanced_product_submissions')
            ->where('specification_id', $spec->sno)
            ->where('ai_photo_enhancer_id', $user->sno)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ai_enhancer.upload_history.show', compact('spec', 'submissions'));
    }
}
