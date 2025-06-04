
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detail Pemeriksaan</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->has("error")): ?>
                        <div class="alert alert-danger"><?= session(
                            "error"
                        ) ?></div>
                    <?php endif; ?>

                    <?php if (session()->has("success")): ?>
                        <div class="alert alert-success"><?= session(
                            "success"
                        ) ?></div>
                    <?php endif; ?>

                    <?php if (!empty($appointment)): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="appointment-detail-panel">
                                    <h6 class="text-primary">Informasi Janji Temu</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Nama Janji</th>
                                            <td><?= esc(
                                                $appointment["NAMA_JANJI"]
                                            ) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <?php
                                                $statusClass = "secondary";
                                                switch (
                                                    $appointment["STATUS"]
                                                ) {
                                                    case "pending":
                                                        $statusClass =
                                                            "warning";
                                                        $statusText =
                                                            "Menunggu Konfirmasi";
                                                        break;
                                                    case "confirmed":
                                                        $statusClass = "info";
                                                        $statusText =
                                                            "Terkonfirmasi";
                                                        break;
                                                    case "completed":
                                                        $statusClass =
                                                            "success";
                                                        $statusText = "Selesai";
                                                        break;
                                                    case "cancelled":
                                                        $statusClass = "danger";
                                                        $statusText =
                                                            "Dibatalkan";
                                                        break;
                                                    default:
                                                        $statusText = ucfirst(
                                                            $appointment[
                                                                "STATUS"
                                                            ]
                                                        );
                                                }
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal & Waktu</th>
                                            <td><?= date(
                                                "d F Y",
                                                strtotime(
                                                    $appointment[
                                                        "TANGGAL_JANJI"
                                                    ]
                                                )
                                            ) ?> pukul <?= date(
     "H:i",
     strtotime($appointment["WAKTU_JANJI"])
 ) ?> WIB</td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat Pada</th>
                                            <td><?= date(
                                                "d F Y H:i",
                                                strtotime(
                                                    $appointment["created_at"]
                                                )
                                            ) ?> WIB</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="doctor-package-panel">
                                    <h6 class="text-primary">Dokter & Paket</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Dokter</th>
                                            <td><?= esc(
                                                $appointment["NAMA_DOKTER"]
                                            ) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Spesialisasi</th>
                                            <td><?= esc(
                                                $appointment[
                                                    "NAMA_SPESIALISASI"
                                                ] ?? "Umum"
                                            ) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Paket</th>
                                            <td><?= esc(
                                                $appointment["NAMA_PAKET"]
                                            ) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Harga</th>
                                            <td>Rp <?= number_format(
                                                $appointment["Harga_Paket"] ??
                                                    0,
                                                0,
                                                ",",
                                                "."
                                            ) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($appointment["DIAGNOSIS"])): ?>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="diagnosis-panel">
                                    <h6 class="text-primary">Hasil Diagnosis</h6>
                                    <div class="card border-light">
                                        <div class="card-body">
                                            <h6>Diagnosa Dokter:</h6>
                                            <p><?= nl2br(
                                                esc($appointment["DIAGNOSIS"])
                                            ) ?></p>

                                            <?php if (
                                                !empty(
                                                    $appointment["REKOMENDASI"]
                                                )
                                            ): ?>
                                                <h6 class="mt-3">Rekomendasi:</h6>
                                                <p><?= nl2br(
                                                    esc(
                                                        $appointment[
                                                            "REKOMENDASI"
                                                        ]
                                                    )
                                                ) ?></p>
                                            <?php endif; ?>

                                            <?php if (
                                                !empty(
                                                    $appointment["HASIL_LAB"]
                                                )
                                            ): ?>
                                                <h6 class="mt-3">Hasil Laboratorium:</h6>
                                                <p><?= nl2br(
                                                    esc(
                                                        $appointment[
                                                            "HASIL_LAB"
                                                        ]
                                                    )
                                                ) ?></p>
                                            <?php endif; ?>

                                            <div class="mt-3">
                                                <small class="text-muted">Diagnosis dibuat pada: <?= date(
                                                    "d F Y H:i",
                                                    strtotime(
                                                        $appointment[
                                                            "TANGGAL_DIAGNOSIS"
                                                        ]
                                                    )
                                                ) ?> WIB</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="actions-panel">
                                    <?php if (
                                        $appointment["STATUS"] === "pending"
                                    ): ?>
                                        <a href="<?= base_url(
                                            "patient/cancel-appointment/" .
                                                $appointment["ID_JANJI_TEMU"]
                                        ) ?>"
                                           class="btn btn-danger"
                                           onclick="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                            <i class="fas fa-times-circle"></i> Batalkan Janji Temu
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?= base_url(
                                        "patient/jadwal-pemeriksaan"
                                    ) ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Jadwal
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            Data janji temu tidak ditemukan atau telah dihapus.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .appointment-detail-panel, .doctor-package-panel, .diagnosis-panel {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        height: 100%;
    }

    .actions-panel {
        padding: 15px 0;
    }

    .table th {
        color: #6c757d;
    }
</style>
