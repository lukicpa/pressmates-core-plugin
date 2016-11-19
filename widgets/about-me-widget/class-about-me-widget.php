<?php
/**
 * About me widget
 *
 * @link       http://www.pressmates.net
 * @since      1.0.0
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/widgets
 */
class PressMates_About_Me_Widget extends WP_Widget {

    public function __construct() {
        /* Widget settings. */
        $widget_ops = array( 'classname' => 'pressmates_about_me_widget', 'description' => __( 'Displays About widget', 'pressmates-core' ) );

        /* Widget control settings. */
        $control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'pressmates_about_me_widget' );

        /* Create the widget. */
        parent::__construct( 'pressmates_about_me_widget', __( 'PressMates - About Me', 'pressmates-core' ), $widget_ops, $control_ops );

        add_action( 'admin_enqueue_scripts', array( $this, 'upload_scripts') );
    }

    /**
     * Upload the Javascripts for the media uploader
     */
    public function upload_scripts() {
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'upload_media_widget', plugin_dir_url( __FILE__ ) . 'js/upload-media.js', array( 'jquery' ) );
        wp_enqueue_style( 'custom_css', plugin_dir_url( __FILE__ ) . 'css/style.css' );
        wp_enqueue_style( 'thickbox' );
    }

    /**
     * How to display the widget on the screen.
     */
    function widget( $args, $instance ) {
        extract( $args );

        /* Our variables from the widget settings. */
        $title       = apply_filters( 'widget_title', $instance['title'] );
        $image       = $instance['image'];
        $name        = $instance['name'];
        $description = $instance['description'];

        /* Before widget (defined by themes). */
        echo $before_widget;

        /* Display the widget title if one was input (before and after defined by themes). */
        if ( $title )
            echo $before_title . $title . $after_title;

        ?>

        <div class="about-widget">

            <?php if ( $image ) : ?>
                <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" />
            <?php endif; ?>

            <?php if ( $name ) :  ?>
                <h4><?php echo $name; ?></h4>
            <?php endif; ?>

            <?php if ( $description ) : ?>
                <p><?php echo $description; ?></p>
            <?php endif; ?>

        </div>

        <?php

        /* After widget (defined by themes). */
        echo $after_widget;
    }

    /**
     * Update the widget settings.
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title']       = strip_tags( $new_instance['title'] );
        $instance['image']       = strip_tags( $new_instance['image'] );
        $instance['name']        = strip_tags( $new_instance['name'] );
        $instance['description'] = $new_instance['description'];

        return $instance;
    }


    function form( $instance ) {

        // Set up some default widget settings.
        $defaults = array(
            'title'       => __( 'About Me', 'pressmates-core' ),
            'image'       => '',
            'name'        => '',
            'description' => ''
        );

        $instance = wp_parse_args( ( array ) $instance, $defaults );

        ?>
        <div class="pressmates_about_me_widget">
            <!-- Widget Title: Text Input -->
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'pressmates-core' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
            </p>

            <!-- image url -->
            <p>
                <label for="<?php echo $this->get_field_id('image'); ?>"><?php _e( 'Image:', 'pressmates-core' ); ?></label><br />
                <small><?php _e( 'Upload image or input image URL', 'pressmates' ); ?></small><br />
                <img class="custom_media_image" src="<?php if ( !empty( $instance['image'] ) ) { echo $instance['image']; } ?>" />
                <input type="text" class="widefat custom_media_url" data-field_id="image_url_field" name="<?php echo $this->get_field_name( 'image' ); ?>" id="<?php echo $this->get_field_id( 'image' ); ?>" value="<?php echo $instance['image']; ?>">
                <br /><br />
                <a href="#" class="button upload_image_button"><?php _e( 'Upload', 'pressmates-core' ); ?></a>
            </p>

            <!-- My name: Text Input -->
            <p>
                <label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e( 'Name:', 'pressmates-core' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" />
            </p>

            <!-- description -->
            <p>
                <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'About me text:', 'pressmates-core' ); ?></label>
                <textarea id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>" style="width:95%;" rows="6"><?php echo $instance['description']; ?></textarea>
            </p>
        </div>

        <?php
    }
}