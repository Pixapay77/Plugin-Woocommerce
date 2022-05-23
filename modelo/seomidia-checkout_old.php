<!DOCTYPE HTML>
<html lang="pt-BR">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title></title>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="/wp-content/plugins/seomidia-checkout-custom/assets/css/onda.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/3.2.1/css/font-awesome.min.css" />
    <link rel="stylesheet" href="/wp-content/plugins/seomidia-checkout-custom/assets/css/style.css" />
    <link rel="stylesheet" href="/wp-content/plugins/seomidia-checkout-custom/assets/css/redensign-checkout.css" />
    <link rel="stylesheet" href="/wp-content/plugins/seomidia-checkout-custom/assets/css/checkout6-custom.css" />
    <link rel="stylesheet"
        href="<?php echo get_option('home');?>/wp-content/plugins/woocommerce-pagseguro/assets/css/frontend/transparent-checkout.min.css" />
    <script type="text/javascript" src="/wp-includes/js/jquery/jquery.min.js"></script>
    <script type='text/javascript' id='seomidia-checkout-custom-script-js-extra'>
    /* <![CDATA[ */
    var ajax_object = {
        "ajax_url": window.location.origin + "/wp-admin\/admin-ajax.php",
        "outro_valor": "1234"
    };
    /* ]]> */
    </script>
    <script type="text/javascript"
        src="http://192.168.0.14:8005/wp-content/plugins/woocommerce-pagseguro/assets/js/frontend/transparent-checkout.min.js?ver=2.14.0"
        id="pagseguro-checkout-js"></script>

    <script>
    jQuery(document).ready(function() {

        jQuery('a#cart-to-orderform').click(function() {
            jQuery('.full-cart').removeClass('active');
            jQuery('.full-cart').addClass('inactive');
            jQuery('#cart-title').addClass('hide');
            jQuery('#cart-title').hide();

            jQuery('.orderform-template').removeClass('inactive');
            jQuery('#orderform-title').removeClass('hide');
            jQuery('#orderform-title').show();

            jQuery('.orderform-template').addClass('active');
        })

        jQuery('a#orderform-to-cart').click(function() {

            jQuery('.orderform-template').removeClass('active');
            jQuery('#orderform-title').removeClass('hide');
            jQuery('#orderform-title').show();

            jQuery('.full-cart').removeClass('inactive');
            jQuery('.full-cart').addClass('active');
            jQuery('#cart-title').addClass('hide');
            jQuery('#cart-title').hide();


            jQuery('.orderform-template').addClass('active');
        })

        jQuery('form[name="checkUser"]').submit(function(event) {
            event.preventDefault()

            var settings = {
                "url": ajax_object.ajax_url,
                "method": "POST",
                "type": "json",
                "data": {
                    "action": 'check_email',
                    "email": jQuery('#client-pre-email').val(),
                }
            };


            jQuery.ajax(settings).done(function(response) {
                console.log(response);
            });

            jQuery(this).hide();

        })

        jQuery('a.link-coupon-add').click(function() {
            jQuery('#cupon-form').show();
            jQuery('p.coupon-data').hide();
        })

        jQuery('a.shipping-calculator-button').click(function() {
            jQuery('.shipping-calculator-form').show();
            jQuery('ul#shipping_method').hide();
            jQuery(this).hide();
        })

        jQuery('.quantity a').click(function(event) {
            event.preventDefault()

            var operacao = jQuery(this).attr('class');
            var input = jQuery(this).attr('href');
            var value = jQuery('input.qty-' + input).val();
            var operador;
            var newQuanty;

            if (operacao == 'increment') {
                newQuanty = parseInt(value) + 1;
            }

            if (operacao == 'decrement') {
                newQuanty = parseInt(value) - 1;
            }

            if (newQuanty > 0) {
                jQuery('input.qty-' + input).val(newQuanty);
                updateCrt('input.qty-' + input);
            }
        })
    });


    jQuery(document).on('submit', 'form.woocommerce-shipping-calculator', function(e) {
        e.preventDefault();

        var settings = {
            "url": ajax_object.ajax_url,
            "method": "POST",
            "data": {
                "action": 'get_and_set_shipping',
                "calc_shipping_country": jQuery('select[name="calc_shipping_country"]').val(),
                "calc_shipping_state": jQuery('select[name="calc_shipping_state"]').val(),
                "calc_shipping_city": jQuery('input[name="calc_shipping_city"]').val(),
                "calc_shipping_postcode": jQuery('input[name="calc_shipping_postcode"]').val()
            }
        };

        jQuery.ajax(settings).done(function(response) {
            jQuery('body').load(window.location.origin + window.location.pathname);
            setTimeout(() => {
                jQuery('input[type="radio"]').removeAttr('checked');
                jQuery('input[value="1"]').attr('checked', '')
            }, 900);
        });

    })

    jQuery(document).on('change', 'input[type="radio"]:checked', function(e) {
        e.preventDefault();

        var requested_city = jQuery(this).val();
        var shipping_methods = {};

        // eslint-disable-next-line max-len
        jQuery(
                'select.shipping_method, :input[name^=shipping_method][type=radio]:checked, :input[name^=shipping_method][type=hidden]'
            )
            .each(function() {
                shipping_methods[jQuery(this).data('index')] = jQuery(this).val();
            });

        // block( jQuery( 'div.cart_totals' ) );

        var data = {
            action: 'get_and_set_shipping_rate',
            security: jQuery('#woocommerce-shipping-calculator-nonce').val(),
            shipping_method: shipping_methods
        };

        jQuery.ajax({
            type: 'post',
            url: ajax_object.ajax_url,
            data: data,
            dataType: 'html',
            success: function(response) {
                jQuery('#shipping-location').html(response);
                setTimeout(() => {
                    jQuery('a.shipping-calculator-button').click(function() {
                        jQuery('.shipping-calculator-form').show();
                        jQuery('ul#shipping_method').hide();
                        jQuery(this).hide();
                    })
                }, 1000);
            }
        });

    })

    jQuery(document).on('click', 'a.item-link-remove', function(e) {
        e.preventDefault();



        var product_id = jQuery(this).attr("data-product_id");
        cart_item_key = jQuery(this).attr("data-cart_item_key");


        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_object.ajax_url,
            data: {
                action: "product_remove",
                product_id: product_id,
                cart_item_key: cart_item_key
            },
            success: function(response) {
                jQuery('body').load(window.location.origin + window.location.pathname + '#cart')
            }
        });

    });

    function updateCrt(classe) {

        var item_hash = jQuery(classe).attr('data-cart_item_key');
        var item_quantity = jQuery(classe).val();
        var currentVal = parseFloat(item_quantity);

        function qty_cart() {

            jQuery.ajax({
                type: 'POST',
                url: ajax_object.ajax_url,
                data: {
                    action: 'qty_cart',
                    hash: item_hash,
                    quantity: currentVal
                },
                success: function(data) {
                    jQuery('body').load(window.location.origin + window.location.pathname + '#cart')
                }
            });

        }

        qty_cart();

    }
    </script>
