<?php
    /*
    Plugin Name: Converteme Checkout
    Plugin URI: https://converte.me/plugin/converteme-checkout
    Description: Descricao provisoria Converteme Checkout.
    Author: Converte
    Version: 1.0.0
    Author URI:
    */


    add_action( 'plugins_loaded', 'Converteme_init' );
    function Converteme_init()
    {
        class WC_Converteme_Gateway extends WC_Payment_Gateway
        {
            public function __construct()
            {
                $this->id = 'converteme'; // payment gateway plugin ID
                $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
                $this->has_fields = true; // in case you need a custom credit card form
                $this->method_title = 'Converteme Gateway';
                $this->method_description = 'Descricao do servico de pagamento'; // will be displayed on the options page

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
                $this->clientid = $this->testmode ? $this->get_option( 'test_client_id' ) : $this->get_option( 'client_id' );
                $this->clientsecret = $this->testmode ? $this->get_option( 'test_client_secret' ) : $this->get_option( 'client_secret' );
                $this->EndpointAuth = $this->testmode ? 'https://apidev.converte.me/api/v1/auth/authorization' : 'https://api.converte.me/api/v1/auth/authorization';
                $this->Endpoint = $this->testmode ? 'https://apidev.converte.me/api/v1/pay/transactions' : 'https://api.converte.me/api/v1/pay/transactions';

                // This action hook saves the settings
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
                add_action( 'woocommerce_checkout_fields', array( $this, 'checkout_billing_fields' ), 9999 );


                // We need custom JavaScript to obtain a token
//                add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

                // You can also register a webhook here
                // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
            }

            public function init_form_fields()
            {
                $this->form_fields = array(
                    'enabled' => array(
                        'title'       => 'Ativar/Desativar',
                        'label'       => 'Ativar Converteme Gateway',
                        'type'        => 'checkbox',
                        'description' => '',
                        'default'     => 'no'
                    ),
                    'title' => array(
                        'title'       => 'Titulo',
                        'type'        => 'text',
                        'description' => 'Nome do metodo de pagamento.',
                        'default'     => 'Converteme',
                        'desc_tip'    => true,
                    ),
                    'description' => array(
                        'title'       => 'Descrição',
                        'type'        => 'textarea',
                        'description' => 'Descricao do servico de pagamento.',
                        'default'     => 'Descricao do servico de pagamento.',
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
                        'title'       => 'Client id teste',
                        'type'        => 'text'
                    ),
                    'test_client_secret' => array(
                        'title'       => 'Client secret teste',
                        'type'        => 'password',
                    ),
                    'client_id' => array(
                        'title'       => 'Client id',
                        'type'        => 'text'
                    ),
                    'client_secret' => array(
                        'title'       => 'Client secret',
                        'type'        => 'password'
                    )
                );
            }

            public function payment_fields()
            {

                // ok, let's display some description before the payment form
                if ( $this->description ) {
                    // you can instructions for test mode, I mean test card numbers etc.
                    if ( $this->testmode ) {
                        $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#">documentation</a>.';
                        $this->description  = trim( $this->description );
                    }
                    // display the description with <p> tags etc.
                    echo wpautop( wp_kses_post( $this->description ) );
                }

                // I will echo() the form, but you can close PHP tags and print it directly in HTML
                echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

                // Add this action hook if you want your custom payment gateway to support it
                do_action( 'woocommerce_credit_card_form_start', $this->id );

                // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
                echo file_get_contents(plugin_dir_url(__FILE__) . 'public/template/methods.html');

                do_action( 'woocommerce_credit_card_form_end', $this->id );

                echo '<div class="clear"></div></fieldset>';
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

            public function process_payment( $order_id )
            {
                global $woocommerce;

                // we need it to get any order detailes
                $order = wc_get_order( $order_id );


                /*
                  * Array with parameters for API interaction
                 */

                $args = $this->Payload($order,$_POST);

                /*
                 * Your API interaction could be built with wp_remote_post()
                  */

                echo $args;
                exit();
                $response = wp_remote_post( $this->Endpoint, array(
                    'body' => $args,
                    'headers' => array(
                        'AUTHORIZATION' => 'Bearer ' . $this->Token(),
                        'Content-Type' => 'application/json'
                    )
                ));

                $returnError = json_decode($response['body']);

                if( !is_wp_error( $response ) && !isset($returnError->statusCode)) {

                    $body = json_decode( $response['body'], true );

                    // it could be different depending on your payment processor
                    if ( isset($body['tid']) && $body['tid'] != '' ) {

                        // we received the payment
                        $order->payment_complete();
                        $order->reduce_order_stock();

                        // some notes to customer (replace true with false to make it private)
                        $order->add_order_note( 'Hey, your order is paid! Thank you!', true );

                        // Empty cart
                        $woocommerce->cart->empty_cart();

                        // Redirect to the thank you page
                        return array(
                            'result' => 'success',
                            'redirect' => $this->get_return_url( $order )
                        );

                    } else {
                        wc_add_notice(  'Algo errado em sua transação, entre em contato.', 'error' );
                        return;
                    }

                } else {
                    wc_add_notice(  $returnError->errors[0], 'error' );
                    return;
                }
            }

            public function Payload($order,$dados)
            {
                extract($dados);

                $items = [];
                foreach ( $order->get_items() as $item ) {
                    $items[] = [
                        "id"=> (string) $item->get_product_id(),
                        "title"=> $item->get_name(),
                        "unit_price"=> (float) $item->get_total(),
                        "quantity"=> $item->get_quantity(),
                        "tangible"=> true
                    ];
                }

                $payload = [
                    "place"           => $order->ID,
                    "cart_amount"     => (float) $order->get_subtotal(),
                    "total_amount"    => (float) $order->get_total(),
                    "shipment_amount" => (int) $order->get_shipping_total(),
                    "installments"    => 1,
                    "soft_descriptor" => get_bloginfo( 'name' ),
                    "customer"        => [
                        "name"     => $order->get_billing_first_name(),
                        "email"    => $order->get_billing_email(),
                        "phone"    => preg_replace('/[^0-9]/', '', $order->get_billing_phone()),
                        "document" => preg_replace('/[^0-9]/', '', $billing_cpf),
                        "ip"       => $_SERVER['REMOTE_ADDR']
                    ],
                    "billing"         => [
                        "street"   => $order->get_billing_address_1(),
                        "number"=> $billing_number,
                        "complement" => $order->get_billing_address_2(),
                        "neighborhood" => $billing_neighborhood,
                        "city"=> $order->get_billing_city(),
                        "zipcode"=>preg_replace('/[^0-9]/', '', $order->get_billing_postcode()),
                        "state"=> $order->get_billing_state(),
                        "country"=> $order->get_billing_country()
                    ],
                    "shipping"        =>[
                        "street"   => $order->get_billing_address_1(),
                        "number"=> $billing_number,
                        "complement" => $order->get_billing_address_2(),
                        "neighborhood" => $billing_neighborhood,
                        "city"=> $order->get_billing_city(),
                        "zipcode"=>preg_replace('/[^0-9]/', '', $order->get_billing_postcode()),
                        "state"=> $order->get_billing_state(),
                        "country"=> $order->get_billing_country()
                    ],
                    "items" => $items,
                    "payment" => [
                        "method"=> $metodo,
                        "brand"=>$brand,
                        "card_holder_name" => $cartName,
                        "card_expiration_date" => preg_replace('/[^0-9]/', '', $expirationdate),
                        "card_number" => preg_replace('/[^0-9]/', '', $cardnumber),
                        "card_cvv" => $securitycode,
                        "pix_expiration_date" => $this->Datedue(),
                        "boleto_instructions" => "Não receber após vencimento",
                        "boleto_expiration_date"=> $this->Datedue()
                    ]
                ];

                if(in_array($brand,['visa','mastercard','amex','elo','hipercard',''])){
                    unset($payload['payment']['pix_expiration_date']);
                    unset($payload['payment']['boleto_instructions']);
                    unset($payload['payment']['boleto_expiration_date']);
                }elseif($brand == 'pix'){
                    unset($payload['payment']['card_holder_name']);
                    unset($payload['payment']['card_expiration_date']);
                    unset($payload['payment']['card_number']);
                    unset($payload['payment']['card_cvv']);
                    unset($payload['payment']['boleto_instructions']);
                    unset($payload['payment']['boleto_expiration_date']);
                }elseif($brand == 'boleto'){
                    unset($payload['payment']['card_holder_name']);
                    unset($payload['payment']['card_expiration_date']);
                    unset($payload['payment']['card_number']);
                    unset($payload['payment']['card_cvv']);
                    unset($payload['payment']['pix_expiration_date']);
                }


                return json_encode($payload);
            }

            public function  Datedue(){
                $novadata = explode("/",date('d/m/Y'));
                $dia = $novadata[0];
                $mes = $novadata[1];
                $ano = $novadata[2];

                return date('Y-m-d',mktime(0,0,0,$mes,$dia+5,$ano));
            }

            public function Token()
            {
                $hash = base64_encode($this->clientid .':'. $this->clientsecret);
                $response = wp_remote_post(  $this->EndpointAuth, [
                    'headers' => [
                        'encrypted-token' => $hash,
                    ]
                ]);
                return json_decode($response['body'])->accessToken;
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
            }
        }
    }

    add_filter( 'woocommerce_payment_gateways', 'WC_Converteme_add_gateway_class' );
    function WC_Converteme_add_gateway_class( $gateways ) {
        $gateways[] = 'WC_Converteme_Gateway'; // your class name is here
        return $gateways;
    }

    add_action('wp_enqueue_scripts', 'woocommerce_converteme_style');
    function woocommerce_converteme_style()
    {
        wp_enqueue_style('woocommerce_converteme_style',plugin_dir_url(__FILE__) . 'public/assets/css/woocommerce_converteme_style.css',[],false,false);
        wp_enqueue_script('woocommerce_converteme_script_imask','https://cdnjs.cloudflare.com/ajax/libs/imask/3.4.0/imask.min.js',['jquery'],false,false);
        wp_enqueue_script('woocommerce_converteme_script',plugin_dir_url(__FILE__) . 'public/assets/js/woocommerce_converteme_script.js',['jquery'],false,false);
    }


//    add_action('wp_footer','script_function');
//    function script_function()
//    {
//        echo "<script>";
//        echo file_get_contents(plugin_dir_url(__FILE__) . 'public/assets/js/woocommerce_converteme_script.js');
//        echo "</script>";
//
//    }
