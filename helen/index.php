<?php
require_once('../assets/common.php');

$title = 'GVOnline Helper :: 헬레네 서버';
$baseURL = 'http://helen.gvonline.ga/';
$orderURL = '';
$orderMessage = '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$name = (($type != '') && isset($_GET['name'])) ? trim($_GET['name']) : '';
$order = isset($_GET['order']) ? trim($_GET['order']) : '';
$port = '6381';

switch ($type) {
    case 'city':
        require_once('../assets/city_list_view_controller.php');
        break;
    case 'item':
        require_once('../assets/item_list_view_controller.php');
        break;
    default:
        require_once('../assets/main_list_view_controller.php');
        break;
}
?>