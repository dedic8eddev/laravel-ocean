<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnableCORS {

    public function handle(Request $request, Closure $next) {
        $headers = [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, Origin, Authorization, X-Organization',
        ];

        if ($request->getMethod() == "OPTIONS") {
            return response('OK', 200, $headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
        return $response;
    }
}
