<?php
class PixapayScripts
{
    public function __construct()
    {
        add_action( 'wp_enqueue_scripts', array($this, 'seomidiascriptcheckoutcustom' ));
        add_action( 'admin_enqueue_scripts', array($this, 'pixapay_script_admin' ));
    }

   public function seomidiascriptcheckoutcustom()
    {
        if (is_checkout()) {
            wp_enqueue_script( 'seomidia-checkout-final', plugin_dir_url(__DIR__) . 'assets/js/seomidia-checkout-final.js', array( 'jquery' ) );
            $script_data_array = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ));
            wp_localize_script( 'seomidia-checkout-final', 'ajax_object', $script_data_array );
        }
    }

    public function pixapay_script_admin()
    {
        wp_enqueue_script( 'pixapay-plugin-admin-script', plugin_dir_url(__DIR__) . 'assets/js/woocommerce-plugin-admin.js', array( 'jquery' ) );
    }
}

new PixapayScripts();