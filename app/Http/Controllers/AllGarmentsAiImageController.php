<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AllGarmentsAiImageController extends Controller
{
    public function images(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $request->validate([
            'specification_id' => 'required|integer'
        ]);

        $specification = DB::table('auto_designer_specification_master')
            ->where('sno', $request->integer('specification_id'))
            ->first([
                'sno',
                'barcode',
                'sku',
                'companyid',
                'subcompanyid',
                'projectid'
            ]);

        if (!$specification) {
            return response()->json([
                'success' => false,
                'message' => 'Product specification not found.'
            ], 404);
        }

        $record = DB::table('ai_enhanced_images_uploded')
            ->where('garment_id', $specification->sno)
            ->where('barcode', $specification->barcode)
            ->first();

        return response()->json([
            'success' => true,
            'barcode' => $specification->barcode,
            'images' => $this->formatImages($record, $specification)
        ]);
    }

    public function save(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $request->validate([
            'specification_id' => 'required|integer',
            'barcode' => 'required|string|max:255',
            'ai_main_image' => 'nullable|file|mimes:jpg,jpeg,png,webp,heic,HEIC|max:10240',
            'ai_sub_images' => 'nullable|array',
            'ai_sub_images.*' => 'file|mimes:jpg,jpeg,png,webp,heic,HEIC|max:10240'
        ]);

        if (!$request->hasFile('ai_main_image') && !$request->hasFile('ai_sub_images')) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a Main AI Image or at least one Sub AI Image.'
            ], 422);
        }

        $specification = DB::table('auto_designer_specification_master')
            ->where('sno', $request->integer('specification_id'))
            ->first([
                'sno',
                'barcode',
                'sku',
                'companyid',
                'subcompanyid',
                'projectid'
            ]);

        if (!$specification) {
            return response()->json([
                'success' => false,
                'message' => 'Product specification not found.'
            ], 404);
        }

        if ((string) $specification->barcode !== (string) $request->input('barcode')) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode does not match the selected product.'
            ], 422);
        }

        $companyId = (int) ($specification->companyid ?? 0);
        $subCompanyId = (int) ($specification->subcompanyid ?? 0);
        $projectId = (int) ($specification->projectid ?? 0);

        $mainDirectory = public_path(
            'uploadedimages/ai/main/' .
            $companyId . '/' .
            $subCompanyId . '/' .
            $projectId
        );

        $subDirectory = public_path(
            'uploadedimages/ai/sub/' .
            $companyId . '/' .
            $subCompanyId . '/' .
            $projectId
        );

        if (!is_dir($mainDirectory)) {
            mkdir($mainDirectory, 0775, true);
        }

        if (!is_dir($subDirectory)) {
            mkdir($subDirectory, 0775, true);
        }

        $existing = DB::table('ai_enhanced_images_uploded')
            ->where('garment_id', $specification->sno)
            ->where('barcode', $specification->barcode)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | AI IMAGE FILE NAME
        |--------------------------------------------------------------------------
        | Use SKU as the AI image filename when SKU exists.
        | If SKU is empty/not available, use barcode.
        */
        $fileNameSource = trim((string) ($specification->sku ?? ''));

        if ($fileNameSource === '') {
            $fileNameSource = trim((string) ($specification->barcode ?? ''));
        }

        $baseName = preg_replace(
            '/[^A-Za-z0-9_-]+/',
            '_',
            $fileNameSource
        );

        $baseName = trim($baseName, '_');

        if ($baseName === '') {
            $baseName = 'barcode_' . $specification->sno;
        }

        $mainImage = $existing->ai_main_image ?? null;
        $subImages = [];

        if ($existing && !empty($existing->ai_sub_images)) {
            $decoded = json_decode($existing->ai_sub_images, true);
            if (is_array($decoded)) {
                $subImages = array_values(array_filter($decoded));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MAIN AI IMAGE
        |--------------------------------------------------------------------------
        | Only one Main AI Image is stored. If a new main image is uploaded,
        | it replaces the previous main image for this barcode.
        */
        if ($request->hasFile('ai_main_image')) {
            $file = $request->file('ai_main_image');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');

            $mainFileName = $baseName . '_AI_MAIN.' . $extension;
            $mainPath = $mainDirectory . DIRECTORY_SEPARATOR . $mainFileName;

            $counter = 1;
            while (file_exists($mainPath)) {
                if ($existing && $existing->ai_main_image === $mainFileName) {
                    break;
                }

                $mainFileName =
                    $baseName . '_AI_MAIN_' . $counter . '.' . $extension;

                $mainPath = $mainDirectory . DIRECTORY_SEPARATOR . $mainFileName;
                $counter++;
            }

            if ($existing && !empty($existing->ai_main_image)) {
                $oldMainPath = $mainDirectory . DIRECTORY_SEPARATOR . basename($existing->ai_main_image);

                if (is_file($oldMainPath) && basename($existing->ai_main_image) !== $mainFileName) {
                    @unlink($oldMainPath);
                }
            }

            $file->move($mainDirectory, $mainFileName);
            $mainImage = $mainFileName;
        }

        /*
        |--------------------------------------------------------------------------
        | SUB AI IMAGES
        |--------------------------------------------------------------------------
        | Sub images are appended. Existing sub images are not removed.
        */
        if ($request->hasFile('ai_sub_images')) {
            foreach ($request->file('ai_sub_images') as $file) {
                $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                $subNumber = count($subImages) + 1;

                do {
                    $subFileName =
                        $baseName .
                        '_AI_SUB_' .
                        $subNumber .
                        '.' .
                        $extension;

                    $subPath = $subDirectory . DIRECTORY_SEPARATOR . $subFileName;
                    $subNumber++;
                } while (file_exists($subPath));

                $file->move($subDirectory, $subFileName);
                $subImages[] = $subFileName;
            }
        }

        $now = now();

        $data = [
            'garment_id' => $specification->sno,
            'barcode' => $specification->barcode,
            'user_id' => Auth::id(),
            'company_id' => $companyId,
            'sub_company_id' => $subCompanyId,
            'project_id' => $projectId,
            'original_filename' => $baseName,
            'ai_main_image' => $mainImage ?: '',
            'ai_sub_images' => json_encode(array_values($subImages)),
            'updated_at' => $now
        ];

        if ($existing) {
            DB::table('ai_enhanced_images_uploded')
                ->where('id', $existing->id)
                ->update($data);
        } else {
            $data['created_at'] = $now;

            DB::table('ai_enhanced_images_uploded')
                ->insert($data);
        }

        $saved = DB::table('ai_enhanced_images_uploded')
            ->where('garment_id', $specification->sno)
            ->where('barcode', $specification->barcode)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'AI images saved successfully for ' . (trim((string) ($specification->sku ?? '')) !== '' ? 'SKU ' . $specification->sku : 'barcode ' . $specification->barcode) . '.',
            'barcode' => $specification->barcode,
            'images' => $this->formatImages($saved, $specification)
        ]);
    }

    private function formatImages(?object $record, object $specification): array
    {
        $images = [];

        if ($record && !empty($record->ai_main_image)) {
            $images[] = [
                'filename' => $record->ai_main_image,
                'type' => 'main',
                'url' => $this->imageUrl(
                    $record->ai_main_image,
                    $specification,
                    'main'
                )
            ];
        }

        $subImages = json_decode($record->ai_sub_images ?? '[]', true);

        if (is_array($subImages)) {
            foreach ($subImages as $fileName) {
                if (!empty($fileName)) {
                    $images[] = [
                        'filename' => $fileName,
                        'type' => 'sub',
                        'url' => $this->imageUrl(
                            $fileName,
                            $specification,
                            'sub'
                        )
                    ];
                }
            }
        }

        return $images;
    }

    private function imageUrl(
        string $fileName,
        object $specification,
        string $type
    ): string {
        return asset(
            'uploadedimages/ai/' .
            $type . '/' .
            (int) $specification->companyid . '/' .
            (int) $specification->subcompanyid . '/' .
            (int) $specification->projectid . '/' .
            rawurlencode($fileName)
        );
    }
}
