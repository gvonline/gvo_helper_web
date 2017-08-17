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
    else {
        return sprintf('%s분 %s초 전', floor($passedTime / 60), $passedTime % 60);
    }
}

function getQuoteStatusString($status=0) {
    $passedTime = time() - $time;
    if ($status < 0) {
        return '하락세';
    }
    elseif ($status > 0) {
        return '상승세';
    }
    else {
        return '통상';
    }
}

$redis = new Redis();
$redis->connect('127.0.0.1','6379', 1, NULL, 100);
$citys = $redis->keys('도시:*');
asort($citys);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>GVOnline Helper :: 에이레네 서버</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="padding-bottom: 10px">
    <h2>GVOnline Helper :: 에이레네 서버</h2>
</div>
<div class="container" style="padding-bottom: 10px">
    <form action="" method="get" class="form-inline">
        <div class="form-group">
            <label class="radio-inline"><input type="radio" name="type" value="city" checked>도시명검색</label>
            <label class="radio-inline"><input type="radio" name="type" value="item">물품명검색</label>
            <input type="text" name="name" class="form-control" placeholder="검색기능 준비중" disabled="disabled">
            <button type="submit" class="btn btn-primary" disabled="disabled">검색</button>
        </div>
    </form>
</div>
<div class="container">
    <table class="table table-bordered table-hover">
<?php foreach ($citys as $cityKey) : $city = unserialize($redis->get($cityKey));?>
        <tr class="warning">
            <th><?php echo($city['NAME']); ?></th>
            <th><?php echo($city['STATUS']); ?></th>
            <th><?php echo(getPassedTimeString($city['TIME'])); ?></th>
        </tr>
<?php
$items = $redis->keys($city['NAME'].':*');
asort($items);
foreach ($items as $itemKey) : $item = unserialize($redis->get($itemKey));?>
        <tr>
            <td><?php echo($item['NAME']); ?></td>
            <td><?php echo($item['QUOTE'].' '.getQuoteStatusString($item['STATUS'])); ?></td>
            <td><?php echo(getPassedTimeString($item['TIME'])); ?></td>
        </tr>
<?php endforeach; ?>
<?php endforeach; ?>
    </table>
</div>
</body>
</html>
<?php $redis->close(); ?>