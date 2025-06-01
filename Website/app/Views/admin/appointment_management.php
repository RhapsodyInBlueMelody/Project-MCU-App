<div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Manajemen Janji Temu</h3>
            <p class="text-sm text-gray-500">Kelola dan pantau semua janji temu</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
            <button type="button" class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-download mr-2"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-6">
        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 bg-opacity-75">
                    <i class="fas fa-calendar-alt text-blue-600 fa-fw"></i>
                </div>
                <div class="ml-4">
                    <h2 class="font-semibold text-xl text-gray-800"><?= isset(
                        $total
                    )
                        ? $total
                        : "0" ?></h2>
                    <p class="text-sm text-gray-600">Total Janji Temu</p>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 bg-opacity-75">
                    <i class="fas fa-clock text-yellow-600 fa-fw"></i>
                </div>
                <div class="ml-4">
                    <h2 class="font-semibold text-xl text-gray-800"><?= isset(
                        $pending
                    )
                        ? $pending
                        : "0" ?></h2>
                    <p class="text-sm text-gray-600">Menunggu Konfirmasi</p>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 bg-opacity-75">
                    <i class="fas fa-check-circle text-green-600 fa-fw"></i>
                </div>
                <div class="ml-4">
                    <h2 class="font-semibold text-xl text-gray-800"><?= isset(
                        $confirmed
                    )
                        ? $confirmed
                        : "0" ?></h2>
                    <p class="text-sm text-gray-600">Terkonfirmasi</p>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 bg-opacity-75">
                    <i class="fas fa-clipboard-check text-purple-600 fa-fw"></i>
                </div>
                <div class="ml-4">
                    <h2 class="font-semibold text-xl text-gray-800"><?= isset(
                        $completed
                    )
                        ? $completed
                        : "0" ?></h2>
                    <p class="text-sm text-gray-600">Selesai</p>
                </div>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 bg-opacity-75">
                    <i class="fas fa-times-circle text-red-600 fa-fw"></i>
                </div>
                <div class="ml-4">
                    <h2 class="font-semibold text-xl text-gray-800"><?= isset(
                        $cancelled
                    )
                        ? $cancelled
                        : "0" ?></h2>
                    <p class="text-sm text-gray-600">Dibatalkan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label for="search" class="sr-only">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input id="search" name="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Cari berdasarkan pasien, dokter, atau ID" type="search">
                </div>
            </div>

            <div>
                <label for="filter-status" class="sr-only">Filter Status</label>
                <select id="filter-status" name="status" class="block w-full py-2 pl-3 pr-10 text-base border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Konfirmasi</option>
                    <option value="confirmed">Terkonfirmasi</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                    <option value="awaiting_lab_results">Menunggu Hasil Lab</option>
                </select>
            </div>

            <div>
                <label for="filter-date" class="sr-only">Filter Tanggal</label>
                <input type="date" id="filter-date" name="date" class="block w-full py-2 pl-3 pr-10 text-base border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>

        <!-- Date filters shortcuts -->
        <div class="flex flex-wrap gap-2 mt-4">
            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="filterByDateRange('today')">
                Hari Ini
            </button>
            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="filterByDateRange('tomorrow')">
                Besok
            </button>
            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="filterByDateRange('week')">
                Minggu Ini
            </button>
            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="filterByDateRange('month')">
                Bulan Ini
            </button>
            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="clearFilters()">
                Reset Filter
            </button>
        </div>
    </div>

    <?php if (empty($appointments)): ?>
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Tidak ada janji temu</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Belum ada janji temu yang terdaftar dalam sistem.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Janji Temu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="appointments-table-body">
                    <?php foreach ($appointments as $appointment): ?>
                        <tr class="hover:bg-gray-50"
                            data-id="<?= $appointment["ID_JANJI_TEMU"] ?>"
                            data-patient="<?= strtolower(
                                esc($appointment["patient_name"])
                            ) ?>"
                            data-doctor="<?= strtolower(
                                esc($appointment["NAMA_DOKTER"])
                            ) ?>"
                            data-date="<?= date(
                                "Y-m-d",
                                strtotime($appointment["TANGGAL_JANJI"])
                            ) ?>"
                            data-status="<?= strtolower(
                                $appointment["STATUS"]
                            ) ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                #<?= $appointment["ID_JANJI_TEMU"] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= esc(
                                    $appointment["NAMA_JANJI"]
                                ) ?></div>
                                <?php if (isset($appointment["nama_paket"])): ?>
                                    <div class="text-sm text-gray-500"><?= esc(
                                        $appointment["nama_paket"]
                                    ) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-gray-100">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc(
                                            $appointment["patient_name"]
                                        ) ?></div>
                                        <?php if (
                                            isset($appointment["patient_phone"])
                                        ): ?>
                                            <div class="text-sm text-gray-500"><?= esc(
                                                $appointment["patient_phone"]
                                            ) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-blue-100">
                                        <i class="fas fa-user-md text-blue-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc(
                                            $appointment["NAMA_DOKTER"]
                                        ) ?></div>
                                        <?php if (
                                            isset(
                                                $appointment[
                                                    "nama_spesialisasi"
                                                ]
                                            )
                                        ): ?>
                                            <div class="text-sm text-gray-500"><?= esc(
                                                $appointment[
                                                    "nama_spesialisasi"
                                                ]
                                            ) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= date(
                                    "d M Y",
                                    strtotime($appointment["TANGGAL_JANJI"])
                                ) ?></div>
                                <div class="text-sm text-gray-500"><?= date(
                                    "H:i",
                                    strtotime($appointment["WAKTU_JANJI"])
                                ) ?> WIB</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusText = "";
                                $statusClass = "";

                                switch ($appointment["STATUS"]) {
                                    case "pending":
                                        $statusText = "Menunggu";
                                        $statusClass =
                                            "bg-yellow-100 text-yellow-800";
                                        break;
                                    case "confirmed":
                                        $statusText = "Terkonfirmasi";
                                        $statusClass =
                                            "bg-blue-100 text-blue-800";
                                        break;
                                    case "completed":
                                        $statusText = "Selesai";
                                        $statusClass =
                                            "bg-green-100 text-green-800";
                                        break;
                                    case "cancelled":
                                        $statusText = "Dibatalkan";
                                        $statusClass =
                                            "bg-red-100 text-red-800";
                                        break;
                                    case "awaiting_lab_results":
                                        $statusText = "Menunggu Hasil Lab";
                                        $statusClass =
                                            "bg-indigo-100 text-indigo-800";
                                        break;
                                    default:
                                        $statusText = $appointment["STATUS"];
                                        $statusClass =
                                            "bg-gray-100 text-gray-800";
                                }
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= base_url(
                                    "admin/appointment/view/" .
                                        $appointment["ID_JANJI_TEMU"]
                                ) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                <button type="button" class="text-blue-600 hover:text-blue-900" onclick="openStatusModal(<?= $appointment[
                                    "ID_JANJI_TEMU"
                                ] ?>, '<?= esc(
    $appointment["STATUS"]
) ?>')">Update</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager)): ?>
            <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($pager->hasPrevious()): ?>
                        <a href="<?= $pager->getPrevious() ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    <?php else: ?>
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100 cursor-not-allowed">
                            Previous
                        </span>
                    <?php endif; ?>

                    <?php if ($pager->hasNext()): ?>
                        <a href="<?= $pager->getNext() ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    <?php else: ?>
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-100 cursor-not-allowed">
                            Next
                        </span>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium"><?= $pager->getFirstIndex() +
                                1 ?></span> to <span class="font-medium"><?= $pager->getLastIndex() +
    1 ?></span> of <span class="font-medium"><?= $pager->getTotal() ?></span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php if ($pager->hasPrevious()): ?>
                                <a href="<?= $pager->getPrevious() ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-5 w-5"></i>
                                </a>
                            <?php else: ?>
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-5 w-5"></i>
                                </span>
                            <?php endif; ?>

                            <?php foreach ($pager->links() as $link): ?>
                                <a href="<?= $link["uri"] ?>" class="<?= $link[
    "active"
]
    ? "bg-indigo-50 border-indigo-500 text-indigo-600"
    : "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    <?= $link["title"] ?>
                                </a>
                            <?php endforeach; ?>

                            <?php if ($pager->hasNext()): ?>
                                <a href="<?= $pager->getNext() ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-5 w-5"></i>
                                </a>
                            <?php else: ?>
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-5 w-5"></i>
                                </span>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-edit text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Update Status Janji Temu
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Pilih status baru untuk janji temu ini.
                            </p>
                        </div>
                        <div class="mt-4">
                            <form id="updateStatusForm" action="" method="post">
                                <?= csrf_field() ?>
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="pending">Menunggu Konfirmasi</option>
                                        <option value="confirmed">Terkonfirmasi</option>
                                        <option value="completed">Selesai</option>
                                        <option value="cancelled">Dibatalkan</option>
                                        <option value="awaiting_lab_results">Menunggu Hasil Lab</option>
                                    </select>
                                </div>
                                <div class="mb-4" id="reasonContainer" style="display: none;">
                                    <label for="reason" class="block text-sm font-medium text-gray-700">Alasan (jika dibatalkan)</label>
                                    <textarea id="reason" name="reason" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="submitStatusUpdate()">
                    Update Status
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeStatusModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search and filter functionality
        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('filter-status');
        const dateFilter = document.getElementById('filter-date');

        searchInput.addEventListener('input', filterAppointments);
        statusFilter.addEventListener('change', filterAppointments);
        dateFilter.addEventListener('change', filterAppointments);

        // Status dropdown logic
        const statusDropdown = document.getElementById('status');
        statusDropdown.addEventListener('change', function() {
            const reasonContainer = document.getElementById('reasonContainer');
            if (this.value === 'cancelled') {
                reasonContainer.style.display = 'block';
            } else {
                reasonContainer.style.display = 'none';
            }
        });

        // Filter function
        function filterAppointments() {
            const searchTerm = searchInput.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();
            const date = dateFilter.value;

            const rows = document.querySelectorAll('#appointments-table-body tr');

            rows.forEach(row => {
                const id = row.getAttribute('data-id').toLowerCase();
                const patient = row.getAttribute('data-patient');
                const doctor = row.getAttribute('data-doctor');
                const appointmentDate = row.getAttribute('data-date');
                const appointmentStatus = row.getAttribute('data-status');

                const matchesSearch = id.includes(searchTerm) ||
                                    patient.includes(searchTerm) ||
                                    doctor.includes(searchTerm);

                const matchesStatus = status === '' || appointmentStatus === status;
                const matchesDate = date === '' || appointmentDate === date;

                if (matchesSearch && matchesStatus && matchesDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    });

    // Date range filter functions
    function filterByDateRange(range) {
        const dateFilter = document.getElementById('filter-date');
        const today = new Date();

        switch(range) {
            case 'today':
                dateFilter.value = formatDate(today);
                break;
            case 'tomorrow':
                const tomorrow = new Date();
                tomorrow.setDate(today.getDate() + 1);
                dateFilter.value = formatDate(tomorrow);
                break;
            case 'week':
                // Reset date filter and filter manually by week
                dateFilter.value = '';
                filterAppointmentsByWeek();
                return;
            case 'month':
                // Reset date filter and set month in separate function
                dateFilter.value = '';
                filterAppointmentsByMonth();
                return;
        }

        // Trigger the change event
        dateFilter.dispatchEvent(new Event('change'));
    }

    function filterAppointmentsByWeek() {
        const today = new Date();
        const startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)

        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6); // End of week (Saturday)

        const rows = document.querySelectorAll('#appointments-table-body tr');

        rows.forEach(row => {
            const appointmentDateStr = row.getAttribute('data-date');
            const appointmentDate = new Date(appointmentDateStr);

            if (appointmentDate >= startOfWeek && appointmentDate <= endOfWeek) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function filterAppointmentsByMonth() {
        const today = new Date();
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();

        const rows = document.querySelectorAll('#appointments-table-body tr');

        rows.forEach(row => {
            const appointmentDateStr = row.getAttribute('data-date');
            const appointmentDate = new Date(appointmentDateStr);

            if (appointmentDate.getMonth() === currentMonth && appointmentDate.getFullYear() === currentYear) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function clearFilters() {
        document.getElementById('search').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-date').value = '';

        // Show all rows
        const rows = document.querySelectorAll('#appointments-table-body tr');
        rows.forEach(row => {
            row.style.display = '';
        });
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Status modal functions
    let currentAppointmentId = null;

    function openStatusModal(appointmentId, currentStatus) {
        currentAppointmentId = appointmentId;

        // Set the form action URL
        document.getElementById('updateStatusForm').action = `${baseUrl}/admin/appointment/update-status/${appointmentId}`;

        // Set the current status in the dropdown
        const statusDropdown = document.getElementById('status');
        statusDropdown.value = currentStatus;

        // Show/hide reason field based on status
        const reasonContainer = document.getElementById('reasonContainer');
        reasonContainer.style.display = currentStatus === 'cancelled' ? 'block' : 'none';

        // Show the modal
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
        currentAppointmentId = null;
    }

    function submitStatusUpdate() {
        document.getElementById('updateStatusForm').submit();
    }
</script>
