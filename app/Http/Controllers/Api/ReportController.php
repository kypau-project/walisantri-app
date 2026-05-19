<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * List reports
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        $reports = $student->reports()
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ReportResource::collection($reports),
        ]);
    }

    /**
     * Show single report detail
     */
    public function show(Request $request, $id): JsonResponse
    {
        $student = $request->user()->student;
        $report = $student->reports()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $report->id,
                'semester' => $report->semester,
                'academic_year' => $report->academic_year,
                'grades' => $report->grades,
                'average_score' => $report->average_score,
                'rank' => $report->rank,
                'notes' => $report->notes,
                'published_at' => $report->published_at?->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Download report as PDF
     */
    public function download(Request $request, $id): mixed
    {
        $student = $request->user()->student;
        $report = $student->reports()->findOrFail($id);

        // Check if DomPDF is available
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return response()->json([
                'success' => false,
                'message' => 'PDF generator tidak tersedia.',
            ], 500);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report', [
            'student' => $student,
            'report' => $report,
        ]);

        $filename = "Raport_{$student->name}_{$report->semester}_{$report->academic_year}.pdf";

        return $pdf->download($filename);
    }
}
