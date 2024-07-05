<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Pix_Discount {

    public function __construct() {
        add_filter( 'woocommerce_get_price_html', array( $this, 'mostrar_preco' ), 10, 2 );
        add_action( 'woocommerce_before_calculate_totals', array( $this, 'aplicar_desconto_carrinho' ) );
        add_action( 'init', array( $this, 'modificar_template' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'adicionar_estilo' ) );
        register_activation_hook( __FILE__, array( $this, 'criar_estilo' ) );
    }

    public function calcular_preco_5( $preco ) {
        return floatval( $preco ) * 0.95;
    }

    public function calcular_preco_pix( $preco ) {
        return floatval( $preco ) * 0.96;
    }

    public function mostrar_preco( $price, $product ) {
        $preco_original = floatval( $product->get_regular_price() );
        $preco_com_desconto_dinamico = $this->calcular_preco_5( $preco_original );
        $preco_com_pix = $this->calcular_preco_pix( $preco_com_desconto_dinamico );

        $preco_formatado_original = wc_price( $preco_original );
        $preco_formatado_desconto_dinamico = wc_price( $preco_com_desconto_dinamico );
        $preco_formatado_pix = wc_price( $preco_com_pix );

        $mensagem_desconto = sprintf(
            '<p class="pix-discount-message">%s <svg id="pix-produtos" data-name="pix-produtos" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 315.4 315.6">
                <defs>
                </defs>
                <path class="cls-1" d="M246,241.3c-12.3,0-24.1-4.8-32.8-13.5l-47.4-47.4c-3.5-3.3-9-3.3-12.4,0l-47.5,47.5c-8.7,8.7-20.5,13.6-32.8,13.6h-9.3l60,60c18.7,18.7,49.1,18.7,67.8,0l60.1-60.1h-5.8.1Z"/>
                <path class="cls-1" d="M73.1,73.9c12.3,0,24.1,4.9,32.8,13.6l47.5,47.5c3.4,3.4,9,3.4,12.4,0l47.3-47.3c8.7-8.7,20.5-13.6,32.8-13.6h5.7l-60.1-60.1c-18.7-18.7-49.1-18.7-67.8,0h0l-59.9,59.9s9.3,0,9.3,0Z"/>
                <path class="cls-1" d="M301.4,123.8l-36.3-36.3c-.8.3-1.7.5-2.6.5h-16.5c-8.6,0-16.8,3.4-22.9,9.5l-47.3,47.3c-8.9,8.9-23.3,8.9-32.1,0l-47.5-47.5c-6.1-6.1-14.3-9.5-22.9-9.5h-20.3c-.8,0-1.7-.2-2.4-.5L14,123.8c-18.7,18.7-18.7,49.1,0,67.8l36.5,36.5c.8-.3,1.6-.5,2.4-.5h20.4c8.6,0,16.8-3.4,22.9-9.5l47.5-47.5c8.6-8.6,23.6-8.6,32.1,0l47.3,47.3c6.1,6.1,14.3,9.5,22.9,9.5h16.5c.9,0,1.8.2,2.6.5l36.3-36.3c18.7-18.7,18.7-49.1,0-67.8h0"/>
                </svg> %s</p>',
            __( 'no pix 4% off', 'woocommerce-pix-discount' )
        );

        $price = '' . $preco_formatado_pix . '<br>' . $mensagem_desconto . '<br>' . '<del style="color: #d3d3d3;">' . $preco_formatado_original . '</del> ' . $preco_formatado_desconto_dinamico;

        return $price;
    }

    public function aplicar_desconto_carrinho() {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $produto = $cart_item['data'];
            $preco_original = floatval( $produto->get_regular_price() );
            $preco_desconto = $this->calcular_preco_5( $preco_original );
            $produto->set_price( $preco_desconto );
        }
    }

    public function modificar_template() {
        remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
        add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 9 );
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        add_action( 'woocommerce_after_shop_loop_item', array( $this, 'adicionar_campo_quantidade_e_botao_comprar' ), 10 );
    }

    public function adicionar_campo_quantidade_e_botao_comprar() {
        global $product;
        if ( $product && $product->is_purchasable() && $product->is_in_stock() ) {
            echo '<form class="cart" action="' . esc_url( $product->add_to_cart_url() ) . '" method="post" enctype="multipart/form-data">';
            echo '<div class="quantity-comprar">';
            woocommerce_quantity_input( array(
                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1,
            ) );
            echo '<button type="submit" class="button add_to_cart_button ajax_add_to_cart" data-product_id="' . esc_attr( $product->get_id() ) . '" data-product_sku="' . esc_attr( $product->get_sku() ) . '" aria-label="' . esc_attr( $product->add_to_cart_description() ) . '">' . esc_html( $product->add_to_cart_text() ) . '</button>';
            echo '</div>';
            echo '</form>';
        }
    }

    public function adicionar_estilo() {
        wp_enqueue_style( 'wp-pix-style', plugins_url( '../css/wp-pix-style.css', __FILE__ ) );
    }

    public function criar_estilo() {
        $css = ".pix-discount-message {
            font-size: 14px;
            color: #ff0000;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        del {
            color: #999;
        }
        .quantity-comprar {
            display: flex;
            align-items: center;
        }
        .quantity-comprar .button {
            margin-left: 10px;
        }";

        $upload_dir = wp_upload_dir();
        $css_file_path = $upload_dir['basedir'] . '/wp-pix-style.css';

        if ( ! file_exists( $css_file_path ) ) {
            file_put_contents( $css_file_path, $css );
        }
    }
}

new WC_Pix_Discount();
