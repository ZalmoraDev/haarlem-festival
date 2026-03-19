<?php

use app\core\Escaper;

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/assets/icons/logo/logo-FFF.svg">
    <link rel="alternate icon" type="image/x-icon" href="/favicon.ico">

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/assets/styles/output.css">
    
    <title><?= Escaper::html($viewData['viewTitle'] ?? '') ?></title>
</head>

<?php
/** @var array $viewData /app/Core/View.php View::render */
require $viewData['viewFile'];
?>

</html>