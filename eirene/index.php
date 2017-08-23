<?php
require_once('../assets/common.php');

$title = 'GVOnline Helper :: 에이레네 서버';
$baseURL = 'http://eirene.gvonline.ga/';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$name = isset($_GET['name']) ? trim($_GET['name']) : '';

$redis = new Redis();
$redis->connect('127.0.0.1','6379', 1, NULL, 100);
$result = getListData($type, $name, $redis);
$redis->close();

require_once('../assets/quote_list_view.php');
?>