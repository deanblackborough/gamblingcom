<?php

namespace App\Http\Controllers;

use App\Service\Math\SphericalLaw;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected string $cache_key_affiliates = 'raw-affiliates-data';
    protected string $cache_key_invitees = 'invitees-data';

    /**
     * Calculate the list of invitees from the affiliates data
     * and then cache the results, we only need to do the calculations once
     */
    protected function calculateAndFetchDublinInvitees(array $affiliates, float $max_distance = 100.00): array
    {
        if (Cache::has($this->cache_key_invitees)) {
            return Cache::get($this->cache_key_invitees);
        }

        $dublin_latitude = 53.3340285;
        $dublin_longitude = -6.2535495;

        $invitees = [];
        foreach ($affiliates as $affiliate) {
            $math = new SphericalLaw(
                $dublin_latitude,
                $dublin_longitude,
                (float) $affiliate['latitude'],
                (float) $affiliate['longitude'],
            );

            $distance = $math->distance();

            if ($distance <= $max_distance) {
                $affiliate['distance'] = $distance;
                $invitees[] = $affiliate;
            }
        }

        Cache::put($this->cache_key_invitees, $invitees);

        return $invitees;
    }

    /**
     * Rather than operate on a file directly I have opted to cache the
     * data the first time we try to fetch it, this way we don't have to deal with
     * any file operations for this static data
     */
    protected function fetchAffiliatesDataFromCache(): array
    {
        if (Cache::has($this->cache_key_affiliates)) {
            return Cache::get($this->cache_key_affiliates);
        }

        $path = resource_path('data/affiliates.txt');
        if (!File::isFile($path)) {
            abort(500, 'Affiliates file cannot be found');
        }

        $raw_data = file_get_contents(resource_path('data/affiliates.txt'));
        $raw_data = explode("\n", $raw_data);

        $data = [];
        foreach($raw_data as $raw_data_row) {
            try {
                $decoded = json_decode($raw_data_row, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                abort(500, 'Unable to decode data row');
            }

            $data['affiliates'][$decoded['affiliate_id']] = $decoded;
        }

        Cache::put($this->cache_key_affiliates, $data['affiliates']);

        return $data['affiliates'];
    }


}
