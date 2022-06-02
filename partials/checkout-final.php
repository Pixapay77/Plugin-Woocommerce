<!--<div class="blockUI blockOverlay" style="z-index: 1000; border: medium none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background: rgb(255, 255, 255) none repeat scroll 0% 0%; opacity: 0.6; cursor: default; position: fixed;">-->
<!--    <p style="position: relative;top: 45%;text-align: center;"><img src="--><?php //echo get_option('home')?><!--/wp-admin/images/spinner-2x.gif" alt="Carregando..." title="Carregando..." </p>-->
<!--</div>-->

<div id="checkout-final">
    <form name="checkout" method="post" class="checkout woocommerce-checkout "
        action="<?php echo get_option('home');?>/<?php echo basename(get_permalink());?>/" enctype="multipart/form-data">
        <div id="billing-form" style="display: none">
        <?php do_action( 'woocommerce_checkout_billing' ); ?>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-4">
                <div id="resumo-pedidos" class="seomidia_table">
                    <h2>RESUMO DA COMPRA</h2>
                    <?php do_action( 'seomidia_checkout_order_resume' ); ?>
                </div>
            </div>
            <div id="billing" class="col-12 col-sm-12 col-md-4 mb-2">
                <div class="seomidia_table form billing mb-2 active">
                    <h2>DADOS PESSOAIS</h2>
                    <div class="resumo-box" style="display: none">
                        <h3 id="nome"></h3>
                        <p id="cpf"></p>
                        <p id="celular"></p>
                        <p id="email"></p>
                    </div>
                    <div class="content-none">
                        <div class="form-group mb-2 nome-completo">
                            <label for="nome-completo">Nome</label>
                            <input type="text" class="form-control" id="nome-completo" value="<?php echo $Checkout->get_value('billing_first_name')?>" placeholder="ex. José Roberto">
                            <span class="error">*campo obrigatório</span>
                        </div>
                        <div class="form-group mb-2 sobre-nome">
                            <label for="nome-completo">Sobrenome</label>
                            <input type="text" class="form-control" id="sobrenome" value="<?php echo $Checkout->get_value('billing_last_name')?>" placeholder="ex. Silva">
                            <span class="error">*campo obrigatório</span>
                        </div>
                        <div class="form-group mb-2 email">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" value="<?php echo $Checkout->get_value('billing_email')?>" id="email" placeholder="ex. joseroberto@gmail.com">
                            <span class="error">*campo obrigatório</span>
                        </div>
                        <div class="form-group mb-2 cpf">
                            <label for="cpf">CPF</label>
                            <input type="text" class="form-control" id="cpf" value="<?php echo $Checkout->get_value('billing_cpf')?>"   placeholder="000.000.000-00">
                            <span class="error">*campo obrigatório</span>
                        </div>
                        <div class="input-group  my-3 whatsapp-celular">
                            <label class="sr-only" for="whatsapp-celular">Whatsapp/Celular</label>

                            <div class="input-group-prepend">
                                <div class="input-group-text">+ 55</div>
                            </div>
                            <input type="text" class="form-control" value="<?php echo $Checkout->get_value('billing_phone')?>" id="whatsapp-celular" placeholder="Whatsapp/Celular">
                            <span class="error my-1">*campo obrigatório</span>
                        </div>
                        <?php if ( !is_user_logged_in() ) :?>

                            <p class="form-row form-row-wide create-account">
                                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                                    <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $Checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
                                </label>
                            </p>

                        <?php endif; ?>
                        <a href="#" data-next="shipping" data-prev="billing" onclick="nextBlock(this)" class="btn next">CONTINUAR</a>
                    </div>
                </div>
                <div id="shipping"  class="seomidia_table form shipping">
                    <h2>ENDEREÇO DE ENTREGA</h2>
                    <!--                    <small>Prencha os dados pessoais </small>-->
                    <div class="resumo-box" style="display: none">
                        <h3 id="ruanumero"></h3>
                        <p id="bairroComplemento"></p>
                        <p id="cidadeUFcep"></p>
                    </div>
                    <div id="optionfrete" class="optionfrete" style="display: none">
                        <h4>Escolha uma forma envio</h4>
                        <?php do_action( 'seomidia_checkout_order_shipping_options' ); ?>
                    </div>
                    <div class="content-none">
                        <div class="row">
                            <div class="form-group mt-2 col-6 col-md-6 cep">
                                <label for="cep">CEP</label>
                                <input type="text" class="form-control" value="<?php echo $Checkout->get_value('billing_postcode')?>" id="cep" placeholder="Digite o seu CEP">
                                <span class="error">*campo obrigatório</span>
                            </div>
                            <div class="form-group mt-2 col-6 col-md-6 cidade-uf poscep" style="display: none">
                                <label for="cep">CIDADE/ESTADO</label>
                                <div class="dados" id="cityState"></div>
                                <input type="hidden" id="city">
                                <input type="hidden" id="state">
                            </div>
                        </div>
                        <div class="form-group mb-3 poscep address"  style="display: none">
                            <label for="address">Informe seu Endereço</label>
                            <input type="text" class="form-control" id="address" value="<?php echo $Checkout->get_value('billing_address_1')?>" placeholder="Informe o seu endereço de entrega">
                            <span class="error">*campo obrigatório</span>
                        </div>
                        <div class="row poscep"  style="display: none">
                            <div class="form-group col-md-4 numero">
                                <label for="numero">Número</label>
                                <input type="text" class="form-control" value="<?php echo $Checkout->get_value('billing_number')?>" id="numero" placeholder="0000">
                                <span class="error">*campo obrigatório</span>
                            </div>
                            <div class="form-group col-md-8 neighborhood">
                                <label for="bairro">Bairro</label>
                                <input type="text" class="form-control" value="<?php echo $Checkout->get_value('billing_neighborhood')?>" id="neighborhood" placeholder="Informe o seu bairro">
                                <span class="error">*campo obrigatório</span>
                            </div>
                        </div>
                        <div class="form-group my-3 poscep"  style="display: none">
                            <label for="complemento">Complemento (opcional)</label>
                            <input type="text" name="billing_address_2" class="form-control" id="complemento" placeholder="Apartamento, sala, loja, casa.....">
                        </div>
                        <a href="#" style="display: none" data-next="" data-prev="shipping" onclick="nextBlock(this)" class="btn next poscep">CONTINUAR</a>
                    </div>
                </div>
            </div>
            <div id="payment" class="col-12 col-sm-12 col-md-4">
                <div class="payment">
                    <div class="seomidia_table form">
                        <h2>PAGAMENTO</h2>
                        <div class="content-none">
                            <?php do_action( 'seomidia_checkout_order_payment' ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>