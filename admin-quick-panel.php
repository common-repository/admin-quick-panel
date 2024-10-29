<?php

/**
 * Plugin Name: Admin Quick Panel
 * Plugin URI:  https://wordpress.org/plugins/admin-quick-panel
 * Description: Adds a menu to the right side. Add your favorite posts and other useful buttons there for quick access.
 * Author: Ivan Chernyakov
 * Author URI: https://businessupwebsite.com
 * Version: 1.2.6
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: admin-quick-panel
 */
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( !function_exists( 'aqpanel_fs' ) ) {
    // Create a helper function for easy SDK access.
    function aqpanel_fs()
    {
        global  $aqpanel_fs ;
        
        if ( !isset( $aqpanel_fs ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $aqpanel_fs = fs_dynamic_init( array(
                'id'             => '4178',
                'slug'           => 'admin-quick-panel',
                'type'           => 'plugin',
                'public_key'     => 'pk_b7733b24e49113c9dca29b1a016df',
                'is_premium'     => false,
                'premium_suffix' => 'Pro',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'    => 'admin-quick-panel',
                'contact' => false,
                'support' => false,
                'parent'  => array(
                'slug' => 'options-general.php',
            ),
            ),
                'is_live'        => true,
            ) );
        }
        
        return $aqpanel_fs;
    }
    
    // Init Freemius.
    aqpanel_fs();
    // Signal that SDK was initiated.
    do_action( 'aqpanel_fs_loaded' );
}

/**
 * Main Class
 */

if ( !class_exists( 'Admin_Quick_Panel' ) ) {
    class Admin_Quick_Panel
    {
        /**
         * Instance of this class.
         */
        protected static  $instance = null ;
        /**
         * Version
         */
        protected  $version ;
        public function __construct()
        {
            
            if ( is_admin() ) {
                // get data
                $this->version = '1.2.6';
                $this->aqpanel_get_data();
                $this->aqpanel_includes();
                // add scripts
                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
                // add links to plugin list
                add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'aqpanel_add_plugin_settings_link' ) );
            }
        
        }
        
        /**
         * Includes
         */
        private function aqpanel_includes()
        {
            include_once plugin_dir_path( __FILE__ ) . 'functions/aqpanel-core-functions.php';
            include_once plugin_dir_path( __FILE__ ) . 'includes/class-aqpanel-options.php';
            include_once plugin_dir_path( __FILE__ ) . 'includes/class-aqpanel-meta.php';
            include_once plugin_dir_path( __FILE__ ) . 'includes/class-aqpanel-column.php';
            include_once plugin_dir_path( __FILE__ ) . 'includes/class-aqpanel-panel.php';
        }
        
        /**
         * Get data
         */
        public function aqpanel_get_data()
        {
            global  $aqpanel_options ;
            $default_options = array(
                'aqpanel_posttypes'  => array(
                'post'       => 1,
                'page'       => 1,
                'product'    => 1,
                'shop_order' => 1,
            ),
                'aqpanel_additional' => array(
                'aqpanel_recently_edited' => 1,
                'aqpanel_widgets_button'  => 1,
                'aqpanel_menus_button'    => 1,
            ),
            );
            $aqpanel_options = array_merge( $default_options, (array) get_option( 'aqpanel_plugin_options', array() ) );
        }
        
        /**
         * Include admin scripts and styles.
         */
        public function enqueue_admin_scripts()
        {
            wp_enqueue_script(
                'aqpanel-admin-js',
                plugin_dir_url( __FILE__ ) . 'assets/js/admin.js',
                array( 'jquery', 'wp-color-picker' ),
                $this->version,
                false
            );
            wp_enqueue_style(
                'aqpanel-admin-css',
                plugin_dir_url( __FILE__ ) . 'assets/css/admin.css',
                array(),
                $this->version,
                false
            );
            wp_enqueue_style( 'wp-color-picker' );
        }
        
        /**
         * Return an instance of this class.
         */
        public static function get_instance()
        {
            // If the single instance hasn't been set, set it now.
            if ( null == self::$instance ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        
        /**
         * Add settings action link to the plugins page.
         */
        public function aqpanel_add_plugin_settings_link( $links )
        {
            $new_links = '<a href="' . esc_url( admin_url( 'options-general.php?page=admin-quick-panel' ) ) . '">' . __( 'Settings', 'admin-quick-panel' ) . '</a>';
            array_unshift( $links, $new_links );
            return $links;
        }
    
    }
    /**
     * Install plugin default options.
     */
    add_action( 'plugins_loaded', array( 'Admin_Quick_Panel', 'get_instance' ) );
}
