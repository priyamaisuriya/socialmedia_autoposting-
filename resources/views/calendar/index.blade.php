@extends('layouts.premium')

@section('content')
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    <style>
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: var(--glass-border) !important;
        }
        .fc-theme-standard .fc-scrollgrid {
            border-color: var(--glass-border) !important;
        }
        .fc .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
        }
        .fc .fc-button-primary {
            background-color: var(--nav-active);
            border-color: var(--glass-border);
            color: var(--text-main);
            border-radius: 12px;
            text-transform: capitalize;
            font-weight: 600;
        }
        .fc .fc-button-primary:not(:disabled):active,
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:hover {
            background-color: var(--accent) !important;
            border-color: var(--accent) !important;
        }
        .fc-daygrid-day-number {
            color: var(--text-main);
            font-weight: 600;
        }
        .fc-col-header-cell-cushion {
            color: var(--text-muted);
            font-weight: 800;
            text-transform: uppercase;
        }
        .fc-day-today {
            background: rgba(99, 102, 241, 0.05) !important;
        }
        .fc-event {
            border-radius: 6px;
            padding: 2px 4px;
            font-size: 0.8rem;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .fc-event:hover {
            transform: scale(1.02);
            filter: brightness(1.1);
        }
    </style>

    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; background: linear-gradient(135deg, var(--text-main), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem;">
                Content Calendar
            </h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0;">Visualize your past and scheduled automated content.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <!-- Legend -->
            <div style="display: flex; align-items: center; gap: 1rem; background: var(--bg-main); border: 1px solid var(--glass-border); padding: 0.5rem 1rem; border-radius: 12px; font-size: 0.8rem; font-weight: 700; color: var(--text-main);">
                <div style="display: flex; align-items: center; gap: 6px;"><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span> Published</div>
                <div style="display: flex; align-items: center; gap: 6px;"><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span> Scheduled</div>
                <div style="display: flex; align-items: center; gap: 6px;"><span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span> Failed</div>
            </div>
            <a href="{{ route('posts.create') }}" class="btn-primary" style="padding: 0.8rem 1.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 12px; font-weight: 600;">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Schedule Post
            </a>
        </div>
    </div>

    <!-- Calendar Quick Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <!-- Total Posts -->
        <div class="premium-card" style="padding: 1.5rem; background: var(--card-bg);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="layers" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Total Posts</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main);">{{ number_format($stats['total']) }}</div>
        </div>

        <!-- Scheduled (Pending) Posts -->
        <div class="premium-card" style="padding: 1.5rem; background: var(--card-bg); border-color: rgba(245, 158, 11, 0.3);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="clock" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Scheduled / Pending</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: #f59e0b;">{{ number_format($stats['scheduled']) }}</div>
        </div>

        <!-- Failed / Canceled Posts -->
        <div class="premium-card" style="padding: 1.5rem; background: var(--card-bg); border-color: rgba(239, 68, 68, 0.3);">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="x-circle" style="width: 20px; height: 20px;"></i>
                </div>
            </div>
            <div style="color: var(--text-muted); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Failed / Canceled</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: #ef4444;">{{ number_format($stats['failed']) }}</div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="premium-card" style="padding: 2rem; background: var(--card-bg);">
        <div id="calendar"></div>
    </div>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: "{{ route('calendar.events') }}",
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                displayEventTime: true,
                dayMaxEvents: true, // allow "more" link when too many events
                eventClick: function(info) {
                    info.jsEvent.preventDefault(); // don't let the browser navigate
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                }
            });

            calendar.render();
            lucide.createIcons();
        });
    </script>
@endsection
