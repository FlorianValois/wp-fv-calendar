<?php
/*
Plugin Name: RBX WP Calendar
Plugin URI: 
Description: Plugin de calendrier
Version: 0.1
Author: Rollingbox
Author URI: https://rollingbox.com
Text Domain: rbx-wp-calendar
Domain Path: /languages/
GitHub Plugin URI: 
*/

if (!defined('ABSPATH')) {
	exit;
}

if ( !function_exists( 'rbx_wpcalendar_admin_css_js' ) ) {
	add_action( 'init', 'rbx_wpcalendar_admin_css_js' );
  function rbx_wpcalendar_admin_css_js() {
		
		/* FULLCALENDAR */
		wp_enqueue_script('moment-js', plugins_url('bower_components/moment/min/moment.min.js', __FILE__), false, '', true);
		wp_enqueue_script('fullcalendar-js', plugins_url('bower_components/fullcalendar/dist/fullcalendar.min.js', __FILE__), false, '', true);
		wp_enqueue_script('fullcalendar-locale-js', plugins_url('bower_components/fullcalendar/dist/locale/fr.js', __FILE__), false, '', true);
    wp_enqueue_style('fullcalendar-css', plugins_url('bower_components/fullcalendar/dist/fullcalendar.min.css', __FILE__));
		
		/* SWEETALERT 2 */
		wp_enqueue_script('sweetalert2-js', plugins_url('bower_components/sweetalert2/dist/sweetalert2.min.js', __FILE__), false, '', true);
    wp_enqueue_style('sweetalert2-css', plugins_url('bower_components/sweetalert2/dist/sweetalert2.min.css', __FILE__));
		
		/* PLUGIN */
		wp_enqueue_script('script-rbx-wp-calendar', plugins_url('script.js', __FILE__), false, '', true);
 
		wp_localize_script( 'fullcalendar-js', 'themeforce', array(
				'events' => plugins_url('json-feed.php', __FILE__),
				)
    );
		
  }
}

if ( !function_exists( 'rbx_wpcalendar_dashboard' ) ) {
	add_shortcode( 'rbx_wpcalendar', 'rbx_wpcalendar_dashboard' );
	function rbx_wpcalendar_dashboard(){
	?>  
	<div id='calendar'></div>
	
	<div id="formAddEvent" style="display: none;">
		<form>
			<input type="text" name="" placeholder="Nom de l'événement">
			<input type="datetime-local" name="" id="eventStartDay" value="">
			<input type="datetime-local" name="" id="eventEndDay" value="">
		</form>
	</div>
	
	<div id="yolo"></div>
	
	<?php
	}
}