<?php
    function mega_menu() {
        ?>
    
        <header class="mega_menu">
    
            <?php
            wp_nav_menu(
                array(
                    'menu'            => 'Menu Cabeçalho Imagem Mirante',
                    'container_id'    => 'primary-menu',
                    'container_class' => 'items-center justify-between hidden w-full md:flex md:w-auto md:order-1',
                    'menu_class'      => 'menuListStyle',
                    'theme_location'  => 'primary',
                    'walker'          => new Custom_Walker_Nav_Menu(), // Adicione o walker personalizado
                )
            );
            ?>
    
        </header>
    
        <?php
    }
    add_shortcode('mega_menu', 'mega_menu');

    // Get menu description as global variable
    // function add_menu_description( $item_output, $item, $depth, $args ) {
    //     global $item;
    //     return $item;
    // }
    // add_filter( 'walker_nav_menu_start_el', 'add_menu_description', 10, 4);

    class Custom_Walker_Nav_Menu extends Walker_Nav_Menu {
        function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
            // Verifique se o item tem sub-itens (children)
            if ($args->walker->has_children) {
                // Adicione sua personalização do item de menu aqui
                $object_id = $item->object_id;

                // Verifica se o item do menu está associado a uma categoria de produto
                if ($item->object === 'product_cat' && is_numeric($object_id)) {
                    // Use o ID do objeto para obter informações da categoria de produto
                    $category = get_term($object_id, 'product_cat');
                    $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );

                    if ($thumbnail_id) {
                        $thumbnail_url = wp_get_attachment_thumb_url( $thumbnail_id );
                        $output .= '<li class="menu-item-has-children" categoryImage="' . esc_url($thumbnail_url) . '">';
                        // $output .= '<li class="menu-item-has-children">';
                        // Adicione um ícone antes do link
                        $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '<i class="fas fa-angle-down premium-dropdown-icon"></i></a>';
                    } else {
                        $output .= '<li class="menu-item-has-children">';
                        // Adicione um ícone antes do link
                        $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '<i class="fas fa-angle-down premium-dropdown-icon"></i></a>';
                    }
                } else {
                    $output .= '<li class="menu-item-has-children">';
                    // Adicione um ícone antes do link
                    $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '<i class="fas fa-angle-down premium-dropdown-icon"></i></a>';
                }

                // $output .= '<li class="menu-item-has-children">';
                // // Adicione um ícone antes do link
                // $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '<i class="fas fa-angle-down premium-dropdown-icon"></i></a>';
            } else {

                // Se não tiver sub-itens, apenas adicione o link
                // Obtém o ID do objeto do item do menu
                $object_id = $item->object_id;

                // Verifica se o item do menu está associado a uma categoria de produto
                if ($item->object === 'product_cat' && is_numeric($object_id)) {
                    // Use o ID do objeto para obter informações da categoria de produto
                    $category = get_term($object_id, 'product_cat');
                    $image_url = get_field('imagem' , 'category_'.$category->term_id);

                    if ($image_url) {
                        $output .= '<li categoryImage="' . get_field('imagem' , 'category_'.$category->term_id) . '">';
                        $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
                    } else {
                        $output .= '<li>';
                        $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
                    }
                }
                // $output .= '<li>';
                // $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
            }
        }


        function start_lvl(&$output, $depth = 0, $args = null) {
            // Adicione sua personalização do submenu aqui
            $output .= '<ul class="sub-menu">'; // Personalize a classe da sub-menu conforme necessário

            $output .= '<div class="imageContainer"><img src="/wp-content/uploads/2023/05/181568379843805d7b93c37a8d0-1024x603.jpg" alt="Imagem da categoria"></div>';

            $output .= '<div class="childrenLiDiv">';

            // global $item;
            // var_dump($item);
            // $output .= '<li></li>';
        }
        
    
        function end_lvl(&$output, $depth = 0, $args = null) {
            // Feche o submenu personalizado
            $output .= '</div>';
            $output .= '</ul>';
        }
    }