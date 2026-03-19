<?php

namespace app\controllers;

use app\core\View;
use app\models\enums\PageIndex;
use app\services\interfaces\IJazzServ;

/** Controller for Jazz theme actions
 * - GET: Display jazz homepage
 * - POST: - */
final readonly class JazzCtrl extends BaseCtrl
{
    private IJazzServ $IJazzServ;

    public function __construct(IJazzServ $IJazzServ)
    {
        $this->IJazzServ = $IJazzServ;
    }

    //region GET Requests

    /** GET /jazz, Landingpage for jazz page */
    public function homePage(): void
    {
        View::render('/jazz/home.php', "Jazz" . View::addSiteName(), PageIndex::JazzHome->value);
    }
    
    /** GET /jazz/{id} - Display detail page for a jazz event */
    public function detailPage(int $id): void
    {
        // Temporary/mock event data (replace with repository/service data later)
        $event = [
            'id' => $id,
            'title' => 'Sunset Quartet',
            'artist' => [
                'name' => 'The Sunset Quartet',
                'bio' => "An acclaimed modern-jazz quartet blending soulful melodies with contemporary arrangements. Formed in 2015, they've toured across Europe and released three studio albums.",
                'image' => '/assets/icons/logo/sunset-quartet.jpg',
            ],
            'date' => '2026-07-24',
            'time' => '20:30',
            'venue' => [
                'name' => 'Patronaat',
                'address' => 'Zijlsingel 82, Haarlem',
            ],
            'genre' => 'Contemporary Jazz',
            'duration' => '90 min',
            'description' => "Join The Sunset Quartet for an intimate evening exploring original compositions and inventive takes on jazz standards.",
            'tickets_url' => '#',
            'lineup' => [
                'Anna Vermeulen — Saxophone',
                'Jonas de Vries — Piano',
                'Liam Brouwer — Double Bass',
                'Mila van Dijk — Drums',
            ],
            'social' => [
                'facebook' => '#',
                'instagram' => '#',
            ],
        ];

        View::render('/jazz/detail.php', $event['title'] . View::addSiteName(), PageIndex::JazzDetail->value, ['event' => $event]);
    }
    //endregion GET Requests


    //region POST Requests

    //endregion POST Requests
}