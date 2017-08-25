<?php
require_once('../assets/common.php');

$title = 'GVOnline Helper :: 폴라리스 서버';
$baseURL = 'http://polaris.gvonline.ga/';
$orderURL = '';
$orderMessage = '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$name = (($type != '') && isset($_GET['name'])) ? trim($_GET['name']) : '';
$order = isset($_GET['order']) ? trim($_GET['order']) : '';

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

$result = array();
$redis = new Redis();
$redis->connect('127.0.0.1','6380', 1, NULL, 100);
switch ($type) {
    case 'city':
        $result = getListDataForCity($name, $order, $redis);
        break;

    case 'item':
        $result = getListDataForItem($name, $order, $redis);
        break;

    default:
        $result = getListDataForDashboard($order, $redis);
        break;
}
$redis->close();

require_once('../assets/quote_list_view.php');
?>
