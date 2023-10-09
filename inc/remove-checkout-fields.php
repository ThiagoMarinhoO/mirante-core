<?php

// add_filter( 'woocommerce_checkout_fields' , 'custom_remove_checkout_fields' );

// function custom_remove_checkout_fields( $fields ) {
//     // Use as chaves do array para especificar quais campos deseja remover.
//     // Exemplo: Para remover o campo "empresa", use unset( $fields['billing']['billing_company'] );
//     // Para remover o campo "telefone", use unset( $fields['billing']['billing_phone'] );

//     // Exemplo de remoção de campos de cobrança
//     unset( $fields['billing']['billing_cpf'] );

//     return $fields;
// }