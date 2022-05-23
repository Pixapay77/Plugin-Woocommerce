<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>

    <?php wp_head()?>

    <style>
    .absolute-footer,
    html {
        background-color: #ffffff;
    }
    </style>

</head>

<body>

    <?php  require_once path_plugin . "partials/checkout-header.php"?>

    <div class="container mt-3 carrinho">
        <h2 class="cart-title">Meu Carrinho</h2>
        <?php  
        require_once path_plugin . "partials/checkout-cart.php";

        ?>
    </div>
    <div class="container mt-3 auth">
        <h2 class="cart-title">FINALIZAR COMPRA</h2>

        <form name="authUser" action="" method="post">
            <div class="busca-email col-md-7 m-auto my-5 text-center">
                <h3 class="my-3">Para finalizar a compra, informe seu e-mail. Rápido. Fácil. Seguro.</h3>
                <input style="height: 39px;" class="w-75"  type="email" name="email" placeholder="seu@email.com">
                <button type="submit" id="search-email" style="margin-left: -4px;">Buscar</button>
                <p><a href="/carrinho" class="btn btn-link">Voltar ao carrinho</a></p>
            </div>

            <div id="emailInfo"  class="col-md-8 m-auto" style="display:none">

                <div class="emailInfo">
                    <h3 data-i18n="clientProfileData.whyPreEmail">Usamos seu e-mail de forma 100% segura para:</h3>
                    <ul class="unstyled">
                        <li> <i class="icon-ok"></i> <span data-i18n="clientProfileData.bullet1WhyEmail">Identificar seu
                                perfil</span> </li>
                        <li> <i class="icon-ok"></i> <span data-i18n="clientProfileData.bullet2WhyEmail">Notificar sobre
                                o andamento do seu pedido</span> </li>
                        <li> <i class="icon-ok"></i> <span data-i18n="clientProfileData.bullet3WhyEmail">Gerenciar seu
                                histórico de compras</span> </li>
                        <li> <i class="icon-ok"></i> <span data-i18n="clientProfileData.bullet4WhyEmail">Acelerar o
                                preenchimento de suas informações</span> </li>
                    </ul>
                    <i class="icon-lock"></i>
                </div>

            </div>
    </div>
    <div class="auth-senha col-md-7 m-auto my-5 text-center">
        <p class="alert alert-warning"></p>
        <h3 class="my-3"><i class="fa fa-lock" aria-hidden="true"></i>
            Sua senha</h3>
        <input style="height: 39px;" class="w-50" type="password" name="senha" placeholder="Informe sua senha">
        <button style="color:#fff;margin-left: -4px;margin-right: 0px;margin-bottom: 16px;" type="submit">Logar</button>
        <div><a href="/carrinho" class="btn btn-link">Voltar ao carrinho</a></div>
    </div>
    </form>

    </div>


    <?php  //require_once path_plugin . "partials/checkout-footer.php"?>

    <?php wp_footer()?>
</body>

</html>