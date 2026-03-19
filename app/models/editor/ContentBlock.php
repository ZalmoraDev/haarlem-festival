<?php

namespace app\models\editor;

final readonly class ContentBlock
{
    public function __construct(
        public int $contentID,
        public Page $page,
        public string $encodedString
    ){
    }
}