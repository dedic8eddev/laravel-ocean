<?php

namespace App\Http\Controllers\Auth;

use App\Services\JwtService\JwtService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JwtGuard implements Guard {
    use GuardHelpers;

    /** @var Request */
    protected $request;

    /** @var \Illuminate\Contracts\Auth\UserProvider */
    protected $provider;

    /** @var JwtService */
    protected $jwtService;

    public function __construct(Request $request, UserProvider $provider, JwtService $jwtService) {
        $this->request = $request;
        $this->provider = $provider;
        $this->jwtService = $jwtService;
    }

    /**
     * Get authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user() {
        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        if ($this->checkOceanAccess()) {
            $sub = $this->getJwtSubject();
            if (!empty($sub)) {
                $user = $this->provider->retrieveByCredentials(
                    ["sid" => $sub]
                );
            }
        }

        return $this->user = $user;
    }

    public function validate(array $credentials = []) {
        return false;
    }

    /**
     * Get jwt token from request
     *
     * @return string|null
     */
    public function getTokenForRequest() {
        return $this->request->bearerToken();
    }

    /**
     * Get tokens subject
     *
     * @return string|null
     */
    public function getJwtSubject() {
        $sub = null;

        $token = $this->getTokenForRequest();
        if (!empty($token) && $this->jwtService->verify($token)) {
            $sub = $this->jwtService->getClaim($token, 'sub');
        }

        return $sub;
    }

    protected function checkOceanAccess() {
        $token = $this->getTokenForRequest();
        return !empty($token) && $this->jwtService->verify($token) &&
            count(array_filter($this->jwtService->getClaim($token, 'role') ?: [], function ($role) {
                return preg_match("/^OCEAN-/", $role);
            })) !== 0;
    }
}