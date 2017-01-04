<?php

namespace App\Services;

use App\User;

class UserService
{
    const SERVICES_EARTHQUACK = 1;
    const SERVICES_WEATHER    = 2;

    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function register($name, $services)
    {
        $this->user->name = $name;
        // $this->user->email = $email;
        $this->user->services = $services;
        return $this->user->save();
    }
}