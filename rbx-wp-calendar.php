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
//		wp_enqueue_script('gcal-js', plugins_url('bower_components/fullcalendar/dist/gcal.js', __FILE__), false, '', true);
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
	
		if($_POST['data']){
			
			global $wpdb;

			$current_user_ID = wp_get_current_user()->ID;
			
			// Récupération des données du form
			$params = array();

			// Mise en place des datas dans le tableau
			foreach($_POST['data'] as $item){
				$params[$item['name']] = $item['value'];
			}
			
			// Conversion des formats de dates
			$start_time = new DateTime($params['start_event']);
			$end_time = new DateTime($params['end_event']);
			$salle = $params['salle_event'];
			
			$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rbx_calendar", OBJECT);
			$string = array();
			
			foreach($results as $key){
				$events_start = $key->rbx_calendar_start_time;
				$events_end = $key->rbx_calendar_end_time;
				$event_salle = $key->slug;
				
				if(
					($start_time->format('Y-m-d H:i:s') >= $events_start && $start_time->format('Y-m-d H:i:s') < $events_end) ||
					($end_time->format('Y-m-d H:i:s') > $events_start && $end_time->format('Y-m-d H:i:s') <= $events_end) ||
					($start_time->format('Y-m-d H:i:s') < $events_start && $end_time->format('Y-m-d H:i:s') > $events_end)
					){
					if($salle === $event_salle){
						/* Réservation impossible */
						$autorisation = false;
					}
				}
			}
			
			if($autorisation === null){
				
				// Sauvegarde des données			
				$table = $wpdb->prefix.'rbx_calendar';

				$data = array(
					'author' => $current_user_ID,
					'name' => $params['nom_event'],
					'start_time' => $start_time->format('Y-m-d H:i:s'),
					'end_time' => $end_time->format('Y-m-d H:i:s'),
					'slug' => $params['salle_event'],
					'description' => $params['description_event']
				);

				$insertData = $wpdb->insert($table, $data);

				// Envoi de la réponse (ok si sauvegarde réussi)
				$update_options = json_encode(array(
						'update' => $insertData
				));

				echo $update_options;
				
			} else{
				// Envoi de la réponse (refus)
				$update_options = json_encode(array(
						'update' => 0
				));

				echo $update_options;
			}
			
		}
				
		die();
	}
}