<?php 

// Adiciona campo de seleção para Tipo de Pessoa
// add_action('woocommerce_after_checkout_billing_form', 'select_person_type');
// function select_person_type() {
//     woocommerce_form_field('person_type', array(
//         'type' => 'select',
//         'class' => array('form-row-wide'),
//         'label' => 'Tipo de Pessoa',
//         'required' => true,
//         'options' => array(
//             '' => 'Selecione o Tipo de Pessoa',
//             'pessoa_fisica' => 'Pessoa Física',
//             'pessoa_juridica' => 'Pessoa Jurídica'
//         )
//     ));
// }

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

// Adiciona campos para Fantasia
add_action('woocommerce_after_checkout_billing_form', 'adicionar_campo_fantasia');
function adicionar_campo_fantasia() {
    woocommerce_form_field('fantasia', array(
        'type' => 'text',
        'class' => array('form-row-wide campo-pessoa-juridica'),
        'label' => 'Fantasia',
        'required' => true,
    ));
}

// Adiciona campos para CNPJ
// add_action('woocommerce_after_checkout_billing_form', 'adicionar_campo_cnpj');
// function adicionar_campo_cnpj() {
//     woocommerce_form_field('cnpj', array(
//         'type' => 'text',
//         'class' => array('form-row-wide, campo-pessoa-juridica'),
//         'label' => 'CNPJ',
//         'required' => true,
//         'custom_attributes' => array(
//             'data-class-pessoa-juridica' => 'campo-pessoa-juridica',
//         )
//     ));
// }

// Adiciona campos para Responsável pelo Orçamento
// add_action('woocommerce_after_checkout_billing_form', 'adicionar_campo_pessoa_responsavel');
// function adicionar_campo_pessoa_responsavel() {
//     woocommerce_form_field('pessoa_responsavel', array(
//         'type' => 'text',
//         'class' => array('form-row-wide, campo-pessoa-juridica'),
//         'label' => 'Pessoa Responsável',
//         'required' => true,
//         'custom_attributes' => array(
//             'data-class-pessoa-juridica' => 'campo-pessoa-juridica',
//         )
//     ));
// }

// Adiciona campos para Nome Completo
// add_action('woocommerce_after_checkout_billing_form', 'adicionar_campo_full_name');
// function adicionar_campo_full_name() {
//     woocommerce_form_field('full_name', array(
//         'type' => 'text',
//         'class' => array('form-row-wide, campo-pessoa-fisica'),
//         'label' => 'Nome Completo',
//         'required' => true,
//         'custom_attributes' => array(
//             'data-class-pessoa-fisica' => 'campo-pessoa-fisica',
//         )
//     ));
// }


add_action('woocommerce_checkout_process', 'custom_checkout_field_validation');

function custom_checkout_field_validation() {
    if ($_POST['billing_persontype'] == "2") {
        // Validação para campos de Pessoa Física
        if (empty($_POST['razao_social'])) {
            wc_add_notice('O campo "Razão Social" do endereço de faturamento é um campo obrigatório', 'error');
        }
        if (empty($_POST['fantasia'])) {
            wc_add_notice('O campo "Fantasia" do endereço de faturamento é um campo obrigatório', 'error');
        }
        // if (empty($_POST['pessoa_responsavel'])) {
        //     wc_add_notice('Por favor, preencha o campo "Pessoa Responsável".', 'error');
    }
}

add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_save');

function custom_checkout_field_save($order_id) {
    if ($_POST['billing_persontype'] == "2") {
        // Salvar campos de Pessoa Jurídica
        if (!empty($_POST['razao_social'])) {
            update_post_meta($order_id, 'Razão Social', sanitize_text_field($_POST['razao_social']));
        }
        if (!empty($_POST['fantasia'])) {
            update_post_meta($order_id, 'Fantasia', sanitize_text_field($_POST['fantasia']));
        }
        // if (!empty($_POST['pessoa_responsavel'])) {
        //     update_post_meta($order_id, 'Pessoa Responsável', sanitize_text_field($_POST['pessoa_responsavel']));
        // }
    }
}