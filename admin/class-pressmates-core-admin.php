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
    private static $cpt_checkboxes = array();

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
    protected static function set_cpt_checkboxes( $args = array() ){

        //$this->cpt_checkboxes = $args;
        if ( !empty( $args ) ) {
            array_push( self::$cpt_checkboxes, $args );
        }
    }

    /**
     * Set plugin default settings on plugin activation
     */
    public function set_default_settings_on_plugin_activation() {

        // Check if option added
        if ( !get_option($this->option_name) ) {

            //Loop through each setting and add it default value
            $options_array = array();
            foreach (self::$cpt_checkboxes as $type => $desc) {
                $options_array = array_merge($options_array, array($desc['slug'] => "on"));
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

        foreach ( self::$cpt_checkboxes as $type => $desc )
        {
            $handle   = $this->option_name . "_" . $desc['slug'];
            $args     = array (
                'label_for' => $handle,
                'type'      => $desc['slug']
            );
            $callback = array ( $this, 'print_input_field' );

            add_settings_field(
                $handle,
                $desc['name'],
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
     * Preparing CPT
     */
    public static function prepare_cpt($cpt_name, $cpt_capability_type, $cpt_plural_name, $cpt_singular_name, $cpt_menu_icon = 'dashicons-media-default', $cpt_menu_position = 25, $cpt_options = array()) {

        $cpt_options['can_export']            = isset($cpt_options['can_export'])           ? $cpt_options['can_export']            : true;
        $cpt_options['capability_type']       = $cpt_capability_type;
        $cpt_options['description']           = isset($cpt_options['description'])          ? $cpt_options['description']           : '';
        $cpt_options['exclude_from_search']   = isset($cpt_options['exclude_from_search'])  ? $cpt_options['exclude_from_search']   : false;
        $cpt_options['has_archive']           = isset($cpt_options['has_archive'])          ? $cpt_options['has_archive']           : true;
        $cpt_options['hierarchical']          = isset($cpt_options['hierarchical'])         ? $cpt_options['hierarchical']          : false;
        $cpt_options['map_meta_cap']          = isset($cpt_options['map_meta_cap'])         ? $cpt_options['map_meta_cap']          : true;
        $cpt_options['menu_icon']             = $cpt_menu_icon;
        $cpt_options['menu_position']	      = $cpt_menu_position;
        $cpt_options['public']                = isset($cpt_options['public'])               ? $cpt_options['public']                : true;
        $cpt_options['publicly_querable']     = isset($cpt_options['publicly_querable'])    ? $cpt_options['publicly_querable']     : true;
        $cpt_options['query_var']             = isset($cpt_options['query_var'])            ? $cpt_options['query_var']             : true;
        $cpt_options['register_meta_box_cb']  = isset($cpt_options['register_meta_box_cb']) ? $cpt_options['register_meta_box_cb']  : '';
        $cpt_options['rewrite']               = isset($cpt_options['rewrite'])              ? $cpt_options['rewrite']               : false;
        $cpt_options['show_in_admin_bar']     = isset($cpt_options['show_in_admin_bar'])    ? $cpt_options['show_in_admin_bar']     : true;
        $cpt_options['show_in_menu']          = isset($cpt_options['show_in_menu'])         ? $cpt_options['show_in_menu']          : true;
        $cpt_options['show_in_nav_menu']      = isset($cpt_options['show_in_nav_menu'])     ? $cpt_options['show_in_nav_menu']      : true;
        $cpt_options['show_ui']               = isset($cpt_options['show_ui'])              ? $cpt_options['show_ui']               : true;
        $cpt_options['supports']              = isset($cpt_options['supports'])             ? $cpt_options['supports']              : array( 'title', 'editor', 'thumbnail' );
        $cpt_options['taxonomies']            = isset($cpt_options['taxonomies'])           ? $cpt_options['taxonomies']            : array();

        $cpt_options['capabilities']['delete_others_posts']	    = isset($cpt_options['capabilities']['delete_others_posts'])    ? $cpt_options['capabilities']['delete_others_posts']       : "delete_others_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['delete_post']			    = isset($cpt_options['capabilities']['delete_post'])            ? $cpt_options['capabilities']['delete_post']               : "delete_" . $cpt_capability_type;
        $cpt_options['capabilities']['delete_posts']			= isset($cpt_options['capabilities']['delete_posts'])           ? $cpt_options['capabilities']['delete_posts']              : "delete_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['delete_private_posts']	= isset($cpt_options['capabilities']['delete_private_posts'])   ? $cpt_options['capabilities']['delete_private_posts']      : "delete_private_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['delete_published_posts']	= isset($cpt_options['capabilities']['delete_published_posts']) ? $cpt_options['capabilities']['delete_published_posts']    : "delete_published_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['edit_others_posts']		= isset($cpt_options['capabilities']['edit_others_posts'])      ? $cpt_options['capabilities']['edit_others_posts']         : "edit_others_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['edit_post']				= isset($cpt_options['capabilities']['edit_post'])              ? $cpt_options['capabilities']['edit_post']                 : "edit_" . $cpt_capability_type;
        $cpt_options['capabilities']['edit_posts']				= isset($cpt_options['capabilities']['edit_posts'])             ? $cpt_options['capabilities']['edit_posts']                : "edit_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['edit_private_posts']		= isset($cpt_options['capabilities']['edit_private_posts'])     ? $cpt_options['capabilities']['edit_private_posts']        : "edit_private_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['edit_published_posts']	= isset($cpt_options['capabilities']['edit_published_posts'])   ? $cpt_options['capabilities']['edit_published_posts']      : "edit_published_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['publish_posts']			= isset($cpt_options['capabilities']['publish_posts'])          ? $cpt_options['capabilities']['publish_posts']             : "publish_" . $cpt_capability_type . "s";
        $cpt_options['capabilities']['read_post']				= isset($cpt_options['capabilities']['read_post'])              ? $cpt_options['capabilities']['read_post']                 : "read_" . $cpt_capability_type;
        $cpt_options['capabilities']['read_private_posts']		= isset($cpt_options['capabilities']['read_private_posts'])     ? $cpt_options['capabilities']['read_private_posts']        : "read_private_" . $cpt_capability_type . "s";

        $cpt_options['labels']['add_new']             =
            isset($cpt_options['labels']['add_new']) ? $cpt_options['labels']['add_new'] :
                sprintf(
                    esc_html__( 'Add New %s', 'pressmates-core' ),
                    $cpt_singular_name
                );
        $cpt_options['labels']['add_new_item']        =
            isset($cpt_options['labels']['add_new_item']) ? $cpt_options['labels']['add_new_item'] :
                sprintf(
                    esc_html__( 'Add New %s', 'pressmates-core' ),
                    $cpt_singular_name
                );
        $cpt_options['labels']['all_items']           =
            isset($cpt_options['labels']['all_items']) ? $cpt_options['labels']['all_items'] :
                sprintf(
                    esc_html__( '%s', 'pressmates-core' ),
                    $cpt_plural_name
                );
        $cpt_options['labels']['edit_item']           =
            isset($cpt_options['labels']['edit_item']) ? $cpt_options['labels']['edit_item'] :
                sprintf(
                    esc_html__( 'Edit %s' , 'pressmates-core' ),
                    $cpt_singular_name
                );
        $cpt_options['labels']['menu_name']           =
            isset($cpt_options['labels']['menu_name']) ? $cpt_options['labels']['menu_name'] :
                sprintf(
                    esc_html__( '%s', 'pressmates-core' ),
                    $cpt_plural_name
                );
        $cpt_options['labels']['name']                =
            isset($cpt_options['labels']['name']) ? $cpt_options['labels']['name'] :
                sprintf(
                    esc_html__( '%s', 'pressmates-core' ),
                    $cpt_plural_name
                );
        $cpt_options['labels']['name_admin_bar']      =
            isset($cpt_options['labels']['name_admin_bar']) ? $cpt_options['labels']['name_admin_bar'] :
                sprintf(
                    esc_html__( '%s', 'pressmates-core' ),
                    $cpt_singular_name
                );
        $cpt_options['labels']['new_item']            =
            isset($cpt_options['labels']['new_item']) ? $cpt_options['labels']['new_item'] :
                sprintf(
                    esc_html__( 'New %s', 'pressmates-core' ),
                    $cpt_singular_name
                );
        $cpt_options['labels']['not_found']           =
            isset($cpt_options['labels']['not_found']) ? $cpt_options['labels']['not_found'] :
                sprintf(
                    esc_html__( 'No %s Found', 'pressmates-core' ),
                    $cpt_plural_name
                );
        $cpt_options['labels']['not_found_in_trash']  =
            isset($cpt_options['labels']['not_found_in_trash']) ? $cpt_options['labels']['not_found_in_trash'] :
                sprintf(
                    esc_html__( 'No %s Found in Trash', 'pressmates-core' ),
                    $cpt_plural_name
                );
        $cpt_options['labels']['parent_item_colon']   =
            isset($cpt_options['labels']['parent_item_colon']) ? $cpt_options['labels']['parent_item_colon'] :
                sprintf(
                    esc_html__( 'Parent %s :', 'pressmates-core' ),
                    $cpt_plural_name
                );
        $cpt_options['labels']['search_items']        =
            isset($cpt_options['labels']['search_items']) ? $cpt_options['labels']['search_items'] :
                sprintf(
                    esc_html__( 'Search %s' , 'pressmates-core' ),
                    $cpt_plural_name
                );
        $cpt_options['labels']['singular_name']       =
            isset($cpt_options['labels']['singular_name']) ? $cpt_options['labels']['singular_name'] :
                sprintf(
                    esc_html__( '%s', 'pressmates-core' ),
                    $cpt_singular_name
                );
        $cpt_options['labels']['view_item']           =
            isset($cpt_options['labels']['view_item']) ? $cpt_options['labels']['view_item'] :
                sprintf(
                    esc_html__( 'View %s', 'pressmates-core' ),
                    $cpt_singular_name
                );

        $cpt_options['rewrite']['ep_mask']  = isset($cpt_options['rewrite']['ep_mask']) ? $cpt_options['rewrite']['ep_mask']    : EP_PERMALINK;
        $cpt_options['rewrite']['feeds']    = isset($cpt_options['rewrite']['feeds']) ? $cpt_options['rewrite']['feeds']        : false;
        $cpt_options['rewrite']['pages']    = isset($cpt_options['rewrite']['pages']) ? $cpt_options['rewrite']['pages']        : true;
        $cpt_options['rewrite']['slug']     =
            isset($cpt_options['rewrite']['slug']) ? $cpt_options['rewrite']['slug'] :
                sprintf(
                    esc_html__( '%s', 'pressmates-core' ),
                    strtolower( $cpt_name )
                );
        $cpt_options['rewrite']['with_front']	= isset($cpt_options['rewrite']['with_front']) ? $cpt_options['rewrite']['with_front'] : false;

        self::set_cpt_checkboxes(
            array(
                'slug' => $cpt_name,
                'name' => $cpt_plural_name
            )
        );

        self::register_cpt($cpt_name, $cpt_options);
    }

    /**
     * Register Custom Post Type based on provided settings
     * @param $cpt_name
     * @param $options
     */
    public static function register_cpt( $cpt_name, $options ) {

        $pressmatess_core_options = get_option('pressmates_core');

        $nesto = true;

        if(array_key_exists($cpt_name, $pressmatess_core_options)) {
            //Allow users to add additional options
            $cpt_options = apply_filters($cpt_name, $options);

            //Create CPT
            register_post_type(strtolower($cpt_name), $cpt_options);
        }
    }

    /**
     * Array of Custom POst Types that we want to register
     * Needs to be activated in pressmates core
     */
    public static function register_cpts (){
        $cpts = array(
            'pressmates_portfolio' => [
                'capability_type'   => 'post',
                'plural_name'       => 'Portfolios',
                'singular_name'     => 'Portfolio',
                'menu_icon'         => 'dashicons-portfolio',
                'menu_position'     => 25,
                'cpt_options'       => []
            ],
            'pressmates_service' => [
                'capability_type'   => 'post',
                'plural_name'       => 'Services',
                'singular_name'     => 'Service',
                'menu_icon'         => 'dashicons-admin-users',
                'menu_position'     => 26,
                'cpt_options'       => []
            ],
            'pressmates_team' => [
                'capability_type'   => 'post',
                'plural_name'       => 'Teams',
                'singular_name'     => 'Team',
                'menu_icon'         => 'dashicons-admin-users',
                'menu_position'     => 27,
                'cpt_options'       => []
            ]
        );


        //Load custom post types
        foreach($cpts as $cpt_name => $options){
            self::prepare_cpt(
                $cpt_name,
                $options['capability_type'],
                $options['plural_name'],
                $options['singular_name'],
                $options['menu_icon'],
                $options['menu_position'],
                $options['cpt_options']
            );
        }
    }

    /**
     * Unregister custom post type if user deactivate it from admin setting page
     * @param $cpt_name
     */
    public static function unregister_cpt( $cpt_name ){
        unregister_post_type( $cpt_name );
    }
}