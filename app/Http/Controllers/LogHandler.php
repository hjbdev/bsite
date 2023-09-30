<?php

namespace App\Http\Controllers;

use App\Models\Series;
use CSLog\CS2\Patterns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LogHandler extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (!$secret = $request->query('secret')) {
            abort(403);
        }

        if (Cache::has('series-' . $secret)) {
            $series = Cache::get('series-' . $secret);
        } else {
            try {
                $series = Series::where('secret', $secret)->firstOrFail();
            } catch (\Exception $e) {
                logger('Couldnt find series with secret: ' . $secret);
                throw $e;
            }
            Cache::put('series-' . $series->secret, $series, 60);
        }

        $rawLog = file_get_contents('php://input');

        str($rawLog)->split("~\R~u")->each(function ($rawLogLine) use ($series) {
            $log = Patterns::match($rawLogLine);

            if (!$log) {
                // logger('NO LOG--' . $rawLogLine);
                return;
            }

            // logger(json_encode($log));
            $series->logs()->create([
                'type' => $log->type,
                'data' => (array) $log,
            ]);
        });
    }
}