</head>

<body class="shopfacil-vtexcommercestable-com-br loading" id="checkoutMainContainer">

    <!-- Header -->

    <header class="header-checkout">
        <div class="header-checkout__wrapper">
            <a href="/" class="header-checkout__logo" title="Shopfacil">
                <figure><img style="width: 22%;" src="/wp-content/uploads/2022/01/Logo-Videosul-20222.fw_.png.webp"
                        alt="Shopfacil.com" class="header-checkout__logo--img"></figure>
            </a>
        </div>
    </header>

    <!-- END Header -->
    <form name="checkout" method="post" class="checkout woocommerce-checkout "
        action="http://192.168.0.14:8005/finalizar-compra/" enctype="multipart/form-data" novalidate="novalidate">
        <div class="container container-main container-cart">
            <h1 class="hide" id="orderform-title" style="display: none;">Finalizar compra</h1>
            <h1 class="hide" id="orderform-sac-title" style="display: none;">Alterar dados do pedido</h1>
            <h1 id="cart-title" style="display: block;">Meu Carrinho</h1>
            <div class="checkout-container row-fluid cart-active">

                <?php wc_print_notices(); ?>

                <!-- CART -->
                <?php require_once "cart-itens.php";?>

                <div class="summary-template-holder">
                    <div class="row-fluid summary" style="display: block;">
                        <div class="cart-more-options span5">
                            <?php require_once "cupom.php";?>

                            <?php do_action( 'seomidia_woocommerce_cart_shipping' );?>
                            <!-- END ENTREGA-->
                        </div>
                        <div class="span5 totalizers summary-totalizers cart-totalizers pull-right">
                            <div>
                                <div class="accordion-group" style="display: block;">
                                    <div class="accordion-heading">
                                        <span class="accordion-toggle collapsed">Resumo do pedido</span>
                                    </div>
                                    <div class="accordion-body collapse in">
                                        <div class="accordion-inner">
                                            <?php require_once "cupom.php";?>
                                            <div id="shipping-location">
                                                <?php
                                            /**
                                             * Cart collaterals hook.
                                             *
                                             * @hooked woocommerce_cross_sell_display
                                             * @hooked woocommerce_cart_totals - 10
                                             */
                                            do_action( 'woocommerce_cart_collaterals' );
                                        ?>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix pull-right cart-links cart-links-bottom hide" style="display: block;">
                    <span class="link-choose-more-products-wrapper">
                        <a id="cart-choose-more-products" class="more link-choose-more-products" href="/">Escolher mais
                            produtos</a>
                    </span>
                    <span class="link-print-cart-wrapper hide" style="display: none;">
                        <a id="cart-print" onclick="window.print()" class="btn btn-large link-print-cart">Imprimir</a>
                    </span>
                    <span class="btn-place-order-wrapper">
                        <a href="#/orderform" target="_self" Id="cart-to-orderform">Fechar pedido</a>
                    </span>
                </div>
                <div class="extensions-checkout-buttons-container row-fluid" style="display: block;">
                    <div class="extensions-checkout-buttons span5 pull-right">
                        <div class="render-container" id="render-checkout-buttons">
                            <div class="unstyled">
                                <div class="btn-visa-container">
                                    <img class="v-button btn-visa" role="button"
                                        src="https://secure.checkout.visa.com/wallet-services-web/xo/button.png?width=248&amp;animation=true&amp;legacy=true&amp;svg=true"
                                        alt="Visa Checkout" tabindex="0" style="cursor: pointer;">
                                    <div class="hidden-ateclaaaaaaaaaphone">
                                        <a href="#" class="v-learn v-learn-default vc-tell-me-more vc-tell-me-more-link"
                                            aria-label="Learn more about Visa Checkout">Saiba mais</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /END CART -->

            <!-- ORDERFORM -->
            <?php require_once 'checkout.php' ?>
            <!-- /END ORDERFORM -->
        </div>
    </form>

    </div>
    </div>
    <footer class="footer-checkout">
        <div class="footer-checkout__wrapper">
            <address class="footer-checkout__address"></address>
        </div>
    </footer>
</body>

</html>