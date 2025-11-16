<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
// use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::ignoreRoutes();

        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });
        Fortify::loginView(function () {
            return view('auth.login');
        });
        //あとで消す！！！
        RateLimiter::for('login', function () {
            return Limit::none();
        });

        Fortify::authenticateUsing(function (Request $request) {
            $loginRequest = \App\Http\Requests\LoginRequest::createFrom($request);
            $loginRequest->setContainer(app())->validateResolved();

            $validated = $loginRequest->validate(
                $loginRequest->rules(),
                $loginRequest->messages(),
                $loginRequest->attributes()
            );
            $user = User::where('email', $loginRequest->email)->first();

            if ($user && Hash::check($loginRequest->password, $user->password)) {
                return $user;
            }

            session()->flash('error', 'ログイン情報が登録されていません');
            return null;
        });
    }
}
