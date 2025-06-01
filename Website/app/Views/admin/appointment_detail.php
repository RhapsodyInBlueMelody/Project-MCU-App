<div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Detail Janji Temu</h3>
            <p class="text-sm text-gray-500">ID: #<?= $appointment[
                "ID_JANJI_TEMU"
            ] ?></p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
            <a href="<?= base_url(
                "admin/appointment-management"
            ) ?>" class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button type="button" class="inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="printAppointmentDetail()">
                <i class="fas fa-print mr-2"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <?php
        $statusText = "";
        $statusClass = "";

        switch ($appointment["STATUS"]) {
            case "pending":
                $statusText = "Menunggu Konfirmasi";
                $statusClass = "bg-yellow-100 text-yellow-800";
                break;
            case "confirmed":
                $statusText = "Terkonfirmasi";
                $statusClass = "bg-blue-100 text-blue-800";
                break;
            case "completed":
                $statusText = "Selesai";
                $statusClass = "bg-green-100 text-green-800";
                break;
            case "cancelled":
                $statusText = "Dibatalkan";
                $statusClass = "bg-red-100 text-red-800";
                break;
            case "awaiting_lab_results":
                $statusText = "Menunggu Hasil Lab";
                $statusClass = "bg-indigo-100 text-indigo-800";
                break;
            default:
                $statusText = $appointment["STATUS"];
                $statusClass = "bg-gray-100 text-gray-800";
        }
        ?>
        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full <?= $statusClass ?>">
            <?= $statusText ?>
        </span>
    </div>

    <!-- Appointment Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Janji Temu</h4>

            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Janji</p>
                    <p class="mt-1 text-base"><?= esc(
                        $appointment["NAMA_JANJI"]
                    ) ?></p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Tanggal & Waktu</p>
                    <p class="mt-1 text-base">
                        <?= date(
                            "d M Y",
                            strtotime($appointment["TANGGAL_JANJI"])
                        ) ?> at
                        <?= date(
                            "H:i",
                            strtotime($appointment["WAKTU_JANJI"])
                        ) ?> WIB
                    </p>
                </div>

                <?php if (isset($appointment["nama_paket"])): ?>
                <div>
                    <p class="text-sm font-medium text-gray-500">Paket</p>
                    <p class="mt-1 text-base"><?= esc(
                        $appointment["nama_paket"]
                    ) ?></p>
                </div>
                <?php endif; ?>

                <div>
                    <p class="text-sm font-medium text-gray-500">Tanggal Dibuat</p>
                    <p class="mt-1 text-base"><?= date(
                        "d M Y, H:i",
                        strtotime($appointment["created_at"])
                    ) ?></p>
                </div>

                <?php if (
                    $appointment["STATUS"] === "cancelled" &&
                    isset($appointment["rejection_reason"])
                ): ?>
                <div>
                    <p class="text-sm font-medium text-gray-500">Alasan Pembatalan</p>
                    <p class="mt-1 text-base"><?= esc(
                        $appointment["rejection_reason"]
                    ) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Pasien</h4>

            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Lengkap</p>
                    <p class="mt-1 text-base"><?= esc(
                        $patient["NAMA_LENGKAP"]
                    ) ?></p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Email</p>
                    <p class="mt-1 text-base"><?= esc(
                        $patient["EMAIL"] ?? "-"
                    ) ?></p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Nomor Telepon</p>
                    <p class="mt-1 text-base"><?= esc(
                        $patient["NO_TELP_PASIEN"] ?? "-"
                    ) ?></p>
                </div>

                <?php if (isset($patient["JENIS_KELAMIN"])): ?>
                <div>
                    <p class="text-sm font-medium text-gray-500">Jenis Kelamin</p>
                    <p class="mt-1 text-base"><?= $patient["JENIS_KELAMIN"] ===
                    "L"
                        ? "Laki-laki"
                        : "Perempuan" ?></p>
                </div>
                <?php endif; ?>

                <?php if (isset($patient["TANGGAL_LAHIR"])): ?>
                <div>
                    <p class="text-sm font-medium text-gray-500">Tanggal Lahir</p>
                    <p class="mt-1 text-base">
                        <?= date(
                            "d M Y",
                            strtotime($patient["TANGGAL_LAHIR"])
                        ) ?>
                        (<?= date_diff(
                            date_create($patient["TANGGAL_LAHIR"]),
                            date_create("today")
                        )->y ?> tahun)
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Doctor and Lab/Diagnosis Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Dokter</h4>

            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Dokter</p>
                    <p class="mt-1 text-base"><?= esc(
                        $appointment["NAMA_DOKTER"]
                    ) ?></p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Spesialisasi</p>
                    <p class="mt-1 text-base"><?= esc(
                        $appointment["nama_spesialisasi"]
                    ) ?></p>
                </div>

                <?php if (isset($doctor_info["NO_TELP_DOKTER"])): ?>
                <div>
                    <p class="text-sm font-medium text-gray-500">Nomor Telepon</p>
                    <p class="mt-1 text-base"><?= esc(
                        $doctor_info["NO_TELP_DOKTER"]
                    ) ?></p>
                </div>
                <?php endif; ?>

                <?php if (isset($doctor_info["NO_LISENSI"])): ?>
                <div>
                    <p class="text-sm font-medium text-gray-500">Nomor Lisensi</p>
                    <p class="mt-1 text-base"><?= esc(
                        $doctor_info["NO_LISENSI"]
                    ) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Diagnosis & Hasil Lab</h4>

            <?php if (isset($diagnosis) && $diagnosis): ?>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Gejala</p>
                        <p class="mt-1 text-base"><?= esc(
                            $diagnosis["symptoms"]
                        ) ?></p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Hasil Diagnosis</p>
                        <p class="mt-1 text-base"><?= esc(
                            $diagnosis["diagnosis_result"]
                        ) ?></p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Rencana Pengobatan</p>
                        <p class="mt-1 text-base"><?= esc(
                            $diagnosis["treatment_plan"]
                        ) ?></p>
                    </div>

                    <?php if (!empty($diagnosis["notes"])): ?>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Catatan</p>
                        <p class="mt-1 text-base"><?= esc(
                            $diagnosis["notes"]
                        ) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            <?php elseif ($appointment["STATUS"] === "awaiting_lab_results"): ?>
                <div class="text-center py-4 text-yellow-500">
                    <i class="fas fa-flask text-3xl mb-2"></i>
                    <p>Menunggu hasil laboratorium.</p>
                </div>
            <?php elseif ($appointment["STATUS"] === "confirmed"): ?>
                <div class="text-center py-4 text-blue-500">
                    <i class="fas fa-stethoscope text-3xl mb-2"></i>
                    <p>Menunggu diagnosis dari dokter.</p>
                </div>
            <?php elseif ($appointment["STATUS"] === "pending"): ?>
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-clock text-3xl mb-2"></i>
                    <p>Janji temu belum dikonfirmasi.</p>
                </div>
            <?php elseif ($appointment["STATUS"] === "cancelled"): ?>
                <div class="text-center py-4 text-red-500">
                    <i class="fas fa-times-circle text-3xl mb-2"></i>
                    <p>Janji temu dibatalkan.</p>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-info-circle text-3xl mb-2"></i>
                    <p>Tidak ada informasi diagnosis.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lab Results Section -->
    <?php if (isset($lab_tests) && !empty($lab_tests)): ?>
        <div class="mt-8">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Hasil Laboratorium</h4>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Tes</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Normal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Petugas Lab</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($lab_tests as $test): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= esc(
                                        $test["test_name"]
                                    ) ?></div>
                                    <div class="text-sm text-gray-500"><?= esc(
                                        $test["test_type"]
                                    ) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (
                                        $test["status"] === "completed"
                                    ): ?>
                                        <div class="text-sm text-gray-900"><?= esc(
                                            $test["test_result"]
                                        ) ?></div>
                                    <?php else: ?>
                                        <div class="text-sm text-gray-500">Menunggu hasil</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc(
                                        $test["normal_range"]
                                    ) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc(
                                        $test["technician_name"] ?? "-"
                                    ) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $testStatusClass = "";
                                    $testStatusText = "";

                                    switch ($test["status"]) {
                                        case "ordered":
                                            $testStatusText = "Dipesan";
                                            $testStatusClass =
                                                "bg-gray-100 text-gray-800";
                                            break;
                                        case "assigned":
                                            $testStatusText = "Ditugaskan";
                                            $testStatusClass =
                                                "bg-blue-100 text-blue-800";
                                            break;
                                        case "processing":
                                            $testStatusText = "Sedang Diproses";
                                            $testStatusClass =
                                                "bg-yellow-100 text-yellow-800";
                                            break;
                                        case "completed":
                                            $testStatusText = "Selesai";
                                            $testStatusClass =
                                                "bg-green-100 text-green-800";
                                            break;
                                        default:
                                            $testStatusText = $test["status"];
                                            $testStatusClass =
                                                "bg-gray-100 text-gray-800";
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $testStatusClass ?>">
                                        <?= $testStatusText ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="flex mt-8 justify-end space-x-3">
        <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="openStatusModal(<?= $appointment[
            "ID_JANJI_TEMU"
        ] ?>, '<?= $appointment["STATUS"] ?>')">
            <i class="fas fa-edit mr-2"></i> Update Status
        </button>
    </div>
</div>

<!-- Status Modal (same as in appointment management page) -->
<div id="statusModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Modal code here (same as the previous one) -->
</div>

<script>
    function printAppointmentDetail() {
        window.print();
    }

    // Status modal functions (same as previous)
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
        reasonContainer.style.display = statusDropdown.value === 'cancelled' ? 'block' : 'none';

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
