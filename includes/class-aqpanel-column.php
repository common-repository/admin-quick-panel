<?php
/**
 * Extra post columns and meta box. 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php 

if ( ! class_exists( 'Admin_Quick_Panel_Column' ) ) :

	class Admin_Quick_Panel_Column {
		/**
		 * Construct
	  	 */
		public function __construct() {
			// column
			add_action( 'admin_init', array($this, 'aqpanel_featured_posts_add_columns_head'));
			add_action( 'wp_ajax_aqpanel_featured_posts', array( $this, 'feature_post' ) );	
		}

		/**
	     * Add heading in the featured column.
	     */
	    public function add_featured_column_heading( $columns ){
	        $columns['aqpanel_featured_posts_col'] = __( 'Featured', 'aqpanel-featured-posts' );
	        return $columns;
	    }

	    /**
	     * Add column content in the featured column.
	     */
	    public function add_featured_column_content( $column, $id ){
	        if ( $column == 'aqpanel_featured_posts_col' ){
	          $class = '';
	          $aqpanel_featured = get_post_meta( $id, '_is_aqpanel_featured_post', true );
	          if ( $aqpanel_featured == 'yes' ){
	          	$aqpanel_featured_reverse = 'no';
	          } 
	          else{
	          	$aqpanel_featured_reverse = 'yes';
	          }
	          $url = wp_nonce_url( admin_url( 'admin-ajax.php?action=aqpanel_featured_posts&post='.$id.'&aqpanel_featured='.$aqpanel_featured_reverse), 'aqpanel-featured-post'  );
	          $classes = array('aqpanel_featured_posts_icon');
	          if ('yes' == $aqpanel_featured) {
	              $classes[] = 'selected';
	          }
	          echo  '<a href="'.esc_url( $url ).'" id="btn-post-featured_'.esc_attr($id).'" class="'.implode(' ', $classes).'"><span class="dashicons dashicons-sticky"></span></a>';
	        }
	    }
		
		/**
		 * Toggle Featured status of a product from admin.
		 */
		public static function feature_post() {
			if ( current_user_can( 'edit_posts' ) && check_admin_referer( 'aqpanel-featured-post' ) && isset( $_GET['post'] ) ) {
				$aqpanel_featured = sanitize_title( $_GET['aqpanel_featured'] );
				$id = sanitize_title( (int)$_GET['post'] );
				if( !empty( $id ) && $aqpanel_featured !== NULL ) {
					if ( $aqpanel_featured == 'no' ){
						delete_post_meta( $id, "_is_aqpanel_featured_post" );
					}
					else {
						update_post_meta( $id, "_is_aqpanel_featured_post", 'yes' );
					}
				}
			}
			wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php' ) );
			exit;
		}

		/**
	     * Add columns to the listing.
	     */
		public function aqpanel_featured_posts_add_columns_head(){
			global $aqpanel_options;
			foreach ( $aqpanel_options['aqpanel_posttypes'] as $post_type => $value ) {
				add_filter('manage_edit-'.$post_type.'_columns', array( $this,'add_featured_column_heading'), 2);
				add_action('manage_'.$post_type.'_posts_custom_column', array( $this,'add_featured_column_content'), 10, 2);
			}
		}

	}

endif;

if ( class_exists( 'Admin_Quick_Panel_Column', false ) ) {
	return new Admin_Quick_Panel_Column();
}