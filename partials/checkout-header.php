    <!-- Header -->
<?php 


$option = get_option('woocommerce_converteme_settings');


?>
    <header class="header-checkout">
        <div class="container">
            <div class="row">
                <div class="col-6 col-md-6 logo">
                    <img src="<?php echo $option['filelogo'];?>" alt="" title="" />
                </div>
                <div class="col-6 col-md-6">
                    <div class="block-ssl">
                        <img src="<?php echo plugins_url('woocommerce-plugin')?>/assets/img/Vector-footer.png" alt="" title="" />
                        <div class="ssl-text">
                            PAGAMENTO<br>
                            100% SEGURO
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- END Header -->

    <section class="bg-black">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center color-white p-2">
                    FRETE GRÁTIS NAS COMPRAS ACIMA DE R$ 200,00
                </div>
            </div>
        </div>
    </section>