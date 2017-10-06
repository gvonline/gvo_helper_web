<?php
require_once('../assets/common.php');

$server = isset($_POST['server']) ? trim($_POST['server']) : '';
$city = isset($_POST['city']) ? trim($_POST['city']) : '';
$item = isset($_POST['item']) ? trim($_POST['item']) : '';

unset($_POST['server']);
$temp = implode('', $_POST);
if (preg_match('/[:;<>&_a-zA-Z]/', $temp) != 0) {
    exit();
}

$redis = NULL;
switch ($server) {
    case 'eirene':
        $redis = new Redis();
        $redis->connect('127.0.0.1','6379', 1, NULL, 100);
        break;

    case 'polaris':
        $redis = new Redis();
        $redis->connect('127.0.0.1','6380', 1, NULL, 100);
        break;

    case 'helen':
        $redis = new Redis();
        $redis->connect('127.0.0.1','6381', 1, NULL, 100);
        break;

    default:
        break;
}

if (is_null($redis)) {
    exit();
}

$cities = array();
if ($city != '') {
    $temp = explode(',', $city);
    foreach ($temp as $value) {
        $cities[] = trim($value);
    }
}

$items = array();
if ($item != '') {
    $temp = explode(',', $item);
    foreach ($temp as $value) {
        $items[] = trim($value);
    }
}

$db = new Database;
$result = array();
$template = array('품명', '품목', '시세', '시세상태', '시세갱신시간', '도시명', '내성항', '도시상태', '상태갱신시간');
if ((count($cities) == 0) && (count($items) == 0)) {
    $template = array('도시명', '등록건수', '도시상태', '갱신시간');
    $temp = getListDataForDashboard('', $redis);
    foreach ($temp as $value) {
        $result[] = array(
            $template[0] => $value['NAME'],
            $template[1] => count($value['ITEMS']),
            $template[2] => $value['SALESTATUS'],
            $template[3] => (int)($value['TIME'] == 0) ? 0 : (time() - $value['TIME']),
        );
    }
}
else if ((count($cities) > 0) && (count($items) == 0)) {
    foreach ($cities as $each) {
        $city = getListDataForCity($each, '', $redis)[0];
        foreach ($city['ITEMS'] as $item) {
            if ($item['SALEQUOTE'] == 0) {
                continue;
            }
            $resistCities = $db->getGoodsResistCities($item['NAME']);
            $result[] = array(
                $template[0] => $item['NAME'],
                $template[1] => $db->getGoodsType($item['NAME']),
                $template[2] => (int)$item['SALEQUOTE'],
                $template[3] => (int)$item['SALESTATUS'],
                $template[4] => (int)($item['TIME'] == 0) ? 0 : (time() - $item['TIME']),
                $template[5] => $city['NAME'],
                $template[6] => (in_array($city['NAME'], $resistCities) === FALSE) ? 0 : 1,
                $template[7] => $city['SALESTATUS'],
                $template[8] => (int)($city['TIME'] == 0) ? 0 : (time() - $city['TIME']),
            );
        }
    }
}
else if ((count($cities) == 0) && (count($items) > 0)) {
    foreach ($items as $each) {
        $item = getListDataForItem($each, '', $redis)[0];
        foreach ($item['CITYS'] as $city) {
            if ($city['SALEQUOTE'] == 0) {
                continue;
            }
            $resistCities = $db->getGoodsResistCities($item['NAME']);
            $temp = array('NAME' => '', 'SALESTATUS' => '', 'TIME' => '');
            $city['DETAIL'] = array_merge($temp, $city['DETAIL']);
            $result[] = array(
                $template[0] => $item['NAME'],
                $template[1] => $db->getGoodsType($item['NAME']),
                $template[2] => (int)$city['SALEQUOTE'],
                $template[3] => (int)$city['SALESTATUS'],
                $template[4] => (int)($city['TIME'] == 0) ? 0 : (time() - $city['TIME']),
                $template[5] => $city['DETAIL']['NAME'],
                $template[6] => (in_array($city['DETAIL']['NAME'], $resistCities) === FALSE) ? 0 : 1,
                $template[7] => $city['DETAIL']['SALESTATUS'],
                $template[8] => (int)($city['DETAIL']['TIME'] == 0) ? 0 : (time() - $city['DETAIL']['TIME']),
            );
        }
    }
}
else {
    foreach ($items as $each) {
        $item = getListDataForItem($each, '', $redis)[0];
        foreach ($item['CITYS'] as $city) {
            if ($city['SALEQUOTE'] == 0) {
               continue;
            }
            if (in_array($city['DETAIL']['NAME'], $cities) === FALSE) {
                continue;
            }
            $resistCities = $db->getGoodsResistCities($item['NAME']);
            $temp = array('NAME' => '', 'SALESTATUS' => '', 'TIME' => 0);
            $city['DETAIL'] = array_merge($temp, $city['DETAIL']);
            $result[] = array(
                $template[0] => $item['NAME'],
                $template[1] => $db->getGoodsType($item['NAME']),
                $template[2] => (int)$city['SALEQUOTE'],
                $template[3] => (int)$city['SALESTATUS'],
                $template[4] => (int)($city['TIME'] == 0) ? 0 : (time() - $city['TIME']),
                $template[5] => $city['DETAIL']['NAME'],
                $template[6] => (in_array($city['DETAIL']['NAME'], $resistCities) === FALSE) ? 0 : 1,
                $template[7] => $city['DETAIL']['SALESTATUS'],
                $template[8] => (int)($city['DETAIL']['TIME'] == 0) ? 0 : (time() - $city['DETAIL']['TIME']),
            );
        }
    }
}
$redis->close();

header('Content-Type:application/json');
echo(json_encode($result, TRUE));
