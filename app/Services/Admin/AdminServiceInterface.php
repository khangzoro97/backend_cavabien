<?php

namespace App\Services\Admin;

interface AdminServiceInterface
{
    public function register($params);
    public function login($params);
    public function logout();
}
