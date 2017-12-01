<?php
/**
 * Plugin Name: Single Use Widget
 * Description: Proof of concept, single use functionality by <a href="https://wordpress.stackexchange.com/a/287518/23011">kraftner</a>.
 * Plugin URI:  https://github.com/glueckpress/single-use-widget/
 * Author:      Caspar Hübinger
 * Author URI:  https://profiles.wordpress.org/glueckpress
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) or die( 'No direct access.' );

/**
 * Class Single_Use_Widget
 */
class Single_Use_Widget extends WP_Widget {

	/**
	 * Widget ID base.
	 */
	public $id_base = 'single_use_widget';

	/**
	 * Constructor.
	 */
	function __construct() {

		$id_base     = $this->id_base;
		$name        = esc_attr__( 'Single Use Widget' );
		$widget_ops  = array(
			'description' => esc_attr__( 'A widget that can be used only once.' ),
		);

		parent::__construct( $id_base, $name, $widget_ops );

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
	}

	/**
	 * Registers, localizes, and enqueues scripts.
	 */
	function enqueue_scripts() {

		wp_register_script(
			'single-use-widget',
			plugins_url( 'single-use-widget.js', __FILE__ ),
			array( 'jquery' ),
			filemtime( __FILE__ ),
			true
		);

		wp_localize_script(
			'single-use-widget',
			'singleUseWidget',
			array( 'idBase' => $this->id_base )
		);
		wp_enqueue_script( 'single-use-widget' );

	}

	/**
	 * HTML output.
	 */
	function widget( $args, $instance ) {

		$text = ! empty( $instance['text'] ) ? $instance['text'] : '';
		$sanitized_text = sanitize_text_field( $text );

		// Before/after arguments not supported.
		printf( '%1$s<div class="single-use-widget">%2$s</div>%3$s',
			$args['before_widget'],
			$sanitized_text,
			$args['after_widget']
		);
	}

	/**
	 * Update handler.
	 */
	function update( $new_instance, $old_instance ) {

		return $new_instance;
	}

	/**
	 * Form HTML.
	 */
	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'text' => '' ) );
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php esc_attr_e( 'Content:' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" type="text" value="<?php echo esc_attr( $instance['text'] ); ?>">
			</p>
		<?php
	}
}

/**
 * Register widget.
 */
add_action( 'widgets_init', function () {

	register_widget( 'Single_Use_Widget' );
});
