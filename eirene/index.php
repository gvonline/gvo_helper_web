<?php
function getPassedTimeString($time=0) {
    if ($time == 0) {
        return '';
    }
    $passedTime = time() - $time;
    if ($passedTime < 0) {
        return '';
    }
    elseif ($passedTime < 60) {
        return sprintf('%s초 전', $passedTime);
    }
    elseif ($passedTime < 3600) {
        return sprintf('%s분 %s초 전', floor($passedTime / 60), $passedTime % 60);
    }
    else {
        return sprintf('%s시간 전', floor($passedTime / 3600));
    }
}

function getQuoteStatusString($status=0) {
    if ($status < 0) {
        return '<i class="fa fa-play text-primary fa-rotate-90" aria-hidden="true"></i>';
    }
    elseif ($status > 0) {
        return '<i class="fa fa-play text-danger fa-rotate-270" aria-hidden="true"></i>';
    }
    else {
        return ''; //'<i class="fa fa-play text-success" aria-hidden="true"></i>';
    }
}

function compareCity($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['CITY'], $b['CITY']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

function compareItem($a, $b) {
    if ($a['SALEQUOTE'] == $b['SALEQUOTE']) {
        return strcmp($a['NAME'], $b['NAME']);
    }
    return ($a['SALEQUOTE'] > $b['SALEQUOTE']) ? -1 : 1;
}

$redis = new Redis();
$redis->connect('127.0.0.1','6379', 1, NULL, 100);
$result = array();
if (isset($_GET['type']) && ($_GET['type'] == 'city')) {
    $searchKey =  sprintf('도시:%s', trim($_GET['name']));
    $listKeys = $redis->keys($searchKey);
    asort($listKeys);
    $i = 0;
    foreach ($listKeys as $cityKey) {
        $city = unserialize($redis->get($cityKey));
        $result[$i] = $city;
        $result[$i]['ITEMS'] = array();
        $items = $redis->keys($city['NAME'].':*');
        foreach ($items as $itemKey) {
            $result[$i]['ITEMS'][] = unserialize($redis->get($itemKey));
        }
        usort($result[$i]['ITEMS'], 'compareItem');
        $i++;
    }
}
else if (isset($_GET['type']) && ($_GET['type'] == 'item')) {
    $searchKey =  sprintf('*:%s', trim($_GET['name']));
    $listKeys = $redis->keys($searchKey);
    $i = 0;
    foreach ($listKeys as $itemKey) {
        $result[$i] = unserialize($redis->get($itemKey));
        $result[$i]['CITY'] = str_replace(':'.$result[$i]['NAME'], '', $itemKey);
        $i++;
    }
    usort($result, 'compareCity');
}
else {
    $listKeys = $redis->keys('도시:*');
    asort($listKeys);
    $i = 0;
    foreach ($listKeys as $cityKey) {
        $city = unserialize($redis->get($cityKey));
        $result[$i] = $city;
        $result[$i]['ITEMS'] = array();
        $items = $redis->keys($city['NAME'].':*');
        asort($items);
        foreach ($items as $itemKey) {
            $result[$i]['ITEMS'][] = unserialize($redis->get($itemKey));
        }
        $i++;
    }
}
$redis->close();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>GVOnline Helper :: 에이레네 서버</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="padding-bottom: 10px">
    <h2>GVOnline Helper :: 에이레네 서버</h2>
</div>
<div class="container" style="padding-bottom: 10px">
    <form action="/" method="get" class="form-inline">
        <div class="form-group">
<?php if (!isset($_GET['type']) || ($_GET['type'] == 'city')) : ?>
            <label class="radio-inline"><input type="radio" name="type" value="city" checked>도시명검색</label>
            <label class="radio-inline"><input type="radio" name="type" value="item">교역품명검색</label>
<?php else : ?>
            <label class="radio-inline"><input type="radio" name="type" value="city">도시명검색</label>
            <label class="radio-inline"><input type="radio" name="type" value="item" checked>교역품명검색</label>
<?php endif; ?>
<?php if (!isset($_GET['name'])) : ?>
            <input type="text" name="name" class="form-control" placeholder="검색어">
<?php else : ?>
            <input type="text" name="name" class="form-control" value="<?php echo($_GET['name']); ?>" placeholder="검색어">
<?php endif; ?>
            <button type="submit" class="btn btn-primary">검색</button>
            <a class="btn btn-default" href="http://eirene.gvonline.ga/" role="button">초기화</a>
            <a class="btn btn-info" href="http://gvonline.ga" role="button">홈으로</a>
        </div>
    </form>
</div>
<?php if (count($result) != 0) : ?>
<div class="container">
    <table class="table table-bordered table-hover">
<?php if (!isset($_GET['type']) || ($_GET['type'] == 'city')) : ?>
<?php foreach ($result as $row) : ?>
        <tr class="warning">
<?php if (!isset($_GET['name'])) : ?>
            <th><a href="/?type=city&name=<?php echo(urlencode($row['NAME'])); ?>"><?php echo($row['NAME']); ?></a></th>
<?php else : ?>
            <th><?php echo($row['NAME']); ?></th>
<?php endif; ?>
            <th><?php echo($row['SALESTATUS']); ?></th>
            <th><?php echo(getPassedTimeString($row['TIME'])); ?></th>
        </tr>
<?php foreach ($row['ITEMS'] as $item) : ?>
        <tr>
            <td><a href="/?type=item&name=<?php echo(urlencode($item['NAME'])); ?>"><?php echo($item['NAME']); ?></a></td>
            <td><?php echo($item['SALEQUOTE'].'% '.getQuoteStatusString($item['SALESTATUS'])); ?></td>
            <td><?php echo(getPassedTimeString($item['TIME'])); ?></td>
        </tr>
<?php endforeach; ?>
<?php endforeach; ?>
<?php else : ?>
        <tr class="warning">
            <th colspan="3"><?php echo($_GET['name']); ?></th>
        </tr>
<?php foreach ($result as $item) : ?>
        <tr>
            <td><a href="/?type=city&name=<?php echo(urlencode($item['CITY'])); ?>"><?php echo($item['CITY']); ?></a></td>
            <td><?php echo($item['SALEQUOTE'].'% '.getQuoteStatusString($item['SALESTATUS'])); ?></td>
            <td><?php echo(getPassedTimeString($item['TIME'])); ?></td>
        </tr>
<?php endforeach; ?>
<?php endif; ?>
    </table>
</div>
<?php endif; ?>
</body>
</html>
