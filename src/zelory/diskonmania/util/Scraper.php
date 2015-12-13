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

use Goutte\Client;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\DomCrawler\Crawler;
use Zelory\DiskonMania\Model\Category;
use Zelory\DiskonMania\Model\Promo;

class Scraper {
    const BASE_URL = "http://www.serbapromosi.co";

    public static function scrapListPromo($category, $page) {
        $page = ($page - 1) * 10;
        $client = new Client();
        $scraper = $client->request('GET', Scraper::BASE_URL . "/$category?start=$page");
        $result = array();

        foreach ($scraper->filter(Promo::TAG_URL) as $domElement) {
            $promo = new Promo();
            $promo->url = trim(Scraper::BASE_URL . $domElement->getAttribute("href"));
            $promo->categoryId = Capsule::table(Category::TABLE_NAME)->where('name', '=', $category)->first()->id;
            array_push($result, $promo);
        }

        $i = 0;
        foreach ($scraper->filter(Promo::TAG_TITLE) as $domElement) {
            $result[$i]->title = trim($domElement->nodeValue);
            $i++;
        }

        $i = 0;
        foreach ($scraper->filter(Promo::TAG_DATE) as $domElement) {
            $result[$i]->dateText = trim(substr($domElement->nodeValue, 0, strpos($domElement->nodeValue, "Penulis")));
            $i++;
        }

        $i = 0;
        foreach ($scraper->filter(Promo::TAG_DESCRIPTION) as $domElement) {
            $result[$i]->description = trim($domElement->nodeValue);
            $i++;
        }

        $i = 0;
        foreach ($scraper->filter(Promo::TAG_IMAGE) as $domElement) {
            $imageUrl = trim($domElement->getAttribute("src"));
            $result[$i]->thumbnail = $imageUrl;
            $result[$i]->image = substr($imageUrl, 0, strpos($imageUrl, "-_-")) . strrchr($imageUrl, '.');
            $i++;
        }

        foreach ($result as $promo) {
            if (Capsule::table(Promo::TABLE_NAME)->where('url', '=', $promo->url)->first() == null) {
                $promo->save();
            }
        }

        return $result;
    }

    public static function scrapDetailPromo($url) {
        $client = new Client();
        $scraper = $client->request('GET', $url);
        $result = null;

        $scraper->filter('p')->each(function (Crawler $node) use (&$result) {
            if (strpos($node->html(), '<strong>SerbaPromosi.co') !== false) {
                $result = $node->text();
            }
        });

        return $result;
    }

    public static function scrapDetailPromoWithId($id) {
        $promo = Promo::findOrNew($id);
        $client = new Client();
        $scraper = $client->request('GET', $promo->url);
        $result = null;

        $scraper->filter('p')->each(function (Crawler $node) use (&$result) {
            if (strpos(strtolower($node->html()), '<strong>serbapromosi.co') !== false) {
                $result = $node->text();
            }
        });

        return $result;
    }

    public static function fillDescriptionPromo() {
        $i = 0;
        foreach (Capsule::table(Promo::TABLE_NAME)->where('fullDescription', '=', '')->limit(3)->get() as $item) {
            $promo = Promo::find($item->id);
            $desc = Scraper::scrapDetailPromoWithId($promo->id);
            $promo->fullDescription = $desc;
            $promo->save();
            if ($desc != '') {
                $i++;
            }
        }

        return "Filled $i Promos";
    }
}
