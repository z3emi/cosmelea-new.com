<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Session;

    class SetLocale
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Closure  $next
         * @return mixed
         */
        public function handle($request, Closure $next)
        {
            if (Session::has('locale')) {
                App::setLocale(Session::get('locale'));
            } else {
                App::setLocale(config('app.locale')); // استخدام اللغة الافتراضية من ملف app.php
            }

            return $next($request);
        }
    }