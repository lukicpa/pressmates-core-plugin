<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.pressmates.net
 * @since      1.0.0
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/admin
 * @author     Darko Lukic <lukic.pa@gmail.com>
 */
class Pressmates_Core_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Slug of the plugin screen.
     * @access   private
     * @var      string
     */
    private $plugin_screen_hook_suffix = null;

    /**
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
     */
    private $option_name = 'pressmates_core';

    /**
     * Custom Post Type Checkboxes
     * @var array
     */
    private $cpt_checkboxes = array();

    /**
     * Custom post type name - should be singular and in lover-case
     * @var
     */
    private static $cpt_name;
    private static $cpt_plural_name;
    private static $cpt_singular_name;
    private static $cpt_capability_type;
    private static $cpt_options = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name = "", $version="" ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->set_cpt_checkboxes();
    }

    /**
     * List of checkboxes for Custom post type to be shown on settings page
     */
    protected function set_cpt_checkboxes(){
        $this->cpt_checkboxes = array(
            'portfolio'     => esc_html__( 'Portfolio', 'pressmates-core' ),
            'services'      => esc_html__( 'Services', 'pressmates-core' ),
            'slider'        => esc_html__( 'Slider', 'pressmates-core' ),
            'team'          => esc_html__( 'Team', 'pressmates-core' ),
            'advertisement' => esc_html__( 'Advertisement', 'pressmates-core' )
        );
    }

    /**
     * Set plugin default settings on plugin activation
     */
    public function set_default_settings_on_plugin_activation() {

        // Check if option added
        if ( !get_option($this->option_name) ) {

            //Loop through each setting and add it default value
            $options_array = array();
            foreach ($this->cpt_checkboxes as $type => $desc) {
                $options_array = array_merge($options_array, array($type => "on"));
            }

            //Update plugin option
            update_option($this->option_name, $options_array);
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pressmates_Core_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pressmates_Core_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pressmates-core-admin.css', array(), $this->version, 'all' );

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pressmates_Core_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pressmates_Core_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pressmates-core-admin.js', array( 'jquery' ), $this->version, false );

    }

    /**
     * Add an Plugin options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_plugin_options_page() {


        $this->plugin_screen_hook_suffix = add_options_page(
            esc_html__( 'PressMates Core Plugin Settings', 'pressmates-core' ),
            esc_html__( 'PressMates Settings', 'pressmates-core' ),
            'manage_options',
            $this->plugin_name,
            array( $this, 'display_plugin_options_page' )
        );

    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function display_plugin_options_page() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/pressmates-core-admin-settings.php';
    }

    /**
     * Register settings
     */
    public function register_setting() {
        // Add a General section
        add_settings_section(
            $this->option_name . '_general_section',
            esc_html__( 'General', 'pressmates-core' ),
            array( $this, $this->option_name . '_settings_text' ),
            $this->plugin_name
        );

        register_setting(
            $this->plugin_name,
            $this->option_name
        );

        foreach ( $this->cpt_checkboxes as $type => $desc )
        {
            $handle   = $this->option_name . "_$type";
            $args     = array (
                'label_for' => $handle,
                'type'      => $type
            );
            $callback = array ( $this, 'print_input_field' );

            add_settings_field(
                $handle,
                $desc,
                $callback,
                $this->plugin_name,
                $this->option_name . '_general_section',
                $args
            );
        }
    }

    /**
     * Render the text for the general section
     *
     * @since  1.0.0
     */
    public function pressmates_core_settings_text() {
        printf( '<p>%1$s</p>',
            esc_html( 'Select Custom Post Types you need.', 'pressmates-core' )
        );

    }

    /**
     * Generate fields for settings page
     * @param array $args
     */
    public function print_input_field( array $args )
    {
        $type   = $args['type'];
        $id     = $args['label_for'];
        $data   = get_option( $this->option_name, array() );
        $name   = $this->option_name . '[' . $type . ']';


        printf( '<input type="checkbox" name="%1$s" id="%2$s" class="" %3$s /> <label for="%4$s">Enable %5$s Custom Post Type</label>',
            $name,
            $id,
            checked( isset( $data[$type] ), true, false ),
            $id,
            esc_html( strtoupper( $type ) )
        );
    }

    /**
     * Settings for creating Portfolio Custom Post Type
     */
    public static function create_cpt_portfolio() {
        self::$cpt_name = 'portfolio';
        self::$cpt_capability_type = 'post';
        self::$cpt_plural_name = 'Portfolio';
        self::$cpt_singular_name = 'Portfolio';

        self::$cpt_options['can_export']            = true;
        self::$cpt_options['capability_type']       = self::$cpt_capability_type;
        self::$cpt_options['description']           = '';
        self::$cpt_options['exclude_from_search']   = false;
        self::$cpt_options['has_archive']           = false;
        self::$cpt_options['hierarchical']          = false;
        self::$cpt_options['map_meta_cap']          = true;
        self::$cpt_options['menu_icon']             = 'dashicons-portfolio';
        self::$cpt_options['menu_position']	        = 26;
        self::$cpt_options['public']                = true;
        self::$cpt_options['publicly_querable']     = true;
        self::$cpt_options['query_var']             = true;
        self::$cpt_options['register_meta_box_cb']  = '';
        self::$cpt_options['rewrite']               = false;
        self::$cpt_options['show_in_admin_bar']     = true;
        self::$cpt_options['show_in_menu']          = true;
        self::$cpt_options['show_in_nav_menu']      = true;
        self::$cpt_options['show_ui']               = true;
        self::$cpt_options['supports']              = array( 'title', 'editor', 'thumbnail' );
        self::$cpt_options['taxonomies']            = array();

        self::$cpt_options['capabilities']['delete_others_posts']	    = "delete_others_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['delete_post']			    = "delete_" . self::$cpt_capability_type;
        self::$cpt_options['capabilities']['delete_posts']			    = "delete_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['delete_private_posts']	    = "delete_private_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['delete_published_posts']	= "delete_published_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_others_posts']		    = "edit_others_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_post']				    = "edit_" . self::$cpt_capability_type;
        self::$cpt_options['capabilities']['edit_posts']				= "edit_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_private_posts']		= "edit_private_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_published_posts']	    = "edit_published_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['publish_posts']			    = "publish_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['read_post']				    = "read_" . self::$cpt_capability_type;
        self::$cpt_options['capabilities']['read_private_posts']		= "read_private_" . self::$cpt_capability_type . "s";

        self::$cpt_options['labels']['add_new']             = esc_html__( "Add New " . self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['add_new_item']        = esc_html__( "Add New " . self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['all_items']           = esc_html__( self::$cpt_plural_name, 'pressmates-core' );
        self::$cpt_options['labels']['edit_item']           = esc_html__( "Edit " . self::$cpt_singular_name , 'pressmates-core' );
        self::$cpt_options['labels']['menu_name']           = esc_html__( self::$cpt_plural_name, 'pressmates-core' );
        self::$cpt_options['labels']['name']                = esc_html__( self::$cpt_plural_name, 'pressmates-core' );
        self::$cpt_options['labels']['name_admin_bar']      = esc_html__( self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['new_item']            = esc_html__( "New " . self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['not_found']           = esc_html__( "No " . self::$cpt_plural_name . " Found", 'pressmates-core' );
        self::$cpt_options['labels']['not_found_in_trash']  = esc_html__( "No " . self::$cpt_plural_name . " Found in Trash", 'pressmates-core' );
        self::$cpt_options['labels']['parent_item_colon']   = esc_html__( "Parent " . self::$cpt_plural_name . " :", 'pressmates-core' );
        self::$cpt_options['labels']['search_items']        = esc_html__( "Search " . self::$cpt_plural_name , 'pressmates-core' );
        self::$cpt_options['labels']['singular_name']       = esc_html__( self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['view_item']           = esc_html__( "View " . self::$cpt_singular_name, 'pressmates-core' );

        self::$cpt_options['rewrite']['ep_mask']        = EP_PERMALINK;
        self::$cpt_options['rewrite']['feeds']  		= false;
        self::$cpt_options['rewrite']['pages']	    	= true;
        self::$cpt_options['rewrite']['slug']			= esc_html__( strtolower( self::$cpt_plural_name ), 'pressmates-core' );
        self::$cpt_options['rewrite']['with_front']	    = false;

        self::register_cpt(self::$cpt_name, self::$cpt_options);
    }

    /**
     * Settings for creating Services Custom Post Type
     */
    public static function create_cpt_services() {
        self::$cpt_name = 'service';
        self::$cpt_capability_type = 'post';
        self::$cpt_plural_name = 'Services';
        self::$cpt_singular_name = 'Services';

        self::$cpt_options['can_export']            = true;
        self::$cpt_options['capability_type']       = self::$cpt_capability_type;
        self::$cpt_options['description']           = '';
        self::$cpt_options['exclude_from_search']   = false;
        self::$cpt_options['has_archive']           = false;
        self::$cpt_options['hierarchical']          = false;
        self::$cpt_options['map_meta_cap']          = true;
        self::$cpt_options['menu_icon']             = 'dashicons-portfolio';
        self::$cpt_options['menu_position']	        = 26;
        self::$cpt_options['public']                = true;
        self::$cpt_options['publicly_querable']     = true;
        self::$cpt_options['query_var']             = true;
        self::$cpt_options['register_meta_box_cb']  = '';
        self::$cpt_options['rewrite']               = false;
        self::$cpt_options['show_in_admin_bar']     = true;
        self::$cpt_options['show_in_menu']          = true;
        self::$cpt_options['show_in_nav_menu']      = true;
        self::$cpt_options['show_ui']               = true;
        self::$cpt_options['supports']              = array( 'title', 'editor', 'thumbnail' );
        self::$cpt_options['taxonomies']            = array();

        self::$cpt_options['capabilities']['delete_others_posts']	    = "delete_others_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['delete_post']			    = "delete_" . self::$cpt_capability_type;
        self::$cpt_options['capabilities']['delete_posts']			    = "delete_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['delete_private_posts']	    = "delete_private_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['delete_published_posts']	= "delete_published_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_others_posts']		    = "edit_others_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_post']				    = "edit_" . self::$cpt_capability_type;
        self::$cpt_options['capabilities']['edit_posts']				= "edit_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_private_posts']		= "edit_private_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['edit_published_posts']	    = "edit_published_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['publish_posts']			    = "publish_" . self::$cpt_capability_type . "s";
        self::$cpt_options['capabilities']['read_post']				    = "read_" . self::$cpt_capability_type;
        self::$cpt_options['capabilities']['read_private_posts']		= "read_private_" . self::$cpt_capability_type . "s";

        self::$cpt_options['labels']['add_new']             = esc_html__( "Add New " . self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['add_new_item']        = esc_html__( "Add New " . self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['all_items']           = esc_html__( self::$cpt_plural_name, 'pressmates-core' );
        self::$cpt_options['labels']['edit_item']           = esc_html__( "Edit " . self::$cpt_singular_name , 'pressmates-core' );
        self::$cpt_options['labels']['menu_name']           = esc_html__( self::$cpt_plural_name, 'pressmates-core' );
        self::$cpt_options['labels']['name']                = esc_html__( self::$cpt_plural_name, 'pressmates-core' );
        self::$cpt_options['labels']['name_admin_bar']      = esc_html__( self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['new_item']            = esc_html__( "New " . self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['not_found']           = esc_html__( "No " . self::$cpt_plural_name . " Found", 'pressmates-core' );
        self::$cpt_options['labels']['not_found_in_trash']  = esc_html__( "No " . self::$cpt_plural_name . " Found in Trash", 'pressmates-core' );
        self::$cpt_options['labels']['parent_item_colon']   = esc_html__( "Parent " . self::$cpt_plural_name . " :", 'pressmates-core' );
        self::$cpt_options['labels']['search_items']        = esc_html__( "Search " . self::$cpt_plural_name , 'pressmates-core' );
        self::$cpt_options['labels']['singular_name']       = esc_html__( self::$cpt_singular_name, 'pressmates-core' );
        self::$cpt_options['labels']['view_item']           = esc_html__( "View " . self::$cpt_singular_name, 'pressmates-core' );

        self::$cpt_options['rewrite']['ep_mask']        = EP_PERMALINK;
        self::$cpt_options['rewrite']['feeds']  		= false;
        self::$cpt_options['rewrite']['pages']	    	= true;
        self::$cpt_options['rewrite']['slug']			= esc_html__( strtolower( self::$cpt_plural_name ), 'pressmates-core' );
        self::$cpt_options['rewrite']['with_front']	    = false;

        self::register_cpt(self::$cpt_name, self::$cpt_options);
    }

    /**
     * Register Custom Post Type based on provided settings
     * @param $cpt_name
     * @param $options
     */
    public static function register_cpt( $cpt_name, $options ) {
        //Allow users to add additional options
        $cpt_options = apply_filters( $cpt_name, $options );

        //Create CPT
        register_post_type( strtolower( $cpt_name ), $cpt_options );
    }

    /**
     * Unregister custom post type if user deactivate it from admin setting page
     * @param $cpt_name
     */
    public static function unregister_cpt( $cpt_name ){
        unregister_post_type( $cpt_name );
    }
}
