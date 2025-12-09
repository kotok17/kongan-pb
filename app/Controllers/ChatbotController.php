<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ChatbotController extends BaseController
{
    protected $templates = [
        // keyword => response
        'pengajuan' => 'Untuk mengajukan kegiatan, silakan masuk ke menu Pengajuan Kegiatan, isi formulir, upload ToR, kemudian klik Submit.',
        'anggaran'  => 'Informasi anggaran dapat dilihat pada menu Keuangan > Saldo Anggaran.',
        'kegiatan'  => 'Daftar kegiatan dapat Anda lihat pada halaman Dashboard Kegiatan.',
        'approval'  => 'Status approval kegiatan dapat dilihat pada menu Persetujuan.',
        'saldo'     => 'Saldo treasury hanya dapat dilihat oleh role BMA pada menu Saldo Rekening.',
    ];

    public function ask()
    {
        $question = strtolower($this->request->getPost('message'));

        $answer = $this->getAnswer($question);

        return $this->response->setJSON([
            'question' => $question,
            'answer' => $answer
        ]);
    }

    private function getAnswer($question)
    {
        foreach ($this->templates as $keyword => $response) {
            if (strpos($question, $keyword) !== false) {
                return $response;
            }
        }

        return "Maaf, saya belum mengerti pertanyaan Anda. Silakan coba dengan kata kunci lain.";
    }
}