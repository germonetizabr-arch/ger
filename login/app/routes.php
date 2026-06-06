<?php

declare(strict_types=1);

use Palmed\Controllers\AuthController;
use Palmed\Controllers\ConsultationController;
use Palmed\Controllers\DashboardController;
use Palmed\Controllers\PatientController;
use Palmed\Controllers\UserController;
use Palmed\Core\Router;

$router = new Router();

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

$router->get('/login', fn() => (new AuthController())->showLogin());
$router->post('/login', fn() => (new AuthController())->login());

$router->get('/logout', fn() => (new AuthController())->logout());

$router->get('/forgot-password', fn() => (new AuthController())->showForgotPassword());
$router->post('/forgot-password', fn() => (new AuthController())->forgotPassword());

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

$router->get('/', fn() => (new DashboardController())->index());
$router->get('/dashboard', fn() => (new DashboardController())->index());

/*
|--------------------------------------------------------------------------
| PATIENTS
|--------------------------------------------------------------------------
*/

$router->get('/api/patients/search', fn() => (new PatientController())->searchApi());

$router->get('/patients', fn() => (new PatientController())->index());

$router->get('/patients/create', fn() => (new PatientController())->create());

$router->post('/patients', fn() => (new PatientController())->store());

$router->get('/patients/{id}', fn($p) => (new PatientController())->show($p));

$router->get('/patients/{id}/edit', fn($p) => (new PatientController())->edit($p));

$router->post('/patients/{id}', fn($p) => (new PatientController())->update($p));

/*
|--------------------------------------------------------------------------
| CONSULTATIONS
|--------------------------------------------------------------------------
*/

$router->get('/consultations/create', fn() => (new ConsultationController())->create());

$router->get('/patients/{patient_id}/consultation', fn($p) => (new ConsultationController())->create($p));

$router->post('/consultations', fn() => (new ConsultationController())->store());

$router->get('/consultations/{id}', fn($p) => (new ConsultationController())->show($p));

$router->get('/consultations/{id}/print', fn($p) => (new ConsultationController())->print($p));

$router->get('/consultations/{id}/edit', fn($p) => (new ConsultationController())->edit($p));

$router->post('/consultations/{id}', fn($p) => (new ConsultationController())->update($p));

$router->get('/api/icd10/search', fn() => (new ConsultationController())->searchIcd10());

/*
|--------------------------------------------------------------------------
| USERS
|--------------------------------------------------------------------------
*/

$router->get('/users', fn() => (new UserController())->index());

$router->get('/users/create', fn() => (new UserController())->create());

$router->post('/users', fn() => (new UserController())->store());

$router->get('/users/{id}/edit', fn($p) => (new UserController())->edit($p));

$router->post('/users/{id}', fn($p) => (new UserController())->update($p));

return $router;