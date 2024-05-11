<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>


    <!-- rrule lib -->
    <script src='https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js'></script>

    <!-- fullcalendar bundle -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

    <!-- the rrule-to-fullcalendar connector. must go AFTER the rrule lib -->
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@6.1.11/index.global.min.js'></script>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
    <script>

        const csrfToken = document.head.querySelector("[name=csrf-token][content]").content;
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                headerToolbar: {
                    start: 'prev next today',
                    center: 'title',
                    end: 'dayGridMonth timeGridWeek timeGridDay'
                },
                eventSources: [{
                    url: '/calendar/fetch',
                }],
                dateClick: function(info) {
                    alert('clicked ' + info.dateStr);
                },
                select: function(info) {
                    let clientName = window.prompt("client name:");
                    let startTime = info.startStr;
                    let endTime = info.endStr;
                    console.log(startTime, endTime, clientName);
                    fetch('/calendar/create', {
                        method: 'post',
                        body: JSON.stringify({clientName, startTime, endTime}),
                        headers: {
                            'Content-Type' : 'application/json',
                            'X-CSRF-TOKEN' : csrfToken
                        },
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            calendar.addEvent({
                                title: data.client_name,
                                start: data.start_date + 'T' + data.start_time,
                                end: data.end_date + 'T' + data.end_time
                            });
                            console.log('Sikerült: ', data);
                        })
                        .catch(error => {
                            console.error('Hiba történt:', error);
                        });
                }
            });
            calendar.render();
        });

    </script>
</head>
<body>
<div id='calendar'></div>
</body>
</html>

