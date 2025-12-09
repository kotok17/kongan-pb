<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        try {
            $data = [
                'totalUsers' => 1250,
                'totalRevenue' => 45000,
            ];

            return $this->response->setJSON(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            \Sentry\captureException($e);
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}