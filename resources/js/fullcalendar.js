import '../css/fullcalendar.css';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import enGbLocale from '@fullcalendar/core/locales/en-gb';

window.FullCalendar = { Calendar, dayGridPlugin, timeGridPlugin, listPlugin, enGbLocale };

