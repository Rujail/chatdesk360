<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $auth;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase-auth.json'));

        $this->auth = $factory->createAuth();
    }

    public function createCustomToken($uid, $claims = [])
    {
        return $this->auth->createCustomToken($uid, $claims);
    }
}