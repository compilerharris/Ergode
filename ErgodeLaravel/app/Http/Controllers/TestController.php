<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User as UserModel;
use \DB;

class TestController extends Controller
{
    /** Login */
    function login(Request $request)
    {
        if($DBUser = UserModel::where('username',$request->username)->where('password',$request->password)->first(['id'])){
            return response()->json([
                "userId" => $DBUser->id,
                "status" => 200,
            ]);
        }
        return response()->json([
            "error" => "User not found!",
            "status" => 500,
        ]);
    }
    
    /** To get all users */
    function getUser(Request $request)
    {
        $DBUser = UserModel::where('id',$request->userId)->first();
        if( $DBUser->username == "admin" ){
            $DBAllUsers = UserModel::where('username','!=','admin')->orderBy('updated_at','desc')->get(['id','username','age']);
            return response()->json([
                "userData" => $DBAllUsers,
                "status" => 200,
                "userType" => "admin"
            ]);
        }
        return response()->json([
            "userData" => 
            [
                "userId" => $DBUser->id,
                "username" => $DBUser->username,
                "age" => $DBUser->age,
            ],
            "status" => 200,
            "userType" => "guest"
        ]);
    }

    /** To get only one user with the help of id */
    function getOneUser(Request $request)
    {
        if($DBUser = UserModel::where('id',$request->userId)->where('username','!=','admin')->first(['id','username','age'])){
            return response()->json([
                "userId" => $DBUser->id,
                "username" => $DBUser->username,
                "age" => $DBUser->age,
                "status" => 200,
            ]);
        }
        return response()->json([
            "error" => "User not found",
            "status" => 500,
        ]);

    }

    /** To add new user */
    function addUser(Request $request)
    {
        $DBUser = new UserModel;
        $DBUser->dynamic_db_name = "dynamic_user_".$request->username;
        $DBUser->username = $request->username;
        $DBUser->password = $request->password;
        $DBUser->age = $request->age;
        if( $DBUser->save() ){
            if(DB::statement('create database '.$DBUser->dynamic_db_name)){
                $config = array(
                    'driver' => 'pgsql',
                    'url' => env('DATABASE_URL'),
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '5432'),
                    'database' => $DBUser->dynamic_db_name,
                    'username' => env('DB_USERNAME', 'forge'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset' => 'utf8',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'schema' => 'public',
                    'sslmode' => 'prefer',
                );
                config(['database.connections.pgsql' => $config]);
                // config(['database.default'=>"pgsqlC"]);
                if(DB::statement('CREATE TABLE info (id int, salary int not null)')){
                    if(DB::statement('INSERT INTO info (id, salary) VALUES (1, '.$request->salary.')')){
                        return response()->json([
                            "status" => 200,
                        ]);
                    }
                }
            }
        }
        return response()->json([
            "status" => 500,
        ]);
    }

    /** To update user info with the help of id */
    function editUser(Request $request)
    {
        $DBUser = UserModel::where('id',$request->userId)->where('username','!=','admin')->first();
        $DBUser->username = $request->username;
        $DBUser->age = $request->age;
        if( $DBUser->save() ){
            return response()->json([
                "status" => 200,
            ]);
        }
        return response()->json([
            "error" => "Data not updated!",
            "status" => 500,
        ]);
    }

    /** To delete user info with the help of id */
    function deleteUser(Request $request)
    {
        $DBUser = UserModel::where('id', $request->userId)->first();
        if(DB::statement('DROP DATABASE '.$DBUser->dynamic_db_name)){
            if($DBUser->delete()){
                $DBAllUsers = UserModel::where('username','!=','admin')->orderBy('updated_at','desc')->get(['id','username','age']);
                return response()->json([
                    "userData" => $DBAllUsers,
                    "status" => 200,
                ]);
            }
        }
    }
}