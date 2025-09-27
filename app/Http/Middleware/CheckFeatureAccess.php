<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // For now, always allow access (MVP phase)
        // Later this will check: $user->hasFeature($feature)
        if (!$user->hasFeature($feature)) {
            return response()->json([
                'success' => false,
                'message' => 'Feature not available in your plan',
                'required_plan' => $this->getRequiredPlan($feature),
                'upgrade_required' => true
            ], 403);
        }

        return $next($request);
    }

    /**
     * Get the required plan for a feature
     */
    private function getRequiredPlan(string $feature): string
    {
        $featurePlans = [
            'photo_documents' => 'pro',
            'fuel_tracking' => 'pro',
            'mileage_tracking' => 'pro',
            'advanced_analytics' => 'pro',
            'smart_reminders' => 'pro',
            'widgets' => 'pro',
            'export_data' => 'pro',
            'gps_integration' => 'premium',
            'obd_diagnosis' => 'premium',
            'ai_assistant' => 'premium',
            'checklists' => 'premium',
            'gamification' => 'premium',
            'cloud_backup' => 'premium',
            'api_integrations' => 'premium',
            'client_management' => 'business',
            'business_reports' => 'business',
            '1c_integration' => 'business',
            'master_app' => 'business',
            'business_analytics' => 'business',
        ];

        return $featurePlans[$feature] ?? 'free';
    }
}
