<?php

namespace App\Services\JwtService;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Throwable;

class JwtServiceImpl implements JwtService {

    protected $key;

    public function __construct(string $key) {
        $this->key = $key;
    }

    public function verify($token) {
        $data = new ValidationData();

        try {
            $parsed = (new Parser())->parse($token);
            $valid = $parsed->validate($data);
            $verified = $parsed->verify(new Sha256(), new Key($this->key));
            return $valid && $verified;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getClaim($token, $claim) {
        if (!$token instanceof Token)
            $token = (new Parser())->parse($token);

        if ($token->hasClaim($claim))
            return $token->getClaim($claim);

        return null;
    }
}

