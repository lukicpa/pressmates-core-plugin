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
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

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
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/pressmates-core-admin-display.php';
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
}
