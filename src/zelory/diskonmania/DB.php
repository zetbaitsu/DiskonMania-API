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
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Zelory\DiskonMania\Model\Category;

$capsule = new Capsule;
$capsule->addConnection(array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'diskonMania',
    'username' => 'root',
    'password' => '',
    'prefix' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_general_ci',
));
$capsule->bootEloquent();
$capsule->setAsGlobal();
$conn = $capsule->connection();

if (!Capsule::schema()->hasTable("categories")) {
    Capsule::schema()->create("categories", function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
    });

    generateCategories();
}

if (!Capsule::schema()->hasTable("promos")) {
    Capsule::schema()->create("promos", function (Blueprint $table) {
        $table->increments('id');
        $table->integer('categoryId')->unsigned();
        $table->foreign('categoryId')->references('id')->on('categories');
        $table->string('url');
        $table->string('title');
        $table->dateTime('date');
        $table->string('thumbnail');
        $table->string('image');
        $table->text('description');
        $table->text('fullDescription');
        $table->unique('url');
    });
}

function generateCategories() {
    $category = new Category();
    $category->name = Category::KARTU_KREDIT;
    $category->save();

    $category = new Category();
    $category->name = Category::SUPERMARKET;
    $category->save();

    $category = new Category();
    $category->name = Category::GADGET;
    $category->save();

    $category = new Category();
    $category->name = Category::FLIGHT;
    $category->save();

    $category = new Category();
    $category->name = Category::FOOD;
    $category->save();

    $category = new Category();
    $category->name = Category::FASHION;
    $category->save();

    $category = new Category();
    $category->name = Category::WISATA;
    $category->save();
}