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
		slotDuration: '00:15:00',
		weekends: false,
		selectable: true,
		timezone: 'local',
		//		timezone: 'Europe/Paris',

		select: function (start, end) {


			var eventStartDay = moment(start._d).format('DD-MM-YYYY HH:mm');
			var eventEndDay = moment(end._d).format('DD-MM-YYYY HH:mm');

			console.log(start);
			console.log(moment(start._d).format('DD-MM-YYYY HH:mm'));

			var formAddEvent =
			'<form id="formAddEvent">' +
				'<input type="text" name="nom_event" id="eventName" placeholder="Nom de l\'événement" value="">' +
				'<input type="datetime-local" name="start_event" id="eventStartDay" value="' + eventStartDay + '">' +
				'<input type="datetime-local" name="end_event" id="eventEndDay" value="' + eventEndDay + '">' +
				'<select name="salle_event" id="eventSalle">' +
				'<option value="">---</option>' +
				'<option value="salle-reunion-1">Salle de réunion 1</option>' +
				'<option value="salle-reunion-2">Salle de réunion 2</option>' +
				'<option value="salle-reunion-3">Salle de réunion 3</option>' +
				'</select>' +
				'<textarea name="description_event" id="eventDescription"></textarea>'
			'</form>';

			swal({
				title: 'Réserver une salle',
				html: formAddEvent,
				showCancelButton: true,
				cancelButtonColor: '#f3545d',
				cancelButtonText: 'Annuler',
				confirmButtonColor: '#92C83C',
				confirmButtonText: 'Réserver la salle',
				confirmButtonClass: 'submitCreateEvent',
				showLoaderOnConfirm: true,
				reverseButtons: true,
				backdrop: 'rgba(0, 0, 0, .75)',
				preConfirm: function (inputValue) {

					return new Promise(function (resolve, reject) {

							var data_field = $('#formAddEvent').serializeArray();

							$.each(data_field, function (key, value) {
								if (!Object(value).value) {
									inputValue = false;
								}
							});

							if (inputValue === false) {
								reject()
							} else {
								resolve()
							}

						})
						.catch(error => {
							swal.showValidationMessage(
								'Remplissez tous les champs avant de valider votre réservation.'
							)
						})

				},
			}).then(function (result) {

				if (result.value) {

					var json = $('#formAddEvent').serializeArray();

					var postData = {
						action: 'createEvent',
						data: json
					}

					$.ajax({
						type: "POST",
						data: postData,
						dataType: "json",
						url: themeforce.ajaxurl,
						success: function (postData) {
							if (postData.create === 1) {
								swal({
									position: 'center',
									type: 'success',
									title: 'titre',
									text: 'sauvegardé',
									backdrop: 'rgba(0, 0, 0, .75)',
								})
								$('#calendar').fullCalendar('refetchEvents');
							}
							if (postData.create === 0) {
								swal({
									position: 'center',
									type: 'error',
									title: 'titre',
									text: 'VTFFB !',
									backdrop: 'rgba(0, 0, 0, .75)',
								})
							}
						}
					});

				} else {


				}
			})

		},

		events: themeforce.events,

		eventDrop: function checkOverlap(event, delta, revertFunc) {

			var start = new Date(event.start);
			var end = new Date(event.end);
			var salle = event.salle_de_reunion_slug;
			var idevent = event.id;

			var overlap = $('#calendar').fullCalendar('clientEvents', function (ev) {
				if (ev == event)
					return false;

				var estart = new Date(ev.start);
				var eend = new Date(ev.end);
				var esalle = ev.salle_de_reunion_slug;
				var eIDevent = ev.id;

				var resultEnd = end - eend;

				if (idevent != eIDevent && salle === esalle) {
					if (
						(start >= estart && start < eend) ||
						(end > estart && end < eend) ||
						(start > estart && end < eend) ||
						(start < estart && end > eend) ||
						(resultEnd === 0)
					) {
						swal({
							type: 'error',
							title: 'Réservation impossible',
							html: 'Vous ne pouvez pas réserver cette salle car une réunion est déjà programmé pendant ce créneau.'
						})
						revertFunc();
					} else {
												
						var dropData = {
							action: 'dropEvent',
							data: {
								id: event.id,
								author: event.author,
								name: event.title, 
								slug: event.salle_de_reunion_slug,
								start_time: moment(start).format("YYYY-MM-DD HH:mm:ss"),
								end_time: moment(end).format("YYYY-MM-DD HH:mm:ss"),
								description: event.description
							}
						}
																		
						$.ajax({
							type: "POST",
							data: dropData,
							dataType: "json",
							url: themeforce.ajaxurl,
							success: function (dropData) {
								if (dropData.update === 1) {
									swal({
										type: 'success',
										toast: true,
										position: 'center',
										title: 'Sauvegardé !',
										showConfirmButton: false,
										timer: 3000
									})
								}
							}
						});
						
					}

				}

			});

		},

		eventResize: function checkOverlap(event, delta, revertFunc) {

			var start = new Date(event.start);
			var end = new Date(event.end);
			var salle = event.salle_de_reunion_slug;
			var idevent = event.id;

			var autorisation = null;

			console.log(event);

			var overlap = $('#calendar').fullCalendar('clientEvents', function (ev) {
				if (ev == event)
					return false;

				var estart = new Date(ev.start);
				var eend = new Date(ev.end);
				var esalle = ev.salle_de_reunion_slug;
				var eIDevent = ev.id;

				var resultEnd = end - eend;

				if (idevent != eIDevent && salle === esalle) {
					if (
						(start >= estart && start < eend) ||
						(end > estart && end < eend) ||
						(start > estart && end < eend) ||
						(start < estart && end > eend) ||
						(resultEnd === 0)
					) {
						swal({
							type: 'error',
							title: 'Réservation impossible',
							html: 'Vous ne pouvez pas réserver cette salle car une réunion est déjà programmé pendant ce créneau.'
						})
						revertFunc();
					}

				}

			});

		},

		//		eventClick: function (event) {
		//			console.log(event);
		//		}

	});

});
