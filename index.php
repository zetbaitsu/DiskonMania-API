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

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Container;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Zelory\DiskonMania\Model\Category;
use Zelory\DiskonMania\Model\Comment;
use Zelory\DiskonMania\Model\Promo;
use Zelory\DiskonMania\Model\User;
use Zelory\DiskonMania\Util\ResultWrapper;
use Zelory\DiskonMania\Util\Scraper;

require 'vendor/autoload.php';
require_once 'src/zelory/diskonmania/DB.php';

$container = new Container();
$container['foundHandler'] = function () {
    return new RequestResponseArgs();
};
$app = new App($container);

$app->get('/', function (Request $request, Response $response) {
    return ResultWrapper::getResult('API Ready', $response);
});

$app->post('/login', function (Request $request, Response $response) {
    try {
        $params = $request->getQueryParams();
        $user = User::login($params['username'], $params['password']);
        if ($user == null) {
            throw new Exception("Invalid username or password!");
        }
        return ResultWrapper::getResult($user, $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->post('/register', function (Request $request, Response $response) {
    try {
        $params = $request->getQueryParams();
        $user = User::register($params['username'], $params['name'], $params['password']);
        if ($user == null) {
            throw new Exception("Invalid username or password!");
        }
        return ResultWrapper::getResult($user, $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->put('/update-password', function (Request $request, Response $response) {
    try {
        $params = $request->getQueryParams();
        $token = $request->getHeader('token');
        $result = User::updatePassword($token, $params['oldPassword'], $params['newPassword']);
        if ($result == null) {
            throw new Exception("Wrong password!");
        }
        return ResultWrapper::getResult($result, $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get('/promo/{page}', function (Request $request, Response $response, $page) {
    try {
        return ResultWrapper::getResult(Promo::get($page), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get('/promo/{category}/{page}', function (Request $request, Response $response, $category, $page) {
    try {
        return ResultWrapper::getResult(Promo::getByCategory($category, $page), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get('/promo-detail/{id}', function (Request $request, Response $response, $id) {
    try {
        $promo = Promo::getById($id);
        if ($promo == null) {
            throw new Exception("Data not found!");
        }
        return ResultWrapper::getResult($promo, $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get('/search/{keyword}/{page}', function (Request $request, Response $response, $keyword, $page) {
    try {
        $promos = Promo::search($keyword, $page);
        if ($promos == null) {
            throw new Exception("Data not found!");
        }
        return ResultWrapper::getResult($promos, $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get('/category', function (Request $request, Response $response) {
    try {
        return ResultWrapper::getResult(Category::all(), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->post("/comment/{promoId}", function (Request $request, Response $response, $promoId) {
    try {
        $params = $request->getQueryParams();
        $token = $request->getHeader('token');
        return ResultWrapper::getResult(Comment::post($token, $promoId, $params['message']), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get("/comment-promo/{promoId}/{page}", function (Request $request, Response $response, $promoId, $page) {
    try {
        return ResultWrapper::getResult(Comment::getByPromo($promoId, $page), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get("/comment-user/{userId}/{page}", function (Request $request, Response $response, $userId, $page) {
    try {
        return ResultWrapper::getResult(Comment::getByUser($userId, $page), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});


//For scraping data from the website only
$app->get('/scrap-data/{category}/{page}', function (Request $request, Response $response, $category, $page) {
    try {
        return ResultWrapper::getResult(Scraper::scrapListPromo($category, $page), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->get('/fill-empty-desc', function (Request $request, Response $response) {
    try {
        return ResultWrapper::getResult(Scraper::fillDescriptionPromo(), $response);
    } catch (Exception $e) {
        return ResultWrapper::getError($e->getMessage(), $response);
    }
});

$app->run();