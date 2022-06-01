<?php 

class NewPathTemplate
{
    public function __construct()
    {
        add_filter( 'woocommerce_locate_template', array($this,'woo_adon_plugin_template'), 1, 3 );
        add_filter( 'page_template', array($this,'wpa3396_page_template') );
    }

    function wpa3396_page_template( $page_template )
    {
            if (is_checkout()) {
                if(!is_order_received_page('order-received')){
                    $page_template =  dirname( path_plugin ) . '/woocommerce-converteme/modelo/seomidia-checkout.php';
                }
            }

        return $page_template;
    }

    public function woo_adon_plugin_template( $template, $template_name, $template_path ) 
    {
        if (is_checkout()) {

            if ($template_name == 'checkout/review-order.php') {
                $template = str_replace(['woocommerce/templates'], ['woocommerce-converteme/templates/woocommerce'], $template);
            }

            if ($template_name == 'checkout/payment.php') {
                $template = str_replace(['woocommerce/templates'], ['woocommerce-converteme/templates/woocommerce'], $template);
            }

            if ($template_name == 'checkout/payment-method.php') {
                $template = str_replace(['woocommerce/templates'], ['woocommerce-converteme/templates/woocommerce'], $template);
            }

        }

        return $template;
    }

}

new NewPathTemplate();