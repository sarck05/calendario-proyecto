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

    // Estado inicial
    let currentView = 'month';
    let currentViewDate = new Date();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // 🔹 Calcular en qué semana del mes actual está el usuario
    function getWeekOfMonth(date) {
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        const firstDayWeekday = firstDay.getDay(); // 0=Domingo
        const dayOfMonth = date.getDate();
        return Math.ceil((dayOfMonth + firstDayWeekday) / 7);
    }

    let currentWeekOfMonth = getWeekOfMonth(today);

    // Nombres
    const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    const dayNames = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];

    // --- Clic en días (mes o semana) ---
    document.addEventListener('click', e => {
        const dayContainer = e.target.closest('.day-container, .week-day');
        if (!dayContainer || !dayContainer.dataset.date) return;

        const date = new Date(dayContainer.dataset.date);
        currentView = 'day';
        currentViewDate = date;
        document.querySelectorAll('.view-button').forEach(btn => btn.classList.remove('active'));
        document.querySelector('[data-view="day"]').classList.add('active');
        renderCalendar();
    });

    // --- Render general ---
    function renderCalendar() {
        updateHeader();

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

    // --- Vista mensual ---
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
            if (date > daysInMonth) break;
        }
    }

    // --- Vista semanal (actualizada con día actual resaltado) ---
    function renderWeekView() {
        const year = currentViewDate.getFullYear();
        const month = currentViewDate.getMonth();
        weekGridEl.innerHTML = '';

        const firstDayOfMonth = new Date(year, month, 1);
        const firstDayOfWeek = firstDayOfMonth.getDay();

        let startDay = 1 + (currentWeekOfMonth - 1) * 7 - firstDayOfWeek;
        if (currentWeekOfMonth === 1) startDay = 1 - firstDayOfWeek;
        if (startDay < 1) startDay = 1;

        let endDay = startDay + 6;
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        if (endDay > daysInMonth) endDay = daysInMonth;

        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(year, month, startDay + i);
            const dayEl = document.createElement('div');
            dayEl.classList.add('week-day');
            dayEl.dataset.date = dayDate.toISOString();

            // ✅ Resaltar el día actual
            const isToday =
                dayDate.getDate() === today.getDate() &&
                dayDate.getMonth() === today.getMonth() &&
                dayDate.getFullYear() === today.getFullYear();

            if (isToday) dayEl.classList.add('today');

            const dayHeader = document.createElement('div');
            dayHeader.classList.add('week-day-header');
            dayHeader.textContent = dayNames[dayDate.getDay()];
            dayEl.appendChild(dayHeader);

            const dayNumber = document.createElement('div');
            dayNumber.classList.add('week-day-number');
            dayNumber.textContent = dayDate.getDate();
            dayEl.appendChild(dayNumber);

            addEventsToContainer(dayEl, dayDate);
            weekGridEl.appendChild(dayEl);
        }
    }

    // --- Vista diaria ---
    function renderDayView() {
        const year = currentViewDate.getFullYear();
        const month = currentViewDate.getMonth();
        const day = currentViewDate.getDate();
        const dayDate = new Date(year, month, day);

        dayDateEl.textContent = `${dayNames[dayDate.getDay()]}, ${dayDate.getDate()} de ${monthNames[month]} de ${year}`;
        dayEventsEl.innerHTML = '';

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

    // --- Header ---
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

// --- Eventos reales desde la base de datos ---
function getEventsForDate(date) {
    const eventsForDay = [];
    const y = date.getFullYear();
    const m = (date.getMonth() + 1).toString().padStart(2, '0');
    const d = date.getDate().toString().padStart(2, '0');
    const formatted = `${y}-${m}-${d}`;

    if (!Array.isArray(eventos)) return eventsForDay;

    eventos.forEach(ev => {
        // Evita errores si 'fecha' no está bien formateada
        const eventDate = ev.fecha.split(' ')[0]; // por si viene con hora
        if (eventDate === formatted) {
            eventsForDay.push({
                title: ev.nombre,
                type: ev.tipo || 'general',
                descripcion: ev.descripcion || ''
            });
        }
    });

    return eventsForDay;
}

// --- Añadir eventos al contenedor ---
function addEventsToContainer(container, date) {
    const events = getEventsForDate(date);
    container.dataset.date = date.toISOString();

    events.forEach(event => {
        const eventEl = document.createElement('div');
        eventEl.classList.add('event', event.type);
        eventEl.textContent = event.title;
        eventEl.title = event.descripcion;
        container.appendChild(eventEl);
    });

    if (events.length > 0) container.classList.add('has-event');
}

    // --- Navegación ---
    prevButton.addEventListener('click', () => {
        switch (currentView) {
            case 'month':
                if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 0) break;
                currentViewDate.setMonth(currentViewDate.getMonth() - 1);
                break;
            case 'week':
                currentWeekOfMonth--;
                if (currentWeekOfMonth < 1) {
                    if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 0) {
                        currentWeekOfMonth = 1;
                    } else {
                        currentViewDate.setMonth(currentViewDate.getMonth() - 1);
                        currentWeekOfMonth = 4;
                    }
                }
                break;
            case 'day':
                const prevDate = new Date(currentViewDate);
                prevDate.setDate(prevDate.getDate() - 1);
                if (prevDate.getFullYear() < 2025) break;
                currentViewDate.setDate(currentViewDate.getDate() - 1);
                break;
        }
        renderCalendar();
    });

    nextButton.addEventListener('click', () => {
        switch (currentView) {
            case 'month':
                if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 11) break;
                currentViewDate.setMonth(currentViewDate.getMonth() + 1);
                break;
            case 'week':
                currentWeekOfMonth++;
                if (currentWeekOfMonth > 4) {
                    if (currentViewDate.getFullYear() === 2025 && currentViewDate.getMonth() === 11) {
                        currentWeekOfMonth = 4;
                    } else {
                        currentViewDate.setMonth(currentViewDate.getMonth() + 1);
                        currentWeekOfMonth = 1;
                    }
                }
                break;
            case 'day':
                const nextDate = new Date(currentViewDate);
                nextDate.setDate(nextDate.getDate() + 1);
                if (nextDate.getFullYear() > 2025) break;
                currentViewDate.setDate(currentViewDate.getDate() + 1);
                break;
        }
        renderCalendar();
    });

    // --- Botones de vista ---
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            currentView = button.dataset.view;
            renderCalendar();
        });
    });

    // --- Inicializar ---
    renderCalendar();
});
