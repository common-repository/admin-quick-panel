<?php
/**
 * Extra post columns and meta box. 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php 

if ( ! class_exists( 'Admin_Quick_Panel_Meta' ) ) :

	class Admin_Quick_Panel_Meta {
		/**
		 * Construct
	  	 */
		public function __construct() {
			// Metabox.
			add_action( 'add_meta_boxes', array( $this, 'add_featured_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'aqpanel_save_meta_box' ) );		
		}

		/**
	     * Add meta box in posts.
	     */
	    public function add_featured_meta_boxes(){
	    	global $typenow, $aqpanel_options;
	    	$allowed = array();
	    	foreach ( $aqpanel_options['aqpanel_posttypes'] as $post_type => $val ) {
	    		$allowed[] = $post_type;
	    	}
	    	if ( ! in_array($typenow,  $allowed )  ) {
	    		return;
	    	}
	    	$screens = $allowed;
	    	foreach ( $screens as $screen ) {
	    		add_meta_box(
	    			'aqpanel_meta_box_featured',
	    			__( 'Featured', 'featured-admin-panel' ),
	    			array( $this, 'aqpanel_meta_box_featured_callback' ),
	    			$screen,
	    			'side'
	    		);
	    	}
	    }

	    /**
	     * Featured meta box callback.
	     */
	    public function aqpanel_meta_box_featured_callback( $post ){
	    	$is_aqpanel_featured_post = get_post_meta( $post->ID, '_is_aqpanel_featured_post', true );
	    	wp_nonce_field( plugin_basename( __FILE__ ), 'aqpanel_featured_metabox_nonce' );
	    	?>
	    	<p>
	    		<label>
	    			<input type="hidden" name="aqpanel_settings[make_this_featured]" value="0" />
	    			<input type="checkbox" name="aqpanel_settings[make_this_featured]" value="yes" <?php checked( $is_aqpanel_featured_post, 'yes', true); ?> />
	    			<span class="small"><?php _e( 'Check this to place it to the featured panel.', 'featured-admin-panel' ); ?></span>
	    		</label>
	    	</p>
	    	<?php
	    }

	    /**
	     * Save Meta Box
	     */
	    public function aqpanel_save_meta_box( $post_id ){
	    	global $aqpanel_options;
	    	$allowed = array();
	    	foreach ( $aqpanel_options['aqpanel_posttypes'] as $post_type => $val ) {
	    		$allowed[] = $post_type;
	    	}
	    	if ( ! in_array( get_post_type( $post_id ),  $allowed )  ) {
	    		return $post_id;
	    	}

	      	// Bail if we're doing an auto save
	    	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	      	// if our nonce isn't there, or we can't verify it, bail
	    	if ( ! isset( $_POST['aqpanel_featured_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['aqpanel_featured_metabox_nonce'], plugin_basename( __FILE__ ) ) )
	    		return $post_id;

	      	// if our current user can't edit this post, bail
	    	if( ! current_user_can( 'edit_post' , $post_id ) )
	    		return $post_id;

	    	$featured_value = '';
	    	if ( isset( $_POST['aqpanel_settings']['make_this_featured'] ) && 'yes' == $_POST['aqpanel_settings']['make_this_featured'] ) {
	    		$featured_value = 'yes';
	    	}
	    	if ( 'yes' == $featured_value ) {
	    		update_post_meta( $post_id, '_is_aqpanel_featured_post', $featured_value );
	    	}
	    	else{
	    		delete_post_meta( $post_id, '_is_aqpanel_featured_post' );
	    	}
	    	return $post_id;

	    }	
			
	}

endif;

if ( class_exists( 'Admin_Quick_Panel_Meta', false ) ) {
	return new Admin_Quick_Panel_Meta();
}