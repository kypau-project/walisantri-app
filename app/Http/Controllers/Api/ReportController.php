<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
     * Download report as PDF
     */
    public function download(Request $request): mixed
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
        ]);

        $student = $request->user()->student;
        $report = $student->reports()->findOrFail($request->report_id);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.report', [
            'student' => $student,
            'report' => $report,
        ]);

        $filename = "Raport_{$student->name}_{$report->semester}_{$report->academic_year}.pdf";

        return $pdf->download($filename);
    }
}
