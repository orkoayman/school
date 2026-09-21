<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Public
$routes->get('/', 'AuthController::loginForm');
$routes->get('login', 'AuthController::loginForm');
$routes->post('login', 'AuthController::login');
$routes->post('logout', 'AuthController::logout');
$routes->get('logout', 'AuthController::logout');

// Card-punch hardware endpoint - NOT behind login (device can't log in).
// TODO before going live: protect with a shared device API key/secret.
$routes->post('attendance/punch', 'AttendanceController::punch');

// Protected - any logged-in user (admin or teacher)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // Students
    $routes->get('students', 'StudentController::index');
    $routes->get('students/create', 'StudentController::create');
    $routes->post('students', 'StudentController::store');
    $routes->get('students/(:num)/edit', 'StudentController::edit/$1');
    $routes->post('students/(:num)', 'StudentController::update/$1');
    $routes->get('students/(:num)', 'StudentController::show/$1');

    // Attendance
    $routes->get('attendance', 'AttendanceController::index');
    $routes->post('attendance/mark', 'AttendanceController::mark');

    // Results
    $routes->get('results', 'ResultController::index');
    $routes->get('results/entry', 'ResultController::entryForm');
    $routes->post('results/entry', 'ResultController::saveEntry');
    $routes->get('results/card/(:num)/(:num)', 'ResultController::resultCard/$1/$2'); // enrollment_id/exam_id
});

// Protected - admin only
$routes->group('', ['filter' => 'auth:admin'], static function ($routes) {
    // Fees
    $routes->get('fees', 'FeeController::index');
    $routes->get('fees/student/(:num)', 'FeeController::studentLedger/$1');
    $routes->post('fees/payment', 'FeeController::recordPayment');
    $routes->get('fees/settings', 'FeeController::settings');
    $routes->post('fees/settings', 'FeeController::saveSettings');
    $routes->post('fees/fee-types', 'FeeController::createFeeType');
    $routes->post('fees/student-override', 'FeeController::saveStudentOverride');

    // Settings: classes, subjects, academic years
    $routes->get('settings/classes', 'SettingsController::classes');
    $routes->post('settings/classes', 'SettingsController::saveClass');
    $routes->post('settings/years', 'SettingsController::saveYear');
    $routes->post('settings/years/(:num)/activate', 'SettingsController::activateYear/$1');
    $routes->post('settings/subjects', 'SettingsController::saveSubject');
    $routes->get('settings/school', 'SettingsController::schoolInfo');
    $routes->post('settings/school', 'SettingsController::saveSchoolInfo');

    // Users
    $routes->get('settings/users', 'UserController::index');
    $routes->post('settings/users', 'UserController::store');
    $routes->post('settings/users/(:num)/toggle', 'UserController::toggleStatus/$1');

    // Result manual override
    $routes->post('results/override', 'ResultController::setOverride');
});
