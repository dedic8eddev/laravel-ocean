<?php

namespace Tests\Unit;

use App\Http\Controllers\Auth\JwtGuard;
use App\Services\JwtService\JwtServiceImpl;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;

class TestJwtGuard extends TestCase
{
    public function testItWorksWithAValidBearerToken()
    {
        $provider = m::mock(UserProvider::class);
        $provider->shouldReceive('retrieveByCredentials')->once()->with(['sid' => 'foo'])->andReturn((object) ['id' => 1]);

        $jwtKey = "test-key";
        $jwtService = new JwtServiceImpl($jwtKey);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->createToken($jwtKey, ["sub" => 'foo'])]);

        $guard = new JwtGuard($request, $provider, $jwtService);

        $user = $guard->user();
        $this->assertEquals(1, $user->id);
    }

    protected function createToken($key, $claims) {
        $builder = new Builder();

        foreach ($claims as $claim => $value)
            $builder->withClaim($claim, $value);

        return (string) $builder->getToken(new Sha256(), new Key($key));
    }
}
