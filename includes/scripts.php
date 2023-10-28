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
            wp_enqueue_script( 'seomidia-checkout-final', plugin_dir_url(__DIR__) . 'assets/js/seomidia-checkout-final.js', array( 'jquery' ) );
        }
    }
}

new SeomidiaScriptsCheckoutCustom();