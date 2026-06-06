<?php

declare(strict_types=1);

namespace Palmed\Controllers;

use Palmed\Core\Auth;
use Palmed\Core\Audit;
use Palmed\Models\Patient;

class PatientController
{
    public function index(): void
    {
        Auth::requirePermission('patients.view');

        $search = trim($_GET['q'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        view('patients.index', [
            'patients' => Patient::all($perPage, $offset, $search ?: null),
            'total' => Patient::count($search ?: null),
            'page' => $page,
            'perPage' => $perPage,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        Auth::requirePermission('patients.manage');
        view('patients.form', ['patient' => null, 'action' => 'create']);
    }

    public function store(): void
    {
        Auth::requirePermission('patients.manage');

        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', __t('auth.invalid_token'));
            redirect('patients/create');
        }

        $data = $this->validatePatient($_POST);
        if (isset($data['errors'])) {
            set_flash('error', implode(' ', $data['errors']));
            set_old_input($_POST);
            redirect('patients/create');
        }

        $data['created_by'] = Auth::id();
        $id = Patient::create($data);

        Audit::log('patient_create', 'patient', $id, 'Patient created: ' . $data['first_name'] . ' ' . $data['last_name']);

        set_flash('success', __t('patients.created'));
        redirect('patients/' . $id);
    }

    public function show(array $params): void
    {
        Auth::requirePermission('patients.view');

        $patient = Patient::find((int) $params['id']);
        if (!$patient) {
            set_flash('error', __t('patients.not_found'));
            redirect('patients');
        }

        view('patients.show', [
            'patient' => $patient,
            'consultations' => Patient::getConsultations((int) $patient['id']),
            'documents' => Patient::getDocuments((int) $patient['id']),
        ]);
    }

    public function edit(array $params): void
    {
        Auth::requirePermission('patients.manage');

        $patient = Patient::find((int) $params['id']);
        if (!$patient) {
            set_flash('error', __t('patients.not_found'));
            redirect('patients');
        }

        view('patients.form', ['patient' => $patient, 'action' => 'edit']);
    }

    public function update(array $params): void
    {
        Auth::requirePermission('patients.manage');

        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            set_flash('error', __t('auth.invalid_token'));
            redirect('patients/' . $params['id'] . '/edit');
        }

        $id = (int) $params['id'];
        $patient = Patient::find($id);
        if (!$patient) {
            set_flash('error', __t('patients.not_found'));
            redirect('patients');
        }

        $data = $this->validatePatient($_POST);
        if (isset($data['errors'])) {
            set_flash('error', implode(' ', $data['errors']));
            set_old_input($_POST);
            redirect('patients/' . $id . '/edit');
        }

        Patient::update($id, $data);
        Audit::log('patient_update', 'patient', $id, 'Patient updated');

        set_flash('success', __t('patients.updated'));
        redirect('patients/' . $id);
    }

    public function searchApi(): void
    {
        Auth::requirePermission('patients.view');

        $query = trim($_GET['q'] ?? '');
        if (strlen($query) < 2) {
            json_response(['results' => []]);
        }

        $patients = Patient::all(15, 0, $query);
        $results = array_map(fn($p) => [
            'id' => (int) $p['id'],
            'name' => $p['first_name'] . ' ' . $p['last_name'],
            'document' => $p['document_type'] . ' ' . $p['document_number'],
            'phone' => $p['phone'],
        ], $patients);

        json_response(['results' => $results]);
    }

    private function validatePatient(array $input): array
    {
        $errors = [];

        $firstName = trim($input['first_name'] ?? '');
        $lastName = trim($input['last_name'] ?? '');
        $documentNumber = trim($input['document_number'] ?? '');

        if ($firstName === '') {
            $errors[] = __t('patients.first_name_required');
        }
        if ($lastName === '') {
            $errors[] = __t('patients.last_name_required');
        }
        if ($documentNumber === '') {
            $errors[] = __t('patients.document_required');
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'document_type' => trim($input['document_type'] ?? 'CC'),
            'document_number' => $documentNumber,
            'date_of_birth' => $input['date_of_birth'] ?? null,
            'sex' => $input['sex'] ?? 'O',
            'phone' => trim($input['phone'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'address' => trim($input['address'] ?? ''),
            'occupation' => trim($input['occupation'] ?? ''),
            'emergency_contact_name' => trim($input['emergency_contact_name'] ?? ''),
            'emergency_contact_phone' => trim($input['emergency_contact_phone'] ?? ''),
            'notes' => trim($input['notes'] ?? ''),
        ];
    }
}
