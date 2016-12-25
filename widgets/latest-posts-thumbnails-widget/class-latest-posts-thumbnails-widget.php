<?php
/**
 * Latest Posts Thumbnails Widget
 *
 * @link       http://www.pressmates.net
 * @since      1.0.0
 *
 * @package    Pressmates_Core
 * @subpackage Pressmates_Core/widgets
 */
class PressMates_Latest_Posts_Thumbnails_Widget extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        /* Widget settings. */
        $widget_ops = array(
            'classname' => 'pressmates_latest_posts_thumbnails_widget',
            'description' => __( 'Shows latest posts with thumbnails', 'pressmates-core' )
        );

        /* Widget control settings. */
        $control_ops = array(
            'id_base' => 'pressmates_latest_posts_thumbnails_widget'
        );

        /* Create the widget. */
        parent::__construct(
            'pressmates_latest_posts_thumbnails_widget',
            __( 'PressMates - Latest Posts Thumbnails Widget', 'pressmates-core' ),
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

        $title          = isset( $instance['title'] )       ? apply_filters('widget_title', $instance['title'] )    : '';
        $categories     = isset( $instance['categories'] )  ? $instance['categories']                               : '';
        $limit          = isset( $instance['limit'] )       ? $instance['limit']                                    : '9';
        $before_widget  = $args['before_widget'];
        $after_widget   = $args['after_widget'];
        $before_title   = $args['before_title'];
        $after_title    = $args['after_title'];

        $query = array(
            'showposts'           => $limit,
            'nopaging'            => 0,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'cat'                 => $categories
        );

        $loop = new WP_Query($query);
        if ($loop->have_posts()) :

            /* Before widget (defined by themes). */
            echo $before_widget;

            /* Display the widget title if one was input (before and after defined by themes). */
            if ( $title )
                echo $before_title . $title . $after_title;
            ?>

            <ul class="pressmates-latest-posts">

            <?php  while ( $loop->have_posts() ) : $loop->the_post(); ?>

                <li>

                    <div class="latest-posts-content">

                        <?php if ( has_post_thumbnail() ) : ?>

                            <div class="latest-posts-img">
                                <a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                                    <?php
                                    // @TODO - Add appropriate thumbnail size
                                    the_post_thumbnail( 'post-thumbnail', array( 'class' => 'latest-posts-img thumb' ) );
                                    ?>
                                </a>
                            </div>

                        <?php endif; ?>

                        <div class="latest-posts-body">
                            <h4>
                                <a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>">
                                    <?php echo $this->pressmates_widgets_limit_chars( get_the_title(), 30 ); ?>
                                </a>
                            </h4>
                            <span><?php the_time( get_option( 'date_format' ) ); ?></span>
                        </div>

                    </div>

                </li>

            <?php
            endwhile;
            wp_reset_query();
            echo '</ul>';
            /* After widget (defined by themes). */
            echo $after_widget;
        endif;

    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
     function form( $instance ) {

        /* Set up some default widget settings. */
        $defaults = array(
            'title'         => __( 'Latest Posts', 'pressmates-core' ),
            'limit'         => 5,
            'categories'    => ''
        );

        $instance = wp_parse_args( (array) $instance, $defaults ); ?>

        <!-- Widget Title: Text Input -->
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'pressmates-core' ); ?></label>
            <input  type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"  />
        </p>

        <!-- Category -->
        <p>
            <label for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php esc_html_e( 'Filter by Category', 'pressmates-core' ); ?>:</label>
            <select id="<?php echo $this->get_field_id( 'categories' ); ?>" name="<?php echo $this->get_field_name( 'categories' ); ?>" class="widefat categories" style="width:100%;">
                <option value='all' <?php if ( 'all' == $instance['categories'] ) echo 'selected="selected"'; ?>><?php esc_html_e( 'All categories', 'pressmates-core' ); ?></option>
                <?php $categories = get_categories( 'hide_empty=0&depth=1&type=post' ); ?>
                <?php foreach( $categories as $category ) { ?>
                    <option value='<?php echo $category->term_id; ?>' <?php if ( $category->term_id == $instance['categories'] ) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
                <?php } ?>
            </select>
        </p>

        <!-- Number of posts -->
        <p>
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php esc_html_e( 'Number of posts to show:', 'pressmates-core' ); ?></label>
            <input  type="text" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $instance['limit']; ?>" size="3" />
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
        $instance['categories'] = $new_instance['categories'];
        $instance['limit']      = strip_tags( $new_instance['limit'] );

        return $instance;
    }

    /**
    * Limit characters in provided string
    * @param $text
    * @param $limit
    * @return string
    */
    function pressmates_widgets_limit_chars( $text, $limit ) {
        if ( strlen( $text ) > $limit ) {
            $text = mb_substr( $text, 0, $limit ) . '...';
        }

        return $text;
    }
}