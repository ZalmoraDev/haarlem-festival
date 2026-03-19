<body class="fest-body">
<?php
use app\core\Csrf;
include_once __DIR__ . "/../../layouts/sidebar.php";
?>
<main class="fest-main justify-start">
    <h1>Payment test</h1>

<form action="/payment/checkout" method="post">
    <input type="hidden" name="csrf" value="<?= Csrf::getToken() ?>">
<button type="submit" 
            style="
                background-color: #007bff; 
                color: white; 
                border: 1px solid #0056b3; 
                padding: 8px 16px; 
                border-radius: 4px; 
                cursor: pointer;
                display: inline-block;
            ">
        checkout
    </button>
  </button>
</form>


</main>