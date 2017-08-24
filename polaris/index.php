<?php
require_once('../assets/common.php');

$title = 'GVOnline Helper :: 폴라리스 서버';
$baseURL = 'http://polaris.gvonline.ga/index2.php';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$name = isset($_GET['name']) ? trim($_GET['name']) : '';

$redis = new Redis();
$redis->connect('127.0.0.1','6380', 1, NULL, 100);
$result = getListData($type, $name, $redis);
$redis->close();

require_once('../assets/quote_list_view.php');
?>
