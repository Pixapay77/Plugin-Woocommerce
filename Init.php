<?php

add_action( 'plugins_loaded', 'Pixapay_init' );
function Pixapay_init()
{

    class WC_Pixapay_Gateway extends WC_Payment_Gateway
    {
        public $TypePayment;
        public $Url;
        protected $Response;
        public $Body;
        public $Bodyd;
        public $Webhook;

        public function __construct()
        {
            $this->id = 'pixapay'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Pixapay Gateway';
            $this->method_description = 'Permite integrar transaçõe da Pixapay com compras woocommerce.'; // will be displayed on the options page

            $this->Webhook  = get_option('home');
            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            $this->idpk     = $this->testmode ? $this->get_option( 'test_client_id' ) : $this->get_option( 'client_id' );
            $this->clientsecret = $this->testmode ? $this->get_option( 'test_client_secret' ) : $this->get_option( 'client_secret' );
            $this->Endpoint = $this->testmode ? 'https://sandbox.tecno.mobi/api/v1' : 'https://api.tecno.mobi/api/v1';

            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_checkout_fields', array( $this, 'checkout_billing_fields' ), 9999 );
            add_action( 'woocommerce_before_thankyou', array($this,'dados_pagamento'));
            add_action( 'woocommerce_admin_order_data_after_billing_address', array($this,'order_cpf_backend'));
            add_action( 'woocommerce_api_webhook_pagamento', array( $this, 'webhook' ) );
            add_action('wp_head',array($this,'custonButton'));
        }

        public function init_form_fields()
        {
            
            $this->form_fields = array(
                'section_general' => array(
                    'type' => 'title',
                    'title' => 'Configurações Gerais',
                ),

                'enabled' => array(
                    'title'       => 'Ativar/Desativar',
                    'label'       => 'Ativar Pixapay Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Titulo',
                    'type'        => 'text',
                    'description' => 'Nome do metodo de pagamento.',
                    'default'     => 'Pixapay',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Descrição',
                    'type'        => 'textarea',
                    'description' => 'Descricao do servico de pagamento.',
                    'default'     => 'PIXAPAY TECNOLOGIA DE PAGAMENTOS',
                ),

                'typepaymts' => array(
                    'title'       => 'Modos dados',
                    'type'        => 'multiselect',
                    'options'     => array(
                        'pix' => 'Pix',
                        'boleto' => 'Boleto',
                        'creditcart' => 'Cartão de Credito'
                    ),
                    'default'     => 'boleto',
                    'description' => 'Selecione os metodos desejado.',
                ),

                'colorButton' => array(
                    'title'       => 'Botão finalizar',
                    'type'        => 'color',
                    'default'     => '#fed700',
                    'description' => 'Selecione a cor de sua preferecia.',
                ),

                'colorButtonfont' => array(
                    'title'       => 'Botão finalizar fonte',
                    'type'        => 'color',
                    'default'     => '#000000',
                    'description' => 'Selecione a cor de sua preferecia.',
                ),

                'section_credit_card' => array(
                    'type' => 'title',
                    'title' => 'Configurações Cartão de Credito',
                ),

                'installments' => array(
                    'title'       => 'Quantidade de Parcelas',
                    'type'        => 'select',
                    'options'     => array(
                        '1' => '1 parcela',
                        '2' => '2 parcelas',
                        '3' => '3 parcelas',
                        '4' => '4 parcelas',
                        '5' => '5 parcelas',
                        '6' => '6 parcelas',
                        '7' => '7 parcelas',
                        '8' => '8 parcelas',
                        '9' => '9 parcelas',
                        '10'=> '10 parcelas',
                        '11'=> '11 parcelas',
                        '12'=> '12 parcelas',
                    ),
                    'default'     => '1',
                    'description' => 'Selecione o número de parcelas desejado.',
                ),

                'credcartdiasvecimto' => array(
                    'title'       => 'Tempo expiração do Cobrança',
                    'type'        => 'number',
                    'description' => 'Tempo de validade da Cobrança após ser criado! Tempo deve ser informado  minutos.',
                    'default'     => 0,
                    'desc_tip'    => true,
                ),

                'credcartantecipacao' => array(
                    'title'       => 'Antecipação',
                    'type'        => 'select',
                    'options'     => array(
                        'sim' => 'Sim',
                        'nao' => 'Não',
                    ),
                    'default'     => 'nao',
                    'description' => 'Caso queira antipar todas as cobranças dcartão dcredito',
                ),

                'section_apix' => array(
                    'type' => 'title',
                    'title' => 'Configurações pix',
                ),

                'pixexpire' => array(
                    'title'       => 'Tempo expiração do Pix',
                    'type'        => 'number',
                    'description' => 'Tempo de validade do pix após ser criado! Tempo deve ser informado  minutos.',
                    'default'     => 0,
                    'desc_tip'    => true,
                ),

                'section_boleto' => array(
                    'type' => 'title',
                    'title' => 'Configurações Boleto',
                ),

                'boletoinstrucao' => array(
                    'title'       => 'Instrução para pagamento',
                    'type'        => 'text',
                    'description' => 'Deixe aqui uma msag para seus boletos',
                    'default'     => 0,
                    'desc_tip'    => true,
                ),

                'boletodiasvecimto' => array(
                    'title'       => 'Vencimento em dias',
                    'type'        => 'number',
                    'description' => 'Dias de validade após a criação do Boleto',
                    'default'     => 0,
                    'desc_tip'    => true,
                ),

                'boletotipodesconto' => array(
                    'title'       => 'Tipo desconto',
                    'type'        => 'select',
                    'options'     => array(
                        '%' => '%',
                    ),
                    'default'     => '1',
                    'description' => 'Selecione o tipo desconto.',
                ),

                'boletodesconto' => array(
                    'title'       => 'Deconto%',
                    'type'        => 'number',
                    'description' => 'Desconto no pagamento Boleto',
                    'default'     => 0,
                    'desc_tip'    => true,
                ),

                'boletomulta' => array(
                    'title'       => 'Multa%',
                    'type'        => 'number',
                    'description' => 'Multa no atraso do pagamento Boleto',
                    'default'     => 0,
                    'desc_tip'    => true,
                ),

                'boletojuros' => array(
                    'title'       => 'Juros%',
                    'type'        => 'number',
                    'description' => 'Juros no atraso do pagamento Boleto',
                    'default'     => 0,
                    'desc_tip'    => true,
                ),
                'section_webhook' => array(
                    'type' => 'title',
                    'title' => "Webhook : " . $this->Webhook . '/wc-api/webhook_pagamento/',
                ),


                'section_auth' => array(
                    'type' => 'title',
                    'title' => 'Configurações Autenticação e ambiente',
                ),

                'testmode' => array(
                    'title'       => 'Modo de teste',
                    'label'       => 'Ativar modo teste',
                    'type'        => 'checkbox',
                    'description' => 'Ativar o modo teste para homologacao.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'test_client_id' => array(
                    'title'       => 'Empresa idpk (teste)',
                    'type'        => 'text'
                ),
                'test_client_secret' => array(
                    'title'       => 'Token (teste)',
                    'type'        => 'text',
                ),
                'client_id' => array(
                    'title'       => 'Empresa idpk',
                    'type'        => 'text'
                ),
                'client_secret' => array(
                    'title'       => 'Token',
                    'type'        => 'text'
                )
            );


        }
        public function payment_fields()
        {

            $settings = get_option( 'woocommerce_pixapay_settings' );


            // ok, let's display some description before the payment form
            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ( $this->testmode ) {
                    $this->description .= '';
                    $this->description  = trim( $this->description );
                }
                // display the description with <p> tags etc.
                echo wpautop( wp_kses_post( $this->description ) );
            }

            // I will echo() the form, but you can close PHP tags and print it directly in HTML
            echo '<div id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

            // Add this action hook if you want your custom payment gateway to support it
            do_action( 'woocommerce_credit_card_form_start', $this->id );

            $Pix = '
                    <fieldset class="pix">
                    <p data-type="pix"  onclick="open_pix(this)"><span>PIX</span></p>
                    <div class="content pix" style="width: 100%; margin: 0px auto;padding: 0px 0;">
                        <ul>
                            <li class="load">
                                <div>
                                    Abra o aplicativo do seu banco de preferência
                                </div>
                            </li>
                            <li class="qrcode">
                                <div>
                                    Selecione a opção pagar com Pix
                                </div>
                            </li>
                            <li class="seguranca">
                                <div>
                                    Leia o QR code ou copie o código e cole no campo de pagamento
                                </div>
                            </li>
                        </ul>
                        <button class="btn finalizar" type="submit">FINALIZAR A COMPRA</button>
                    </div>
                </fieldset>
            ';

            $boleto = '
                <fieldset class="boleto">
                    <p data-type="boleto"  onclick="open_boleto(this)"><span>BOLETO BANCÁRIO</span></p>
                    <div class="content boleto" style="width: 100%;margin: 0 auto;">
                        <ul>
                            <li class="codebar">
                            <div> Você pode pagar o boleto pelo código de barras ou</div>
                            </li>
                            <li class="print">
                                <div>Pode Imprimir e pagar o boleto</div>
                            </li>
                            <li class="calendar">
                                <div>Prazo para o boleto confirmar o pagamento é 3 dias úteis</div>
                            </li>
                        </ul>
                        <button class="btn finalizar" type="submit">FINALIZAR A COMPRA</button>

                    </div>
                </fieldset>
            ';

            $credit_card = '
                    <fieldset class="cred_card">
                    <p class="title" data-type="cred_card" onclick="open_cred_card(this)">
                        <span>CARTÃO DE CRÉDITO</span>
                    </p>
                        <span class="brands"></span>
                        <div class="center-container">
                            <div class="content cred_card" style="max-width: 382px;margin-bottom: 50px;">
                                <div class="form-container">
                                    <div class="field-container">
                                        <label for="cardnumber">Numero do Cartão</label><span style="display: none" id="generatecard"></span>
                                        <input id="cardnumber" type="text" name="cardnumber" pattern="[0-9]*" inputmode="numeric" placeholder="Digite somente números">
                                    </div>
                                    <div class="field-container">
                                        <label for="name">Nome igual consta em seu cartão</label>
                                        <input id="name" name="cartName" maxlength="20" type="text" placeholder="Ex. Maria José Castro">
                                    </div>
                                    <div class="field-container">
                                        <label for="expirationdate">Validade (Mês/Ano)</label>
                                        <input id="expirationdate" name="expirationdate" type="text" pattern="[0-9]*" inputmode="numeric" placeholder="MM/AA">
                                    </div>
                                    <div class="field-container">
                                        <label for="securitycode">Código de segurança</label>
                                        <input id="securitycode" name="securitycode" type="text" pattern="[0-9]*" inputmode="numeric" placeholder="CVC">
                                    </div>
                                    <div class="field-container">
                                        <label for="cpf">CPF do títular</label>
                                        <input id="cpf" name="cpf" type="text" pattern="[0-9]*" inputmode="numeric" placeholder="000.000.000-00">
                                    </div>
                                    <div class="field-container">
                                        <label for="securitycode">Número de Parcelas</label>
                                        <select name="installments"  style="width: 100%;" onclick="get_installments(this);">
                                            <option selected value="1">1x</option>
                                        </select>
                                    </div>
                                    <button class="btn finalizar" type="submit">FINALIZAR A COMPRA</button>
                                </div>
                            </div>
                        </div>
                
                </fieldset>
            ';

            $html = '';
            foreach ($settings['typepaymts'] as $key => $typepaymts) {
               switch ($typepaymts) {
                case 'pix':
                    $html .= $Pix;
                break;
                case 'boleto':
                    $html .= $boleto;
                break;
                case 'creditcart':
                    $html .= $credit_card;
                break;
                                       
                }
            }


            $box = '
                <img src="https://14-imagem-777.s3.sa-east-1.amazonaws.com/PixapayLOGO60x60.png" alt="">
                <div id="woocommercerConverteme">'.$html.'</div>
                <input type="hidden" name="payment_type" value="credit_card_pixapay">
            ';

            echo $box;

            $html = '';

            do_action( 'woocommerce_credit_card_form_end', $this->id );

            echo '<div class="clear"></div></div>';
        }
        public function payment_scripts()
        {
            // we need JavaScript to process a token only on cart/checkout pages, right?
            if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
                return;
            }

            // if our payment gateway is disabled, we do not have to enqueue JS too
            if ( 'no' === $this->enabled ) {
                return;
            }

            // no reason to enqueue JavaScript if API keys are not set
            if ( empty( $this->clientid ) || empty( $this->clientsecret ) ) {
                return;
            }

            // do not work with card detailes without SSL unless your website is in a test mode
            if ( ! $this->testmode && ! is_ssl() ) {
                return;
            }
            // integracao ----------------------

        }
        public function validate_fields()
        {
            if( empty( $_POST[ 'billing_neighborhood' ]) ) {
                wc_add_notice(  'Bairro é obrigatório', 'error' );
                return false;
            }
            if( empty( $_POST[ 'billing_number' ]) ) {
                wc_add_notice(  'Numero é obrigatório!', 'error' );
                return false;
            }
            if( empty( $_POST[ 'billing_cpf' ]) ) {
                wc_add_notice(  'CPF é obrigatório!', 'error' );
                return false;
            }
            return true;
        }
        function order_cpf_backend($order){
            $order = wc_get_order( $order->ID );
            $order_data = $order->get_data(); // The Order data
            $user_id = $order_data['customer_id'];
            $cpf = get_user_meta($user_id,'billing_cpf',true);
            echo "<p><strong>CPF:</strong> {$cpf}</p>";
        }
        public function process_payment( $order_id )
        {
            global $woocommerce;

            extract($_POST);


            // we need it to get any order detailes
            $order = wc_get_order( $order_id );
            $order_data = $order->get_data(); // The Order data
            $user_id = $order_data['customer_id'];
            
            update_user_meta( $user_id, 'billing_cpf', $_POST['billing_cpf'] );

            /*
              * Array with parameters for API interaction
             */

             $args = $this->Payload($order_data,$_POST);

            /*
             * Your API interaction could be built with wp_remote_post()
              */

              $this->Response = wp_remote_post( $this->Url, array(
                'body' => json_encode($args),
                'headers' => array(
                    'AUTHORIZATION' => 'Basic ' . $this->clientsecret
                ),
                'timeout' => 15
            ));

            // FAZ O PAGAMTO da cobrança caso seja cartao


            try {
                $this->Body = json_decode($this->Response["body"])->registros[0];
                if(is_null($this->Body)){
                    $this->Body =  json_decode($this->Response["body"]);
                }

            } catch (\Throwable $th) {
                var_dump($this->Response);exit();
                $msg = $th->getMessage();
                wc_add_notice(   $msg , 'error' );
                wp_die();
            }



            if( $this->Response["response"]['code'] == 200) {

                // Empty cart
                 $woocommerce->cart->empty_cart();

                // Redirect to the thank you page

                $this->TypePayment = $payment_type;

                update_post_meta($order_id,'_pixapay_payment_type',$this->TypePayment);

                if($this->TypePayment == 'boleto_pixapay'){
                    update_post_meta($order_id,'_pixapay_fmb_link_compartilhamento',$this->Body->fmb_link_compartilhamento);
                    update_post_meta($order_id,'_pixapay_fmb_linha_digitavel',$this->Body->fmb_linha_digitavel);
                    update_post_meta($order_id,'_pixapay_pedido_referencia',$this->Body->fmb_idpk);
                    update_post_meta($order_id,'_pixapay_fmb_link_url',$this->Body->fmb_link_url);
                }elseif($this->TypePayment == 'pix_pixapay'){
                    update_post_meta($order_id,'_pixapay_fmp_link_qrcode',$this->Body->fmp_link_qrcode);
                    update_post_meta($order_id,'_pixapay_fmp_link_compartilhamento',$this->Body->fmp_link_compartilhamento);
                    update_post_meta($order_id,'_pixapay_fmp_hash',$this->Body->fmp_hash);
                    update_post_meta($order_id,'_pixapay_pedido_referencia',$this->Body->fmp_idpk);
                }elseif($this->TypePayment == 'credit_card_pixapay'){
                    update_post_meta($order_id,'_pixapay_pedido_referencia',$this->Body->fmc_idpk);
                }

                if($payment_type == 'credit_card_pixapay'){
                    $this->CreditCartPagar($order_data,$_POST);
                    update_post_meta($order_id,'_pixapay_cartao_token',$this->Bodyd->cartao_token);
                 }

                 $this->OrderCreate($order,$order_id);


                 return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url( $order )
                );
            }elseif($body["status"] == 'erro'){
                $msg = $body["mensagem"];
                wc_add_notice(  $body['status'] .': '. $msg , 'error' );
            } else {
                $msg = $body["mensagem"];
                wc_add_notice(  $body['status'] .': '. $msg , 'error' );
            }
        }
        
        protected function get_response()
        {
            return json_decode($this->Response["body"]);
        }
        public function Payload($order,$dados)
        {
            extract($dados);

            

            switch ($payment_type) {
                case 'credit_card_pixapay':
                    return $this->PayloadCreditCardCreate($order,$dados);
                break;
                case 'pix_pixapay':
                    return $this->PayloadPix($order,$dados);
                break;
                case 'boleto_pixapay':
                    return $this->PayloadBoleto($order,$dados);
                break;
            }
        }
        public function  Datedue()
        {
            $novadata = explode("/",date('d/m/Y'));
            $dia = $novadata[0];
            $mes = $novadata[1];
            $ano = $novadata[2];

            return date('Y-m-d',mktime(0,0,0,$mes,$dia+5,$ano));
        }
        public function checkout_billing_fields( $fields )
        {
            if ( !isset(  $fields['billing']['billing_neighborhood'] ) ) {
                $fields['billing']['billing_neighborhood'] = array(
                    'label' => __('Bairro', 'woocommerce'), // Add custom field label
                    'required' => true, // if field is required or not
                    'clear' => false, // add clear or not
                    'type' => 'text', // add field type
                    'class' => array('input-text'),   // add class name
                    'priority' => 60, // Priority sorting option
                );
            }

            if ( !isset(  $fields['billing']['billing_number'] ) ) {
                $fields['billing']['billing_number'] = array(
                    'label' => __('Numero', 'woocommerce'), // Add custom field label
                    'required' => true, // if field is required or not
                    'clear' => false, // add clear or not
                    'type' => 'text', // add field type
                    'class' => array('input-text'),   // add class name
                    'priority' => 60, // Priority sorting option
                );
            }

            if ( !isset(  $fields['billing']['billing_cpf'] ) ) {
                $fields['billing']['billing_cpf'] = array(
                    'label' => __('CPF', 'woocommerce'), // Add custom field label
                    'required' => true, // if field is required or not
                    'clear' => false, // add clear or not
                    'type' => 'text', // add field type
                    'class' => array('input-text'),   // add class name
                    'priority' => 25, // Priority sorting option
                );
            }


            return $fields;
        }
        public function webhook()
        {
            $data = json_decode(file_get_contents('php://input'), true);
            $this->OrderReturn((object) $data);
            exit();
        }
        public function OrderCreate($order,$order_id)
        {
            $ref = get_post_meta($order_id,'_pixapay_pedido_referencia',true);


            if($this->TypePayment == "boleto_pixapay"){
                $description =  "Aguardando pagamento por Pixapay - boleto Código: #{$ref}. \n";
            }elseif($this->TypePayment== "credit_card_pixapay"){
                $description = "Aguardando pagamento por Pixapay - Cartão de credito Código: #{$ref}.";
            }elseif($this->TypePayment == "pix_pixapay"){
                $description = "Aguardando pagamento por Pixapay -  PIX Código: #{$ref}.";
            }

            // some notes to customer (replace true with false to make it private)
            $order->add_order_note(  $description, true );
        }
        public function OrderReturn($data)
        {
            switch ($data->tipo_cobranca) {
                case 'pix':
                    $this->LiquidacaoPix($data);
                break;
                case 'bolepix':
                    $this->LiquidacaoBoleto($data);
                break;
                case 'cartao':
                    $this->LiquidacaoCreditCart($data);
                break;
            }
        }

        public function dados_pagamento($order_id) 
        {

            $payment_method = $this->get_paymentMethod(get_post_meta($order_id,'_pixapay_payment_type',true));

            if($payment_method == 'PIX'){
                $pixapay_fmp_hash = get_post_meta($order_id,'_pixapay_fmp_hash',true);
                $_pixapay_fmp_link_qrcode = get_post_meta($order_id,'_pixapay_fmp_link_qrcode',true);
    
                echo '
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const openModalBtn = document.getElementById("openModalBtn");
                        const modal = document.getElementById("myModal");
                        const closeModal = document.getElementById("closeModal");
                    
                        openModalBtn.addEventListener("click", function() {
                        modal.style.display = "block";
                        });
                    
                        closeModal.addEventListener("click", function() {
                        modal.style.display = "none";
                        });
                    
                        window.addEventListener("click", function(event) {
                        if (event.target === modal) {
                            modal.style.display = "none";
                        }
                        });
                    });              
                </script>
                <style>
                    /* Estilo para esconder a janela modal por padrão */
                    .modal {
                    display: none;
                    position: fixed;
                    z-index: 1;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.7);
                    }
    
                    /* Estilo para o conteúdo da janela modal */
                    .modal-content {
                    background-color: #fff;
                    margin: 15% auto;
                    padding: 20px;
                    border: 1px solid #888;
                    width: 50%;
                    text-align: center;
                    }
    
                    /* Estilo para o botão de fechar a janela modal */
                    .close {
                    color: #888;
                    float: right;
                    font-size: 24px;
                    cursor: pointer;
                    }
    
                    .close:hover {
                    color: #000;
                    }
                </style>
                    <div id="myModal" class="modal">
                        <div class="modal-content">
                            <span class="close" id="closeModal">&times;</span>
                            <h2 style="font-size: 30px;">Pague com QRCODE</h2>
                            <center><img src="'.$_pixapay_fmp_link_qrcode.'" style="width: 252px;"></center>
                        </div>
                    </div>
                    <h3 class="woocommerce-order-details__title">Detalhes de pagamento</h3>
                    <ul>
                        <li><strong>Metódo de pagamento:</strong>  '.$payment_method.'</li>
                        <li><strong>Copie e cole:</strong> <input type="text" name="copiecole" value="'.$pixapay_fmp_hash.'">  <a href="#" onclick="copy();return false;">Clique aqui!</a></li>
                        <li><strong>Ler QRCODE:</strong>  <a href="#" id="openModalBtn" onclick="return false;" data-action="qrcode">Clique aqui!</a></li>
                    </ul>
                    <br>
                    <hr>
                    <br>
                ';
            }elseif ($payment_method == 'BOLETO') {
                $_pixapay_fmb_link_url = get_post_meta($order_id,'_pixapay_fmb_link_url',true);

               echo '
                    <h3 class="woocommerce-order-details__title">Detalhes de pagamento</h3>
                    <ul>
                        <li><strong>Metódo de pagamento:</strong>  '.$payment_method.'</li>
                        <li><strong>Ver Boleto:</strong>  <a target="_blanck" href="'.$_pixapay_fmb_link_url.'" >Clique aqui!</a></li>
                    </ul>
                    <br>
                    <hr>
                    <br>
                ';
            }

        }

        public function get_paymentMethod($data){
            switch ($data) {
                case 'pix_pixapay':
                    return 'PIX';
                break;
                case 'boleto_pixapay':
                    return 'BOLETO';
                break;
            }
        }

        public function PayloadPix($order,$dados)
        {
            extract($dados);

            $settings = get_option( 'woocommerce_pixapay_settings' );
            $this->Url = $this->Endpoint . '/Pix/Instantaneo?empresa_idpk=' . $this->idpk;

            $expirepix = $this->expirepix();

            return [
                "fmp_descricao"     => 'WC Pedido - ' . $billing_first_name .' '. $billing_last_name,        
                "fmp_valor"         => $order['total'],
                "fmp_data_expicarao"=> $expirepix,
                "fmp_webhook"       =>  $this->Webhook . '/wc-api/webhook_pagamento/',
                "fmp_idpk"          => (string) $order["id"]
            ];

        }

        public function PayloadBoleto($order,$dados)
        {
            extract($dados);
            $settings = get_option( 'woocommerce_pixapay_settings' );

            $this->Url = $this->Endpoint . '/Boleto/Inserir?empresa_idpk=' . $this->idpk;
            
            $vencimento = $this->vencimento();


            return [
                "fmb_sacado_nome"                   => $billing_first_name .' '. $billing_last_name,
                "fmb_sacado_cnpj_cpf"               => $billing_cpf,    
                "fmb_sacado_endereco"               => $billing_address_1,
                "fmb_sacado_endereco_numero"        => $billing_number,
                "fmb_sacado_endereco_complemento"   => $billing_address_2,
                "fmb_sacado_bairro"                 => $billing_neighborhood,
                "fmb_sacado_cep"                    => $billing_postcode,
                "fmb_sacado_cidade"                 => $billing_city,
                "fmb_sacado_uf"                     => $billing_state,    
                "fmb_valor"                         => (double) $order['total'],
                "fmb_vencimento"                    => $vencimento,        
                "fmb_referencia"                    => "VENDA-" . $order["id"],
                "fmb_instrucao"                     => $settings['boletoinstrucao'],
                "fmb_desconto_tipo"                 => $settings['boletotipodesconto'],
                "fmb_desconto_valor"                => $settings['boletodesconto'],
                "fmb_juros_mensal"                  => $settings['boletojuros'],
                "fmb_multa"                         => $settings['boletomulta'],
                "fmb_idpk"                          => (string) $order["id"],
                "fmb_webhook"       =>  $this->Webhook . '/wc-api/webhook_pagamento/',

            ];

        }

        public function PayloadCreditCardCreate($order,$dados)
        {
            extract($dados);
            $settings = get_option( 'woocommerce_pixapay_settings' );

            $this->Url = $this->Endpoint . '/Cartao/Inserir?empresa_idpk=' . $this->idpk;
            
            $expiracao = $this->expiracaoCreditCart();

           return [
                "fmc_descricao"=> "Venda". $order["id"],
                "fmc_cliente_nome"=> $billing_first_name .' '. $billing_last_name,
                "fmc_cliente_documento"=> $billing_cpf,        
                "fmc_qtde_parcelas"=> $installments,
                "fmc_valor"=> (double) $order['total'],
                "fmc_data_expiracao"=> $expiracao,
                "fmc_webhook"       =>  $this->Webhook . '/wc-api/webhook_pagamento/',
                "fmc_antecipar"     => $settings['credcartantecipacao'] == 'sim' ? 'S' : ''

           ];
        }


        public function expirepix()
        {
            $settings = get_option( 'woocommerce_pixapay_settings' );

            // Data no formato "Y-m-d H:i:s"
            $data = date('Y/m/d');

            // Crie um objeto DateTime a partir da string da data
            $dataHora = new DateTime($data);

            $expirepix = $settings['pixexpire'] == '' ? 60 : $settings['pixexpire'];

            // Adicione 5 minutos
            $dataHora->add(new DateInterval('PT'.$expirepix.'M'));

            // Obtenha a nova data no mesmo formato
            $novaData = $dataHora->format('d/m/Y H:i:s');

            return $novaData;

        }

        public function expiracaoCreditCart()
        {
            $settings = get_option( 'woocommerce_pixapay_settings' );
            // Data no formato "Y-m-d H:i:s"
            $data = date('Y/m/d');

            // Crie um objeto DateTime a partir da string da data
            $dataHora = new DateTime($data);

            // Adicione 5 minutos
            $dataHora->add(new DateInterval('P'.$settings['credcartdiasvecimto'].'D'));

            // Obtenha a nova data no mesmo formato
            $novaData = $dataHora->format('d/m/Y');

            return $novaData;

        }

        public function vencimento()
        {
            $settings = get_option( 'woocommerce_pixapay_settings' );
            // Data no formato "Y-m-d H:i:s"
            $data = date('Y/m/d');

            // Crie um objeto DateTime a partir da string da data
            $dataHora = new DateTime($data);

            // Adicione 5 minutos
            $dataHora->add(new DateInterval('P'.$settings['boletodiasvecimto'].'D'));

            // Obtenha a nova data no mesmo formato
            $novaData = $dataHora->format('d/m/Y');
            return $novaData;
        }

        public function LiquidacaoPix($data)
        {
            global $wpdb;

            ///  priso dinir a logica do starus do retorno

            $fmc_identificador = $data->pix["fmp_idpk"];
            $fmp_status = $data->pix["fmp_status"];

            if($fmp_status == 'Liquidado'){
                $sql = "SELECT post_id FROM {$wpdb->postmeta} where meta_key = '_pixapay_pedido_referencia' AND meta_value = '{$fmc_identificador}'";

                $result = $wpdb->get_results($sql);
    
                $order_id = $result[0]->post_id;
    
                $order = wc_get_order( $order_id );
    
                $order->payment_complete();
                $order->reduce_order_stock();
                // some notes to customer (replace true with false to make it private)
                $order->add_order_note( "Pagamento confirmado por Pixapay.\n Código #" . $fmc_identificador , true );
            }
        }

        public function LiquidacaoBoleto($data)
        {
            global $wpdb;

            ///  priso dinir a logica do starus do retorno

            $fmc_identificador = $data->pix["boleto"]["fmb_idpk"];
            $fmb_status = $data->pix["boleto"]["fmb_status"];
            if($fmb_status == 'Liquidado'){
                $sql = "SELECT post_id FROM {$wpdb->postmeta} where meta_key = '_pixapay_pedido_referencia' AND meta_value = '{$fmc_identificador}'";

                $result = $wpdb->get_results($sql);
    
                $order_id = $result[0]->post_id;
    
                $order = wc_get_order( $order_id );
    
                $order->payment_complete();
                $order->reduce_order_stock();
                // some notes to customer (replace true with false to make it private)
                $order->add_order_note( "Pagamento confirmado por Pixapay.\n Código #" . $fmc_identificador , true );
    
            }
        }


        public function LiquidacaoCreditCart($data)
        {
            global $wpdb;

                        ///  priso dinir a logica do starus do retorno

            $fmc_identificador = $data->cartao["fmc_idpk"];
            $fmc_status = $data->cartao["fmc_status"];

            if($fmc_status == 'Confirmado'){
                $sql = "SELECT post_id FROM {$wpdb->postmeta} where meta_key = '_pixapay_pedido_referencia' AND meta_value = '{$fmc_identificador}'";

                $result = $wpdb->get_results($sql);

                if(count($result) > 0){
                    $order_id = $result[0]->post_id;

                    $order = wc_get_order( $order_id );
        
                    $order->payment_complete();
                    $order->reduce_order_stock();
                    // some notes to customer (replace true with false to make it private)
                    $order->add_order_note( "Pagamento confirmado por Pixapay.\n Código #" . $fmc_identificador, true );
                }else{
                    $order->add_order_note( "erro " . print_r($result), true );
                }
            }
        }



        public function CreditCartPagar($order_data,$POST)
        {

            $args = $this->PayloadCreditCartPagar($order_data,$POST);
            // var_dump($this->Response);exit;
            $fmc_idpk = $this->Body->fmc_idpk;
             $Response = wp_remote_request( $this->Endpoint . '/Cartao/Pagar/'.$fmc_idpk.'?empresa_idpk=' . $this->idpk, array(
                'body' => json_encode($args),
                'headers' => array(
                    'AUTHORIZATION' => 'Basic ' . $this->clientsecret
                ),
                'method'  => 'PUT',
                'timeout' => 15
            ));
            $this->Bodyd = json_decode($Response["body"]);


        }

        public function PayloadCreditCartPagar($order_data,$POST)
        {
            extract($POST);

            $settings = get_option( 'woocommerce_pixapay_settings' );

            $this->Url = $this->Endpoint . '/Cartao/Inserir?empresa_idpk' . $this->idpk;

            $validate = explode('/',$expirationdate);
            
           return [
                'pagador' => [
                    'nome' => $billing_first_name .' '. $billing_last_name,
                    'documento' => $billing_cpf
                ],
                "cartao" =>[
                    "titular_nome"      => $cartName,
                    "titular_documento" => $cpf,
                    "numero"            => $cardnumber,
                    "cvc"               => $securitycode,
                    "validade_mes"      => $validate[0],
                    "validade_ano"      => $validate[1]
                ]
            ];
        }

        public function custonButton()
        {
            $settings = get_option( 'woocommerce_pixapay_settings' );

            echo "
            
                <style>
                #woocommercerConverteme button.finalizar, #woocommercerConverteme a.finalizar {
                    box-sizing: border-box;
                    background: ".$settings['colorButton'].";
                    border-radius: 4px;
                    padding: 10px 30px;
                    position: absolute;
                    color: ".$settings['colorButtonfont'].";
                    font-family: unset;
                    font-style: unset;
                    font-weight: unset;
                    font-size: unset;
                    width: 90%;
                    bottom: -20px;
                    left: 5%;
                    right: 5%;
                }
                </style>
            ";
        }
    }
}

