<?php

/**
 * Extra post columns and meta box. 
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !class_exists( 'Admin_Quick_Panel_Options' ) ) {
    class Admin_Quick_Panel_Options
    {
        /**
         * Construct
         */
        public function __construct()
        {
            // add the options page and menu item.
            add_action( 'admin_menu', array( $this, 'aqpanel_featured_posts_add_plugin_admin_menu' ) );
            //settings
            add_action( 'admin_init', array( $this, 'plugin_register_settings' ) );
        }
        
        /**
         * Register the options menu.
         */
        public function aqpanel_featured_posts_add_plugin_admin_menu()
        {
            $this->plugin_screen_hook_suffix = add_options_page(
                __( 'Admin Quick Panel', 'admin-quick-panel' ),
                __( 'Admin Quick Panel', 'admin-quick-panel' ),
                'manage_options',
                'admin-quick-panel',
                array( $this, 'display_plugin_admin_page' )
            );
            /* $this->plugin_screen_hook_suffix = add_menu_page(
                       __('Featured Admin Panel', 'aqpanel-featured-posts'), __('Featured Admin Panel', 'admin-quick-panel'), 'manage_options', 'aqpanel-featured-posts', array($this, 'display_plugin_admin_page')
               );	*/
        }
        
        /**
         * Render the settings page for this plugin.
         */
        public function display_plugin_admin_page()
        {
            // Check that the user is allowed to update options
            if ( !current_user_can( 'manage_options' ) ) {
                wp_die( 'You do not have sufficient permissions to access this page.' );
            }
            ?>
	    	<form action="options.php" method="post">
	    		<?php 
            settings_fields( 'aqpanel-plugin-options-group' );
            ?>
	    		<?php 
            do_settings_sections( 'aqpanel-featured-posts-main' );
            ?>
	    		<?php 
            submit_button( __( 'Save Changes', 'admin-quick-panel' ) );
            ?>
	    		<?php 
            do_settings_sections( 'aqpanel-pro-info' );
            ?>
	    	</form>
	    	<?php 
        }
        
        /**
         * Plugin settings
         */
        public function plugin_register_settings()
        {
            register_setting( 'aqpanel-plugin-options-group', 'aqpanel_plugin_options', array( $this, 'aqpanel_featured_posts_plugin_options_validate' ) );
            add_settings_section(
                'main_settings',
                __( 'Plugin Settings', 'admin-quick-panel' ),
                array( $this, 'aqpanel_featured_posts_plugin_section_text_callback' ),
                'aqpanel-featured-posts-main'
            );
            // Post types
            add_settings_field(
                'aqpanel_posttypes',
                __( 'Enable Featured', 'admin-quick-panel' ),
                array( $this, 'aqpanel_posttypes_callback' ),
                'aqpanel-featured-posts-main',
                'main_settings'
            );
            // Recently edited posts and pages
            add_settings_field(
                'aqpanel_additional',
                __( 'Additional', 'admin-quick-panel' ),
                array( $this, 'aqpanel_additional_callback' ),
                'aqpanel-featured-posts-main',
                'main_settings'
            );
            // Pro info
            add_settings_section(
                'main_settings',
                __( 'Pro info', 'admin-quick-panel' ),
                array( $this, 'aqpanel_pro_info_callback' ),
                'aqpanel-pro-info'
            );
        }
        
        /**
         * Options description.
         */
        public function aqpanel_pro_info_callback( $description )
        {
            echo  '
	    	<div class="aqpanel-pro-info">
		    	<p>Want more features?</p>
		    	<p>
		    		<ul>
		    			<li>Find link for finding in a post list.</li>
		    			<li>Notes.</li>
		    		</ul>
		    	</p>
		    	<a href="https://businessupwebsite.com/introducing-the-admin-quick-panel/" target="_blank">Learn More</a>
	    	</div>' ;
        }
        
        /**
         * Add empty array if doesn't exist.
         */
        public function aqpanel_featured_posts_plugin_options_validate( $input )
        {
            if ( !isset( $input['aqpanel_posttypes'] ) ) {
                $input['aqpanel_posttypes'] = array();
            }
            if ( !isset( $input['aqpanel_additional'] ) ) {
                $input['aqpanel_additional'] = array();
            }
            return $input;
        }
        
        /**
         * Options description.
         */
        public function aqpanel_featured_posts_plugin_section_text_callback( $description )
        {
            echo  "Choose the types of posts that can be attached to the side panel." ;
        }
        
        /**
         * Post Types.
         */
        public function aqpanel_additional_callback()
        {
            global  $aqpanel_options ;
            ?>
			<p>
				<label><input type="checkbox" name="aqpanel_plugin_options[aqpanel_additional][aqpanel_recently_edited]" value="1"
					<?php 
            checked( isset( $aqpanel_options['aqpanel_additional']['aqpanel_recently_edited'] ) && 1 == $aqpanel_options['aqpanel_additional']['aqpanel_recently_edited'] );
            ?> /><?php 
            _e( "Recently edited", 'admin-quick-panel' );
            ?>
				</label>
			</p>
			<p>
				<label><input type="checkbox" name="aqpanel_plugin_options[aqpanel_additional][aqpanel_widgets_button]" value="1"
					<?php 
            checked( isset( $aqpanel_options['aqpanel_additional']['aqpanel_widgets_button'] ) && 1 == $aqpanel_options['aqpanel_additional']['aqpanel_widgets_button'] );
            ?> /><?php 
            _e( "Widgets", 'admin-quick-panel' );
            ?>
				</label>
			</p>
			<p>
				<label><input type="checkbox" name="aqpanel_plugin_options[aqpanel_additional][aqpanel_menus_button]" value="1"
					<?php 
            checked( isset( $aqpanel_options['aqpanel_additional']['aqpanel_menus_button'] ) && 1 == $aqpanel_options['aqpanel_additional']['aqpanel_menus_button'] );
            ?> /><?php 
            _e( "Menus", 'admin-quick-panel' );
            ?>
				</label>
			</p>
			<?php 
        }
        
        /**
         * Post Types.
         */
        public function aqpanel_posttypes_callback()
        {
            global  $aqpanel_options ;
            ?>
			<p>
				<label><input type="checkbox" name="aqpanel_plugin_options[aqpanel_posttypes][post]" value="1"
					<?php 
            checked( isset( $aqpanel_options['aqpanel_posttypes']['post'] ) && 1 == $aqpanel_options['aqpanel_posttypes']['post'] );
            ?> /><?php 
            _e( "Post", 'admin-quick-panel' );
            ?>
				</label>
			</p>
			<p>
				<label><input type="checkbox" name="aqpanel_plugin_options[aqpanel_posttypes][page]" value="1"
					<?php 
            checked( isset( $aqpanel_options['aqpanel_posttypes']['page'] ) && 1 == $aqpanel_options['aqpanel_posttypes']['page'] );
            ?> /><?php 
            _e( "Page", 'admin-quick-panel' );
            ?>
				</label>
			</p>
			<?php 
            $args = array(
                'public'   => true,
                '_builtin' => false,
            );
            $post_types_custom = get_post_types( $args, 'objects' );
            if ( !empty($post_types_custom) ) {
                foreach ( $post_types_custom as $key => $ptype ) {
                    $name = $ptype->labels->{'name'};
                    ?>
					<p>
						<label><input type="checkbox" name="aqpanel_plugin_options[aqpanel_posttypes][<?php 
                    echo  esc_html( $key ) ;
                    ?>]" value="1"
							<?php 
                    checked( isset( $aqpanel_options['aqpanel_posttypes'][$key] ) && 1 == $aqpanel_options['aqpanel_posttypes'][$key] );
                    ?> /><?php 
                    echo  esc_html( $name ) ;
                    ?>
						</label>
					</p>
					<?php 
                }
            }
            $post_types_extra = array(
                'shop_order' => 'Orders',
            );
            if ( !empty($post_types_extra) && class_exists( 'woocommerce' ) ) {
                foreach ( $post_types_extra as $key => $ptype ) {
                    ?>
					<p>
						<label><input type="checkbox" name="aqpanel_plugin_options[aqpanel_posttypes][<?php 
                    echo  esc_html( $key ) ;
                    ?>]" value="1"
							<?php 
                    checked( isset( $aqpanel_options['aqpanel_posttypes'][$key] ) && 1 == $aqpanel_options['aqpanel_posttypes'][$key] );
                    ?> /><?php 
                    echo  esc_html( $ptype ) ;
                    ?>
						</label>
					</p>
					<?php 
                }
            }
        }
    
    }
}
if ( class_exists( 'Admin_Quick_Panel_Options', false ) ) {
    return new Admin_Quick_Panel_Options();
}