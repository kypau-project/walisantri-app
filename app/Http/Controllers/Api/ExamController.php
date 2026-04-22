<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExamResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * List available exams
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        $exams = $student->exams()
            ->orderBy('exam_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ExamResource::collection($exams),
        ]);
    }

    /**
     * Start an exam
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $student = $request->user()->student;
        $exam = $student->exams()->where('exam_id', $request->exam_id)->first();

        if (!$exam) {
            return response()->json(['success' => false, 'message' => 'Ujian tidak ditemukan.'], 404);
        }

        if ($exam->pivot->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Ujian sudah selesai.'], 400);
        }

        if ($exam->pivot->status === 'in_progress') {
            return response()->json([
                'success' => true,
                'message' => 'Ujian sedang berlangsung.',
                'data' => [
                    'exam_url' => $exam->exam_url,
                    'started_at' => $exam->pivot->started_at,
                ],
            ]);
        }

        // Start the exam
        $student->exams()->updateExistingPivot($request->exam_id, [
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian dimulai.',
            'data' => [
                'exam_url' => $exam->exam_url,
                'duration_minutes' => $exam->duration_minutes,
            ],
        ]);
    }
}
