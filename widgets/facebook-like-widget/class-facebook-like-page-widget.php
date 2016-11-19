<?php
/**
 * Facebook like page widget
 *
 * @link       http://www.pressmates.net
 * @since      1.0.0
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/widgets
 */
class Fb_Like_Page_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'fb_like_page_widget', // Base ID
            esc_html__( 'PressMates - FB Like page', 'pressmates-core' ), // Name
            array( 'description' => esc_html__( 'Facebook Like page Widget', 'pressmates-core' ), ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        $title = isset( $instance['title'] ) ? $instance['title'] : 'PressMates';
        $link = isset( $instance['link'] ) ? $instance['link'] : 'https://www.facebook.com/pressmates';
        $width = isset( $instance['width'] ) ? $instance['width'] : '340';
        $height = isset( $instance['height'] ) ? $instance['height'] : '500';

        $data_tabs_timeline = ( isset( $instance['data_tabs_timeline'] ) && $instance['data_tabs_timeline'] == 'on' ) ? 'timeline' . ',' : '';
        $data_tabs_events = ( isset( $instance['data_tabs_events'] ) && $instance['data_tabs_events'] == 'on' ) ? 'events' . ',' : '';
        $data_tabs_messages = ( isset( $instance['data_tabs_messages'] ) && $instance['data_tabs_messages'] == 'on' ) ? 'messages' . ',' : '';

        $use_small_header = isset( $instance['use_small_header'] ) ? $instance['use_small_header'] : 'false';
        $adapt_container_width = isset( $instance['adapt_container_width'] ) ? $instance['adapt_container_width'] : '';
        $hide_cover_photo = isset( $instance['hide_cover_photo'] ) ? $instance['hide_cover_photo'] : '';
        $show_faces = isset( $instance['show_faces'] ) ? $instance['show_faces'] : '';

        ?>

        <div id="fb-root"></div>
        <script>(function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/<?php echo get_locale(); ?>/sdk.js#xfbml=1&version=v2.8";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
        <div class="fb-page"
             data-href="<?php echo $link; ?>"
             data-tabs="<?php echo $data_tabs_timeline . $data_tabs_events . $data_tabs_messages; ?>"
             data-width="<?php echo $width; ?>"
             data-height="<?php echo $height; ?>"
             data-small-header="<?php echo $use_small_header ?>"
             data-adapt-container-width="<?php echo $adapt_container_width ?>"
             data-hide-cover="<?php echo $hide_cover_photo ?>"
             data-show-facepile="<?php echo $show_faces ?>">
            <blockquote
                cite="<?php echo $link; ?>"
                class="fb-xfbml-parse-ignore">
                <a href="<?php echo $link; ?>"><?php echo $title; ?></a>
            </blockquote>
        </div>

        <?php
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        $defaults = array(
            'title' => esc_html__( 'PressMates', 'pressmates-core' ),
            'link' => esc_html__( 'https://www.facebook.com/pressmates', 'pressmates-core' ),
            'data_tabs_timeline' => 'on',
            'data_tabs_events' => 'off',
            'data_tabs_messages' => 'off',
            'width' => '340',
            'height' => '500',
            'use_small_header' => 'off',
            'adapt_container_width' => 'on',
            'hide_cover_photo' => 'off',
            'show_faces' => 'on'

        );
        $instance = wp_parse_args( ( array ) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'pressmates-core' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_attr_e( 'Facebook page link:', 'pressmates-core' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['link'] ); ?>">
        </p>

        <table class="widefat">
            <tr>
                <td colspan="3"><?php esc_html_e( 'Tabs to render', 'pressmates-core' ) ?></td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'data_tabs_timeline' ) ); ?>"><?php esc_attr_e( 'Timeline:', 'pressmates-core' ); ?></label>
                    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'data_tabs_timeline' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'data_tabs_timeline' ); ?>" name="<?php echo $this->get_field_name( 'data_tabs_timeline' ); ?>" />
                </td>
                <td>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'data_tabs_events' ) ); ?>"><?php esc_attr_e( 'Events:', 'pressmates-core' ); ?></label>
                    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'data_tabs_events' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'data_tabs_events' ); ?>" name="<?php echo $this->get_field_name( 'data_tabs_events' ); ?>" />
                </td>
                <td>
                    <label for="<?php echo esc_attr( $this->get_field_id( 'data_tabs_messages' ) ); ?>"><?php esc_attr_e( 'Messages:', 'pressmates-core' ); ?></label>
                    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'data_tabs_messages' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'data_tabs_messages' ); ?>" name="<?php echo $this->get_field_name( 'data_tabs_messages' ); ?>" />
                </td>
            </tr>
        </table>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php esc_attr_e( 'Width:', 'pressmates-core' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['width'] ); ?>">
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_attr_e( 'Height:', 'pressmates-core' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['height'] ); ?>">
        </p>

        <table class="widefat">
            <tr>
                <td colspan="2"><?php esc_html_e( 'Additional Settings', 'pressmates-core' ) ?></td>
            </tr>
            <tr>
                <td width="40%">
                    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'use_small_header' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'use_small_header' ); ?>" name="<?php echo $this->get_field_name( 'use_small_header' ); ?>" />
                    <label for="<?php echo esc_attr( $this->get_field_id( 'use_small_header' ) ); ?>"><?php esc_attr_e( 'Use Small Header', 'pressmates-core' ); ?></label>
                </td>
                <td>
                    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'adapt_container_width' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'adapt_container_width' ); ?>" name="<?php echo $this->get_field_name( 'adapt_container_width' ); ?>" />
                    <label for="<?php echo esc_attr( $this->get_field_id( 'adapt_container_width' ) ); ?>"><?php esc_attr_e( 'Adapt to plugin container width', 'pressmates-core' ); ?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'hide_cover_photo' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'hide_cover_photo' ); ?>" name="<?php echo $this->get_field_name( 'hide_cover_photo' ); ?>" />
                    <label for="<?php echo esc_attr( $this->get_field_id( 'hide_cover_photo' ) ); ?>"><?php esc_attr_e( 'Hide Cover Photo', 'pressmates-core' ); ?></label>
                </td>
                <td>
                    <input class="checkbox" type="checkbox" <?php checked( $instance[ 'show_faces' ], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_faces' ); ?>" name="<?php echo $this->get_field_name( 'show_faces' ); ?>" />
                    <label for="<?php echo esc_attr( $this->get_field_id( 'show_faces' ) ); ?>"><?php esc_attr_e( 'Show Friend\'s Faces', 'pressmates-core' ); ?></label>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['link'] = ( ! empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : '';
        $instance['data_tabs_timeline'] = ( ! empty( $new_instance['data_tabs_timeline'] ) ) ? strip_tags( $new_instance['data_tabs_timeline'] ) : '';
        $instance['data_tabs_events'] = ( ! empty( $new_instance['data_tabs_events'] ) ) ? strip_tags( $new_instance['data_tabs_events'] ) : '';
        $instance['data_tabs_messages'] = ( ! empty( $new_instance['data_tabs_messages'] ) ) ? strip_tags( $new_instance['data_tabs_messages'] ) : '';
        $instance['width'] = ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
        $instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';
        $instance['use_small_header'] = ( ! empty( $new_instance['use_small_header'] ) ) ? strip_tags( $new_instance['use_small_header'] ) : '';
        $instance['adapt_container_width'] = ( ! empty( $new_instance['adapt_container_width'] ) ) ? strip_tags( $new_instance['adapt_container_width'] ) : '';
        $instance['hide_cover_photo'] = ( ! empty( $new_instance['hide_cover_photo'] ) ) ? strip_tags( $new_instance['hide_cover_photo'] ) : '';
        $instance['show_faces'] = ( ! empty( $new_instance['show_faces'] ) ) ? strip_tags( $new_instance['show_faces'] ) : '';

        return $instance;
    }

} // class Foo_Widget