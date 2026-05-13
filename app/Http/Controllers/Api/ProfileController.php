<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\StudentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get student profile
     */
    public function show(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data santri tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new StudentResource($student),
        ]);
    }

    /**
     * Update student profile (hanya father_phone, mother_phone, address)
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data santri tidak ditemukan.',
            ], 404);
        }

        $student->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => new StudentResource($student->fresh()),
        ]);
    }

    /**
     * Get student photo (pass foto)
     */
    public function photo(Request $request)
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data santri tidak ditemukan.',
            ], 404);
        }

        if (!$student->photo) {
            return response()->json([
                'success' => false,
                'message' => 'Foto santri belum tersedia.',
            ], 404);
        }

        // Cek file di storage
        if (Storage::disk('public')->exists($student->photo)) {
            $file = Storage::disk('public')->get($student->photo);
            $mimeType = Storage::disk('public')->mimeType($student->photo);

            return response($file, 200)->header('Content-Type', $mimeType);
        }

        // Fallback: return URL info
        return response()->json([
            'success' => true,
            'data' => [
                'photo_path' => $student->photo,
                'photo_url' => asset('storage/' . $student->photo),
            ],
        ]);
    }
}
