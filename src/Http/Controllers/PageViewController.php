<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use Prajwal89\Analytics\Jobs\RecordPageViewJob;
use Prajwal89\Analytics\Services\AnalyticService;
use Prajwal89\Analytics\Traits\ApiResponser;

class PageViewController
{
    use ApiResponser;

    // ! we are assuming that this request is made by the same user who visited it
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string|url',
        ]);

        if ($validator->fails()) {
            Log::info('failed validation', [
                'message' => $validator->errors()->first(),
            ]);

            return null;
        }

        $agent = new Agent;

        $analyticData = [
            'referrer_url' => $request->referrer,
            'user_agent' => $request->userAgent(),

            'ip_address' => $request->ip(),
            'session_id' => $request->session()->getId(),

            'viewport_height' => $request->viewport_height,
            'viewport_width' => $request->viewport_width,
            'time_on_page' => $request->time_on_page,
            'scroll_depth' => $request->scroll_depth,
            'device' => $agent->device(),        // Returns device name (iPhone, Nexus, etc)
            'platform' => $agent->platform(),    // Returns platform (iOS, Android, Windows, etc)
            'browser' => $agent->browser(),
            'created_at' => now(), // bc. job can execute after some time
        ];

        if (config('analytics.use_queue')) {
            dispatch(new RecordPageViewJob(
                url: $request->url,
                userId: auth()?->user()?->id,
                analyticData: $analyticData
            ))->onQueue(config('analytics.queue_name'));
        } else {
            AnalyticService::recordPageView(
                url: $request->url,
                analyticData: $analyticData,
                userId: auth()?->user()?->id,
            );
        }

        return $this->successResponse();
    }
}
