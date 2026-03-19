<h1>YUMMY</h1>

<?php

use app\models\editor\Page;
use app\views\components\EditorComp;

$thisPage = new Page(-1, "yummy");

EditorComp::CreateNewEditorComp($thisPage);