<?php

namespace app\models\editor;

final readonly class Page
{
    public function __construct(
        public int $pageID,
        public string $pageName
        ){

    }

}