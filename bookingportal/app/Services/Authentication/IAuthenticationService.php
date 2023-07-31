<?php
declare(strict_types=1);

namespace App\Services\Authentication;
use Illuminate\Database\Eloquent\Collection;

interface IAuthenticationService
{
    public function authenticate(string $email, string $password): ?Collection;
}
