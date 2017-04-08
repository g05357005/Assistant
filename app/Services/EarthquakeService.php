<?php

namespace App\Services;

use App\Repositories\UserRepo;

class EarthquakeService
{

    public function __construct()
    {
    }

    public static function register($name, $userMid)
    {
        $userRepo = new UserRepo();
        $userRepo->registerNewUser($name, $userMid, UserRepo::SERVICES_EARTHQUAKE);
    }

    public function getRegisteredUser()
    {
        $userRepo = new UserRepo();

        return $userRepo->getUsersByService(UserRepo::SERVICES_EARTHQUAKE);
    }
}
