<?php 
/*
Plugin Name: BMI Widget
Plugin URI: https://github.com/swaincreates/bmi-widget
Description: Adds a widget that displays a BMI calculator with standard or metric measurements.
Author: Swain Strickland
Author URI: https://github.com/swaincreates	
Version: 1.0
*/

//Register Widget
add_action( 'widgets_init', function(){
     register_widget( 'BMI_Widget' );
});

//Register stylesheet and add user color options with wp_add_inline_style
function bmi_widget_styles() {
	wp_enqueue_style('bmi-widget', WP_PLUGIN_URL . '/bmi-widget/bmi-widget-style.css');
	$bmi_option = get_option( 'widget_bmi_widget');
	$custom_css = "
				.bmi-widget {
					border: 1px solid {$bmi_option[2]['border_color']};
				}
				.widget-area .widget .bmi-widget-title {
					background-color: {$bmi_option[2]['title_backround_color']};
					color: {$bmi_option[2]['title_font_color']};
				}
				#bmi_submit {
					background-color: {$bmi_option[2]['button_background_color']};
					border: 1px solid {$bmi_option[2]['button_background_color']};
					color: {$bmi_option[2]['button_font_color']};
				}
				";
	wp_add_inline_style( 'bmi-widget', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'bmi_widget_styles');


class BMI_Widget extends WP_Widget {

	/**
		 * Register widget with WordPress.
		 */
		function __construct() {
			parent::__construct(
				'bmi_widget', // Base ID
				__( 'BMI Widget', 'text_domain' ), // Name
				array( 'description' => __( 'A simple BMI widget that displays standard or metric measurements.', 'text_domain' ), ) // Args
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

			$standard = array('height' => 'in.', 'weight' => 'lbs.');
			$metric = array('height' => 'cm.', 'weight' => 'kg.');
			
			if (empty($instance['measurement_system'])) {
				$use_measures = $standard;
			} else {
				$use_measures = $metric;
			}
			 ?>
			
			<form class="bmi-widget">

				<h3 class="bmi-widget-title"><?php echo $instance['title'] ?></h3>

				<div class="bmi-form-wrapper">
					<fieldset>
						<p>
							<label for="height">Height (<?php echo $use_measures['height']; ?>): </label><input type="number" id="height">
						</p>
						<p>
							<label for="weight">Weight (<?php echo $use_measures['weight']; ?>): </label><input type="number" id="weight"> </p>				
						<p>
							<label for="bmi">BMI: </label><input type="number" id="user_bmi"> 
						</p>
					</fieldset>
					<!-- <p>Your BMI: <span id="user_bmi"></span></p> -->
					<input type="submit" id="bmi_submit">
				</div>
			</form>
			<?php 
			//Define BMI Formula
			$standard_formula = '((weight)/(height*height))*703';
			$metric_formula = '((weight)/((height/100)*(height/100)))';

			if($use_measures['height'] === 'in.') {
				$user_formula = $standard_formula;
			} else {
				$user_formula = $metric_formula;
			}
				echo "<script>
						jQuery('document').ready(function() {
							function calculateBMI(){
								var height = jQuery('#height').val();
								var weight = jQuery('#weight').val();

								var bmi = " . $user_formula . ";
								jQuery('#user_bmi').val(Math.round(bmi));
								
							}

							jQuery('#bmi_submit').click(function() {
								calculateBMI();
								return false;
							});

						});
						</script>"; 
 ?>
			
			

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
			$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'BMI Calculator', 'text_domain' );
			$measurement_system = ! empty( $instance['measurement_system'] ) ? $instance['measurement_system'] : __( 'standard', 'text_domain' );
			// vars
			$border_color = ! empty( $instance['border_color'] ) ? $instance['border_color'] : __( '#000', 'text_domain' );
			$title_backround_color = ! empty( $instance['title_backround_color'] ) ? $instance['title_backround_color'] : __( '#000', 'text_domain' );
			$title_font_color = ! empty( $instance['title_font_color'] ) ? $instance['title_font_color'] : __( '#fff', 'text_domain' );
			$button_background_color = ! empty( $instance['button_background_color'] ) ? $instance['button_background_color'] : __( '#000', 'text_domain' );
			$button_font_color = ! empty( $instance['button_font_color'] ) ? $instance['button_font_color'] : __( '#fff', 'text_domain' );
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'measurement_system' ); ?>"><?php _e( 'Measurement System:' ); ?></label> <br />
				<select name="<?php echo $this->get_field_name( 'measurement_system'); ?> " id="<?php echo $this->get_field_id( 'measurement_system' ); ?>">
					<option value="0" <?php if (empty($instance['measurement_system'])) {echo "selected";} ?>>Standard (US)</option>
					<option value="1" <?php if (!empty($instance['measurement_system'])) {echo "selected";} ?>>Metric</option>
				</select>
			</p>
			<!-- border_color -->
			<p>
				<label for="<?php echo $this->get_field_id( 'border_color' ); ?>"><?php _e( 'Border Color:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'border_color' ); ?>" name="<?php echo $this->get_field_name( 'border_color' ); ?>" type="text" value="<?php echo esc_attr( $border_color ); ?>">
			</p>
			<!-- title_backround_color -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title_backround_color' ); ?>"><?php _e( 'Title Background Color:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title_backround_color' ); ?>" name="<?php echo $this->get_field_name( 'title_backround_color' ); ?>" type="text" value="<?php echo esc_attr( $title_backround_color ); ?>">
			</p>
			<!-- title_font_color -->
			<p>
				<label for="<?php echo $this->get_field_id( 'title_font_color' ); ?>"><?php _e( 'Title Font Color:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title_font_color' ); ?>" name="<?php echo $this->get_field_name( 'title_font_color' ); ?>" type="text" value="<?php echo esc_attr( $title_font_color ); ?>">
			</p>
			<!-- button_background_color -->
			<p>
				<label for="<?php echo $this->get_field_id( 'button_background_color' ); ?>"><?php _e( 'Button Background Color:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'button_background_color' ); ?>" name="<?php echo $this->get_field_name( 'button_background_color' ); ?>" type="text" value="<?php echo esc_attr( $button_background_color ); ?>">
			</p>
			<!-- border_font_color -->
			<p>
				<label for="<?php echo $this->get_field_id( 'button_font_color' ); ?>"><?php _e( 'Button Font Color:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'button_font_color' ); ?>" name="<?php echo $this->get_field_name( 'button_font_color' ); ?>" type="text" value="<?php echo esc_attr( $button_font_color ); ?>">
			</p>
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
			$instance['measurement_system'] = ( ! empty( $new_instance['measurement_system'] ) ) ? strip_tags( $new_instance['measurement_system'] ) : '';
			//add
			$instance['border_color'] = ( ! empty( $new_instance['border_color'] ) ) ? strip_tags( $new_instance['border_color'] ) : '';
			$instance['title_backround_color'] = ( ! empty( $new_instance['title_backround_color'] ) ) ? strip_tags( $new_instance['title_backround_color'] ) : '';
			$instance['title_font_color'] = ( ! empty( $new_instance['title_font_color'] ) ) ? strip_tags( $new_instance['title_font_color'] ) : '';
			$instance['button_background_color'] = ( ! empty( $new_instance['button_background_color'] ) ) ? strip_tags( $new_instance['button_background_color'] ) : '';
			$instance['button_font_color'] = ( ! empty( $new_instance['button_font_color'] ) ) ? strip_tags( $new_instance['button_font_color'] ) : '';

			return $instance;
		}
}