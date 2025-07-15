<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Diagnosis</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12pt;
            margin: 40px 40px 60px 40px;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            margin-bottom: 24px;
        }

        .logo-cell {
            width: 80px;
            vertical-align: middle;
            text-align: left;
        }

        .title-cell {
            text-align: left;
            vertical-align: middle;
        }

        .hospital-name {
            font-size: 16pt;
            font-weight: bold;
            color: #2563eb;
        }

        .report-title {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin-top: 16px;
            margin-bottom: 24px;
            color: #2563eb;
        }

        .section {
            margin-bottom: 14px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            min-width: 120px;
        }

        .footer {
            position: absolute;
            right: 40px;
            bottom: 40px;
            text-align: right;
        }

        .signature {
            margin-top: 60px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <!-- Adjust the path to your logo image as needed -->
                <img src="<?= base_url('assets/images/hospital_logo.png') ?>" alt="Logo Rumah Sakit" width="70">
            </td>
            <td class="title-cell">
                <div class="hospital-name">Rumah Sakit Sehat Selalu</div>
                <div>Jl. Contoh No.123, Jakarta</div>
                <div>Telp: (021) 12345678</div>
            </td>
        </tr>
    </table>

    <div class="report-title">LAPORAN DIAGNOSIS PASIEN</div>

    <div class="section"><span class="label">Nama Pasien</span>: <?= esc($diagnosis['patient_name'] ?? '') ?></div>
    <div class="section"><span class="label">Nama Dokter</span>: <?= esc($diagnosis['doctor_name'] ?? '') ?></div>
    <div class="section"><span class="label">Tanggal Pemeriksaan</span>: <?= esc($diagnosis['created_at'] ?? '') ?></div>
    <div class="section"><span class="label">Gejala</span>: <?= esc($diagnosis['symptoms'] ?? '') ?></div>
    <div class="section"><span class="label">Hasil Diagnosis</span>: <?= esc($diagnosis['diagnosis_result'] ?? '') ?></div>
    <div class="section"><span class="label">Rencana Tindakan</span>: <?= esc($diagnosis['treatment_plan'] ?? '') ?></div>
    <div class="section"><span class="label">Catatan</span>: <?= esc($diagnosis['notes'] ?? '-') ?></div>

    <div class="footer">
        <div>Jakarta, <?= date('d F Y', strtotime($diagnosis['created_at'] ?? 'now')) ?></div>
        <div>Dokter Pemeriksa,</div>
        <div class="signature">
            <?= esc($diagnosis['doctor_name'] ?? '') ?>
        </div>
    </div>
</body>

</html>
