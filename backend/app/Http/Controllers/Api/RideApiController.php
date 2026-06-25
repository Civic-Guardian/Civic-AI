<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RideApiController extends Controller
{
    /**
     * POST /ride/start
     * Log entry tracking start of navigation session.
     */
    public function start(Request $request)
    {
        // TODO: Start safe ride navigation session
        return response()->json([
            'success' => true,
            'session_id' => null,
            'message' => 'Navigation session started successfully'
        ]);
    }

    /**
     * POST /ride/end
     * Concludes navigation session, logging total distance, time, and safety incidents.
     */
    public function end(Request $request)
    {
        // TODO: End safe ride navigation session
        return response()->json([
            'success' => true,
            'message' => 'Navigation session ended successfully'
        ]);
    }

    /**
     * POST /ride/share-location
     * Publishes active ride telemetry coordinates for family sharing or tracking.
     */
    public function shareLocation(Request $request)
    {
        // TODO: Log telemetry coordinates
        return response()->json([
            'success' => true
        ]);
    }
}
