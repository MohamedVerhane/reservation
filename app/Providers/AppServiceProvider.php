<?php

namespace App\Providers;

use App\Services\AvailabilityService;
use App\Services\BookingService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\ViewErrorBag;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BookingService::class, fn () => new BookingService);
        $this->app->singleton(AvailabilityService::class, fn () => new AvailabilityService);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        RedirectResponse::macro('orJson', function () {
            if (! request()->wantsJson()) {
                return $this;
            }

            $errors = session('errors');

            if ($errors instanceof ViewErrorBag && $errors->isNotEmpty()) {
                session()->forget('errors');

                return response()->json([
                    'message' => __('The given data was invalid.'),
                    'errors' => $errors->getMessages(),
                ], 422);
            }

            $success = session('success');
            session()->forget('success');

            return response()->json([
                'success' => $success,
                'redirect' => $this->getTargetUrl(),
            ], 201);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
        });

        RateLimiter::for('password', function (Request $request) {
            return Limit::perMinute(3)->by($request->input('email').'|'.$request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
    }
}
