<?php
switch ($order) {
    case 'time':
        $orderMessage = (strlen($type) == 0) ? '가나다 정렬' : '시세별 정렬';
        $orderURL = sprintf('%s?type=%s&name=%s', $baseURL, urlencode($type), urlencode($name));
        break;

    default :
        $orderMessage = '시간순 정렬';
        $orderURL = sprintf('%s?type=%s&name=%s&order=time', $baseURL, urlencode($type), urlencode($name));
        break;
}

$names = array();
if ($name != '') {
    $temp = explode(',', $name);
    foreach ($temp as $value) {
        $names[] = trim($value);
    }
}

$result = array();
$redis = new Redis();
$redis->connect('127.0.0.1', $port, 1, NULL, 100);
$db = new Database;
foreach ($names as $value) {
    $result = array_merge_recursive($result, getListDataForCity($value, $order, $redis));
}
$redis->close();

require_once('header_view.php');
require_once('city_list_view.php');
require_once('footer_view.php');
