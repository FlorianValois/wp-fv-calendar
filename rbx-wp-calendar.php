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
		wp_enqueue_script('moment-js', plugins_url('bower_components/moment/min/moment.min.js', __FILE__), false, '', true);
		wp_enqueue_script('fullcalendar-js', plugins_url('bower_components/fullcalendar/dist/fullcalendar.min.js', __FILE__), false, '', true);
		wp_enqueue_script('fullcalendar-locale-js', plugins_url('bower_components/fullcalendar/dist/locale/fr.js', __FILE__), false, '', true);
    wp_enqueue_style('fullcalendar-css', plugins_url('bower_components/fullcalendar/dist/fullcalendar.min.css', __FILE__));
		wp_enqueue_script('script-rbx-wp-calendar', plugins_url('script.js', __FILE__), false, '', true);
 
		wp_localize_script( 'fullcalendar-js', 'themeforce', array(
				'events' => plugins_url('json-feed.php', __FILE__),
				)
    );
		
  }
}

//if ( !function_exists( 'rbx_wpcalendar_init' ) ) {
//	add_action('admin_menu','rbx_wpcalendar_init');
//  function rbx_wpcalendar_init(){ 
//    add_menu_page(
//      __( 'WP Calendar', 'rbx-wp-calendar' ), 
//      __( 'WP Calendar', 'rbx-wp-calendar' ), 
//      'manage_options', 
//      'rbx_wpcalendar', 
//      'rbx_wpcalendar_dashboard', 
//      'dashicons-vault', /* A remplacer avec le ligne ci-dessous */
//  //    plugins_url('admin/images/favicon.png', __FILE__), 
//      99 
//    );
//  }
//}

if ( !function_exists( 'rbx_wpcalendar_dashboard' ) ) {
	add_shortcode( 'rbx_wpcalendar', 'rbx_wpcalendar_dashboard' );
	function rbx_wpcalendar_dashboard(){
	?>  
	<div id='calendar'></div>
	<?php
	}
}