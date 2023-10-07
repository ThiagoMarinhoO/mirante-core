<?php

function wp_menu_mega_menu() {
    // wp_nav_menu( array('menu'=>'Menu Cabeçalho Imagem Mirante','depth' => 1));
    // wp_nav_menu( array('menu'=>'Menu Cabeçalho Imagem Mirante','walker' => new Child_Only_Walker(), 'depth' => 0));
    wp_nav_menu( array(
        'container' =>false,
        'menu_class' => 'nav',
        'echo' => true,
        'before' => '',
        'after' => '',
        'link_before' => '',
        'link_after' => '',
        'depth' => 0,
        'walker' => new description_walker())
        );
}
add_shortcode('wp_menu_mega_menu', 'wp_menu_mega_menu');



// class Child_Only_Walker extends Walker_Nav_Menu {
// 	// Don't start the top level 
// 	function start_lvl(&$output, $depth=0, $args=array()) {
// 		if( 0 == $depth )
// 			return;
// 		parent::start_lvl($output, $depth,$args);
// 	}
// 	// Don't end the top level 
// 	function end_lvl(&$output, $depth=0, $args=array()) {
// 		if( 0 == $depth )
// 			return;
// 		parent::end_lvl($output, $depth,$args);
// 	}
// 	// Don't print top-level elements 
// 	function start_el(&$output, $item, $depth=0, $args=array(), $id = 0) {
// 		if( 0 == $depth )
// 			return;
// 		parent::start_el($output, $item, $depth, $args);
// 	}
// 	function end_el(&$output, $item, $depth=0, $args=array()) {
// 		if( 0 == $depth )
// 			return;
// 		parent::end_el($output, $item, $depth, $args);
// 	}
// 	// Only follow down one branch 
// 	// function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
// 	// 	// Check if element as a 'current element' class 
// 	// 	$current_element_markers = array( 'current-menu-item', 'current-menu-parent', 'current-menu-ancestor' );
// 	// 	$current_class = array_intersect( $current_element_markers, $element->classes );
// 	// 	// If element has a 'current' class, it is an ancestor of the current element 
// 	// 	$ancestor_of_current = !empty($current_class);
// 	// 	// If this is a top-level link and not the current, or ancestor of the current menu item - stop here. 
// 	// 	if ( 0 == $depth && !$ancestor_of_current)
// 	// 		return;
// 	// 	parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
// 	// }
// }

class description_walker extends Walker_Nav_Menu
{
      function start_el(&$output, $item, $depth=0, $args = array(), $id = 0)
      {
           global $wp_query;
           $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

           $class_names = $value = '';

           $classes = empty( $item->classes ) ? array() : (array) $item->classes;

           $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
           $class_names = ' class="'. esc_attr( $class_names ) . '"';

           $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

           $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
           $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
           $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
           $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

           $prepend = '<strong>';
           $append = '</strong>';
           $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

           if($depth != 0)
           {
                     $description = $append = $prepend = "";
           }

            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
            $item_output .= $description.$args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
            }
}