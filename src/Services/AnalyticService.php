<?php

declare(strict_types=1);

namespace Prajwal89\Analytics\Services;

use App\Models\Bias;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;
use Prajwal89\Analytics\Events\NewPageViewEvent;
use Prajwal89\Analytics\Interfaces\RouteModelResolverInterface;
use Prajwal89\Analytics\Models\PageView;

// todo fire page view event
// we can fire events like 1000k views on tools etc
class AnalyticService
{
    // job should be able to execute this
    public static function recordPageView(
        string $url,
        array $analyticData,
        ?int $userId = null,
    ): ?PageView {
        // current url
        $currentRouteName = self::resolveRouteName($url);

        $modelResolver = app(RouteModelResolverInterface::class);

        $model = $modelResolver->resolve($url);

        $modelData = [];

        if ($model instanceof Model) {
            $modelData['viewable_id'] = $model->id;
            $modelData['viewable_type'] = get_class($model);
        }

        $uri = Uri::of($url);

        $pageView = PageView::query()->create($modelData
            + $analyticData
            + self::getLocationData($analyticData['ip_address'])
            + [
                'path' => $uri->path(),
                'route_name' => $currentRouteName,
                ...$userId ? ['user_id' => $userId] : [],
                ...$uri->query()->toArray() === []
                    ? []
                    : ['query_string' => $uri->query()->toArray()],
            ]);

        if (config('analytics.record_bias')) {

            if ($analyticData['referrer_url'] === null) {
                return null;
            }

            // biasable2
            $referralRouteName = self::resolveRouteName($analyticData['referrer_url']);

            $referralModel = $modelResolver->resolve($analyticData['referrer_url']);

            if (!$referralModel instanceof Model) {
                return null;
            }

            self::trackBias(
                biasable1: $model,
                biasable1RouteName: $currentRouteName,
                biasable2: $referralModel,
                biasable2RouteName: $referralRouteName,
                sessionId: $analyticData['session_id'],
            );
        }

        event(new NewPageViewEvent($pageView));

        return $pageView;
    }

    public static function trackBias(
        Model $biasable1,
        string $biasable1RouteName,
        Model $biasable2,
        string $biasable2RouteName,
        string $sessionId
    ): void {
        // biasable should have always smaller id
        // * we can take meaning as

        // if ($biasable1->getKey() > $biasable2->getKey()) {
        //     [$biasable1, $biasable2] = [$biasable2, $biasable1];
        // }

        // biasable1
        $biasable1Data = [];
        $biasable1Data['biasable1_id'] = $biasable1->id;
        $biasable1Data['biasable1_type'] = get_class($biasable1);
        $biasable1Data['biasable1_route_name'] = $biasable1RouteName;

        $biasable2Data = [];
        $biasable2Data['biasable2_id'] = $biasable2->id;
        $biasable2Data['biasable2_type'] = get_class($biasable2);
        $biasable2Data['biasable2_route_name'] = $biasable2RouteName;

        $biasData = array_merge($biasable1Data, $biasable2Data);

        // * check if biasable1 and 2 are of the same type

        $bias1String = $biasable1Data['biasable1_id'] . ':' . $biasable1Data['biasable1_type'] . ':' . $biasable1Data['biasable1_route_name'];
        $bias2String = $biasable2Data['biasable2_id'] . ':' . $biasable2Data['biasable2_type'] . ':' . $biasable2Data['biasable2_route_name'];

        if ($bias1String === $bias2String) {
            return;
        }

        // $existingBias = Bias::query()
        //     ->where($biasData)
        //     ->where('last_session_id', $sessionId)
        //     ->first();

        // if (!$existingBias) {
        // }
        // If no existing record for the session, update or create a new one

        Bias::query()->updateOrCreate($biasData, [
            'bias' => DB::raw('bias + 1'),
            'last_session_id' => $sessionId,
        ]);

        // Bias::query()->create($biasData);
        // todo check if last session is same

    }

    public static function getLocationData(string $ipAddress): array
    {
        $dbPath = config('analytics.geolite_db_path');

        if (!File::exists($dbPath)) {
            Log::info("GeoLite DB is not downloaded please run command 'php artisan analytics:sync-geolite-db'");

            return [];
        }

        try {
            $cityDbReader = new Reader($dbPath);

            $record = $cityDbReader->city($ipAddress);

            return [
                'country_code' => $record->country->isoCode,
                'city' => $record->city->name,
                'latitude' => $record->location->latitude,
                'longitude' => $record->location->longitude,
            ];
        } catch (AddressNotFoundException $e) {
            return [];
        }
    }

    public static function resolveRouteName(string $url): string
    {
        // Parse the path from the URL
        $path = parse_url($url, PHP_URL_PATH);

        if ($path === null) {
            $url .= '/';
            $path = parse_url($url, PHP_URL_PATH);
        }

        return Route::getRoutes()->match(request()->create($path))->getName();
    }
}
