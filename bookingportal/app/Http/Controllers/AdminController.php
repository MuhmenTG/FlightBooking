<?php

namespace App\Http\Controllers;

use App\Models\Useraccount;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //

    public function createAgent(Request $request){

        $validator = Validator::make($request->all(), [
            'firstName'               => 'required|string',
            'lastName'                => 'required|string',
            'email'                   => 'required|string',
            'status'                  => 'required|int',
            'role'                    => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $password = "systemAgentUser";
        $status = $request->input('status');
        $role = $request->input('role');

        $userAccount = new UserAccount();
        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setPassword(Hash::make($password));
        $userAccount->setStatus($status);
        $userAccount->setRole($role);
        return $userAccount->save();

    }

    public function getSpecificAgentDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $userId = $request->input('userId');

        $user = Useraccount::ById();

    }

    public function removeAgentAccount(Request $request){

    }

    public function editAgentDetails(Request $request){

    }

    public function showListOfAgent(Request $request){

    }

    public function showAllBookings(Request $request){

    }
}
