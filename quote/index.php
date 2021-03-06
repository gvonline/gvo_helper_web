<?php
require_once('../assets/common.php');

if (isset($_POST['server']) && isset($_POST['city_name'])) {
    $server = isset($_POST['server']) ? trim($_POST['server']) : '';
    $cityName = isset($_POST['city_name']) ? trim($_POST['city_name']) : '';
    $cityStatus = isset($_POST['city_status']) ? trim($_POST['city_status']) : '';
    $itemName = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
    $saleQuote = isset($_POST['sale_quote']) ? trim($_POST['sale_quote']) : '';
    $saleStatus = isset($_POST['sale_status']) ? trim($_POST['sale_status']) : '';
    $ttl = 60 * 60 * 3; // 3시간 후 자동 삭제
    $redis = NULL;
    $db = new Database;

    if (!in_array($cityName, $db->getHasTraderCities())) {
        exit();
    }

    unset($_POST['server']);
    $temp = implode('', $_POST);
    if (preg_match('/[:;<>&_a-zA-Z]/', $temp) != 0) {
        exit();
    }

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

    if ((strlen($cityName) != 0) && (strlen($cityStatus) == 0)) {
        $key = '도시:'.$cityName;
        $value = unserialize($redis->get($key));
        if (!is_array($value)) {
            $value = serialize(array('TIME'=>0, 'NAME'=>$cityName, 'SALESTATUS'=>''));
            $redis->setex($key, $ttl, $value);
        }
    }

    if ((strlen($cityName) != 0) && (preg_match('/(남아돌고|대폭락|대폭등|잘 팔려|잘팔리고|잘 팔리고|어서 오세요)/', $cityStatus) != 0)) {
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
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>GVOnline Quote Helper :: 시세 도우미</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container" style="padding-bottom: 10px">
    <h2>GVOnline Quote Helper :: 시세 도우미</h2>
</div>
<div class="container" style="padding-bottom: 10px">
    <div class="center-block">
        <p>이 프로그램은 게임화면을 분석하여 <strong class="text-success">도시명</strong>, <strong class="text-primary">교역소 주인의 대화</strong>, <strong class="text-danger">교역품명 및 매각시세(인근도시시세 포함)</strong> 정보를 수집합니다.</p>
        <p>수집은 상기 정보를 제외한 어떠한 내용도 수집하지 않습니다.</p>
        <p>과거 DHOAgent 와 유사한 동작을 하는 프로그램입니다.<br />다른 점은 이 프로그램은 자동으로 수집만 하며 실제 자료는 <a href="http://gvonline.ga/">이곳 Site</a> 에서 확인 하게 됩니다.<br />모바일로 확인할 수도 있으며, 프로그램이 없어도 확인은 가능합니다.</p>
        <p>일반 네비 프로그램 처럼 게임 실행 시 아래 파일을 다운로드 받아 실행하면 시세 데이터는 자동으로 수집됩니다.</p>
        <p>Windows 10 기준으로 제작되어 있으며 아직은 많은 기간 테스트가 필요합니다.</p>
        <p><br /></p>
    </div>
    <div class="center-block">
        <h4>1. 다운로드 화면</h4>
        <p><img src="/images/image001.jpg" style="border: 1px solid #000;" /></p>
        <p>아래 <a href="https://github.com/gvonline/gvo_quote/releases/latest" target="_blank"><strong class="text-warning">[프로그램 다운로드]</strong></a> 버튼을 클릭후 GitHub 사이트에서 최신버전을 다운로드 해 주시기 바랍니다.<br />
        <strong class="text-danger">(GitHub 사이트가 아닌 다른곳에서 받으셨다면 프로그램이 변조 되었을 가능성이 있으니 꼭 이곳에서 다운로드 하시기 바랍니다.)</strong></p>
        <p><br /></p>
    </div>
    <div class="center-block">
        <h4>2. 프로그램 동작 전 화면</h4>
        <img src="/images/image_20171103_01.png" />
        <p>네비게이션 프로그램 과 동일 한 방식으로 동작함으로 프로그램 실행후 게임을 진행 하시면 되겠습니다.<br />
        서버를 선택 하기 전에는 동작 하지 않습니다.<br />
        <strong class="text-danger" style="font-size: 200%;">서버 선택에 주의 해 주시기 바랍니다.</strong></p>
        <p><br /></p>
    </div>
    <div class="center-block">
        <h4>3. 수집기 동작 화면</h4>
        <img src="/images/image_20171103_02.png" />
        <p><strong class="text-success">매각</strong> 화면 및 <strong class="text-success">인근도시시세</strong> 화면을 읽어 자동으로 갱신 하며, 화면을 읽어 분석하는데 대략 1초 정도의 시간이 걸립니다.<br />
        수집된 결과는 프로그램내부 검색 또는 각 서버 페이지에서 확인 하도록 되어 있어 모바일 및 PC 에서 확인 하실 수 있습니다.<br />
        Clear 버튼을 누르면 서버를 변경 하거나 새로운 정보로 갱신 할 수 있습니다.</p>
        <p><br /></p>
    </div>
    <div class="center-block">
        <h4>4. 검색 동작 화면</h4>
        <img src="/images/image_20171103_03.png" />
        <p>필요한 도시 또는 교역품을 검색하여 결과를 볼 수 있습니다.<br />
        검색어는 , 로 구분 되며 여러개를 넣을 수 있습니다.<br />
        검색된 결과에서 도시명 또는 교역품 명을 더블클릭하여 바로 해당 내용을 확인 할 수 있습니다.<br />
        검색어를 입력 후 Enter 키를 누르면 바로 검색이 실행 됩니다.<br />
        검색된 결과에서 상단 분류명을 누르면 정순/역순 으로 정렬 할 수 있습니다.</p>
        <p><br /></p>
    </div>
    <div class="center-block">
        <h4>5. 즐겨찾기 편집 화면</h4>
        <img src="/images/image_20171103_04.png" />
        <p><strong>SearchShortcuts.txt</strong> 파일을 편집 하면 나만의 즐겨찾기를 만들 수 있습니다.<br />
        <strong class="text-success">즐겨찾기이름</strong> = <strong class="text-primary">도시명</strong> + <strong class="text-danger">교역품명</strong><br />
        위 양식으로 다양하게 구성 할 수 있습니다.<br />
        이미 등록되어있는 내용은 지워도 상관 없으니 마음껏 수정 하여 나만의 목록을 만들어 보시기 바랍니다.</p>
        <p><br /></p>
    </div>
    <div class="center-block">
        <h4>6. 즐겨찾기 동작 화면</h4>
        <img src="/images/image_20171103_05.png" />
        <p>즐겨찾기를 수정 후 프로그램을 다시 시작하면 해당 내용을 선택 하여 결과를 볼 수 있습니다.</p>
        <p><br /></p>
    </div>
    <div class="center-block">
        <br />
        <a class="btn btn-warning btn-lg btn-block" href="https://github.com/gvonline/gvo_quote/releases/latest" role="button" target="_blank">프로그램 다운로드</a>
        <br />
        <a class="btn btn-primary btn-lg btn-block" href="http://gvonline.ga" role="button">돌아가기</a>
        <br />
    </div>
</div>
</body>
</html>

