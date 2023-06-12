<?php

namespace App\Services\Authentication;

interface IAuthenticationService
{
    public function authenticate(string $email, string $password): ?array;
}
