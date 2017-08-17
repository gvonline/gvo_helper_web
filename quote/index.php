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
<title>GVOnline Quote Helper :: 시세 도우미</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="padding-bottom: 10px">
    <h2>GVOnline Quote Helper :: 시세 도우미</h2>
</div>
<div class="container" style="padding-bottom: 10px">
    <div class="center-block">
        <p>이 프로그램은 게임화면을 분석하여 <strong class="text-success">도시명</strong>, <strong class="text-primary">교역소 주인의 대화</strong>, <strong class="text-danger">교역품명 및 시세</strong> 정보를 수집합니다.</p>
        <p>수집은 상기 정보를 제외한 어떠한 내용도 수집하지 않습니다.</p>
        <p>과거 DHOAgent 와 유사한 동작을 하는 프로그램입니다.<br />다른 점은 이 프로그램은 자동으로 수집만 하며 실제 자료는 <a href="http://gvonline.ga/">이곳 Site</a> 에서 확인 하게 됩니다.<br />모바일로 확인할 수도 있으며, 프로그램이 없어도 확인은 가능합니다.</p>
        <p>일반 네비 프로그램 처럼 게임 실행 시 아래 파일을 다운로드 받아 실행하면 시세 데이터는 자동으로 수집됩니다.</p>
        <p>Windows 10 기준으로 제작되어 있으며 아직은 많은 기간 테스트가 필요합니다.</p>
    </div>
    <div class="center-block">
        <br />
        <a class="btn btn-warning btn-lg btn-block" href="http://gvonline.ga/asset/GVOnlineQuoteHelper.zip" role="button">프로그램 다운로드</a>
        <br />
        <a class="btn btn-primary btn-lg btn-block" href="http://gvonline.ga" role="button">돌아가기</a>
    </div>
</div>
</body>
</html>