<?php

namespace App\Controllers;

use App\Models\AppointmentModel;

class JadwalDokterController extends BaseController
{

    protected $appointmentModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel;
    }


    public function getScheduleApi($doctorId)
    {
        $year = $this->request->getGet('year');
        $month = $this->request->getGet('month');

        $appointments = $this->appointmentModel->getDoctorAppointmentsByMonth($doctorId, $year, $month);

        // Convert to TUI Calendar event format
        $events = [];
        foreach ($appointments as $a) {
            $events[] = [
                'id' => $a['id_janji_temu'],
                'calendarId' => '1',
                'title' => $a['patient_name'] . ' - ' . $a['nama_paket'],
                'category' => 'time',
                'start' => $a['tanggal_janji'] . 'T' . $a['waktu_janji'],
                'end' => $a['tanggal_janji'] . 'T' . date('H:i:s', strtotime($a['waktu_janji'] . ' +1 hour')), // adjust duration as needed
            ];
        }
        return $this->response->setJSON($events);
    }
}
