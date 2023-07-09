<?php

namespace App\Services\User;

interface UserServiceInterface
{
    public function register($params);
    public function login($params);
    public function logout();
    public function sendMailResetPassword($email);
    public function verifyTokenResetPassword($token);
    public function updatePassword($request);
}
