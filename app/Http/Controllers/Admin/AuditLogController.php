<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    /**
     * Display a listing of system audit trails.
     * Strictly restricted to platform administrators.
     */
    public function index(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403, 'Administrator privilege required.');

        $query = AuditLog::with('user');

        // Filter by Actor (user_id)
        if ($actorId = $request->input('user_id')) {
            $query->where('user_id', $actorId);
        }

        // Filter by Action (e.g. created, updated, deleted, status_updated, exception_logged, setting_updated)
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // Filter by Entity Type (e.g. Shipment, ShipmentException, TrackingEvent, Setting, Carrier, Location)
        if ($entityType = $request->input('entity_type')) {
            $query->where('entity_type', $entityType);
        }

        // Filter by Date Range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $auditLogs = $query->latest('created_at')->paginate(20)->withQueryString();

        $actors = User::whereHas('auditLogs')->orderBy('name')->get();
        $actions = AuditLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $entityTypes = AuditLog::select('entity_type')->distinct()->orderBy('entity_type')->pluck('entity_type');

        return view('admin.audit-logs.index', compact('auditLogs', 'actors', 'actions', 'entityTypes'));
    }
}
