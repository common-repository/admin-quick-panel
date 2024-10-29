<?php
/**
 * Right sidebar on admin. 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php 

if ( ! class_exists( 'Admin_Quick_Panel_Panel' ) ) :

	class Admin_Quick_Panel_Panel {
		/**
		 * Construct
	  	 */
		public function __construct() {
			// add right panel	
			add_action( 'in_admin_footer',  array( $this, 'aqpanel_add_admin_panel' ) );

			// add class for admin body 
			add_filter( 'admin_body_class', array($this, 'filter_admin_body_class'), 10, 1 ); 

		}

		/**
		 * Right panel load
		 */
		public function aqpanel_add_admin_panel($options){
			$aqpanel_classes = '';
			$is_aqpanel_show_thumbnails = '';
			if ( get_user_setting( 'aqpanel_collapsed' ) == 'true' ) {
				$aqpanel_classes = 'minimized';
			}
			if ( get_user_setting( 'aqpanel_show_thumbnails' ) == 'true' ) {
				$is_aqpanel_show_thumbnails = 'checked';
			}
			$html = '<div id="aqpanel" class="'.$aqpanel_classes.'">
			<div class="container">
			<div class="display-settings">				
				<label>
				<input type="checkbox" name="display_thumbnails" '.$is_aqpanel_show_thumbnails.'>
				Thumbnails</label>
			</div>
			';
			$html .= aqpanel_inside();
			$html .='
				</div>
			</div>';			
			echo $html;
		}

		/**
		 * Add closed class to admin body
		 */
	    public function filter_admin_body_class( $array ) {
	    	//$body_classes = array();
	    	$aqpanel_closed = '';
	    	$aqpanel_show_thumbnails = '';
	    	if ( get_user_setting( 'aqpanel_collapsed' ) == 'true' ) {
	    		$aqpanel_closed = ' aqpanel-closed';
	    	} 
	    	if ( get_user_setting( 'aqpanel_show_thumbnails' ) == 'true' ) {
	    		$aqpanel_show_thumbnails = ' aqpanel-show-thumbnails';
	    	}
			//array_merge($body_classes,$array );
	    	return "$array".$aqpanel_closed.$aqpanel_show_thumbnails; 
	    }	    

	}

endif;

if ( class_exists( 'Admin_Quick_Panel_Panel', false ) ) {
	return new Admin_Quick_Panel_Panel();
}