<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white rounded-top-4" style="background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%) !important;">
                    <h5 class="mb-0 text-white"><i class="fas fa-notes-medical me-2"></i>Detail Pemeriksaan</h5>
                </div>
                <div class="card-body px-4">
                    <?php if (session()->has("error")): ?>
                        <div class="alert alert-danger fancy-shadow"><?= session("error") ?></div>
                    <?php endif; ?>
                    <?php if (session()->has("success")): ?>
                        <div class="alert alert-success fancy-shadow"><?= session("success") ?></div>
                    <?php endif; ?>

                    <?php if (!empty($appointment)): ?>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="panel appointment-detail-panel mb-3">
                                    <h6 class="panel-title text-gradient-blue mb-3">Informasi Janji Temu</h6>
                                    <table class="table table-borderless mb-0 detail-table">
                                        <tr>
                                            <th>Nama Janji</th>
                                            <td><?= esc($appointment["nama_janji"]) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php
                                                $statusClass = "secondary";
                                                $statusText = ucfirst($appointment["status"]);
                                                switch ($appointment["status"]) {
                                                    case "pending":
                                                        $statusClass = "warning";
                                                        $statusText = "Menunggu Konfirmasi";
                                                        break;
                                                    case "confirmed":
                                                        $statusClass = "info";
                                                        $statusText = "Terkonfirmasi";
                                                        break;
                                                    case "completed":
                                                        $statusClass = "success";
                                                        $statusText = "Selesai";
                                                        break;
                                                    case "cancelled":
                                                        $statusClass = "danger";
                                                        $statusText = "Dibatalkan";
                                                        break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?> px-3 py-2"><?= $statusText ?></span>
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
                                    <h6 class="panel-title text-gradient-blue mb-3">Dokter & Paket</h6>
                                    <table class="table table-borderless mb-0 detail-table">
                                        <tr>
                                            <th>Dokter</th>
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
                                    <h6 class="panel-title text-gradient-blue mb-3"><i class="fas fa-credit-card me-1"></i> Pembayaran</h6>
                                    <?php if (!empty($appointment['status_pembayaran']) && $appointment['status_pembayaran'] === 'lunas'): ?>
                                        <div class="alert alert-success mb-2 fancy-shadow">
                                            <i class="fas fa-check-circle"></i> Pembayaran telah diterima.
                                        </div>
                                    <?php elseif (!empty($appointment['doku_payment_url']) && !empty($appointment['doku_expired_time']) && strtotime($appointment['doku_expired_time']) > time()): ?>
                                        <div class="alert alert-warning mb-2 fancy-shadow">
                                            <i class="fas fa-hourglass-half"></i> Pembayaran belum selesai.<br>
                                            Silakan lanjutkan pembayaran sebelum <strong><?= date("d F Y H:i", strtotime($appointment['doku_expired_time'])) ?> WIB</strong>
                                        </div>
                                        <a href="<?= esc($appointment['doku_payment_url']) ?>" class="fancy-btn" target="_blank">
                                            <i class="fas fa-money-bill-wave"></i> Bayar Sekarang
                                        </a>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-2 fancy-shadow">
                                            <i class="fas fa-info-circle"></i> Belum ada pembayaran untuk janji temu ini.
                                        </div>
                                        <a href="<?= base_url('payment/checkout/' . $appointment['id_transaksi']) ?>" class="fancy-btn">
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
                                        <h6 class="panel-title text-gradient-blue mb-3"><i class="fas fa-stethoscope me-1"></i>Hasil Diagnosis</h6>
                                        <div class="card border-light shadow-sm">
                                            <div class="card-body bg-light rounded-4">
                                                <p><strong>Nama Dokter:</strong> <?= esc($appointment["nama_dokter"] ?? '-') ?></p>
                                                <h6 class="mb-1 text-primary">Diagnosa Dokter:</h6>
                                                <div class="diagnosis-content"><?= nl2br(esc($appointment["diagnosis_result"] ?? '')) ?></div>
                                                <?php if (!empty($appointment["diagnosis_treatment_plan"])): ?>
                                                    <h6 class="mt-3 mb-1 text-primary">Rekomendasi / Rencana Tindakan:</h6>
                                                    <div class="diagnosis-content"><?= nl2br(esc($appointment["diagnosis_treatment_plan"])) ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($appointment["diagnosis_hasil_lab"])): ?>
                                                    <h6 class="mt-3 mb-1 text-primary">Hasil Laboratorium:</h6>
                                                    <div class="diagnosis-content bg-gradient-lab"><?= nl2br(esc($appointment["diagnosis_hasil_lab"])) ?></div>
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
                                                <div class="text-end mt-3">
                                                    <a href="<?= base_url('pasien/diagnosis/print/' . $appointment["diagnosis_id"]) ?>"
                                                        class="fancy-btn btn-lg shadow"
                                                        title="Cetak diagnosis ini sebagai PDF"
                                                        target="_blank">
                                                        <i class="fas fa-file-pdf"></i> Download PDF Diagnosis
                                                    </a>
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
                                            class="fancy-btn bg-gradient-danger me-2"
                                            onclick="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                            <i class="fas fa-times-circle"></i> Batalkan Janji Temu
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url("pasien/jadwal-pemeriksaan") ?>" class="fancy-btn bg-gradient-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center fancy-shadow">
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
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(50, 50, 93, 0.07), 0 1.5px 4px rgba(0, 0, 0, 0.03);
        margin-bottom: 10px;
    }

    .panel-title {
        font-weight: 700;
        letter-spacing: 0.7px;
        font-size: 1.12em;
    }

    .text-gradient-blue {
        background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-fill-color: transparent;
    }

    .bg-gradient-primary {
        background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%) !important;
    }

    .fancy-btn {
        display: inline-block;
        background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
        color: #fff !important;
        padding: 0.7em 2.1em;
        border-radius: 999px;
        font-size: 1.06em;
        font-weight: 600;
        text-decoration: none;
        border: none;
        transition: background 0.18s, color 0.18s, box-shadow 0.12s;
        box-shadow: 0 3px 12px rgba(59, 130, 246, 0.12);
        letter-spacing: 0.5px;
    }

    .fancy-btn:hover,
    .fancy-btn:focus {
        background: linear-gradient(90deg, #1e40af 0%, #2563eb 100%);
        color: #fff !important;
        box-shadow: 0 5px 20px rgba(59, 130, 246, 0.18);
        text-decoration: none;
    }

    .bg-gradient-danger {
        background: linear-gradient(90deg, #e53935 0%, #ef5350 100%) !important;
    }

    .bg-gradient-secondary {
        background: linear-gradient(90deg, #64748b 0%, #94a3b8 100%) !important;
    }

    .diagnosis-content {
        background: #e3f0fc;
        border-radius: 9px;
        padding: 11px 15px;
        margin-bottom: 10px;
        font-size: 1.06em;
    }

    .bg-gradient-lab {
        background: linear-gradient(90deg, #e0f2fe 0%, #bae6fd 100%) !important;
    }

    .fancy-shadow {
        box-shadow: 0 3px 14px rgba(59, 130, 246, 0.10);
        border-radius: 8px;
    }

    .detail-table th {
        color: #2563eb;
        font-weight: 600;
        background: #e3eafc;
        border-radius: 7px 0 0 7px;
        padding: 12px 10px;
        width: 36%;
    }

    .detail-table td {
        background: #f6f9ff;
        border-radius: 0 7px 7px 0;
        padding: 12px 10px;
    }

    .badge {
        font-size: 1em;
        padding: 0.6em 1.2em;
        border-radius: 8px;
        letter-spacing: 0.4px;
        box-shadow: 0 2px 10px rgba(60, 130, 246, 0.08);
    }

    .actions-panel {
        padding: 10px 0 5px 0;
    }

    .card .card-body h6 {
        font-weight: 600;
        color: #2563eb;
    }

    .card .card-body .mt-3 {
        margin-top: 1.3rem !important;
    }

    @media (max-width: 600px) {

        .diagnosis-content,
        .panel,
        .card-body {
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        .panel {
            padding: 10px 6px 8px 6px;
        }
    }
</style>
