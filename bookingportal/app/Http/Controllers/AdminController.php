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

        echo "hi";exit;
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
        $user = $userAccount->save();

        return response()->json($user, 200);

    }

    public function getSpecificAgentDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $userId = $request->input('userId');

        $user = Useraccount::ById($userId)->first();
        if($user){
            return response()->json($user, 200);
        }

        return response()->json("Agent could not be found", 404);

    }

    public function removeAgentAccount(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $userId = $request->input('userId');

        $user = Useraccount::ById($userId)->first();

        $user->setStatus(0);
        $user->save();

    }

    public function editAgentDetails(Request $request){
        
        $validator = Validator::make($request->all(), [
            'firstName'               => 'required|string',
            'lastName'                => 'required|string',
            'email'                   => 'required|string',
            'status'                  => 'required|int',
            'role'                    => 'required|int',
            'userId'                  => 'required|int',

        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $status = $request->input('status');
        $role = $request->input('role');
        $userId = $request->input('userId');

        $userAccount = Useraccount::ById($userId)->first();
        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setStatus($status);
        $userAccount->setRole($role);
        $user = $userAccount->save();

        return response()->json($user, 400);
    }

    public function showListOfAgent(Request $request){

    }

    public function showAllBookings(Request $request){

    }
}
