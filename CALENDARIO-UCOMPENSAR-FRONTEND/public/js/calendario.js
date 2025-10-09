document.addEventListener('DOMContentLoaded', () => {
    const periodEl = document.getElementById('currentPeriod');
    const calendarDaysEl = document.getElementById('calendarDays');
    const weekGridEl = document.getElementById('weekGrid');
    const dayDateEl = document.getElementById('dayDate');
    const dayEventsEl = document.getElementById('dayEvents');
    const prevButton = document.getElementById('prevButton');
    const nextButton = document.getElementById('nextButton');
    const viewButtons = document.querySelectorAll('.view-button');
    const monthViewEl = document.getElementById('monthView');
    const weekViewEl = document.getElementById('weekView');
    const dayViewEl = document.getElementById('dayView');

    // Estado de la aplicación
    let currentView = 'month';
    let currentViewDate = new Date();
    let currentWeekOfMonth = 1; // 1-4 para las semanas del mes
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    const dayNames = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];

    // --- FUNCIONES ---

    function renderCalendar() {
        updateHeader();

        // Ocultar todas las vistas
        monthViewEl.style.display = 'none';
        weekViewEl.style.display = 'none';
        dayViewEl.style.display = 'none';

        switch (currentView) {
            case 'month':
                renderMonthView();
                monthViewEl.style.display = 'block';
                break;
            case 'week':
                renderWeekView();
                weekViewEl.style.display = 'block';
                break;
            case 'day':
                renderDayView();
                dayViewEl.style.display = 'block';
                break;
        }
    }

    function renderMonthView() {
        const year = currentViewDate.getFullYear();
        const month = currentViewDate.getMonth();

        calendarDaysEl.innerHTML = '';

        const firstDayOfMonth = new Date(year, month, 1);
        const lastDayOfMonth = new Date(year, month + 1, 0);

        const firstDayOfWeek = firstDayOfMonth.getDay();
        const daysInMonth = lastDayOfMonth.getDate();

        let date = 1;
        for (let i = 0; i < 6; i++) {
            const row = document.createElement('tr');

            for (let j = 0; j < 7; j++) {
                const cell = document.createElement('td');

                if (i === 0 && j < firstDayOfWeek) {
                    cell.classList.add('empty');
                } else if (date > daysInMonth) {
                    cell.classList.add('empty');
                } else {
                    const dayContainer = document.createElement('div');
                    dayContainer.classList.add('day-container');

                    const dayNumberEl = document.createElement('div');
                    dayNumberEl.classList.add('day-number');
                    dayNumberEl.textContent = date;
                    dayContainer.appendChild(dayNumberEl);

                    // Añadir eventos de ejemplo
                    addEventsToContainer(dayContainer, new Date(year, month, date));

                    cell.appendChild(dayContainer);

                    const cellDate = new Date(year, month, date);
                    if (cellDate.getTime() === today.getTime()) {
                        cell.classList.add('day', 'today');
                    }

                    date++;
                }
                row.appendChild(cell);
            }
            calendarDaysEl.appendChild(row);
            if (date > daysInMonth) {
                break;
            }
        }
    }

    function renderWeekView() {
        const year = currentViewDate.getFullYear();
        const month = currentViewDate.getMonth();

        weekGridEl.innerHTML = '';

        // Calcular el inicio de la semana actual
        const firstDayOfMonth = new Date(year, month, 1);
        const firstDayOfWeek = firstDayOfMonth.getDay();

        // Calcular el día de inicio de la semana actual (1-4)
        let startDay = 1 + (currentWeekOfMonth - 1) * 7 - firstDayOfWeek;
        if (currentWeekOfMonth === 1) {
            startDay = 1 - firstDayOfWeek;
        }

        // Asegurarse de que no sea negativo
        if (startDay < 1) {
            startDay = 1;
        }

        // Calcular el día final de la semana
        let endDay = startDay + 6;
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        if (endDay > daysInMonth) {
            endDay = daysInMonth;
        }

        // Crear las celdas para cada día de la semana
        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(year, month, startDay + i);

            // Si el día está fuera del mes actual, mostrar una celda vacía
            if (dayDate.getMonth() !== month || dayDate.getDate() < startDay || dayDate.getDate() > endDay) {
                const emptyDay = document.createElement('div');
                emptyDay.classList.add('week-day', 'empty');
                weekGridEl.appendChild(emptyDay);
                continue;
            }

            const dayEl = document.createElement('div');
            dayEl.classList.add('week-day');

            if (dayDate.getTime() === today.getTime()) {
                dayEl.classList.add('today');
            }

            const dayHeader = document.createElement('div');
            dayHeader.classList.add('week-day-header');
            dayHeader.textContent = dayNames[dayDate.getDay()];
            dayEl.appendChild(dayHeader);

            const dayNumber = document.createElement('div');
            dayNumber.classList.add('week-day-number');
            dayNumber.textContent = dayDate.getDate();
            dayEl.appendChild(dayNumber);

            // Añadir eventos
            addEventsToContainer(dayEl, dayDate);

            weekGridEl.appendChild(dayEl);
        }
    }

    function renderDayView() {
        const year = currentViewDate.getFullYear();
        const month = currentViewDate.getMonth();
        const day = currentViewDate.getDate();

        const dayDate = new Date(year, month, day);

        // Actualizar la fecha del día
        dayDateEl.textContent = `${dayNames[dayDate.getDay()]}, ${dayDate.getDate()} de ${monthNames[month]} de ${year}`;

        // Limpiar eventos anteriores
        dayEventsEl.innerHTML = '';

        // Añadir eventos del día
        const events = getEventsForDate(dayDate);

        if (events.length === 0) {
            const noEvents = document.createElement('p');
            noEvents.textContent = 'No hay eventos para este día';
            dayEventsEl.appendChild(noEvents);
        } else {
            events.forEach(event => {
                const eventEl = document.createElement('div');
                eventEl.classList.add('day-event');
                eventEl.innerHTML = `<h3>${event.title}</h3>`;
                dayEventsEl.appendChild(eventEl);
            });
        }
    }

    function updateHeader() {
        const year = currentViewDate.getFullYear();
        const month = currentViewDate.getMonth();

        switch (currentView) {
            case 'month':
                periodEl.textContent = `${monthNames[month]} ${year}`;
                break;
            case 'week':
                periodEl.textContent = `${monthNames[month]} ${year} - Semana ${currentWeekOfMonth}`;
                break;
            case 'day':
                const day = currentViewDate.getDate();
                periodEl.textContent = `${dayNames[currentViewDate.getDay()]}, ${day} de ${monthNames[month]} de ${year}`;
                break;
        }
    }

    function getEventsForDate(date) {
        // En una aplicación real, esto vendría de una base de datos o API
        const events = [];

        // Eventos de ejemplo
        if (date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear()) {
            if (date.getDate() === 15) {
                events.push({ title: 'Reunión importante', type: 'work' });
            }
            if (date.getDate() === 22) {
                events.push({ title: 'Cumpleaños', type: 'personal' });
            }
            if (date.getDate() === 28) {
                events.push({ title: 'Médico', type: 'personal' });
            }
        }

        return events;
    }

    function addEventsToContainer(container, date) {
        const events = getEventsForDate(date);

        events.forEach(event => {
            const eventEl = document.createElement('div');
            eventEl.classList.add('event', event.type);
            eventEl.textContent = event.title;
            container.appendChild(eventEl);
        });
    }

    // --- EVENT LISTENERS ---
    prevButton.addEventListener('click', () => {
        switch (currentView) {
            case 'month':
                // Evitar retroceder antes del año 2025
                if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 0) {
                    // Si estamos en enero 2025, no permitir ir atrás
                    break;
                }
                currentViewDate.setMonth(currentViewDate.getMonth() - 1);
                break;
            case 'week':
                currentWeekOfMonth--;
                if (currentWeekOfMonth < 1) {
                    // Antes de cambiar mes, validar año y mes para no ir antes de 2025
                    if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 0) {
                        currentWeekOfMonth = 1; // No permitir bajar más
                    } else {
                        currentViewDate.setMonth(currentViewDate.getMonth() - 1);
                        currentWeekOfMonth = 4;
                    }
                }
                break;
            case 'day':
                // Evitar retroceder antes del 1 de enero de 2025
                const prevDate = new Date(currentViewDate);
                prevDate.setDate(prevDate.getDate() - 1);
                if (prevDate.getFullYear() < 2025) {
                    break;
                }
                currentViewDate.setDate(currentViewDate.getDate() - 1);
                break;
        }
        renderCalendar();
    });

    nextButton.addEventListener('click', () => {
        switch (currentView) {
            case 'month':
                // Evitar avanzar más allá de diciembre 2025
                if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 11) {
                    break;
                }
                currentViewDate.setMonth(currentViewDate.getMonth() + 1);
                break;
            case 'week':
                currentWeekOfMonth++;
                if (currentWeekOfMonth > 4) {
                    // Antes de cambiar mes, validar año y mes para no ir más allá de 2025
                    if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 11) {
                        currentWeekOfMonth = 4; // No permitir subir más
                    } else {
                        currentViewDate.setMonth(currentViewDate.getMonth() + 1);
                        currentWeekOfMonth = 1;
                    }
                }
                break;
            case 'day':
                // Evitar avanzar más allá del 31 de diciembre de 2025
                const nextDate = new Date(currentViewDate);
                nextDate.setDate(nextDate.getDate() + 1);
                if (nextDate.getFullYear() > 2025) {
                    break;
                }
                currentViewDate.setDate(currentViewDate.getDate() + 1);
                break;
        }
        renderCalendar();
    });

    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            currentView = button.dataset.view;
            renderCalendar();
        });
    });

    // --- INICIALIZACIÓN ---
    renderCalendar();
});
