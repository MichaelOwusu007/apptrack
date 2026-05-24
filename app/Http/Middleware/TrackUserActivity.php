<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    public function __construct(private readonly AuditService $auditService) {}

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && $request->is('login')) {
            $this->auditService->log('login', null, null, null, null, 'User logged in');
        }

        return $response;
    }
}
