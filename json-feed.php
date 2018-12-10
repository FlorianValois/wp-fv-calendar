<?php

//if (!defined('ABSPATH')) {
//	exit;
//}

//header('Content-Type:application/json');
 
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
	
	var_dump($slug);
	
	$author_event = intval($event->author);
	$current_user_ID = wp_get_current_user()->ID;
	
	if($author_event === $current_user_ID){	
		
		$jsonevents[]= array(
			'id' =>$event->id,
			'author' => $event->author,
			'title' => $event->name,
			'salle_de_reunion' => $event->slug,
			'start' => $event->start_time,
			'end' => $event->end_time,
			'description' => $event->description,
			'backgroundColor' => $slug[0]->backgroundColor,
			'borderColor' => $slug[0]->borderColor,
			'textColor' => $slug[0]->textColor,
			'editable' => true
		);
		
	}
	else{
		$jsonevents[]= array(
			'id' =>$event->id,
			'author' => $event->author,
			'title' => $event->name,
			'salle_de_reunion' => $event->slug,
			'start' => $event->start_time,
			'end' => $event->end_time,
			'description' => $event->description,
			'backgroundColor' => $slug[0]->backgroundColor,
			'borderColor' => $slug[0]->borderColor,
			'textColor' => $slug[0]->textColor,
			'editable' => false
		);
	}
	
}

echo json_encode($jsonevents);

?>