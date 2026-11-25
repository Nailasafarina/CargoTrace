<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Dispatch user to their role-specific dashboard.
     */
    public function index(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            default => redirect()->route('customer.dashboard'),
        };
    }

    /**
     * Administrator Dashboard.
     */
    public function admin(Request $request): View
    {
        $metrics = $this->dashboardService->getAdminOperatorMetrics($request->user());

        return view('dashboard.admin', [
            'user' => $request->user(),
            'metrics' => $metrics,
        ]);
    }

    /**
     * Logistics Operator Dashboard.
     */
    public function operator(Request $request): View
    {
        $metrics = $this->dashboardService->getAdminOperatorMetrics($request->user());

        return view('dashboard.operator', [
            'user' => $request->user(),
            'metrics' => $metrics,
        ]);
    }

    /**
     * Customer Dashboard (Personalized and scoped to user's cargo).
     */
    public function customer(Request $request): View
    {
        $metrics = $this->dashboardService->getCustomerMetrics($request->user());

        return view('dashboard.customer', [
            'user' => $request->user(),
            'metrics' => $metrics,
        ]);
    }
}
