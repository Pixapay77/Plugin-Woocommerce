<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar compra</title>

    <?php wp_head()?>

    <style>
    .absolute-footer,
    html {
        background-color: #ffffff;
    }
    </style>

</head>

<body>

    <?php  
    require_once path_plugin . "partials/checkout-header.php";
    $Checkout = new WC_Checkout();
    ?>

    <div class="container mt-3 finaliza">
        <?php  require_once path_plugin . "partials/checkout-final.php"?>
    </div>

    <?php  require_once path_plugin . "partials/checkout-footer.php"?>

    <?php wp_footer()?>
</body>

</html>