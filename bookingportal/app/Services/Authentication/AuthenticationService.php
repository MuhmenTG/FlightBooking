<?php

declare(strict_types=1);

namespace App\Services\Authentication;

use App\Repositories\IBackOfficeRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection; // Import the correct type


class AuthenticationService implements IAuthenticationService {

    protected $backOfficeRepository;

    public function __construct(IBackOfficeRepository $backOfficeRepository)
    {
        $this->backOfficeRepository = $backOfficeRepository;
    }

    public function authenticate(string $email, string $password): ?Collection
    {
        $user = $this->backOfficeRepository->getUserByEmail($email);

        if (!$user || !Hash::check($password, $user->getPassword())) {
            return null;
        }

        $token = $user->createToken('apiToken')->plainTextToken;

        $userData = collect([
            'user' => $user,
            'token' => $token
        ]);

        return new Collection($userData->all());
    }
}