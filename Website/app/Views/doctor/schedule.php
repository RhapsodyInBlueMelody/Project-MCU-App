<!-- TUI Calendar CSS -->
<link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
<!-- Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<!-- Calendar Header -->
<div id="calendar-header" class="flex items-center justify-center gap-4 mb-4">
    <button id="prev-month"
        class="p-2 rounded-full bg-blue-500 text-white text-lg shadow hover:bg-blue-600 transition duration-150"
        aria-label="Previous Month">
        &lt;
    </button>
    <span id="current-month-year" class="font-semibold text-xl text-gray-800 tracking-wide px-4"></span>
    <button id="next-month"
        class="p-2 rounded-full bg-blue-500 text-white text-lg shadow hover:bg-blue-600 transition duration-150"
        aria-label="Next Month">
        &gt;
    </button>
</div>

<!-- Calendar Container -->
<div id="calendar"
    class="bg-gray-50 dark:bg-gray-900 rounded-2xl shadow-lg p-4 border border-gray-200 dark:border-gray-800"
    style="height: 700px;">
</div>

<!-- TUI Calendar JS -->
<script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create calendar with theme
        const calendar = new tui.Calendar('#calendar', {
            defaultView: 'month',
            theme: {
                common: {
                    backgroundColor: '#f3f4f6', // Tailwind gray-100
                    border: '1px solid #e5e7eb', // Tailwind gray-200
                    gridSelection: {
                        backgroundColor: 'rgba(59, 130, 246, 0.08)',
                        border: '1px solid #3b82f6',
                    },
                    dayName: {
                        color: '#2563eb'
                    },
                    holiday: {
                        color: '#ef4444'
                    },
                    saturday: {
                        color: '#2563eb'
                    },
                    today: {
                        color: '#fbbf24'
                    },
                },
                month: {
                    dayExceptThisMonth: {
                        color: 'rgba(31, 41, 55, 0.25)'
                    },
                    dayName: {
                        borderLeft: 'none',
                        backgroundColor: '#e0e7ef',
                    },
                    holidayExceptThisMonth: {
                        color: 'rgba(239, 68, 68, 0.25)'
                    },
                    moreView: {
                        backgroundColor: '#f3f4f6',
                        border: '1px solid #e5e7eb',
                        boxShadow: '0 2px 12px 0 rgba(0,0,0,0.10)'
                    },
                    weekend: {
                        backgroundColor: '#f1f5f9'
                    },
                    gridCell: {
                        headerHeight: 28,
                        footerHeight: null,
                    }
                }
            }
        });

        // PHP doctorId to JS (make sure $doctorId is defined in PHP)
        const doctorId = <?= json_encode($doctorId) ?>;

        const monthNames = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        function updateMonthYearDisplay() {
            const date = calendar.getDate();
            const month = date.getMonth();
            const year = date.getFullYear();
            document.getElementById('current-month-year').textContent = monthNames[month] + ' ' + year;
        }

        function loadEvents(year, month) {
            fetch(`/dokter/schedule/api/${doctorId}?year=${year}&month=${month}`)
                .then(response => response.json())
                .then(events => {
                    calendar.clear();
                    calendar.createEvents(events);
                });
        }

        // Initialize calendar with current month
        const today = new Date();
        loadEvents(today.getFullYear(), today.getMonth() + 1);
        updateMonthYearDisplay();

        // Navigation buttons
        document.getElementById('prev-month').addEventListener('click', function() {
            calendar.prev();
            const date = calendar.getDate();
            loadEvents(date.getFullYear(), date.getMonth() + 1);
            updateMonthYearDisplay();
        });

        document.getElementById('next-month').addEventListener('click', function() {
            calendar.next();
            const date = calendar.getDate();
            loadEvents(date.getFullYear(), date.getMonth() + 1);
            updateMonthYearDisplay();
        });

        // Update month/year display if calendar is changed by other means
        calendar.on('afterRender', function() {
            updateMonthYearDisplay();
        });
    });
</script>
