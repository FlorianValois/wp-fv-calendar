jQuery(document).ready(function ($) {

	$('#calendar').fullCalendar({
		defaultView: 'agendaWeek',
		firstDay: 1,
		navLinks: true,
		allDaySlot: false,
		slotEventOverlap: false,
		header: {
			left: 'prev,next today myCustomButton',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		minTime: '08:00:00',
		maxTime: '19:00:00',
		weekends: false,
		//		eventRender: function (calEvent) {
		////			if (calEvent.salle_de_reunion === 'salle-reunion-1') {
		//				calEvent.editable = true;
		////			}
		////			else{
		////				calEvent.editable = false;
		////			}
		////			console.log(calEvent);
		////			console.log(calEvent.salle_de_reunion);
		//		},
		eventClick: function (calEvent, jsEvent, view) {

			alert(calEvent.title);

			// Formulaire sweetalert

			//			alert('Event: ' + calEvent.start['_i']);
			//			alert('Event: ' + calEvent.end['_i']);
			console.log(calEvent);

		},

		events: themeforce.events,

		eventDrop: function checkOverlap(event, delta, revertFunc) {

			var start = new Date(event.start);
			var end = new Date(event.end);
			var salle = event.salle_de_reunion;

			var overlap = $('#calendar').fullCalendar('clientEvents', function (ev) {
				if (ev == event)
					return false;

				var estart = new Date(ev.start);
				var eend = new Date(ev.end);
				var esalle = ev.salle_de_reunion;

				if ((start > estart && start < eend && salle === esalle) || (end > estart && end < eend && salle === esalle)) {
					alert('ok');
					revertFunc();
				}

			});

		}

	});

});