<?php
class SeomidiaScriptsCheckoutCustom
{
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this, 'seomidiascriptcheckoutcustom' ));
    }

   public function seomidiascriptcheckoutcustom()
    {
        if (is_checkout()) {
            wp_enqueue_script( 'woocommerce-converteme-script', plugin_dir_url(__DIR__) . 'assets/js/woocommerce-converteme-script.js', array( 'jquery' ) );
            wp_localize_script('woocommerce-converteme-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'outro_valor' => 1234));
            wp_enqueue_style( 'woocommerce-converteme-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' );
            wp_enqueue_style( 'woocommerce-converteme-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
            wp_enqueue_script( 'seomidia-checkout-final', plugin_dir_url(__DIR__) . 'assets/js/seomidia-checkout-final.js', array( 'jquery' ) );
            wp_enqueue_script( 'seomidia-checkout-sweetalert2', '//cdn.jsdelivr.net/npm/sweetalert2@11', array( 'jquery' ) );
            wp_enqueue_script( 'seomidia-checkout-maskedinput', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js', array( 'jquery' ) );
            wp_enqueue_style( 'woocommerce-converteme-page', plugin_dir_url(__DIR__) . 'assets/css/woocommerce-converteme-page.css');
        }
    }
}

new SeomidiaScriptsCheckoutCustom();