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
		selectable: true,
		
		select: function (start, end) {
			
			var formAddEvent = $('#formAddEvent > form').clone()[0];
			
			alert(start._d);
			console.log(end);
			
			
			swal({
				title: 'Error!',
				html: formAddEvent,
				confirmButtonText: 'Cool'
			})
			
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
					alert('Vous ne pouvez pas réserver sur la même heure.');
					revertFunc();
				}

			});

		}

	});

});
