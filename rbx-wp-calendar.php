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
				'ajaxurl' => admin_url( 'admin-ajax.php' )
				)
    );
		
  }
}

if ( !function_exists( 'rbx_wpcalendar_dashboard' ) ) {
	add_shortcode( 'rbx_wpcalendar', 'rbx_wpcalendar_dashboard' );
	function rbx_wpcalendar_dashboard(){
	?>  
	<div id='calendar'></div>
	<?php
	}
}

add_action( 'wp_ajax_' . 'createEvent', 'createEvent_function' );
add_action( 'wp_ajax_nopriv_' . 'createEvent', 'createEvent_function' );
if ( !function_exists( 'createEvent_function' ) ) {
	function createEvent_function(){

		global $wpdb;
		
		$current_user_ID = wp_get_current_user()->ID;
				
		if($_POST['data']){
			
			// Récupération des données du form
			$params = array();

			// Mise en place des datas dans le tableau
			foreach($_POST['data'] as $item){
				$params[$item['name']] = $item['value'];
			}
			
			var_dump($params['start_event']);

			// Sauvegarde des données			
//			$table = $wpdb->prefix.'rbx_calendar';
//			
//			$data = array(
//				'rbx_calendar_author' => $current_user_ID,
//				'rbx_calendar_name' => $params['nom_event'],
//				'rbx_calendar_start_time' => date($params['start_event']),
//				'rbx_calendar_end_time' => date($params['end_event']),
//				'slug' => $params['salle_event']
//			);
//						
//			$insertData = $wpdb->insert($table, $data);
													
			// Envoi de la réponse
			$update_options = json_encode(array(
//					'update' => update_option( $option_name, $params )
					'update' => $insertData
			));

			echo $update_options;
		}
				
		die();
	}
}