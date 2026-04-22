<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Dashboard;
use App\Livewire\Pages\Organisations\Index as OrganisationsIndex;
use App\Livewire\Pages\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Livewire\Pages\Users\Profile;
use App\Livewire\Pages\Survivants\Index as SurvivantsIndex;
use App\Livewire\Pages\Incidents\Index as IncidentsIndex;
use App\Livewire\Pages\Incidents\Show as IncidentsShow;
use App\Livewire\Pages\ServiceProviders\Index as ServiceProvidersIndex;
use App\Http\Controllers\IncidentPrintController;
use App\Http\Controllers\IncidentExportController;
use App\Livewire\Pages\Supervision\SuperviseurPerformance;
use App\Livewire\Pages\Superviseurs\Performance;


//Route::view('/', 'welcome');

//Route::view('/', 'landing')->name('landing');

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::get('/a-propos', function () {
    return view('about');
})->name('about');

Route::get('/a-propos-nous', function () {
    return view('about_us');
})->name('about_us');

Route::get('/phpinfo', function () {
    phpinfo();
});

/*Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');*/

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/organisations', OrganisationsIndex::class)->name('organisations.index');
    Route::get('/profile', Profile::class)->name('profile');

    Route::get('/survivants', SurvivantsIndex::class)->name('survivants.index');
    Route::get('/incidents', IncidentsIndex::class)->name('incidents.index');
    Route::get('/incidents/{incident}', IncidentsShow::class)->name('incidents.show');

    Route::get('/incidents/{incident}/print', [IncidentPrintController::class, 'show'])
        ->name('incidents.print');

    Route::get('/exports/incidents', [IncidentExportController::class, 'export'])
        ->name('exports.incidents');

    Route::middleware(['role:superadmin,admin'])->group(function () {
        Route::get('/service-providers', ServiceProvidersIndex::class)->name('service-providers.index');
    });

    Route::get('/organisations', OrganisationsIndex::class)->name('organisations.index');



    Route::middleware(['role:superadmin,admin'])->group(function () {
        Route::get('/supervision/performance', Performance::class)->name('supervision.performance');
    });
});

/*Route::middleware(['auth', 'active', 'role:superadmin'])->group(function () {
    Route::get('/service-providers', ServiceProvidersIndex::class)->name('service-providers.index');
});*/




//Temporaire pour tester les rôles et permissions
Route::middleware(['auth', 'active'])->get('/whoami', function () {
    $u = Auth::user();
    return [
        'email' => $u->email,
        'province' => $u->code_province,
        'roles' => $u->roles->pluck('slug'),
    ];
});

Route::middleware(['auth', 'active', 'role:superadmin'])->group(function () {
    Route::get('/users', UsersIndex::class)->name('users.index');
});



/*
// Superadmin uniquement
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/organisations', \App\Livewire\Pages\Organisations\Index::class)
            ->name('organisations.index');
    });

    // Admin + Superadmin
    Route::middleware(['role:superadmin,admin'])->group(function () {
        Route::get('/users', \App\Livewire\Pages\Users\Index::class)
            ->name('users.index');
    });

    // Superviseur + Admin + Superadmin
    Route::middleware(['role:superadmin,admin,superviseur'])->group(function () {
        Route::get('/incidents', \App\Livewire\Pages\Incidents\Index::class)
            ->name('incidents.index');
    });*/




/*Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');*/

//Route::middleware(['auth', 'active'])->get('/profile', Profile::class)->name('profile');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('landing');
})->name('logout');






require __DIR__ . '/auth.php';
