<?php
class SeomidiaScriptsCheckoutCustom
{
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this, 'seomidiascriptcheckoutcustom' ));
    }

   public function seomidiascriptcheckoutcustom()
    {
        $slug = basename(get_permalink());

        wp_enqueue_script( 'seomidia-checkout-custom-script', plugin_dir_url(__DIR__) . 'assets/js/seomidia-checkout-custom-script.js', array( 'jquery' ) );
        wp_localize_script('seomidia-checkout-custom-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php'), 'outro_valor' => 1234));

        if ( in_array($slug,['checkout','finalizar-compra']) ) {
            wp_enqueue_style( 'seomidia-checkout-custom-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' );
            wp_enqueue_style( 'seomidia-checkout-custom-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
            wp_enqueue_script( 'seomidia-checkout-final', plugin_dir_url(__DIR__) . 'assets/js/seomidia-checkout-final.js', array( 'jquery' ) );
            wp_enqueue_script( 'seomidia-checkout-sweetalert2', '//cdn.jsdelivr.net/npm/sweetalert2@11', array( 'jquery' ) );
            wp_enqueue_script( 'seomidia-checkout-maskedinput', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js', array( 'jquery' ) );
            wp_enqueue_style( 'seomidia-checkout-custom-page', plugin_dir_url(__DIR__) . 'assets/css/seomidia-checkout-custom-page.css');
        }
    }
}

new SeomidiaScriptsCheckoutCustom();