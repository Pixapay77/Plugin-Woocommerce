<?php
    /*
    Plugin Name: Woocommerce Converteme
    Plugin URI: https://converte.me/plugin/converteme-checkout
    Description: Descricao provisoria Converteme Checkout.
    Author: Converte
    Version: 1.0.0
    Author URI:
    */

    require_once 'vendor/autoload.php';
    use chillerlan\QRCode\QRCode;

    define('path_plugin',ABSPATH . 'wp-content/plugins/woocommerce-converteme/');

    require_once ABSPATH ."/wp-load.php";
    include_once WP_PLUGIN_DIR .'/woocommerce/woocommerce.php';
    require_once __DIR__ . '/includes/PathTemplate.php';
    require_once __DIR__ . '/includes/scripts.php';
    require_once __DIR__ . '/includes/ajax.php';
    require_once __DIR__ . '/hooks/cart.php';

    add_action( 'plugins_loaded', 'Converteme_init' );
    function Converteme_init()
    {

        class WC_Converteme_Gateway extends WC_Payment_Gateway
        {
            public $TypePayment;

            public function __construct()
            {
                $this->id = 'converteme'; // payment gateway plugin ID
                $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
                $this->has_fields = true; // in case you need a custom credit card form
                $this->method_title = 'Converteme Gateway';
                $this->method_description = ''; // will be displayed on the options page

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

                add_action( 'woocommerce_before_thankyou', array($this,'dados_pagamento'));

                add_action( 'woocommerce_admin_order_data_after_billing_address', array($this,'order_cpf_backend'));


                // We need custom JavaScript to obtain a token
//                add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

                // You can also register a webhook here
                 add_action( 'woocommerce_api_pagamento', array( $this, 'webhook' ) );
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


                // we need it to get any order detailes
                $order = wc_get_order( $order_id );
                $order_data = $order->get_data(); // The Order data
                $user_id = $order_data['customer_id'];

                update_user_meta( $user_id, 'billing_cpf', $_POST['billing_cpf'] );



                /*
                  * Array with parameters for API interaction
                 */

                if(($order->get_total() / $_POST[ 'installments' ]) < 5) {
                    wc_add_notice(  'Valor minimo por parcela e R$ 5,00', 'error' );
                    return false;
                }


                $args = $this->Payload($order,$_POST);

                /*
                 * Your API interaction could be built with wp_remote_post()
                  */

                $response = wp_remote_post( $this->Endpoint, array(
                    'body' => $args,
                    'headers' => array(
                        'AUTHORIZATION' => 'Bearer ' . $this->Token(),
                        'Content-Type' => 'application/json'
                    )
                ));


                $body = json_decode( $response['body'], true );


                if( !is_wp_error( $response ) && $response["response"]["code"] == 200) {
                    // it could be different depending on your payment processor
                    if ( isset($body['transaction']) && $body['transaction'] != '' ) {

                        // Empty cart
                        $woocommerce->cart->empty_cart();

                        // Redirect to the thank you page
                        $this->TypePayment = $body["payment_method"];

                        update_post_meta($order_id,'_converteme_payment_type',$this->TypePayment);
                        if($this->TypePayment == 'boleto'){
                            update_post_meta($order_id,'_converteme_boleto_barcode',$body["boleto_barcode"]);
                            update_post_meta($order_id,'_converteme_boleto_expiration_date',$body["boleto_expiration_date"]);
                            update_post_meta($order_id,'_converteme_boleto_boleto_url',$body["boleto_url"]);
                        }elseif($this->TypePayment == 'pix'){
                            update_post_meta($order_id,'_converteme_pix_qr_code',$body["pix_qr_code"]);
                            update_post_meta($order_id,'_converteme_pix_expiration_date',$body["pix_expiration_date"]);
                        }

                        return array(
                            'result' => 'success',
                            'redirect' => $this->get_return_url( $order )
                        );

                    } else {
                        wc_add_notice(  'Algo errado em sua transação, entre em contato.', 'error' );
                        return;
                    }

                }elseif($body["statusCode"] == 400){
                    $msg = (is_array($body["errors"][0])) ? $body["errors"][0]['messages'] : $body["errors"][0];
                    wc_add_notice(  $body['statusText'] .': '. $msg , 'error' );
                } else {
                    $msg = (is_array($body["errors"][0])) ? $body["errors"][0]['messages'] : $body["errors"][0];
                    wc_add_notice(  $body['statusText'] .': '. $msg , 'error' );
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
                        "unit_price"=> (int) $item->get_total() * 100,
                        "quantity"=> $item->get_quantity(),
                        "tangible"=> true
                    ];
                }

                $metodo = (in_array($brand,['pix','boleto'])) ? $brand : 'credit_card';

                $payload = [
                    "external_reference" => "{$order->ID}",
                    "cart_amount"     => (int) $order->get_subtotal() * 100,
                    "total_amount"    => (int) $order->get_total() * 100,
                    "shipment_amount" => (int) $order->get_shipping_total() * 100,
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
                        "card_token" => $dados['wc-converteme-cardtoken'],
                        "pix_expiration_date" => $this->Datedue(),
                        "boleto_instructions" => "Não receber após vencimento",
                        "boleto_expiration_date"=> $this->Datedue(),
                        "installments"    => (int) $installments
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
                    unset($payload['payment']['card_token']);
                }elseif($brand == 'boleto'){
                    unset($payload['payment']['card_holder_name']);
                    unset($payload['payment']['card_expiration_date']);
                    unset($payload['payment']['card_number']);
                    unset($payload['payment']['card_cvv']);
                    unset($payload['payment']['pix_expiration_date']);
                    unset($payload['payment']['card_token']);
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
                $data = json_decode(file_get_contents('php://input'), true);
                $this->OrderReturn((object) $data);
                exit();
            }
            public function OrderReturn($data)
            {
                $transition     = $data->resource['id'];
                $status         = $data->resource['status'];
                $order_id       = $data->resource['reference_id'];
                $payment_method = $data->resource['payment_method']['type'];
                $link_boleto    = $data->resource['link'];

                $order = wc_get_order( $order_id );

                $description = ($payment_method == "BOLETO") ? $payment_method . " Link <a target='_blanck' href='".$link_boleto."'>" . $link_boleto . '</a>' : $payment_method;

                if($status == 'paid'){
                    $order->payment_complete();
                    $order->reduce_order_stock();
                    // some notes to customer (replace true with false to make it private)
                    $order->add_order_note( 'Pagamento confirmado por ' . $payment_method, true );
                }elseif($status == 'waiting_payment'){
                    // some notes to customer (replace true with false to make it private)
                    $order->add_order_note( 'Aguardando pagamento por ' . $description, true );
                }
            }
            public function dados_pagamento($order_id) {
                        $code   = get_post_meta($order_id,'_converteme_pix_qr_code',true);
                        if($code != '')
                            $base64 = (new QRCode)->render($code);
                        $pix = '
                            <td class="woocommerce-table__product-name product-name" style="text-align: center">
                                <div id="converteme-pix">
                                Utilize o QRCode e pague o PIX pelo celular
                                    <img src="'.$base64.'" />
                                </div>
                            </td>
                            <td class="woocommerce-table__product-name product-name" style="text-align: center">
                                <div id="converteme-pix-codigo">
                                    <label>Código de pagamento</label><br>
                                    <input type="text" id="codepix" value="'.get_post_meta($order_id,'_converteme_pix_qr_code',true).'">
                                    <br><span>Copiar código</span>
                                 </div>
                            </td>
                            <td class="woocommerce-table__product-name product-name" style="text-align: center">
                                Vencimento
                                '.get_post_meta($order_id,'_converteme_pix_expiration_date',true).'
                            </td>
                        ';

                        $boleto = '
                            <td colspan="2" class="woocommerce-table__product-name product-name" style="text-align: center">
                                <div id="converteme-pix-codigo">
                                    <label>Boleto</label><br>
                                    <a target="_blank" href="'.get_post_meta($order_id,'_converteme_boleto_boleto_url',true).'">Abrir boleto</a>
                                 </div>
                            </td>
                            <td class="woocommerce-table__product-name product-name" style="text-align: center">
                                Vencimento<br>
                                '.get_post_meta($order_id,'_converteme_boleto_expiration_date',true).'
                            </td>
                        ';

                        $TypePayment = get_post_meta($order_id,'_converteme_payment_type',true);
                        $typePayment = ($TypePayment == 'pix') ? $pix : $boleto;

                        $html = '
                           <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">Detalhes para pagamento.</p>
                           <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
                                <tr class="woocommerce-table__line-item order_item">
                                     '.$typePayment.'
                                </tr>
                            </table> 
                            <style>
                                #converteme-pix{
                                     width: 203px;
                                     padding: 15px;
                                     font-size: 12px;
                                }
                                #converteme-boleto{
                                     width: 203px;
                                     padding: 15px
                                }
                                #converteme-boleto a{
                                    line-height: 12px;
                                    white-space: normal;
                                    font-weight: 700;
                                    text-align: center;
                                    text-transform: uppercase;
                                    font-size: 14px;
                                    color: #149221;
                                    background-color: #e5ebe0;
                                    padding: 6px;
                                    margin: 7px 0;
                                }
                                #converteme-pix .copiar{
                                    line-height: 12px;
                                    white-space: normal;
                                    font-weight: 700;
                                    text-align: center;
                                    text-transform: uppercase;
                                    font-size: 14px;
                                    color: #149221;
                                    background-color: #e5ebe0;
                                    padding: 6px;
                                    margin: 7px 0;
                                }
                            </style>

                    ';

                echo $html;
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


