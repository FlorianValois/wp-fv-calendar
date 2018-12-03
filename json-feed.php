<?php

//if (!defined('ABSPATH')) {
//	exit;
//}

header('Content-Type:application/json');
 
// - grab wp load, wherever it's hiding -
if(file_exists('../../../wp-load.php')) :
    include '../../../wp-load.php';
else:
    include '../../../../wp-load.php';
endif;
 
global $wpdb;

$events = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rbx_calendar", OBJECT );

$jsonevents = array();

foreach($events as $event){
	
	$slug = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rbx_calendar_category WHERE slug = '$event->slug'", OBJECT );
	
	$author_event = intval($event->rbx_calendar_author);
	$current_user_ID = wp_get_current_user()->ID;
	
//	if($author_event === $current_user_ID){	
		
		$jsonevents[]= array(
			'author' => $event->rbx_calendar_author,
			'title' => $event->rbx_calendar_name,
			'salle_de_reunion' => $event->slug,
			'start' => $event->rbx_calendar_start_time,
			'end' => $event->rbx_calendar_end_time,
			'backgroundColor' => $slug[0]->backgroundColor,
			'borderColor' => $slug[0]->borderColor,
			'textColor' => $slug[0]->textColor,
			'editable' => true
		);
		
//	}
//	else{
//		$jsonevents[]= array(
//			'author' => $event->rbx_calendar_author,
//			'title' => $event->rbx_calendar_name,
//			'salle_de_reunion' => $event->slug,
//			'start' => $event->rbx_calendar_start_time,
//			'end' => $event->rbx_calendar_end_time,
//			'backgroundColor' => $slug[0]->backgroundColor,
//			'borderColor' => $slug[0]->borderColor,
//			'textColor' => $slug[0]->textColor,
//			'editable' => false
//		);
//	}
	
}

echo json_encode($jsonevents);

?>