<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function index(Request $request)
    {
        $query = Activity::with(['assignedUser', 'creator', 'updates'])
            ->orderBy('activity_date', 'desc')
            ->orderBy('created_at', 'desc');

        $this->applyFilters($query, $request);

        $activities = $query->paginate(20)->withQueryString();

        $summary = [
            'total'    => $query->getQuery()->getCountForPagination(),
            'done'     => Activity::where('status', 'done')->tap(fn($q) => $this->applyFilters($q, $request))->count(),
            'pending'  => Activity::where('status', 'pending')->tap(fn($q) => $this->applyFilters($q, $request))->count(),
        ];

        $personnel = User::active()->select('id', 'full_name')->get();

        return view('reports.index', compact('activities', 'personnel', 'summary'));
    }

    public function exportPdf(Request $request)
    {
        $this->auditService->log('export_pdf_report', null, null, null, $request->all(), 'Generated PDF report');

        $query = Activity::with(['assignedUser', 'creator', 'latestUpdate'])
            ->orderBy('activity_date', 'desc');

        $this->applyFilters($query, $request);
        $activities = $query->get();

        $pdf = Pdf::loadView('reports.pdf', compact('activities', 'request'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('activity-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $this->auditService->log('export_excel_report', null, null, null, $request->all(), 'Generated Excel report');

        return Excel::download(
            new \App\Exports\ActivitiesExport($request->all()),
            'activity-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function auditLogs(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->filled('action'), fn($q) => $q->where('action', $request->action))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        $personnel = User::select('id', 'full_name')->get();

        return view('reports.audit-logs', compact('logs', 'personnel'));
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->date_to);
        }

        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereDate('activity_date', today());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('search')) {
            $query->where(fn($q) => $q
                ->where('title', 'ilike', "%{$request->search}%")
                ->orWhere('description', 'ilike', "%{$request->search}%")
            );
        }
    }
}
