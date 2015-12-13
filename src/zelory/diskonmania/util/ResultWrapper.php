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

namespace Zelory\DiskonMania\Util;

use Psr\Http\Message\ResponseInterface as Response;

class ResultWrapper {
    public static function getResult($item, Response $response) {
        $result = json_encode(array('status' => 'success', 'result' => $item),
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($result);
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    public static function getError($item, Response $response) {
        $result = json_encode(array('status' => 'error', 'message' => $item),
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($result);
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }
}