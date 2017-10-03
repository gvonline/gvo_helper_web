<?php
require_once('database.php');

function getPassedTimeString($time=0) {
    if ($time == 0) {
        return '';
    }
    $passedTime = time() - $time;
    if ($passedTime < 0) {
        return '';
    }
    elseif ($passedTime < 60) {
        return sprintf('%s초 전', $passedTime);
    }
    elseif ($passedTime < 3600) {
        return sprintf('%s분 %s초 전', floor($passedTime / 60), $passedTime % 60);
    }
    else {
        return sprintf('%s시간 전', floor($passedTime / 3600));
    }
}

function getQuoteStatusString($status=0) {
    if ($status < 0) {
        return '<strong class="text-primary quote" data-toggle="tooltip" data-placement="right" title="하락세">▼</strong>';
    }
    elseif ($status > 0) {
        return '<strong class="text-danger quote" data-toggle="tooltip" data-placement="right" title="상승세">▲</strong>';
    }
    else {
        return '';
    }
}

function getResistStatusString($cityName, $itemName) {
    $goodsType = trim(getGoodsType($itemName));
    if (strlen($goodsType) == 0) {
        return '';
    }
    if (isCityHasGoodsType($cityName, $goodsType)) {
        return '<span class="text-danger resist" data-toggle="tooltip" data-placement="right" title="내성항">★</span>';
    }
    else {
        return '';
    }
}

function compareQuoteCityName($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['CITY'], $b['CITY']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

function compareTimeQuoteCityName($a, $b) {
    if ($a['TIME'] == $b['TIME']) {
        return compareQuoteCityName($a, $b);
    }
    return ($a['TIME'] > $b['TIME']) ? -1 : 1;
}

function compareQuoteItemName($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['NAME'], $b['NAME']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

function compareTimeQuoteItemName($a, $b) {
    if ($a['TIME'] == $b['TIME']) {
        return compareQuoteItemName($a, $b);
    }
    return ($a['TIME'] > $b['TIME']) ? -1 : 1;
}

function compareTimeItemName($a, $b) {
    if ($a['TIME'] == $b['TIME']) {
        return strcmp($a['NAME'], $b['NAME']);
    }
    return ($a['TIME'] > $b['TIME']) ? -1 : 1;
}

function compareTimeCityName($a, $b) {
    if ($a['TIME'] == $b['TIME']) {
        return strcmp($a['CITY'], $b['CITY']);
    }
    return ($a['TIME'] > $b['TIME']) ? -1 : 1;
}

function getListDataForCity($name, $order, $redis) {
    $result = array();
    $i = 0;
    $searchKey =  sprintf('도시:%s', $name);
    $listKeys = $redis->keys($searchKey);
    asort($listKeys);
    foreach ($listKeys as $cityKey) {
        $city = unserialize($redis->get($cityKey));
        $result[$i] = $city;
        $result[$i]['ITEMS'] = array();
        $items = $redis->keys($city['NAME'].':*');
        foreach ($items as $itemKey) {
            $result[$i]['ITEMS'][] = unserialize($redis->get($itemKey));
        }
        switch ($order) {
            case 'time':
                usort($result[$i]['ITEMS'], 'compareTimeQuoteItemName');
                break;

            default:
                usort($result[$i]['ITEMS'], 'compareQuoteItemName');
                break;
        }
        $i++;
    }
    return $result;
}

function getListDataForItem($name, $order, $redis) {
    $result = array();
    $i = 0;
    $searchKey =  sprintf('*:%s', $name);
    $listKeys = $redis->keys($searchKey);
    foreach ($listKeys as $itemKey) {
        $result[$i] = unserialize($redis->get($itemKey));
        $result[$i]['CITY'] = str_replace(':'.$result[$i]['NAME'], '', $itemKey);
        $i++;
    }
    switch ($order) {
        case 'time':
            usort($result, 'compareTimeQuoteCityName');
            break;

        default:
            usort($result, 'compareQuoteCityName');
            break;
    }
    return array(array('NAME' => $name, 'CITYS' => $result));
}

function getListDataForDashboard($order, $redis) {
    $result = array();
    $i = 0;
    $listKeys = $redis->keys('도시:*');
    asort($listKeys);
    foreach ($listKeys as $cityKey) {
        $city = unserialize($redis->get($cityKey));
        $result[$i] = $city;
        $result[$i]['ITEMS'] = array();
        $items = $redis->keys($city['NAME'].':*');
        asort($items);
        foreach ($items as $itemKey) {
            $result[$i]['ITEMS'][] = unserialize($redis->get($itemKey));
        }
        $result[$i]['COUNT'] = count($result[$i]['ITEMS']);
        usort($result[$i]['ITEMS'], 'compareTimeItemName');
        if ((strlen($result[$i]['SALESTATUS']) == 0) && ($result[$i]['COUNT'] != 0)) {
            $result[$i]['TIME'] = $result[$i]['ITEMS'][0]['TIME'];
        }
        elseif (strlen($result[$i]['SALESTATUS']) == 0) {
            $result[$i]['TIME'] = time() + (60 * 60 * 3);
        }
        $i++;
    }
    switch ($order) {
        case 'time':
            usort($result, 'compareTimeItemName');
            break;

        default:
            break;
    }
    return $result;
}

function isCityInTrader($cityName) {
    global $_HAS_TRADER_CITY_NAMES;
    return in_array($cityName, $_HAS_TRADER_CITY_NAMES);
}

function isCityHasGoodsType($cityName, $goodsType) {
    global $_GOODS_TYPE_CITIS;
    if (!array_key_exists($goodsType, $_GOODS_TYPE_CITIS)) {
        return FALSE;
    }
    return in_array($cityName, $_GOODS_TYPE_CITIS[$goodsType]);
}

function getGoodsType($goodsName) {
    global $_GOODS_TYPE;
    return array_key_exists($goodsName, $_GOODS_TYPE) ? $_GOODS_TYPE[$goodsName] : FALSE;
}
