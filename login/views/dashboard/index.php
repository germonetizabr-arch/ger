<?php
$pageTitle = __t('dashboard.title');
$activeNav = 'dashboard';
ob_start();
?>
<div class="top-bar">
    <div>
        <h1 class="page-title"><?= e(__t('dashboard.title')) ?></h1>
        <p class="text-muted mb-0"><?= e(__t('dashboard.welcome', ['name' => $user['first_name'] ?? ''])) ?></p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-value"><?= (int) $stats['today_appointments'] ?></div>
            <div class="stat-label"><?= e(__t('dashboard.today_appointments')) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card accent">
            <div class="stat-value"><?= (int) $stats['today_consultations'] ?></div>
            <div class="stat-label"><?= e(__t('dashboard.today_consultations')) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-value"><?= (int) $stats['pending_documents'] ?></div>
            <div class="stat-label"><?= e(__t('dashboard.pending_documents')) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-value"><?= (int) $stats['total_patients'] ?></div>
            <div class="stat-label"><?= e(__t('dashboard.total_patients')) ?></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <h5 class="mb-3"><?= e(__t('dashboard.quick_actions')) ?></h5>
        <div class="row g-3">
            <?php if (\Palmed\Core\Auth::can('patients.manage')): ?>
            <div class="col-md-4">
                <a href="<?= url('patients/create') ?>" class="quick-action-btn">
                    <span class="icon">👤</span>
                    <span><?= e(__t('dashboard.new_patient')) ?></span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (\Palmed\Core\Auth::can('consultations.manage')): ?>
            <div class="col-md-4">
                <a href="<?= url('consultations/create') ?>" class="quick-action-btn">
                    <span class="icon">🩺</span>
                    <span><?= e(__t('dashboard.new_consultation')) ?></span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (\Palmed\Core\Auth::can('appointments.manage')): ?>
            <div class="col-md-4">
                <a href="#" class="quick-action-btn">
                    <span class="icon">📅</span>
                    <span><?= e(__t('dashboard.new_appointment')) ?></span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <?= e(__t('dashboard.today_appointments')) ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($todayAppointments)): ?>
                    <p class="text-muted p-4 mb-0"><?= e(__t('dashboard.no_appointments_today')) ?></p>
                <?php else: ?>
                    <table class="table-palmed mb-0">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Paciente</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($todayAppointments as $apt): ?>
                            <tr>
                                <td><?= e(substr($apt['start_time'], 0, 5)) ?></td>
                                <td><?= e($apt['patient_first'] . ' ' . $apt['patient_last']) ?></td>
                                <td><span class="badge-palmed badge-<?= e($apt['status']) ?>"><?= e(__t('appointment.' . $apt['status'])) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <?= e(__t('dashboard.upcoming_appointments')) ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcomingAppointments)): ?>
                    <p class="text-muted p-4 mb-0"><?= e(__t('dashboard.no_upcoming')) ?></p>
                <?php else: ?>
                    <table class="table-palmed mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingAppointments as $apt): ?>
                            <tr>
                                <td><?= e(format_date($apt['appointment_date'])) ?></td>
                                <td><?= e($apt['patient_first'] . ' ' . $apt['patient_last']) ?></td>
                                <td><?= e(substr($apt['start_time'], 0, 5)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <?= e(__t('dashboard.recent_patients')) ?>
                <a href="<?= url('patients') ?>" class="btn-palmed-outline btn-palmed-sm"><?= e(__t('view')) ?> <?= e(__t('nav.patients')) ?></a>
            </div>
            <div class="card-body p-0">
                <table class="table-palmed mb-0">
                    <thead>
                        <tr>
                            <th><?= e(__t('patients.first_name')) ?></th>
                            <th><?= e(__t('patients.document_number')) ?></th>
                            <th><?= e(__t('patients.phone')) ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPatients as $p): ?>
                        <tr>
                            <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
                            <td><?= e($p['document_type'] . ' ' . $p['document_number']) ?></td>
                            <td><?= e($p['phone']) ?></td>
                            <td><a href="<?= url('patients/' . $p['id']) ?>" class="btn-palmed-outline btn-palmed-sm"><?= e(__t('view')) ?></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require PALMED_VIEWS . '/layouts/app.php';
