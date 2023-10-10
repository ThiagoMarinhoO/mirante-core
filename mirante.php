<?php

   /*
   Plugin Name: Mirante core
   Plugin URI: #
   description: Complemento do site mirante
   Version: 1.0
   Author: Marcos Macedo
   Author URI: #
   License: GPL2
   */


require_once plugin_dir_path(__FILE__) . '/inc/mirante-core.php';
require_once plugin_dir_path(__FILE__) . '/inc/new-checkout-fields.php';
require_once plugin_dir_path(__FILE__) . '/inc/remove-checkout-fields.php';
require_once plugin_dir_path(__FILE__) . '/inc/shortcodes/menu.php';
require_once plugin_dir_path(__FILE__) . '/inc/generate-pdf.php';
require_once plugin_dir_path(__FILE__) . '/inc/generate-pdf-ajax.php';
require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');


function mirante_scripts() {
    wp_enqueue_style( 'main-css', plugin_dir_url( __FILE__ ).'/assets/css/main.css' );
    wp_enqueue_script( 'app-script', plugin_dir_url( __FILE__ ) . '/assets/js/app.js', array('jquery'), '1.0.0', true );
// 	wp_localize_script( 'fonon-script', 'wpurl',
//   array( 

//       'ajax' => admin_url( 'admin-ajax.php' ),
//       'my_account' => get_home_url() .'/minha-conta',
//       'home' => get_home_url(),
//       'user_logged_in' => is_user_logged_in(),
//       'user_id' => get_current_user_id(),
//       'post_id' => get_the_ID(),
//   )

// );

}

add_action( 'wp_enqueue_scripts', 'mirante_scripts' );

function my_custom_admin_script() {
    wp_enqueue_script( 'admin-js', plugin_dir_url( __FILE__ ) . '/assets/js/admin.js', array( 'jquery' ), '1.0.0', true );
    // wp_enqueue_style( 'admin-estoque-style' , plugin_dir_url( __FILE__ ) . '/assets/css/admin.css' );
    wp_localize_script( 'admin-js', 'wpurl',
    array( 
        'ajax' => admin_url( 'admin-ajax.php' ),
    ) );
  }
  
  add_action( 'admin_enqueue_scripts', 'my_custom_admin_script' );

add_filter( 'woocommerce_locate_template', 'intercept_wc_template', 10, 3 );
/**
 * Filter the cart template path to use cart.php in this plugin instead of the one in WooCommerce.
 *
 * @param string $template      Default template file path.
 * @param string $template_name Template file slug.
 * @param string $template_path Template file name.
 *
 * @return string The new Template file path.
 */
function intercept_wc_template( $template, $template_name, $template_path ) {

	if ( 'cart.php' === basename( $template ) ) {
		$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/cart/cart.php';
	} elseif ( 'review-order.php' === basename( $template ) ) {
		$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/checkout/review-order.php';
	}

	return $template;

}

?>