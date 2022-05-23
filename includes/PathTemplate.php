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
            $slug = basename(get_permalink());
            if ( in_array($slug,['checkout','finalizar-compra'])) {
                if(!is_order_received_page('order-received')){
                    $page_template =  dirname( path_plugin ) . '/seomidia-checkout-custom/modelo/seomidia-checkout.php';
                }
            }

        return $page_template;
    }

    public function woo_adon_plugin_template( $template, $template_name, $template_path ) 
    {
        
        $templatet = str_replace(['themes','flatsome'],['plugins','seomidia-checkout-custom/templates'],$template);

        if($template_name == 'checkout/review-order.php'){
            $templatet =  str_replace(['woocommerce/templates'],['seomidia-checkout-custom/templates/woocommerce'],$template);
        }

        if($template_name == 'checkout/payment.php'){
            $templatet =  str_replace(['woocommerce/templates'],['seomidia-checkout-custom/templates/woocommerce'],$template);
        }

        if($template_name == 'checkout/payment-method.php'){
            $templatet =  str_replace(['woocommerce/templates'],['seomidia-checkout-custom/templates/woocommerce'],$template);
        }

        if($template_name == 'cart/mini-cart.php'){
            $templatet =  str_replace(['woocommerce/templates'],['seomidia-checkout-custom/templates/woocommerce'],$template);
        }

//         var_dump($template_name);
        return $templatet;
    }

}

new NewPathTemplate();