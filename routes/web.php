<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\MyVehicleController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookingCalendarController;
use App\Http\Controllers\BookingRedirectController;
use App\Http\Controllers\PublicOwnerController;
use App\Http\Controllers\LibMunicipalityController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DispatchingController;
use App\Http\Controllers\Admin\CarwashServicePaymentsController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\OwnerRatingsController;
use App\Http\Controllers\Admin\ServiceFeePaymentsController;
use App\Http\Controllers\Admin\BannedRenterController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicVehicleController;
use App\Http\Controllers\MyIncomeController;

Route::get('/', [PublicVehicleController::class , 'index'])->name('home');
Route::get('/book-now/{vehicle}', BookingRedirectController::class)->name('book.now');
Route::get('/owners/{user}/profile', [PublicOwnerController::class, 'show'])->name('owners.profile');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');

Route::get('/calendar/vehicles/{vehicle}/events', [BookingCalendarController::class, 'vehicleEvents'])->name('vehicles.calendar.events');

Route::get('/my-bookings', [RentalController::class, 'index'])
    ->middleware(['auth', 'verified', 'profile.complete'])->name('dashboard');

Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile/legitimacy-proofs/{proof}', [ProfileController::class , 'destroyLegitimacyProof'])->name('profile.legitimacy-proofs.destroy');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');

    Route::get('/all-cars', [VehicleController::class , 'index'])->name('vehicles.index');

    // Booking Routes
    Route::get('/book/{enc_id}', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/book/{enc_id}', [RentalController::class, 'store'])->name('rentals.store');

    // User Loan Routes
    Route::get('/loans', [\App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [\App\Http\Controllers\LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans', [\App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}', [\App\Http\Controllers\LoanController::class, 'show'])->name('loans.show');

    Route::post('/rentals/{rental}/confirm', [RentalController::class, 'confirm'])->name('rentals.confirm');
    Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel'])->name('rentals.cancel');
    Route::post('/rentals/{rental}/complete', [RentalController::class, 'complete'])->name('rentals.complete');
    Route::post('/rentals/{rental}/cancel-by-renter', [RentalController::class, 'cancelByRenter'])->name('rentals.cancel_by_renter');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/vehicles/{vehicle}/reviews', [ReviewController::class, 'vehicle'])->name('reviews.vehicle');

});

Route::middleware(['auth', 'profile.complete', 'aaracc'])->group(function () {
    Route::resource('my-cars', MyVehicleController::class)->except(['create', 'show', 'edit']);
    Route::post('/my-cars/images/{image}/primary', [MyVehicleController::class , 'setPrimaryImage'])->name('my-cars.images.primary');

    Route::get('/booking-calendar', [BookingCalendarController::class, 'index'])->name('booking.calendar');
    Route::get('/booking-calendar/events', [BookingCalendarController::class, 'events'])->name('booking.calendar.events');
    Route::post('/booking-calendar/owner-bookings', [BookingCalendarController::class, 'storeOwnerBooking'])->name('booking.calendar.owner-bookings.store');
    Route::put('/booking-calendar/owner-bookings/{rental}', [BookingCalendarController::class, 'updateOwnerBooking'])->name('booking.calendar.owner-bookings.update');
    Route::delete('/booking-calendar/owner-bookings/{rental}', [BookingCalendarController::class, 'destroyOwnerBooking'])->name('booking.calendar.owner-bookings.destroy');

    Route::get('/client-bookings', [RentalController::class, 'manageOwnedBookings'])
        ->name('bookings.manage');

    Route::get('/my-income', [MyIncomeController::class, 'index'])->name('my-income');
});

Route::middleware(['auth', 'verified', 'profile.complete', 'aaracc'])->group(function () {
    Route::get('/price-per-location', [LibMunicipalityController::class, 'index'])->name('municipalities.index');
    Route::get('/price-per-location/create', [LibMunicipalityController::class, 'create'])->name('municipalities.create');
    Route::post('/price-per-location', [LibMunicipalityController::class, 'store'])->name('municipalities.store');
    Route::get('/price-per-location/{libMunicipality}/edit', [LibMunicipalityController::class, 'edit'])->name('municipalities.edit');
    Route::put('/price-per-location/{libMunicipality}', [LibMunicipalityController::class, 'update'])->name('municipalities.update');
    Route::delete('/price-per-location/{libMunicipality}', [LibMunicipalityController::class, 'destroy'])->name('municipalities.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie-consent.store');
    Route::patch('/cookie-consent', [CookieConsentController::class, 'update'])->name('cookie-consent.update');
    Route::post('/cookie-consent/decline', [CookieConsentController::class, 'decline'])->name('cookie-consent.decline');
    Route::delete('/cookie-consent', [CookieConsentController::class, 'forget'])->name('cookie-consent.forget');
});

Route::middleware(['auth', 'verified', 'profile.complete', 'aaracc'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/dashboard-data', [AdminController::class, 'dashboardData'])->name('dashboard.data');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/dispatching', [DispatchingController::class, 'index'])->name('dispatching.index');
    Route::get('/dispatching/vehicles', [DispatchingController::class, 'vehicles'])->name('dispatching.vehicles');
    Route::get('/dispatching/dispatch-form', [DispatchingController::class, 'dispatchForm'])->name('dispatching.dispatch-form');
    Route::post('/dispatching/dispatch', [DispatchingController::class, 'dispatchStore'])->name('dispatching.dispatch-store');
    Route::get('/service-fee-payments', [ServiceFeePaymentsController::class, 'index'])->name('service-fee-payments.index');
    Route::get('/service-fee-payments/members', [ServiceFeePaymentsController::class, 'members'])->name('service-fee-payments.members');
    Route::post('/service-fee-payments', [ServiceFeePaymentsController::class, 'store'])->name('service-fee-payments.store');
    Route::put('/service-fee-payments/{payment}', [ServiceFeePaymentsController::class, 'update'])->name('service-fee-payments.update');
    Route::delete('/service-fee-payments/{payment}', [ServiceFeePaymentsController::class, 'destroy'])->name('service-fee-payments.destroy');

    Route::get('/carwash-service-payments', [CarwashServicePaymentsController::class, 'index'])->name('carwash-service-payments.index');
    Route::post('/carwash-service-payments', [CarwashServicePaymentsController::class, 'store'])->name('carwash-service-payments.store');
    Route::put('/carwash-service-payments/{payment}', [CarwashServicePaymentsController::class, 'update'])->name('carwash-service-payments.update');
    Route::delete('/carwash-service-payments/{payment}', [CarwashServicePaymentsController::class, 'destroy'])->name('carwash-service-payments.destroy');

    // Admin Member Capitals
    Route::resource('member-capitals', \App\Http\Controllers\Admin\MemberCapitalController::class)->except(['show']);

    // Admin Loans
    Route::get('/loans', [\App\Http\Controllers\Admin\LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/{loan}', [\App\Http\Controllers\Admin\LoanController::class, 'show'])->name('loans.show');
    Route::put('/loans/{loan}', [\App\Http\Controllers\Admin\LoanController::class, 'update'])->name('loans.update');
    Route::post('/loans/{loan}/approve', [\App\Http\Controllers\Admin\LoanController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [\App\Http\Controllers\Admin\LoanController::class, 'reject'])->name('loans.reject');
    Route::get('/loans/{loan}/payments/create', [\App\Http\Controllers\Admin\LoanController::class, 'createPayment'])->name('loans.payments.create');
    Route::post('/loans/{loan}/payments', [\App\Http\Controllers\Admin\LoanController::class, 'storePayment'])->name('loans.payments.store');
    Route::put('/loans/{loan}/payments/{payment}', [\App\Http\Controllers\Admin\LoanController::class, 'updatePayment'])->name('loans.payments.update');
    Route::delete('/loans/{loan}/payments/{payment}', [\App\Http\Controllers\Admin\LoanController::class, 'destroyPayment'])->name('loans.payments.destroy');

    Route::get('/loan-collections', [\App\Http\Controllers\Admin\LoanCollectionController::class, 'index'])->name('loan-collections.index');

    Route::get('/faqs', [AdminFaqController::class, 'index'])->name('faqs.index');
    Route::get('/faqs/create', [AdminFaqController::class, 'create'])->name('faqs.create');
    Route::post('/faqs', [AdminFaqController::class, 'store'])->name('faqs.store');
    Route::get('/faqs/{faq}/edit', [AdminFaqController::class, 'edit'])->name('faqs.edit');
    Route::put('/faqs/{faq}', [AdminFaqController::class, 'update'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [AdminFaqController::class, 'destroy'])->name('faqs.destroy');

    Route::get('/owner-ratings', [OwnerRatingsController::class, 'index'])->name('owner-ratings.index');
    Route::resource('banned-renters', BannedRenterController::class);
});

Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleController::class , 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class , 'handleGoogleCallback'])->name('google.callback');

require __DIR__ . '/auth.php';
