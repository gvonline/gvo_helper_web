<?php
require_once('../assets/common.php');

$title = 'GVOnline Helper :: 헬레네 서버';
$baseURL = 'http://helen.gvonline.ga/index2.php';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$name = isset($_GET['name']) ? trim($_GET['name']) : '';

$redis = new Redis();
$redis->connect('127.0.0.1','6381', 1, NULL, 100);
$result = getListData($type, $name, $redis);
$redis->close();

require_once('../assets/quote_list_view.php');
?>
