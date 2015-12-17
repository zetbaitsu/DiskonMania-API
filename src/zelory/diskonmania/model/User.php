<?php
/*
 * Copyright (c) 2015 Zetra.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * Created on : 12/17/15
 * Author     : zetbaitsu
 * Name       : Zetra
 * Email      : zetra@mail.ugm.ac.id
 * GitHub     : https://github.com/zetbaitsu
 * LinkedIn   : https://id.linkedin.com/in/zetbaitsu
 */

namespace Zelory\DiskonMania\Model;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Model;

class User extends Model {
    const TABLE_NAME = "users";

    public $timestamps = false;

    public static function login($username, $password) {
        return Capsule::table(User::TABLE_NAME)
            ->where('username', '=', $username)
            ->where('password', '=', md5($password))
            ->first(['username', 'name', 'token']);
    }

    public static function register($username, $name, $password) {
        $user = new User();
        $user->username = $username;
        $user->name = $name;
        $user->password = md5($password);
        $user->token = md5(uniqid($username, true));

        if (Capsule::table(User::TABLE_NAME)->where('username', '=', $username)->first() == null) {
            $user->save();
            return User::login($username, $password);
        } else {
            throw new \Exception($username . " has ben taken.");
        }
    }

    public static function updatePassword($token, $oldPassword, $newPassword) {

        if ($oldPassword == $newPassword) {
            throw new \Exception("Please insert different password with old password");
        } else if ($token == null or $token == "") {
            throw new \Exception("Session expired, please re-login");
        } else if ($oldPassword == null or $oldPassword == "") {
            throw new \Exception("Invalid old password");
        } else if ($newPassword == null or $newPassword == "") {
            throw new \Exception("New password must be not empty!");
        }

        $user = User::query()
            ->where('token', '=', $token)
            ->where('password', '=', md5($oldPassword))
            ->first();

        if ($user == null) {
            throw new \Exception("Invalid password");
        } else {

            $user->password = md5($newPassword);
            $user->save();
            return "Password updated.";
        }
    }
}