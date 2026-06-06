<?php

declare(strict_types=1);

namespace Palmed\Controllers;

use Palmed\Core\Auth;
use Palmed\Models\Dashboard;
use Palmed\Models\Patient;

class DashboardController
{
    public function index(): void
    {
        Auth::requirePermission('dashboard.view');

        $user = Auth::user();
        $isPhysician = in_array($user['role_slug'] ?? '', ['physician'], true);
        $physicianId = $isPhysician ? Auth::id() : null;

        view('dashboard.index', [
            'stats' => Dashboard::stats($physicianId),
            'todayAppointments' => Dashboard::todayAppointments($physicianId),
            'upcomingAppointments' => Dashboard::upcomingAppointments($physicianId),
            'recentPatients' => Patient::recent(5),
            'user' => $user,
        ]);
    }
}
