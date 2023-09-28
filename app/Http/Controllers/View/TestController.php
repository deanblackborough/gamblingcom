<?php

declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * I'm a fan of view and action controllers, for this example there is only one
 * controller but I still wanted to add it into a view namespace to show how I would
 * typically structure an app.
 */

class TestController extends Controller
{
    public function index(): View
    {
        $max_distance = 100.00;
        $invitees = $this->calculateAndFetchDublinInvitees(
            $this->fetchAffiliatesDataFromCache(),
            $max_distance
        );

        usort($invitees, static function($a, $b){
            return $a['name'] <=> $b['name'];
        });

        return view(
            'test.index',
            [
                'max_distance' => $max_distance,
                'invitees' => $invitees
            ]
        );
    }
}
