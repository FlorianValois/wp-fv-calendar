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
		timezone: 'local',
		//		timezone : 'Europe/Paris',

		select: function (start, end) {

			var eventStartDay = moment(start._d).format('DD/MM/YYYY HH:mm');
			var eventEndDay = moment(end._d).format('DD/MM/YYYY HH:mm');

			$('#eventStartDay').val(eventStartDay);
			$('#eventEndDay').val(eventEndDay);

			var formAddEvent = $('#formAddEvent > form').clone()[0];

			swal({
				title: 'Réserver une salle',
				html: formAddEvent,
				showCancelButton: true,
				cancelButtonColor: '#f3545d',
				cancelButtonText: 'Annuler',
				confirmButtonColor: '#92C83C',
				confirmButtonText: 'Réserver la salle',
			}).then(function (result) {
				if (result.value) {
					alert('ok');
				}else{
					alert('pas ok');
				}
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
					swal({
						type: 'error',
						title: 'Réservation impossible',
						html: 'Vous ne pouvez pas réserver cette salle car une réunion est déjà programmé pendant ce créneau.'
					})
					revertFunc();
				}

			});

		}

	});

});
