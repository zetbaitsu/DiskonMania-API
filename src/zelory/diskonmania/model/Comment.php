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

class Comment extends Model {
    const TABLE_NAME = "comments";

    public $timestamps = false;

    public static function post($token, $promoId, $message) {
        if ($token == null or $token == "") {
            throw new \Exception("Session expired, please re-login");
        } else if ($promoId == null or $promoId == "" or Promo::find($promoId) == null) {
            throw new \Exception("Invalid promo item");
        } else if ($message == null or $message == "") {
            throw new \Exception("Comment must be not empty!");
        }

        $userId = User::query()->where('token', '=', $token)->first()->id;
        if ($userId == null or $userId == "") {
            throw new \Exception("Session expired, please re-login");
        }

        $comment = new Comment();
        $comment->userId = $userId;
        $comment->promoId = $promoId;
        $comment->date = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
        $comment->message = $message;
        $comment->save();

        $savedComment = Comment::find($comment->id);
        $savedComment->user = User::query()->where('id', '=', $savedComment->userId)->first(['id', 'username', 'name']);
        unset($savedComment->userId);
        return $savedComment;
    }

    public static function getByPromo($promoId, $page) {
        $result = Capsule::table(Comment::TABLE_NAME)
            ->where('promoId', '=', $promoId)
            ->orderBy('date')
            ->skip(10 * ($page - 1))->take(10)
            ->get();

        foreach ($result as $item) {
            $item->user = User::query()->where('id', '=', $item->userId)->first(['id', 'username', 'name']);
            unset($item->userId);
        }

        return $result;
    }

    public static function getByUser($userId, $page) {
        $result = Capsule::table(Comment::TABLE_NAME)
            ->where('userId', '=', $userId)
            ->orderBy('date', 'desc')
            ->skip(10 * ($page - 1))->take(10)
            ->get();

        foreach ($result as $item) {
            $item->promo = Promo::query()->where('id', '=', $item->promoId)->first(['id', 'title', 'thumbnail']);
            unset($item->promoId);
        }

        return $result;
    }

    public static function del($token, $id) {
        if ($token == null or $token == "") {
            throw new \Exception("Session expired, please re-login");
        }

        $userId = User::query()->where('token', '=', $token)->first()->id;
        if ($userId == null or $userId == "") {
            throw new \Exception("Session expired, please re-login");
        }

        $comment = Comment::find($id);
        if ($comment == null) {
            throw new \Exception("Invalid comment item");
        }

        if ($comment->delete()) {
            return "Comment item has ben deleted";
        } else {
            return "Error while deleting comment";
        }
    }
}