<?php

namespace App\Http\Controllers;

use App\Jobs\Series\UpdateFromFaceit;
use Illuminate\Http\Request;

class FaceitWebhook extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (! $request->header('B-Site-Token')) {
            logger('faceit webhook received, no token');
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        if ($request->header('B-Site-Token') !== config('services.faceit.webhook_secret')) {
            logger('faceit webhook received, token incorrect');
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        if (
            $request->event === 'match_status_ready'
            || $request->event === 'match_status_finished'
            || $request->event === 'match_status_cancelled'
            || $request->event === 'match_object_created'
        ) {
            dispatch(new UpdateFromFaceit($request->payload['id'], $request->event));
        }
    }
}
