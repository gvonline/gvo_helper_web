<?php
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
        return '<strong class="text-primary" title="하락세">▼</strong>';
    }
    elseif ($status > 0) {
        return '<strong class="text-danger" title="상승세">▲</strong>';
    }
    else {
        return ''; //'<i class="fa fa-play text-success" aria-hidden="true"></i>';
    }
}

function compareQuoteCity($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['CITY'], $b['CITY']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

function compareQuoteItem($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['NAME'], $b['NAME']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

function compareTimeItem($a, $b) {
    if ($a['TIME'] == $b['TIME']) {
        return strcmp($a['NAME'], $b['NAME']);
    }
    return ($a['TIME'] > $b['TIME']) ? -1 : 1;
}

function getListData($type, $name, $redis) {
    $result = array();
    $i = 0;
    if ($type == 'city') {
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
            usort($result[$i]['ITEMS'], 'compareQuoteItem');
            $i++;
        }
    }
    elseif ($type == 'item') {
        $searchKey =  sprintf('*:%s', $name);
        $listKeys = $redis->keys($searchKey);
        foreach ($listKeys as $itemKey) {
            $result[$i] = unserialize($redis->get($itemKey));
            $result[$i]['CITY'] = str_replace(':'.$result[$i]['NAME'], '', $itemKey);
            $i++;
        }
        usort($result, 'compareQuoteCity');
    }
    else {
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
			usort($result[$i]['ITEMS'], 'compareTimeItem');
			if ((strlen($result[$i]['ITEMS']) == 0) && ($result[$i]['COUNT'] != 0)) {
                $result[$i]['TIME'] = $result[$i]['ITEMS'][0]['TIME'];
			}
            $i++;
        }
    }
    return $result;
}
