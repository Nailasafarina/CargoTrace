<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\EtaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected EtaService $etaService
    ) {}

    /**
     * Display the system configuration management view.
     */
    public function index(): View
    {
        $currentThreshold = $this->etaService->getThresholdHours();

        return view('admin.settings', [
            'delayThreshold' => $currentThreshold,
        ]);
    }

    /**
     * Update system configuration parameters.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'delay_threshold_hours' => ['required', 'integer', 'min:1', 'max:168'],
            're_evaluate_now' => ['nullable', 'boolean'],
        ]);

        $oldThreshold = $this->etaService->getThresholdHours();
        $newThreshold = (int) $validated['delay_threshold_hours'];

        Setting::set(
            'delay_threshold_hours',
            $newThreshold,
            'integer',
            'Configurable delay threshold before a shipment is classified as DELAYED.'
        );

        // Record Audit Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'SETTING_UPDATED',
            'entity_type' => Setting::class,
            'entity_id' => 1,
            'old_values' => ['delay_threshold_hours' => $oldThreshold],
            'new_values' => ['delay_threshold_hours' => $newThreshold],
            'ip_address' => $request->ip() ?? '127.0.0.1',
        ]);

        $message = "Delay threshold updated from {$oldThreshold}h to {$newThreshold}h.";

        // Optional immediate batch re-evaluation
        if (! empty($validated['re_evaluate_now'])) {
            $count = $this->etaService->evaluateBatch($newThreshold);
            $message .= " Automatically re-evaluated {$count} active shipments.";
        }

        return redirect()->route('admin.settings.index')->with('status', $message);
    }
}
