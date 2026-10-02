<?php

namespace App\Controllers\Manager;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index(): string
    {
        return view('manager/dashboard', [
            'title' => 'Dashboard | CMS MIN 6 Jember',
            'pageTitle' => 'Dashboard',
        ]);
    }
}
