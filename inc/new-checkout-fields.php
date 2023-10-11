<?php 


// Adiciona campos para Razão Social
add_action('woocommerce_after_checkout_billing_form', 'adicionar_campo_razao_social');
function adicionar_campo_razao_social() {
    woocommerce_form_field('razao_social', array(
        'type' => 'text',
        'class' => array('form-row-wide campo-pessoa-juridica'),
        'label' => 'Razão Social',
        'required' => true,
    ));
}


add_action('woocommerce_checkout_process', 'custom_checkout_field_validation');

function tornar_billing_company_opcional( $fields ) {
    if (isset($fields['billing']['billing_company'])) {
        $fields['billing']['billing_company']['required'] = false;
    }
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'tornar_billing_company_opcional' );

function custom_checkout_field_validation() {
    if ($_POST['billing_persontype'] == "2") {
        // Validação para campos de Pessoa Física
        if (empty($_POST['razao_social'])) {
            wc_add_notice('O campo "Razão Social" do endereço de faturamento é um campo obrigatório', 'error');
        }
    }
}

add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_save');

function custom_checkout_field_save($order_id) {
    if ($_POST['billing_persontype'] == "2") {
        // Salvar campos de Pessoa Jurídica
        if (!empty($_POST['razao_social'])) {
            update_post_meta($order_id, 'Razão Social', sanitize_text_field($_POST['razao_social']));
        }
    }
}