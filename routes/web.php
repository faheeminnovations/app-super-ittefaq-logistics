<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\ProfessionalBillingController;
use App\Http\Controllers\TripClaimController;
use App\Http\Controllers\BiltyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseTripController;
use App\Http\Controllers\WarehouseInvoiceController;
use App\Http\Controllers\TripLogController;
use App\Http\Controllers\TransportManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/settings', [NotificationController::class, 'settings'])->name('notifications.settings');
    Route::post('/notifications/settings', [NotificationController::class, 'updateSettings'])->name('notifications.update-settings');
    Route::delete('/notifications', [NotificationController::class, 'delete'])->name('notifications.delete');
});

require __DIR__.'/auth.php';

// Resource routes for main modules with role-based access
// Admin - Full access to users and settings
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UsersController::class);
    Route::resource('settings', SettingController::class);
    Route::post('users/{id}/permissions', [UsersController::class, 'assignPermissions'])->name('users.permissions');
    Route::get('users/{id}/permissions/data', [UsersController::class, 'permissions'])->name('users.permissions.data');
});

// Dispatcher & Manager - Operations access
Route::middleware(['auth', 'role:admin,dispatcher,manager'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('jobs', JobController::class);
    Route::resource('trips', TripController::class);
    Route::resource('dispatch', DispatchController::class);
    Route::post('/dispatch/export', [DispatchController::class, 'export'])->name('dispatch.export');
    Route::resource('pod', PodController::class);
    Route::resource('tracking', TrackingController::class);
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::resource('maintenance', MaintenanceController::class);
    Route::post('/maintenance/export', [MaintenanceController::class, 'export'])->name('maintenance.export');
    Route::post('/import/process', [ImportController::class, 'process'])->name('import.process');
    Route::post('/import/new-format', [ImportController::class, 'processNewFormat'])->name('import.new-format');
    Route::get('/import/export', [ImportController::class, 'export'])->name('import.export');
    Route::post('/pod/export', [PodController::class, 'export'])->name('pod.export');
    Route::post('/jobs/export', [JobController::class, 'export'])->name('jobs.export');
    Route::post('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::post('/drivers/export', [DriverController::class, 'export'])->name('drivers.export');
    
    // Bilty routes
    Route::resource('bilties', BiltyController::class);
    Route::post('/bilties/export', [BiltyController::class, 'export'])->name('bilties.export');
    Route::get('/bilties/{bilty}/print', [BiltyController::class, 'print'])->name('bilties.print');
    
    // Warehouse Trip routes
    Route::resource('warehouse-trips', WarehouseTripController::class);
    Route::get('/warehouse-trips/export', [WarehouseTripController::class, 'export'])->name('warehouse-trips.export');
    Route::match(['get', 'post'], '/warehouse-trips/generate-invoice', [WarehouseTripController::class, 'generateInvoice'])->name('warehouse-trips.generate-invoice');
    Route::get('/warehouse-trips/{id}/edit-data', [WarehouseTripController::class, 'getEditData'])->name('warehouse-trips.edit-data');
    
    // Warehouse Invoice routes
    Route::resource('warehouse.invoices', WarehouseInvoiceController::class);
    Route::get('/warehouse-invoices/{id}/mark-sent', [WarehouseInvoiceController::class, 'markAsSent'])->name('warehouse.invoices.mark-sent');
    Route::get('/warehouse-invoices/{id}/mark-paid', [WarehouseInvoiceController::class, 'markAsPaid'])->name('warehouse.invoices.mark-paid');
    
    // Trip Claims routes
    Route::get('/trip-claims', [TripClaimController::class, 'index'])->name('trip-claims.index');
    Route::post('/trip-claims', [TripClaimController::class, 'store'])->name('trip-claims.store');
    Route::get('/trip-claims/{id}', [TripClaimController::class, 'show'])->name('trip-claims.show');
    Route::put('/trip-claims/{id}', [TripClaimController::class, 'update'])->name('trip-claims.update');
    Route::delete('/trip-claims/{id}', [TripClaimController::class, 'destroy'])->name('trip-claims.destroy');
    Route::post('/trip-claims/filter', [TripClaimController::class, 'filter'])->name('trip-claims.filter');
});

// Professional Billing System - Separate from old billing
Route::middleware(['auth', 'role:admin,accounts,manager'])->group(function () {
    Route::get('/professional-billing', [ProfessionalBillingController::class, 'index'])->name('professional-billing.index');
    
    // Supply Chain Billing
    Route::get('/professional-billing/supply-chain', [ProfessionalBillingController::class, 'supplyChainIndex'])->name('professional-billing.supply-chain');
    Route::post('/professional-billing/supply-chain/store', [ProfessionalBillingController::class, 'supplyChainStore'])->name('professional-billing.supply-chain.store');
    Route::delete('/professional-billing/supply-chain/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.supply-chain.destroy');

    // Breading Billing
    Route::get('/professional-billing/branding', [ProfessionalBillingController::class, 'brandingIndex'])->name('professional-billing.branding');
    Route::post('/professional-billing/branding/store', [ProfessionalBillingController::class, 'brandingStore'])->name('professional-billing.branding.store');
    Route::delete('/professional-billing/branding/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.branding.destroy');
    
    // Marketing Development Billing
    Route::get('/professional-billing/marketing-development', [ProfessionalBillingController::class, 'marketingDevelopmentIndex'])->name('professional-billing.marketing-development');
    Route::post('/professional-billing/marketing-development/store', [ProfessionalBillingController::class, 'marketingDevelopmentStore'])->name('professional-billing.marketing-development.store');
    Route::delete('/professional-billing/marketing-development/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.marketing-development.destroy');
    
    // SPR Billing
    Route::get('/professional-billing/spr', [ProfessionalBillingController::class, 'sprIndex'])->name('professional-billing.spr');
    Route::post('/professional-billing/spr/store', [ProfessionalBillingController::class, 'sprStore'])->name('professional-billing.spr.store');
    Route::delete('/professional-billing/spr/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.spr.destroy');

    // Syngenta Billing
    Route::get('/professional-billing/cement-pakistan', [ProfessionalBillingController::class, 'cementPakistanIndex'])->name('professional-billing.cement-pakistan');
    Route::post('/professional-billing/cement-pakistan/store', [ProfessionalBillingController::class, 'cementPakistanStore'])->name('professional-billing.cement-pakistan.store');
    Route::delete('/professional-billing/cement-pakistan/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.cement-pakistan.destroy');

    // Syngenta Breading Billing
    Route::get('/professional-billing/syngenta-breading', [ProfessionalBillingController::class, 'syngentaBreadingIndex'])->name('professional-billing.syngenta-breading');
    Route::post('/professional-billing/syngenta-breading/store', [ProfessionalBillingController::class, 'syngentaBreadingStore'])->name('professional-billing.syngenta-breading.store');
    Route::delete('/professional-billing/syngenta-breading/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.syngenta-breading.destroy');

    // Open Market Work Billing
    Route::get('/professional-billing/open-market-work', [ProfessionalBillingController::class, 'openMarketWorkIndex'])->name('professional-billing.open-market-work');
    Route::post('/professional-billing/open-market-work/store', [ProfessionalBillingController::class, 'openMarketWorkStore'])->name('professional-billing.open-market-work.store');
    Route::delete('/professional-billing/open-market-work/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.open-market-work.destroy');
    
    // Seed Supply Billing
    Route::get('/professional-billing/seed-supply', [ProfessionalBillingController::class, 'seedSupplyIndex'])->name('professional-billing.seed-supply');
    Route::post('/professional-billing/seed-supply/store', [ProfessionalBillingController::class, 'seedSupplyStore'])->name('professional-billing.seed-supply.store');
    Route::delete('/professional-billing/seed-supply/{id}', [ProfessionalBillingController::class, 'destroy'])->name('professional-billing.seed-supply.destroy');
});

// Accounts & Manager - Financial access
Route::middleware(['auth', 'role:admin,accounts,manager'])->group(function () {
    Route::resource('expenses', ExpenseController::class);
    Route::post('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::post('/invoices/{id}/verify', [InvoiceController::class, 'markAsVerified'])->name('invoices.verify');
    Route::post('/invoices/{id}/calculate', [InvoiceController::class, 'calculateTotals'])->name('invoices.calculate');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::get('/billing/{id}', [BillingController::class, 'show'])->name('billing.show');
    Route::put('/billing/{id}', [BillingController::class, 'update'])->name('billing.update');
    Route::delete('/billing/{id}', [BillingController::class, 'destroy'])->name('billing.destroy');
    Route::post('/billing/filter', [BillingController::class, 'filter'])->name('billing.filter');
    Route::get('/billing/export', [BillingController::class, 'export'])->name('billing.export');
    Route::get('/billing/monthly-summary', [BillingController::class, 'showMonthlySummary'])->name('billing.monthly-summary');
    Route::get('/billing/generate-invoice', [BillingController::class, 'generateInvoice'])->name('billing.generate-invoice');
    Route::post('/billing/import-excel', [BillingController::class, 'importExcel'])->name('billing.import-excel');
    Route::get('/billing/export-excel', [BillingController::class, 'exportExcel'])->name('billing.export-excel');
    Route::resource('reports', ReportsController::class);
    Route::post('/reports/filter', [ReportsController::class, 'filter'])->name('reports.filter');
    Route::post('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
});

// Permission-based routes for fine-grained control (admin can assign individual permissions)
// These routes work alongside role-based access for additional security
Route::middleware(['auth'])->group(function () {
    // Individual permission checks can be added here for specific actions
    // Example: Route::post('users/{id}/permissions', [UsersController::class, 'assignPermissions'])
    //          ->middleware('permission:manage_permissions');
});

// API routes for AJAX requests with role-based access
Route::middleware(['auth', 'role:admin,dispatcher,manager'])->group(function () {
    Route::get('/api/jobs/{id}', [JobController::class, 'show']);
    Route::get('/api/customers/{id}', [CustomerController::class, 'show']);
    Route::get('/api/drivers/{id}', [DriverController::class, 'show']);
    Route::get('/api/vehicles/{id}', [VehicleController::class, 'show']);
    Route::get('/api/trips/{id}', [TripController::class, 'show']);
    Route::get('/api/dispatch/{id}', [DispatchController::class, 'show']);
    Route::get('/api/pod/{id}', [PodController::class, 'show']);
    Route::get('/api/maintenance/{id}', [MaintenanceController::class, 'show']);
    Route::get('/api/documents/{id}', [DocumentController::class, 'show']);
    Route::get('/api/tracking/search', [TrackingController::class, 'search']);
    Route::get('/api/bilties/{bilty}', [BiltyController::class, 'show']);
    Route::get('/api/warehouse-trips/{id}', [WarehouseTripController::class, 'show']);
});

// Global search - available to all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/api/global-search', [GlobalSearchController::class, 'search']);
});

Route::middleware(['auth', 'role:admin,accounts,manager'])->group(function () {
    Route::get('/api/invoices/{id}', [InvoiceController::class, 'show']);
    Route::get('/api/expenses/{id}', [ExpenseController::class, 'show']);
    Route::get('/api/billing/{id}', [BillingController::class, 'show']);
    Route::get('/api/reports/{id}', [ReportsController::class, 'show']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/api/users/{id}', [UsersController::class, 'show']);
    Route::get('/api/settings/{id}', [SettingController::class, 'show']);
});

// Include generated CRUD resource routes (admin)
// require __DIR__ . '/crud_resources.php'; // Disabled to avoid conflicts with new controllers

// Transport Management System Routes
Route::middleware(['auth'])->group(function () {
    // Trip Log Routes
    Route::get('/transport/trip-logs', [TripLogController::class, 'index'])->name('trip-logs.index');
    Route::get('/transport/trip-logs/create', [TripLogController::class, 'create'])->name('trip-logs.create');
    Route::post('/transport/trip-logs', [TripLogController::class, 'store'])->name('trip-logs.store');
    Route::get('/transport/trip-logs/{tripLog}', [TripLogController::class, 'show'])->name('trip-logs.show');
    Route::get('/transport/trip-logs/{tripLog}/edit', [TripLogController::class, 'edit'])->name('trip-logs.edit');
    Route::put('/transport/trip-logs/{tripLog}', [TripLogController::class, 'update'])->name('trip-logs.update');
    Route::delete('/transport/trip-logs/{tripLog}', [TripLogController::class, 'destroy'])->name('trip-logs.destroy');
    Route::get('/transport/trip-logs/monthly/{month}/{year}', [TripLogController::class, 'monthlyView'])->name('trip-logs.monthly');
    Route::get('/transport/trip-logs/get-rate', [TripLogController::class, 'getRate'])->name('trip-logs.get-rate');
    Route::get('/transport/trip-logs/{tripLog}/print', [TripLogController::class, 'print'])->name('trip-logs.print');

    // Transport Management Dashboard
    Route::get('/transport/dashboard', [TransportManagementController::class, 'dashboard'])->name('transport.dashboard');
    
    // Rate Management
    Route::get('/transport/rate-management', [TransportManagementController::class, 'rateManagement'])->name('transport.rate-management');
    Route::post('/transport/rate-management', [TransportManagementController::class, 'updateRates'])->name('transport.rate-management.update');
    
    // Invoice Generation
    Route::get('/transport/invoice/{month}/{year}', [TransportManagementController::class, 'generateInvoice'])->name('transport.invoice');
    
    // Reports
    Route::get('/transport/reports/vehicle', [TransportManagementController::class, 'vehicleReport'])->name('transport.reports.vehicle');
    Route::get('/transport/reports/driver', [TransportManagementController::class, 'driverReport'])->name('transport.reports.driver');
    Route::get('/transport/reports/category', [TransportManagementController::class, 'categoryReport'])->name('transport.reports.category');
    
    // Excel Import
    Route::get('/transport/import', function() {
        return view('transport.import');
    })->name('transport.import');
    Route::get('/transport/import-new-format', function() {
        return view('pages.import-new-format');
    })->name('transport.import-new-format');
    Route::post('/transport/import-excel', [TransportManagementController::class, 'importExcel'])->name('transport.import-excel');
    Route::post('/transport/import-new-format', [ImportController::class, 'processNewFormat'])->name('transport.import-new-format.process');
});

// Generic page router fallback: any URL like /customers-page will try to load resources/views/pages/{page}.blade.php
Route::get('{page}', function ($page) {
    if (view()->exists('pages.' . $page)) {
        return view('pages.' . $page);
    }
    abort(404);
});
