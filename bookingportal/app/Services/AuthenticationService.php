<?php

declare(strict_types=1);
namespace App\Services;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;

class AuthenticationService {

    public static function authenticate(string $email, string $password) : array
    {
        $user = UserAccount::ByEmail($email)->first();

        if (!$user || !Hash::check($password, $user->getPassword())) {
            return null;
        }

        $token = $user->createToken('apiToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return $response;
    }
}