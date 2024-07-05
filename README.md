# WooCommerce Pix Discount

## Descrição
O WooCommerce Pix Discount é um plugin para WooCommerce que aplica um desconto de 5% no valor normal do produto e exibe uma mensagem informando o desconto adicional de 4% para pagamentos via Pix. Além disso, o plugin adiciona um campo de quantidade ao lado do botão de compra na listagem de produtos.

## Funcionalidades
- Aplica um desconto de 5% no valor original do produto.
- Exibe um preço adicional com desconto de 4% para pagamentos via Pix.
- Adiciona um campo de quantidade ao lado do botão de compra na listagem de produtos.
- Internacionalização e tradução.

## Instalação

### Instalação via WordPress
1. No painel de administração do WordPress, vá para Plugins > Adicionar Novo.
2. Clique no botão "Enviar plugin" e selecione o arquivo ZIP do plugin.
3. Clique em "Instalar agora" e, em seguida, em "Ativar".

### Instalação manual
1. Faça o upload da pasta `woocommerce-pix-discount` para o diretório `/wp-content/plugins/`.
2. Ative o plugin através do menu "Plugins" no WordPress.

## Uso
Uma vez ativado, o plugin automaticamente:
- Aplica um desconto de 5% ao preço original do produto.
- Mostra o preço com desconto de 4% para pagamentos via Pix.
- Adiciona um campo de quantidade ao lado do botão de compra na listagem de produtos.

### Configuração
O plugin não requer configurações adicionais após a ativação. Todas as funcionalidades são aplicadas automaticamente.

## Customização

### Adicionar/Remover Campos
Para adicionar ou remover campos ou alterar a disposição dos elementos, edite a função `adicionar_campo_quantidade_e_botao_comprar` na classe `WC_Pix_Discount` localizada no arquivo `includes/class-wc-pix-discount.php`.

### Alterar Mensagens e Estilos
- Para alterar a mensagem de desconto, modifique o texto na variável `$mensagem_desconto` na função `mostrar_preco` na classe `WC_Pix_Discount`.
- Para alterar estilos, edite o arquivo CSS localizado em `css/wp-pix-style.css`.

## Arquivos e Estrutura
- `woocommerce-pix-discount.php`: Arquivo principal do plugin.
- `includes/class-wc-pix-discount.php`: Classe principal que contém a lógica do plugin.
- `css/wp-pix-style.css`: Estilos personalizados para o plugin.
- `languages/woocommerce-pix-discount.pot`: Arquivo de template para tradução.

## Detalhamento das Funções

### Funções Principais

#### `wp_pix_calcular_preco_5`
Calcula o preço com um desconto de 5%.

```php
public function calcular_preco_5( $preco ) {
    return floatval( $preco ) * 0.95;
}
```

#### `wp_pix_calcular_preco_pix`
Calcula o preço com um desconto adicional de 4% para pagamentos via Pix.

```php
public function calcular_preco_pix( $preco ) {
    return floatval( $preco ) * 0.96;
}
```

#### `wp_pix_mostrar_preco`
Mostra o preço original, o preço com desconto de 5% e o preço com desconto adicional de 4% para pagamentos via Pix, incluindo a mensagem de desconto.

```php
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
```

#### `wp_pix_aplicar_desconto_carrinho`
Aplica o desconto de 5% ao adicionar o produto ao carrinho.

```php
public function aplicar_desconto_carrinho() {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $produto = $cart_item['data'];
        $preco_original = floatval( $produto->get_regular_price() );
        $preco_desconto = $this->calcular_preco_5( $preco_original );
        $produto->set_price( $preco_desconto );
    }
}
```

#### `wp_pix_modificar_template`
Modifica o template para adicionar o campo de quantidade e o botão de compra.

```php
public function modificar_template() {
    remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
    add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 9 );
    remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
    add_action( 'woocommerce_after_shop_loop_item', array( $this, 'adicionar_campo_quantidade_e_botao_comprar' ), 10 );
