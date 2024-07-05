<?php
/*
Plugin Name: WooCommerce Pix Discount
Description: Aplica um desconto de 5% no valor normal do produto e exibe uma mensagem informando o desconto de 4% no Pix. Adiciona campo de quantidade ao lado do botão de compra na listagem de produtos.
Version: 1.7
Author: Michael Campos
Author URI: https://michaelcampos.com.br
Text Domain: woocommerce-pix-discount
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WC_PIX_DISCOUNT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once WC_PIX_DISCOUNT_PLUGIN_DIR . 'includes/class-wc-pix-discount.php';

function wc_pix_discount_load_textdomain() {
    load_plugin_textdomain( 'woocommerce-pix-discount', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wc_pix_discount_load_textdomain' );
