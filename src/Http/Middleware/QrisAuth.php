<?php

namespace Mdigi\QrisBankJateng\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Mdigi\QrisBankJateng\Services\QrisService;

class QrisAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!app(QrisService::class)->verifyExternalApiKey($request->query('api_key'))) {
            abort(401);
        }
        return $next($request);
    }
}
