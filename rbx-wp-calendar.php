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

if ( !function_exists( 'rbx_wpcalendar_create_table' ) ) {
	register_activation_hook( __FILE__, 'rbx_wpcalendar_create_table' );
  function rbx_wpcalendar_create_table() {

		global $wpdb;
				
		$charset_collate = $wpdb->get_charset_collate();
		
		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		
		$rbx_calendar = $wpdb->prefix.'rbx_calendar';
		
		$sql = "CREATE TABLE IF NOT EXISTS $rbx_calendar (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			author bigint(20) NOT NULL,
			name text NOT NULL,
			slug varchar(200) NOT NULL,
			start_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			end_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			description text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		dbDelta( $sql );
		
		$wpdb->query("
		INSERT INTO $rbx_calendar 
		(id, author, name, slug, start_time, end_time, description) 
		VALUES 
		('1', '1', 'Réunion Salle 1er étage', 'salle-reunion-1', '2000-01-01 08:00:00', '2000-01-01 08:30:00', 'data test'),
		('2', '1', 'Réunion Salle 2nd étage', 'salle-reunion-2', '2000-01-01 08:30:00', '2000-01-01 09:00:00', 'data test'),
		('3', '1', 'Réunion Salle 3ème étage', 'salle-reunion-3', '2000-01-01 09:00:00', '2000-01-01 09:30:00', 'data test'),
		('4', '1', 'Réunion Salle 4ème étage', 'salle-reunion-4', '2000-01-01 09:30:00', '2000-01-01 010:00:00', 'data test'),
		('5', '1', 'Réunion Salle 5ème étage', 'salle-reunion-5', '2000-01-01 10:00:00', '2000-01-01 010:30:00', 'data test'),
		('6', '1', 'Réunion Salle 6ème étage', 'salle-reunion-6', '2000-01-01 10:30:00', '2000-01-01 011:00:00', 'data test'),
		('7', '1', 'Réunion Salle 7ème étage', 'salle-reunion-7', '2000-01-01 11:00:00', '2000-01-01 011:30:00', 'data test'),
		('8', '1', 'Réunion Salle 8ème étage', 'salle-reunion-8', '2000-01-01 11:30:00', '2000-01-01 12:00:00', 'data test')
		");
				
		$rbx_calendar_category = $wpdb->prefix.'rbx_calendar_category';
		
		$sql = "CREATE TABLE IF NOT EXISTS $rbx_calendar_category (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			name text NOT NULL,
			slug varchar(200) NOT NULL,
			background_color text NOT NULL,
			border_color text NOT NULL,
			text_color text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		dbDelta( $sql );
			
		$wpdb->query("
		INSERT INTO $rbx_calendar_category 
		(id, name, slug, background_color, border_color, text_color) 
		VALUES 
		('1', 'Salle de réunion 1', 'salle-reunion-1', '#ff9831', '#cc6b2e', '#723a1c'),
		('2', 'Salle de réunion 2', 'salle-reunion-2', '#4fa756', '#37773c', '#1b4b1f'),
		('3', 'Salle de réunion 3', 'salle-reunion-3', '#DE4E4E', '#9E1D1D', '#9E1D1D')
		");
		
  }
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
				$create_options = json_encode(array(
						'create' => $insertData
				));

				echo $create_options;
				
			} else{
				// Envoi de la réponse (refus)
				$create_options = json_encode(array(
						'create' => 0
				));

				echo $create_options;
			}
			
		}
				
		die();
	}
}

add_action( 'wp_ajax_' . 'updateEvent', 'updateEvent_function' );
add_action( 'wp_ajax_nopriv_' . 'updateEvent', 'updateEvent_function' );
if ( !function_exists( 'updateEvent_function' ) ) {
	function updateEvent_function(){
						
		if($_POST['data']){
			
			global $wpdb;
			
			// Récupération des données du form
			$params = array();

			// Mise en place des datas dans le tableau
			foreach($_POST['data'] as $key => $value){
//				var_dump($key);
				$params[$key] = $value;
			}
			
			
//			$start_time = $params['start_time'];
//			$end_time = new DateTime($params['end_time']);
			
//			var_dump($_POST['data']);
			
			
			// Sauvegarde des données			
			$table = $wpdb->prefix.'rbx_calendar';

			$data = array(
//				'author' => $params['author'],
//				'name' => $params['name'],
				'start_time' => $params['start_time'],
				'end_time' => $params['end_time']
//				'slug' => $params['slug'],
//				'description' => $params['description']
			);
			
			$tid = array(
				'id' => $params['id']
			);
			
			$updateData = $wpdb->update(
				$table, 
				$data, 
				$tid,
				array( 
					'%s',	
					'%s'	
				), 
				array( '%d' ) 
			);
			
			$update_options = json_encode(array(
//					'update' => 1,
					'update' => $updateData
			));

			echo $update_options;
			
		}
	
		die();
	}
}