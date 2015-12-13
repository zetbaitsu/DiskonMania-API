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
 * Created on : 12/13/15
 * Author     : zetbaitsu
 * Name       : Zetra
 * Email      : zetra@mail.ugm.ac.id
 * GitHub     : https://github.com/zetbaitsu
 * LinkedIn   : https://id.linkedin.com/in/zetbaitsu
 */

namespace Zelory\DiskonMania\Model;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model as Model;

class Promo extends Model {
    const TAG_TITLE = "td.contentheading_news";
    const TAG_DATE = "span.small";
    const TAG_IMAGE = "img.timthumb";
    const TAG_DESCRIPTION = "div.list-thumb";
    const TAG_URL = "a.contentpagetitle_news";
    const TABLE_NAME = "promos";

    public $timestamps = false;

    public static function get($page) {
        return Capsule::table(Promo::TABLE_NAME)
            ->join(Category::TABLE_NAME, Promo::TABLE_NAME . '.categoryId', '=', Category::TABLE_NAME . '.id')
            ->skip(10 * ($page - 1))->take(10)
            ->get([Promo::TABLE_NAME . '.id', 'url', 'title', 'name as category', 'dateText', 'thumbnail', 'description']);
    }

    public static function getByCategory($category, $page) {
        return Capsule::table(Promo::TABLE_NAME)
            ->join(Category::TABLE_NAME, Promo::TABLE_NAME . '.categoryId', '=', Category::TABLE_NAME . '.id')
            ->where('name', '=', $category)
            ->skip(10 * ($page - 1))->take(10)
            ->get([Promo::TABLE_NAME . '.id', 'url', 'title', 'name as category', 'dateText', 'thumbnail', 'description']);
    }

    public static function getById($id) {
        return Capsule::table(Promo::TABLE_NAME)
            ->join(Category::TABLE_NAME, Promo::TABLE_NAME . '.categoryId', '=', Category::TABLE_NAME . '.id')
            ->where(Promo::TABLE_NAME . '.id', '=', $id)
            ->first([Promo::TABLE_NAME . '.id', 'url', 'title', 'name as category', 'dateText', 'image', 'fullDescription']);
    }
}