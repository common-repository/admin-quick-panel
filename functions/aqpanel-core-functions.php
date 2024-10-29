<?
/**
 * Post list
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php 
function aqpanel_inside()
{
    global  $aqpanel_options ;
    $html = '';
    $buttons_classes = '';
    if ( get_user_setting( 'aqpanel_collapsed' ) == 'true' ) {
        $buttons_classes = 'closed';
    }
    /* Additional */
    
    if ( !empty($aqpanel_options['aqpanel_additional']) ) {
        // Recently edited
        if ( array_key_exists( 'aqpanel_recently_edited', $aqpanel_options['aqpanel_additional'] ) ) {
            
            if ( $aqpanel_options['aqpanel_additional']['aqpanel_recently_edited'] == 1 ) {
                $posts = get_posts( array(
                    'numberposts' => 3,
                    'post_type'   => array( 'post', 'page' ),
                    'orderby'     => 'modified',
                ) );
                
                if ( $posts ) {
                    $html .= '<div class="post-type-container"><h2 class="post-type-title"><span class="icon dashicons dashicons-edit"></span><span class="text">' . __( 'Recently Edited' ) . '</span></h2>';
                    $html .= '<ul class="link-list recent-list">';
                    $html .= '<h4 class="sub-menu-title">' . __( 'Recently Edited' ) . '</h4>';
                    foreach ( $posts as $post ) {
                        $html .= '<li><a href="' . esc_html( get_edit_post_link( $post->ID ) ) . '">' . esc_html( $post->post_title ) . '</a></li>';
                    }
                    $html .= '</ul></div>';
                }
            
            }
        
        }
        if ( array_key_exists( 'aqpanel_widgets_button', $aqpanel_options['aqpanel_additional'] ) ) {
            
            if ( $aqpanel_options['aqpanel_additional']['aqpanel_widgets_button'] == 1 ) {
                $url = admin_url( 'widgets.php' );
                $html .= '<a class="aqpanel-button" href="' . esc_url( $url ) . '"><h2 class="post-type-title"><span class="icon dashicons dashicons-admin-appearance"></span><span class="text">' . __( 'Widgets' ) . '</span></h2></a>';
            }
        
        }
        if ( array_key_exists( 'aqpanel_menus_button', $aqpanel_options['aqpanel_additional'] ) ) {
            
            if ( $aqpanel_options['aqpanel_additional']['aqpanel_menus_button'] == 1 ) {
                $url = admin_url( 'nav-menus.php' );
                $html .= '<a class="aqpanel-button" href="' . esc_url( $url ) . '"><h2 class="post-type-title"><span class="icon dashicons dashicons-menu"></span><span class="text">' . __( 'Menus' ) . '</span></h2></a>';
            }
        
        }
        $html .= '<div class="aqpanel-devider"></div>';
    }
    
    // Main post list
    
    if ( !empty($aqpanel_options['aqpanel_posttypes']) ) {
        $has_one_or_more = false;
        foreach ( $aqpanel_options['aqpanel_posttypes'] as $post_type => $value ) {
            // Do not show products without WooCommerce
            if ( !class_exists( 'woocommerce' ) && $post_type == 'product' ) {
                continue;
            }
            $icon = '';
            switch ( $post_type ) {
                case 'post':
                    $icon = 'dashicons-admin-post';
                    break;
                case 'page':
                    $icon = 'dashicons-admin-page';
                    break;
                case 'product':
                    $icon = 'dashicons-admin-post';
                    break;
                default:
                    $icon = 'dashicons-admin-post';
                    break;
            }
            $query = new WP_Query( array(
                'post_type'  => $post_type,
                'meta_key'   => '_is_aqpanel_featured_post',
                'meta_value' => 'yes',
            ) );
            
            if ( $query->have_posts() ) {
                $has_one_or_more = true;
                $html .= '<div class="post-type-container"><h2 class="post-type-title"><span class="icon dashicons ' . $icon . '"></span><span class="text">' . esc_html( $post_type ) . 's</span></h2>';
                $html .= '<div class="link-list">';
                $html .= '<h4 class="sub-menu-title">' . esc_html( $post_type ) . 's</h4>';
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $id = get_the_ID();
                    $url = wp_nonce_url( admin_url( 'admin-ajax.php?action=aqpanel_featured_posts&post=' . $id . '&aqpanel_featured=no' ), 'aqpanel-featured-post' );
                    $html .= '
					<div class="aqpanel-post">
					<div class="aqpanel-post-container">';
                    $html .= '<a href="' . get_edit_post_link() . '" aria-label="Edit “' . get_the_title() . '”" class="thumbnail">' . get_the_post_thumbnail( $id, 'thumbnail' ) . '</a>';
                    $html .= '<span><a href="' . get_edit_post_link() . '" aria-label="Edit “' . get_the_title() . '”">' . get_the_title() . '</a></span>	
					<div class="aqpanel-post-footer">
					<span class="edit"><a href="' . get_edit_post_link() . '" aria-label="Edit “' . get_the_title() . '”">' . __( 'Edit', 'admin-quick-panel' ) . '</a> | </span>
					<span class="view"><a href="' . get_permalink() . '" aria-label="View “' . get_the_title() . '”">' . __( 'View', 'admin-quick-panel' ) . '</a></span>';
                    $html .= '<span class="aqpanel-delete"><a href="' . esc_url( $url ) . '" >X</a> </span>
					</div>											
					</div>
					</div>	
					';
                }
                $html .= '</div></div>';
            }
            
            wp_reset_query();
        }
        // Featured orders
        if ( class_exists( 'woocommerce' ) && array_key_exists( 'shop_order', $aqpanel_options['aqpanel_posttypes'] ) ) {
            
            if ( $aqpanel_options['aqpanel_posttypes']['shop_order'] == '1' ) {
                $customer_orders = get_posts( array(
                    'numberposts' => -1,
                    'post_type'   => 'shop_order',
                    'meta_key'    => '_is_aqpanel_featured_post',
                    'post_status' => array_keys( wc_get_order_statuses() ),
                ) );
                
                if ( !empty($customer_orders) ) {
                    $has_one_or_more = true;
                    // Going through each current customer orders
                    $html .= '<div class="post-type-container"><h2 class="post-type-title"><span class="icon dashicons dashicons-admin-post"></span><span class="text">' . __( 'Orders' ) . '</span></h2>';
                    $html .= '<ul class="link-list">';
                    $html .= '<h4 class="sub-menu-title">' . __( 'Orders' ) . '</h4>';
                    foreach ( $customer_orders as $customer_order ) {
                        // Getting Order ID, title and status
                        $order_id = $customer_order->ID;
                        $order_title = $customer_order->post_title;
                        $order_status = $customer_order->post_status;
                        $the_order = wc_get_order( $order_id );
                        $url = wp_nonce_url( admin_url( 'admin-ajax.php?action=aqpanel_featured_posts&post=' . esc_html( $order_id ) . '&aqpanel_featured=no' ), 'aqpanel-featured-post' );
                        $html .= '
						<div class="aqpanel-post">
						<div class="aqpanel-post-container">';
                        $html .= '<span><a href="' . esc_url( get_edit_post_link( $order_id ) ) . '" aria-label="Edit “' . esc_html( $order_title ) . '”">' . esc_html( $order_title ) . '</a></span><br>';
                        $html .= '<div class="order-footer">';
                        // Order status
                        $html .= sprintf( '<mark class="order-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $order_status ) ), esc_html( wc_get_order_status_name( $order_status ) ) ) . '<br>';
                        // Total price
                        $html .= esc_html( strip_tags( $the_order->get_formatted_order_total() ) ) . '<br>';
                        $html .= '<div class="aqpanel-post-footer"><span class="edit"><a href="' . esc_url( get_edit_post_link( $order_id ) ) . '" aria-label="Edit “' . esc_html( $order_title ) . '”">' . __( 'Edit', 'admin-quick-panel' ) . '</a></span>';
                        $html .= '<span class="aqpanel-delete"><a href="' . esc_url( $url ) . '" >X</a> </span>
						</div>
						</div>											
						</div>
						</div>';
                    }
                    $html .= '</ul>';
                    $html .= '</div>';
                }
            
            }
        
        }
        // Empty message
        if ( !$has_one_or_more ) {
            $html .= '<div class="aqpanel-empty">You can add posts here through post list column or through special post meta box.</div>';
        }
        $html .= '<span class="collapse-button-icon ' . $buttons_classes . '" aria-hidden="true"></span>';
    }
    
    return $html;
}
