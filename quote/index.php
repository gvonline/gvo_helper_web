<?php
if (isset($_POST['city_name'])) {
    $redis = new Redis();
    $redis->connect('127.0.0.1','6379', 1, NULL, 100);
    $ttl = 60 * 60 * 3; // 3시간 후 자동 삭제
    $cityName = isset($_POST['city_name']) ? trim($_POST['city_name']) : '';
    $cityStatus = isset($_POST['city_name']) ? trim($_POST['city_status']) : '';
    $itemName = isset($_POST['city_name']) ? trim($_POST['item_name']) : '';
    $saleQuote = isset($_POST['city_name']) ? trim($_POST['sale_quote']) : '';
    $saleStatus = isset($_POST['city_name']) ? trim($_POST['sale_status']) : '';

    if ((strlen($cityName) != 0) && (strlen($cityStatus) == 0)) {
        $key = '도시:'.$cityName;
        $value = unserialize($redis->get($key));
        if (!is_array($value)) {
            $value = serialize(array('TIME'=>0, 'NAME'=>$cityName, 'SALESTATUS'=>''));
            $redis->setex($key, $ttl, $value);
        }
    }

    if ((strlen($cityName) != 0) && (preg_match('/(남아|대폭|잘팔|어서)/', $cityStatus) != 0)) {
        $key = '도시:'.$cityName;
        $value = serialize(array('TIME'=>time(), 'NAME'=>$cityName, 'SALESTATUS'=>$cityStatus));
        $redis->setex($key, $ttl, $value);
    }

    if ((strlen($cityName) != 0) && (strlen($itemName) != 0) && is_numeric($saleQuote) && is_numeric($saleStatus)) {
        $key = $cityName.':'.$itemName;
        $value = serialize(array('TIME'=>time(), 'NAME'=>$itemName, 'SALEQUOTE'=>$saleQuote, 'SALESTATUS'=>$saleStatus));
        $redis->setex($key, $ttl, $value);
    }

    $redis->close();
    return;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>GVOnline Helper :: 시세 도우미</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="padding-bottom: 10px">
    <h2>GVOnline Helper :: 시세 도우미</h2>
</div>
<div class="container" style="padding-bottom: 10px">
    <p>게임 실행시 아래 파일을 다운로드 받어 실행 하면 자동으로 등록 됩니다.</p>
</div>
</body>
</html>