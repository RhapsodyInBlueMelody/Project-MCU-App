<div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Manajemen Pasien</h3>
            <p class="text-sm text-gray-500">Daftar semua pasien yang terdaftar di sistem</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
            <button type="button" class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-download mr-2"></i> Export Data
            </button>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input id="search" name="search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Cari nama pasien atau email" type="search">
            </div>
        </div>

        <div class="w-full md:w-48">
            <label for="filter-status" class="sr-only">Filter by Status</label>
            <select id="filter-status" name="status" class="block w-full py-2 pl-3 pr-10 text-base border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
            </select>
        </div>
    </div>

    <?php if (empty($patients)): ?>
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Tidak ada pasien</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Belum ada pasien yang terdaftar dalam sistem.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pasien</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Lahir</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Janji Temu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="patients-table-body">
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-green-100 rounded-full">
                                        <i class="fas fa-user text-green-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc(
                                            $patient["NAMA_LENGKAP"]
                                        ) ?></div>
                                        <div class="text-sm text-gray-500">
                                            <?= isset($patient["JENIS_KELAMIN"])
                                                ? ($patient["JENIS_KELAMIN"] ==
                                                "L"
                                                    ? "Laki-laki"
                                                    : "Perempuan")
                                                : "-" ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= esc(
                                    $patient["EMAIL"] ?? "-"
                                ) ?></div>
                                <div class="text-sm text-gray-500"><?= esc(
                                    $patient["NO_TELP_PASIEN"] ?? "-"
                                ) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if (
                                    isset($patient["TANGGAL_LAHIR"]) &&
                                    $patient["TANGGAL_LAHIR"]
                                ): ?>
                                    <div class="text-sm text-gray-900"><?= date(
                                        "d M Y",
                                        strtotime($patient["TANGGAL_LAHIR"])
                                    ) ?></div>
                                    <div class="text-sm text-gray-500">
                                        <?php
                                        $birthdate = new DateTime(
                                            $patient["TANGGAL_LAHIR"]
                                        );
                                        $today = new DateTime("today");
                                        $age = $birthdate->diff($today)->y;
                                        echo $age . " tahun";
                                        ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-sm text-gray-500">-</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?= isset($patient["appointment_count"])
                                        ? $patient["appointment_count"]
                                        : "0" ?> janji temu
                                </div>
                                <?php if (
                                    isset($patient["last_appointment"]) &&
                                    $patient["last_appointment"]
                                ): ?>
                                    <div class="text-sm text-gray-500">
                                        Terakhir: <?= date(
                                            "d M Y",
                                            strtotime(
                                                $patient["last_appointment"]
                                            )
                                        ) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= base_url(
                                    "admin/patient/detail/" .
                                        $patient["PASIEN_ID"]
                                ) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDeletePatient(<?= $patient[
                                    "PASIEN_ID"
                                ] ?>, '<?= esc(
    $patient["NAMA_LENGKAP"]
) ?>')">Hapus</button>
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
    1 ?></span> of <span class="font-medium"><?= $pager->getTotal() ?></span> patients
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

<!-- Delete Confirmation Modal -->
<div id="deletePatientModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Hapus Pasien
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="delete-patient-message">
                                Apakah Anda yakin ingin menghapus pasien ini? Semua data termasuk riwayat janji temu akan dihapus. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form id="deletePatientForm" action="" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Hapus
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeDeletePatientModal()">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', filterPatients);

        // Filter by status
        const statusFilter = document.getElementById('filter-status');
        statusFilter.addEventListener('change', filterPatients);

        function filterPatients() {
            const searchTerm = searchInput.value.toLowerCase();
            const status = statusFilter.value.toLowerCase();

            const rows = document.querySelectorAll('#patients-table-body tr');

            rows.forEach(row => {
                const patientName = row.querySelector('td:nth-child(1) .text-gray-900').textContent.toLowerCase();
                const patientEmail = row.querySelector('td:nth-child(2) .text-gray-900').textContent.toLowerCase();
                const patientStatus = row.querySelector('td:nth-child(5) span').textContent.toLowerCase();

                const matchesSearch = patientName.includes(searchTerm) || patientEmail.includes(searchTerm);
                const matchesStatus = status === '' || patientStatus.includes(status);

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    });

    // Delete modal functions
    function confirmDeletePatient(patientId, patientName) {
        document.getElementById('delete-patient-message').textContent = `Apakah Anda yakin ingin menghapus pasien "${patientName}"? Semua data termasuk riwayat janji temu akan dihapus. Tindakan ini tidak dapat dibatalkan.`;
        document.getElementById('deletePatientForm').action = `${baseUrl}/admin/delete-patient/${patientId}`;
        document.getElementById('deletePatientModal').classList.remove('hidden');
    }

    function closeDeletePatientModal() {
        document.getElementById('deletePatientModal').classList.add('hidden');
    }
</script>
