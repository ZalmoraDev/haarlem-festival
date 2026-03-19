<?php
/**
 * a editor component that retrieves data from the database and allows an administrator to edit it
 */
namespace app\views\components;

use app\models\editor\ContentBlock;
use app\models\editor\Page;

class EditorComp
{
    private ContentBlock $contentBlock;

    private function __construct(ContentBlock $block)
    {
        $contentBlock = $block;
        echo($contentBlock->encodedString);
    }

    public Static function CreateNewEditorComp(Page $page): EditorComp{
        $encodedString = "<div class='absolute top-100 left-150'>hello world</div>";

        $contentBlock = new ContentBlock(-1, $page, $encodedString);
        return new editorComp($contentBlock);
    }

    public static function EditorCompFromDB(ContentBlock $block): EditorComp
    {
        return new EditorComp($block);
    }
}