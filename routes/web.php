<?php

use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\Auth\AuthenticatedSessionController as ShopAuthenticatedSessionController;
use App\Http\Controllers\Shop\Auth\RegisteredUserController as ShopRegisteredUserController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\CategoryController;
use App\Http\Controllers\Shop\EventCalendarController;
use App\Http\Controllers\Shop\EventRegistrationController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ProductReservationController;
use App\Livewire\Admin\Categories as CategoriesPage;
use App\Livewire\Admin\EventTypes as EventTypesPage;
use App\Livewire\Admin\Events as EventsPage;
use App\Livewire\Admin\EventRegistrations as EventRegistrationsPage;
use App\Livewire\Admin\Products as ProductsPage;
use App\Livewire\Admin\Reservations as ReservationsPage;
use App\Livewire\Admin\ShopUsers as ShopUsersPage;
use App\Livewire\Admin\StaffUsers as StaffUsersPage;
use App\Models\Event;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('productos/{product:slug}', [ProductController::class, 'show'])->name('shop.products.show');
Route::post('productos/{product:slug}/reservations', [ProductReservationController::class, 'store'])
    ->name('shop.products.reserve');
Route::get('colecciones/{category:slug}', [CategoryController::class, 'show'])->name('shop.categories.show');
Route::get('eventos', EventCalendarController::class)->name('shop.events.index');
Route::post('eventos/{event:slug}/registro', [EventRegistrationController::class, 'store'])
    ->name('shop.events.register');

Route::prefix('tienda')->name('shop.')->group(function () {
    Route::middleware('guest:shop')->group(function () {
        Route::get('ingresar', [ShopAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('ingresar', [ShopAuthenticatedSessionController::class, 'store'])->name('login.store');

        Route::get('registro', [ShopRegisteredUserController::class, 'create'])->name('register');
        Route::post('registro', [ShopRegisteredUserController::class, 'store'])->name('register.store');
    });

    Route::middleware('auth:shop')->group(function () {
        Route::get('cuenta', AccountController::class)->name('account');
        Route::put('cuenta', [AccountController::class, 'update'])->name('account.update');
        Route::post('salir', [ShopAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

Route::get('admin', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('admin.redirect');

Route::get('admin/dashboard', function () {
    $currentMonth = Carbon::now()->startOfMonth();
    $calendarStart = $currentMonth->copy()->startOfWeek();
    $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek();

    $events = Event::query()
        ->whereBetween('start_at', [$calendarStart->copy()->startOfDay(), $calendarEnd->copy()->endOfDay()])
        ->orderBy('start_at')
        ->get();

    $eventsByDate = $events->groupBy(fn ($event) => $event->start_at->toDateString());

    return view('dashboard', [
        'currentMonth' => $currentMonth,
        'calendarPeriod' => CarbonPeriod::create($calendarStart, $calendarEnd),
        'eventsByDate' => $eventsByDate,
    ]);
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('categories', CategoriesPage::class)->name('categories.index');
        Route::get('products', ProductsPage::class)->name('products.index');
        Route::get('reservations', ReservationsPage::class)->name('reservations.index');
        Route::get('newsletters', \App\Livewire\Admin\Newsletters::class)->name('newsletters.index');
        Route::get('events', EventsPage::class)->name('events.index');
        Route::get('event-types', EventTypesPage::class)->name('event-types.index');
        Route::get('shop-users', ShopUsersPage::class)->name('shop-users.index');
        Route::get('staff', StaffUsersPage::class)->name('staff.index');
        Route::get('event-registrations', EventRegistrationsPage::class)->name('event-registrations.index');
    });

require __DIR__.'/settings.php';
