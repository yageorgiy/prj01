<?php

namespace App\Services\Auth;

use Illuminate\Auth\SessionGuard;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
//use GuzzleHttp\json_decode;
//use phpDocumentor\Reflection\Types\Array_;
use Illuminate\Contracts\Auth\Authenticatable;

class HeaderGuard implements Guard
{
    protected $request;
    protected $provider;
    protected $user;

    /**
     * Create a new authentication guard.
     *
     * @param UserProvider $provider
     * @param Request $request
     * @return void
     */

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->user = NULL;
    }


    public function check()
    {
        return !is_null($this->user());
    }

    public function guest()
    {
        return !$this->check();
    }

    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $authHeader = $this->request->header("Authorization");
        if ($authHeader == null)
            return null;

        $authData = explode(" ", $authHeader);
        if (count($authData) != 2 || $authData[0] != "Basic")
            return null;

        $base64 = base64_decode($authData[1], true);
        if (!$base64)
            return null;

        $decoded = explode(":", $base64);
        if (count($decoded) != 2)
            return null;

        $login = $decoded[0];
        $password = $decoded[1];

        $found = $this->provider->retrieveByCredentials([
            "email" => $login,
            "password" => $password
        ]);

        if ($found == null)
            return null;

        $this->user = $found;
        return $this->user;
    }

    public function id()
    {
        if ($user = $this->user()) {
            return $this->user()->getAuthIdentifier();
        }
    }

    public function validate(array $credentials = [])
    {

    }

    public function hasUser()
    {
        return !is_null($this->user());
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }
}
