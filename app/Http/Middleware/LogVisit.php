<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldLog($request)) {
            $user = auth()->user();

            activity('Kunjungan')
                ->causedBy($user)
                ->withProperties([
                    'url' => $request->fullUrl(),
                    'route' => $request->route()?->getName(),
                    'ip' => $request->ip(),
                    'browser' => $this->detectBrowser($request->userAgent()),
                    'user_agent' => $request->userAgent(),
                ])
                ->event('visit')
                ->log($user->name.' mengunjungi '.($request->route()?->getName() ?? $request->path()).' menggunakan '.$this->detectBrowser($request->userAgent()));
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        return $request->isMethod('get')
            && ! $request->ajax()
            && ! $request->wantsJson()
            && auth()->check();
    }

    private function detectBrowser(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Tidak diketahui';
        }

        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'OPR/') || str_contains($userAgent, 'Opera') => 'Opera',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Tidak diketahui',
        };
    }
}
