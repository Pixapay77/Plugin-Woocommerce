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

        public function __construct()
        {
            $this->id = 'pixapay'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Pixapay Gateway';
            $this->method_description = 'Permite integrar transaçõe da Pixapay com compras woocommerce.'; // will be displayed on the options page

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
            $this->Endpoint = $this->testmode ? 'https://sandbox.tecno.mobi/api/v1' : 'https://sandbox.tecno.mobi/api/v1';
            $this->webhook_url =  $this->testmode ? 'https://6607-2001-1284-f50f-caf9-e199-56a0-336c-2204.ngrok-free.app' : get_option('home');

            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            add_action( 'woocommerce_checkout_fields', array( $this, 'checkout_billing_fields' ), 9999 );
            add_action( 'woocommerce_before_thankyou', array($this,'dados_pagamento'));
            add_action( 'woocommerce_admin_order_data_after_billing_address', array($this,'order_cpf_backend'));
            add_action( 'woocommerce_api_webhook_pagamento', array( $this, 'webhook' ) );
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
                    'default'     => 'Descricao do servico de pagamento.',
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

            // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
            include_once plugin_dir_path(__FILE__) . 'public/template/methods.html';

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

                $this->OrderCreate($order,$order_id);

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
        public function OrderCreate($order)
        {

            if($this->TypePayment == "boleto_pixapay"){
                $description =  "Aguardando pagamento por Pixapay - boleto. \n";
            }elseif($this->TypePayment== "credit_card_pixapay"){
                $ref = get_post_meta($order_id,'_pixapay_pedido_referencia',true);
                $description = "Aguardando pagamento por Pixapay - Cartão de credito.";
            }elseif($this->TypePayment == "pix_pixapay"){
                $description = "Aguardando pagamento por Pixapay -  PIX.";
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
                            <h2>Pague com QRCODE</h2>
                            <img src="'.$_pixapay_fmp_link_qrcode.'" style="width: 252px;">
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
                $_pixapay_fmb_link_compartilhamento = get_post_meta($order_id,'_pixapay_fmb_link_compartilhamento',true);

               echo '
                    <h3 class="woocommerce-order-details__title">Detalhes de pagamento</h3>
                    <ul>
                        <li><strong>Metódo de pagamento:</strong>  '.$payment_method.'</li>
                        <li><strong>Ver Boleto:</strong>  <a target="_blanck" href="'.$_pixapay_fmb_link_compartilhamento.'" >Clique aqui!</a></li>
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
                "fmp_webhook"       =>  $this->webhook_url . '/wc-api/webhook_pagamento',
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
                "fmp_webhook"       =>  $this->webhook_url . '/wc-api/webhook_pagamento',

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
                "fmp_webhook"       =>  $this->webhook_url . '/wc-api/webhook_pagamento',
                "fmc_antecipar"     => $settings['credcartantecipacao'] == 'sim' ? 'S' : ''

           ];
        }


        public function expirepix()
        {
            $settings = get_option( 'woocommerce_pixapay_settings' );
            // Data no formato "Y-m-d H:i:s"
            $data = date('Y-m-d H:i:s');

            // Crie um objeto DateTime a partir da string da data
            $dataHora = new DateTime($data);

            // Adicione 5 minutos
            $dataHora->add(new DateInterval('PT'.$settings['pixexpire'].'M'));

            // Obtenha a nova data no mesmo formato
            $novaData = $dataHora->format('d/m/Y H:i:s');

            return $novaData;

        }

        public function expiracaoCreditCart()
        {
            $settings = get_option( 'woocommerce_pixapay_settings' );
            // Data no formato "Y-m-d H:i:s"
            $data = date('Y-m-d H:i:s');

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
            $data = date('Y-m-d H:i:s');

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
    }
}

add_filter( 'woocommerce_payment_gateways', 'WC_Pixapay_add_gateway_class' );
function WC_Pixapay_add_gateway_class( $gateways ) {
    $gateways[] = 'WC_Pixapay_Gateway'; // your class name is here
    return $gateways;
}



add_action('wp_enqueue_scripts', 'woocommerce_pixapay_style');
function woocommerce_pixapay_style()
{
    wp_enqueue_style('woocommerce_converteme_style',plugin_dir_url(__FILE__) . 'public/assets/css/woocommerce_converteme_style.css',[],false,false);
    wp_enqueue_script('woocommerce_converteme_script_imask','https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js',['jquery'],false,false);
    wp_enqueue_script('woocommerce_converteme_script_mercadopago','https://sdk.mercadopago.com/js/v2',['jquery'],false,false);
    wp_enqueue_script('woocommerce_converteme_script',plugin_dir_url(__FILE__) . 'public/assets/js/woocommerce_converteme_script.js',['jquery'],false,false);
}

if ( ! function_exists( 'woocommerce_order_review' ) ) {

    /**
     * Output the Order review table for the checkout.
     *
     * @param bool $deprecated Deprecated param.
     */
    function woocommerce_order_review( $deprecated = false ) {
        wc_get_template(
            'checkout/review-order.php',
            array(
                'checkout' => WC()->checkout(),
            )
        );
    }
}
