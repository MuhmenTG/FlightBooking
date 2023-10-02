<?php

declare(strict_types=1);

namespace App\Services\Authentication;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IAuthenticationService
 *
 * This interface defines the contract for authentication services.
 * Authentication services are responsible for authenticating users based on their credentials,
 * typically a combination of email and password.
 */
interface IAuthenticationService
{
    /**
     * Authenticate a user based on their email and password.
     *
     * @param string $email The user's email address.
     * @param string $password The user's password.
     *
     * @return Collection|null Returns a collection of user data if authentication is successful.
     *                       Returns null if authentication fails.
     */
    public function authenticate(string $email, string $password): ?Collection;
}
