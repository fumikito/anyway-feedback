<?php

namespace AFB\Widget;
use AFB\Model\FeedBacks;

/**
 * Widget for Popular post
 *
 * @package Anyway Feedback
 * @property-read FeedBacks $feedbacks
 */
class Popular extends \WP_Widget {


	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct(
			'anyway-feedback-popular-widgets',
			__( 'Feedback widget', 'anyway-feedback' ),
			array(
				'description' => __( 'This widget shows most popular post per post type.', 'anyway-feedback' ),
				'classname'   => 'anyway-feedback-popular-widget',
			)
		);
	}

	/**
	 * Retrieve
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$title     = empty( $instance['title'] ) ? $this->name : apply_filters( 'widget_title', $instance['title'] );
		$post_type = empty( $instance['post_type'] ) ? 'post' : esc_attr( $instance['post_type'] );
		$num_posts = empty( $instance['num_posts'] ) ? 5 : max( 1, intval( $instance['num_posts'] ) );
		$posts     = $this->feedbacks->search(array(
			'post_type'   => $post_type,
			'post_status' => 'publish',
			'allow_empty' => false,
			'orderby'     => 'positive',
			'order'       => 'DESC',
		), 1, $num_posts);
		?>
			<?php echo $args['before_widget']; ?>
				<?php if ( $title ) : ?>
						<?php echo $args['before_title'] . esc_html( $title ) . $args['after_title']; ?>
				<?php endif; ?>
				<ul>
					<?php
					if ( empty( $posts ) ) :
						?>
							<li class="empty"><?php _e( 'There is no feedback.', 'anyway-feedback' ); ?></li>
						<?php
					else :
						foreach ( $posts as $post ) :
							?>
							<li>
								<a href="<?php echo get_permalink( $post ); ?>"><?php echo get_the_title( $post ); ?></a>
								<span class="count">(<?php printf( __( '%d says useful', 'anyway-feedback' ), $post->positive ); ?>)</span>
							</li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			<?php echo $args['after_widget']; ?>
		<?php
	}

	/**
	 * Update function
	 *
	 * @see WP_Widget
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Form to update widget
	 *
	 * @see WP_Widget
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance ) {
		$current_post_type = esc_attr( isset( $instance['post_type'] ) ? $instance['post_type'] : 'post' );
		$num_posts         = isset( $instance['num_posts'] ) ? max( 1, intval( $instance['num_posts'] ) ) : 5;
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:' ); ?>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( isset( $instance['title'] ) ? $instance['title'] : sprintf( __( 'Popular %s', 'anyway-feedback' ), get_post_type_object( $current_post_type )->labels->name ) ); ?>" />
				</label>
				<label for="<?php echo $this->get_field_id( 'post_type' ); ?>">
					<?php _e( 'Post Type:', 'anyway-feedback' ); ?> <br />
					<select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
						<?php
						foreach ( get_post_types() as $post_type ) :
							$post_type = get_post_type_object( $post_type );
							?>
							<option<?php selected( $post_type->name, $current_post_type ); ?> value="<?php echo esc_attr( $post_type->name ); ?>"><?php echo esc_html( $post_type->labels->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</label><br />
				<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>">
					<?php _e( 'Number of posts:', 'anyway-feedback' ); ?> <br />
					<input type="text" id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" value="<?php echo $num_posts; ?>" />
				</label>
			</p>
		<?php
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 * @return \AFB\Pattern\Singleton|null
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'feedbacks':
				return FeedBacks::get_instance();
			default:
				return null;
		}
	}
}
