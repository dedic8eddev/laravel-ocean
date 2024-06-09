<?php

namespace App\Services\JwtService;

interface JwtService {

    /**
     * Verify a jwt token.
     *
     * @param $token
     *
     * @return mixed
     */
    public function verify($token);

    /**
     * Get claim from token.
     *
     * @param $token
     * @param $claim
     *
     * @return mixed
     */
    public function getClaim($token, $claim);
}