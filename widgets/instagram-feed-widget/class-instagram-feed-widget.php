<?php
/**
 * Instagram Feed widget
 *
 * @link       http://www.pressmates.net
 * @since      1.0.0
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/widgets
 */
class PressMates_Instagram_Feed_Widget extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        /* Widget settings. */
        $widget_ops = array(
            'classname' => 'pressmates_instagram_feed_widget',
            'description' => __( 'Instagram Feed Widget', 'pressmates-core' )
        );

        /* Widget control settings. */
        $control_ops = array(
            'id_base' => 'pressmates_instagram_feed_widget'
        );

        /* Create the widget. */
        parent::__construct(
            'pressmates_instagram_feed_widget',
            __( 'PressMates - Instagram Feed Widget', 'pressmates-core' ),
            $widget_ops,
            $control_ops
        );

        //add_action( 'admin_enqueue_scripts', array( $this, 'upload_scripts') );
    }

    /**
     * Upload the Javascripts for Instagram Feed
     */
    /*public function upload_scripts() {
        wp_enqueue_script( 'upload_media_widget', plugin_dir_url( __FILE__ ) . 'js/upload-media.js', array( 'jquery' ) );
        wp_enqueue_style( 'custom_css', plugin_dir_url( __FILE__ ) . 'css/style.css' );
    }*/

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        $title          = isset( $instance['title'] )       ? $instance['title']        : esc_html__( 'PressMates Instagram Feed', 'pressmates-core' );
        $username       = isset( $instance['username'] )    ? $instance['username']     : 'wordpressdotcom';
        $limit          = isset( $instance['limit'] )       ? $instance['limit']        : '9';
        $photo_size     = isset( $instance['photo_size'] )  ? $instance['photo_size']   : 'large';
        $target         = isset( $instance['target'] )      ? $instance['target']       : '_blank';
        $link_text      = isset( $instance['link_text'] )   ? $instance['link_text']    : esc_html__( 'Follow' );
        $before_widget  = $args['before_widget'];
        $after_widget   = $args['after_widget'];
        $before_title   = $args['before_title'];
        $after_title    = $args['after_title'];

        /* Before widget (defined by themes). */
        echo $before_widget;

        /* Display the widget title if one was input (before and after defined by themes). */
        if ( $title ) {
            echo $before_title . $title . $after_title;
        }

        if ( $username != '' ) {

            $media_array = $this->scrape_instagram( $username );

            if ( is_wp_error( $media_array ) ) {

                echo wp_kses_post( $media_array->get_error_message() );

            } else {

                // filter for images only?
                if ( $images_only = apply_filters( 'wpiw_images_only', FALSE ) ) {
                    $media_array = array_filter( $media_array, array( $this, 'images_only' ) );
                }

                // slice list down to required limit
                $media_array = array_slice( $media_array, 0, $limit );

                echo '<ul class="pressmates_instagram">';
                foreach ( $media_array as $item ) {
                    echo '<li><a href="'. esc_url( $item['link'] ) .'" target="'. esc_attr( $target ) .'" ><img src="'. esc_url( $item[$photo_size] ) .'"  alt="'. esc_attr( $item['description'] ) .'" title="'. esc_attr( $item['description'] ).'"  /></a></li>';
                }
                echo '</ul>';
            }
        }

        if ( $link_text != '' ) { ?>
            <p class="pressmates_instagram_follow">
                <a href="<?php echo trailingslashit( '//instagram.com/' . esc_attr( trim( $username ) ) ); ?>" rel="me" target="<?php echo esc_attr( $target ); ?>">
                    <?php echo wp_kses_post( $link_text ); ?>
                </a>
            </p>
            <?php
        }

        /* After widget (defined by themes). */
        echo $after_widget;
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // Set up some default widget settings.
        $defaults = array(
            'title'         => esc_html__( 'PressMates Instagram Feed', 'pressmates-core' ),
            'username'      => 'wordpressdotcom',
            'limit'         => 9,
            'photo_size'    => 'large',
            'target'        => '_blank',
            'link_text'     => esc_html__( 'Follow' )
        );

        $instance = wp_parse_args( ( array ) $instance, $defaults );

        $title = $instance['title'];
        $username = $instance['username'];
        $limit = absint( $instance['limit'] );
        $photo_size = $instance['photo_size'];
        $target = $instance['target'];
        $link_text = $instance['link_text'];

        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
                <?php esc_html_e( 'Title', 'pressmates-core' ); ?>:
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>">
                <?php esc_html_e( 'Username', 'pressmates-core' ); ?>:
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>">
                <?php esc_html_e( 'Number of photos', 'pressmates-core' ); ?>:
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'photo_size' ) ); ?>">
                <?php esc_html_e( 'Photo size', 'pressmates-core' ); ?>:
            </label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'photo_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'photo_size' ) ); ?>" class="widefat">
                <option value="thumbnail" <?php selected( 'thumbnail', $photo_size ) ?>><?php esc_html_e( 'Thumbnail', 'pressmates-core' ); ?></option>
                <option value="small" <?php selected( 'small', $photo_size ) ?>><?php esc_html_e( 'Small', 'pressmates-core' ); ?></option>
                <option value="large" <?php selected( 'large', $photo_size ) ?>><?php esc_html_e( 'Large', 'pressmates-core' ); ?></option>
                <option value="original" <?php selected( 'original', $photo_size ) ?>><?php esc_html_e( 'Original', 'pressmates-core' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>">
                <?php esc_html_e( 'Open links in', 'pressmates-core' ); ?>:
            </label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" class="widefat">
                <option value="_self" <?php selected( '_self', $target ) ?>><?php esc_html_e( 'Current window (_self)', 'pressmates-core' ); ?></option>
                <option value="_blank" <?php selected( '_blank', $target ) ?>><?php esc_html_e( 'New window (_blank)', 'pressmates-core' ); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'link_text' ) ); ?>">
                <?php esc_html_e( 'Link text', 'pressmates-core' ); ?>:
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_text' ) ); ?>" type="text" value="<?php echo esc_attr( $link_text ); ?>" />
            </label>
        </p>
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
// processes widget options to be saved
        $instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title']      = strip_tags( $new_instance['title'] );
        $instance['username']   = strip_tags( $new_instance['username'] );
        $instance['limit']      = strip_tags( $new_instance['limit'] );
        $instance['photo_size'] = strip_tags( $new_instance['photo_size'] );
        $instance['target']     = strip_tags( $new_instance['target'] );
        $instance['link_text']  = strip_tags( $new_instance['link_text'] );

        return $instance;
    }

    function scrape_instagram( $username ) {
        $username = strtolower( $username );
        $username = str_replace( '@', '', $username );
        if ( false === ( $instagram = get_transient( 'instagram-a6-' . sanitize_title_with_dashes( $username ) ) ) ) {
            $remote = wp_remote_get( 'https://instagram.com/' . trim( $username ) );
            if ( is_wp_error( $remote ) ) {
                return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'pressmates-core' ) );
            }
            if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
                return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'pressmates-core' ) );
            }
            $shards = explode( 'window._sharedData = ', $remote['body'] );
            $insta_json = explode( ';</script>', $shards[1] );
            $insta_array = json_decode( $insta_json[0], true );
            if ( ! $insta_array ) {
                return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'pressmates-core' ) );
            }
            if ( isset( $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'] ) ) {
                $images = $insta_array['entry_data']['ProfilePage'][0]['user']['media']['nodes'];
            } else {
                return new WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'pressmates-core' ) );
            }
            if ( ! is_array( $images ) ) {
                return new WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'pressmates-core' ) );
            }
            $instagram = array();
            foreach ( $images as $image ) {
                $image['thumbnail_src'] = preg_replace( '/^https?\:/i', '', $image['thumbnail_src'] );
                $image['display_src'] = preg_replace( '/^https?\:/i', '', $image['display_src'] );
                // handle both types of CDN url.
                if ( ( strpos( $image['thumbnail_src'], 's640x640' ) !== false ) ) {
                    $image['thumbnail'] = str_replace( 's640x640', 's160x160', $image['thumbnail_src'] );
                    $image['small'] = str_replace( 's640x640', 's320x320', $image['thumbnail_src'] );
                } else {
                    $urlparts = wp_parse_url( $image['thumbnail_src'] );
                    $pathparts = explode( '/', $urlparts['path'] );
                    array_splice( $pathparts, 3, 0, array( 's160x160' ) );
                    $image['thumbnail'] = '//' . $urlparts['host'] . implode( '/', $pathparts );
                    $pathparts[3] = 's320x320';
                    $image['small'] = '//' . $urlparts['host'] . implode( '/', $pathparts );
                }
                $image['large'] = $image['thumbnail_src'];
                if ( true === $image['is_video'] ) {
                    $type = 'video';
                } else {
                    $type = 'image';
                }
                $caption = __( 'Instagram Image', 'pressmates-core' );
                if ( ! empty( $image['caption'] ) ) {
                    $caption = $image['caption'];
                }
                $instagram[] = array(
                    'description'   => $caption,
                    'link'		  	=> trailingslashit( '//instagram.com/p/' . $image['code'] ),
                    'time'		  	=> $image['date'],
                    'comments'	  	=> $image['comments']['count'],
                    'likes'		 	=> $image['likes']['count'],
                    'thumbnail'	 	=> $image['thumbnail'],
                    'small'			=> $image['small'],
                    'large'			=> $image['large'],
                    'original'		=> $image['display_src'],
                    'type'		  	=> $type,
                );
            } // End foreach().
            // do not set an empty transient - should help catch private or empty accounts.
            if ( ! empty( $instagram ) ) {
                $instagram = base64_encode( serialize( $instagram ) );
                set_transient( 'instagram-a6-' . sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS * 2 ) );
            }
        }
        if ( ! empty( $instagram ) ) {
            return unserialize( base64_decode( $instagram ) );
        } else {
            return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'pressmates-core' ) );
        }
    }
    function images_only( $media_item ) {
        if ( 'image' === $media_item['type'] ) {
            return true;
        }
        return false;
    }
}