<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-black"><i class="fas fa-notes-medical me-2"></i>Detail Pemeriksaan</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->has("error")): ?>
                        <div class="alert alert-danger"><?= session("error") ?></div>
                    <?php endif; ?>
                    <?php if (session()->has("success")): ?>
                        <div class="alert alert-success"><?= session("success") ?></div>
                    <?php endif; ?>

                    <?php if (!empty($appointment)): ?>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="panel appointment-detail-panel mb-3">
                                    <h6 class="panel-title text-primary mb-3">Informasi Janji Temu</h6>
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th width="40%">Nama Janji</th>
                                            <td><?= esc($appointment["nama_janji"]) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php
                                                $statusClass = "secondary";
                                                switch ($appointment["status"]) {
                                                    case "pending": $statusClass = "warning"; $statusText = "Menunggu Konfirmasi"; break;
                                                    case "confirmed": $statusClass = "info"; $statusText = "Terkonfirmasi"; break;
                                                    case "completed": $statusClass = "success"; $statusText = "Selesai"; break;
                                                    case "cancelled": $statusClass = "danger"; $statusText = "Dibatalkan"; break;
                                                    default: $statusText = ucfirst($appointment["status"]);
                                                }
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <td><?= date("d F Y", strtotime($appointment["tanggal_janji"])) ?> pukul <?= date("H:i", strtotime($appointment["waktu_janji"])) ?> WIB</td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat Pada</th>
                                            <td>
                                                <?= $appointment["created_at"] && $appointment["created_at"] !== "1970-01-01 00:00:00"
                                                    ? date("d F Y H:i", strtotime($appointment["created_at"])) . " WIB"
                                                    : '<em>Belum tersedia</em>'
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel doctor-package-panel mb-3">
                                    <h6 class="panel-title text-primary mb-3">Dokter & Paket</h6>
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <th width="40%">Dokter</th>
                                            <td><?= esc($appointment["nama_dokter"]) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Spesialisasi</th>
                                            <td><?= esc($appointment["nama_spesialisasi"] ?? "Umum") ?></td>
                                        </tr>
                                        <tr>
                                            <th>Paket</th>
                                            <td><?= esc($appointment["nama_paket"]) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Harga</th>
                                            <td>Rp <?= number_format($appointment["harga"] ?? 0, 0, ",", ".") ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="panel payment-panel">
                                <h6 class="panel-title text-primary mb-3"><i class="fas fa-credit-card me-1"></i> Pembayaran</h6>
                                <?php if (!empty($appointment['status_pembayaran']) && $appointment['status_pembayaran'] === 'lunas'): ?>
                                    <div class="alert alert-success mb-2">
                                        <i class="fas fa-check-circle"></i> Pembayaran telah diterima.
                                    </div>
                                <?php elseif (!empty($appointment['doku_payment_url']) && !empty($appointment['doku_expired_time']) && strtotime($appointment['doku_expired_time']) > time()): ?>
                                    <div class="alert alert-warning mb-2">
                                        <i class="fas fa-hourglass-half"></i> Pembayaran belum selesai.<br>
                                        Silakan lanjutkan pembayaran sebelum <strong><?= date("d F Y H:i", strtotime($appointment['doku_expired_time'])) ?> WIB</strong>
                                    </div>
                                    <a href="<?= esc($appointment['doku_payment_url']) ?>" class="btn btn-success" target="_blank">
                                        <i class="fas fa-money-bill-wave"></i> Bayar Sekarang
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-info mb-2">
                                        <i class="fas fa-info-circle"></i> Belum ada pembayaran untuk janji temu ini.
                                    </div>
                                    <a href="<?= base_url('payment/checkout/'.$appointment['id_transaksi']) ?>" class="btn btn-primary">
                                        <i class="fas fa-money-bill-wave"></i> Bayar Sekarang
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                        <?php if (!empty($appointment["diagnosis_id"])): ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="panel diagnosis-panel">
                                    <h6 class="panel-title text-primary mb-3"><i class="fas fa-stethoscope me-1"></i>Hasil Diagnosis</h6>
                                    <div class="card border-light shadow-sm">
                                        <div class="card-body">
                                            <h6>Diagnosa Dokter:</h6>
                                            <p><?= nl2br(esc($appointment["diagnosis_result"] ?? '')) ?></p>
                                            <?php if (!empty($appointment["diagnosis_treatment_plan"])): ?>
                                                <h6 class="mt-3">Rekomendasi / Rencana Tindakan:</h6>
                                                <p><?= nl2br(esc($appointment["diagnosis_treatment_plan"])) ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($appointment["diagnosis_hasil_lab"])): ?>
                                                <h6 class="mt-3">Hasil Laboratorium:</h6>
                                                <p><?= nl2br(esc($appointment["diagnosis_hasil_lab"])) ?></p>
                                                <?php if (!empty($appointment["diagnosis_tanggal_hasil_lab"]) && $appointment["diagnosis_tanggal_hasil_lab"] !== "1970-01-01"): ?>
                                                    <div><small class="text-muted">Tanggal Hasil Lab: <?= date("d F Y", strtotime($appointment["diagnosis_tanggal_hasil_lab"])) ?></small></div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    Diagnosis dibuat pada:
                                                    <?= !empty($appointment["diagnosis_created_at"]) && $appointment["diagnosis_created_at"] !== "1970-01-01 00:00:00"
                                                        ? date("d F Y H:i", strtotime($appointment["diagnosis_created_at"])) . " WIB"
                                                        : '<em>Belum tersedia</em>'
                                                    ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row mt-4">
                            <div class="col-md-12 text-end">
                                <div class="actions-panel">
                                    <?php if ($appointment["status"] === "pending"): ?>
                                        <a href="<?= base_url("pasien/cancel-appointment/" . $appointment["id_janji_temu"]) ?>"
                                           class="btn btn-danger me-2"
                                           onclick="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                            <i class="fas fa-times-circle"></i> Batalkan Janji Temu
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url("pasien/jadwal-pemeriksaan") ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            Data janji temu tidak ditemukan atau telah dihapus.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .panel {
        background-color: #f8f9fa;
        padding: 18px 20px 12px 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }
    .panel-title {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .actions-panel {
        padding: 10px 0 5px 0;
    }
    .table th {
        color: #6c757d;
        width: 40%;
    }
    .badge {
        font-size: 0.97em;
        padding: 0.6em 1em;
    }
    .card .card-body h6 {
        font-weight: 500;
        color: #2471a3;
    }
    .card .card-body .mt-3 {
        margin-top: 1.3rem !important;
    }
</style>
