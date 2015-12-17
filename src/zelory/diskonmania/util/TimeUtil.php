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

namespace Zelory\DiskonMania\Util;

class TimeUtil {

    public static function getDate($string) {
        $words = explode(" ", $string);

        return $words[3] . '-' . TimeUtil::getMonth($words[2]) . '-' . $words[1] . ' ' . $words[4] . ':00';
    }

    private static function getMonth($string) {
        switch ($string) {
            case "Januari":
                return '01';
            case "Februari":
                return '02';
            case "Maret":
                return '03';
            case "April":
                return '04';
            case "Mei":
                return '05';
            case "Juni":
                return '06';
            case "Juli":
                return '07';
            case "Agustus":
                return '08';
            case "September":
                return '09';
            case "Oktober":
                return '10';
            case "November":
                return '11';
            case "Desember":
                return '12';
            default:
                return '00';
        }
    }

}

?>