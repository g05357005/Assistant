<?php

namespace App\Repositories;

use App\User;
use App\Helper\FlagHelper as Flag;

class UserRepo
{
    const SERVICES_EARTHQUAKE = 1;
    const SERVICES_WEATHER    = 2;

    private $user;

    function __construct()
    {
        $this->user = new User();
    }

    /**
     * @param int $service
     * @return array
     */
    public function getUsersByService($service)
    {
        $users = User::all()
            ->reduce(function($carry, $item) use ($service) {
                if (Flag::isOn($service, $item->services)) {
                    $carry[] = $item;
                }

                return $carry;
            }, []);

        return $users;
    }

    public function registerNewUser($name, $userMid, $services = 0)
    {
        $this->user->mid      = $userMid;
        $this->user->name     = $name;
        $this->user->email    = '';
        $this->user->services = $services;
        return $this->user->save();
    }
}