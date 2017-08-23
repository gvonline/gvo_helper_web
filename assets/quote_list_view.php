<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo($title); ?></title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container" style="padding-bottom: 10px">
    <h2><?php echo($title); ?></h2>
</div>
<div class="container" style="padding-bottom: 10px">
    <form action="<?php echo($baseURL); ?>" method="get" class="form-inline">
        <div class="form-group">
<?php if ($type == 'item') : ?>
            <label class="radio-inline"><input type="radio" name="type" value="city">도시명검색</label>
            <label class="radio-inline"><input type="radio" name="type" value="item" checked>교역품명검색</label>
<?php else : ?>
            <label class="radio-inline"><input type="radio" name="type" value="city" checked>도시명검색</label>
            <label class="radio-inline"><input type="radio" name="type" value="item">교역품명검색</label>
<?php endif; ?>
            <input type="text" name="name" class="form-control" value="<?php echo($name); ?>" placeholder="검색어">
            <button type="submit" class="btn btn-primary">검색</button>
            <a class="btn btn-default" href="<?php echo($baseURL); ?>" role="button">초기화</a>
            <a class="btn btn-info" href="http://gvonline.ga" role="button">홈으로</a>
        </div>
    </form>
</div>
<?php if (count($result) != 0) : ?>
<div class="container">
    <table class="table table-bordered table-hover">
<?php if ($type == 'item') : ?>
        <tr class="warning">
            <th colspan="3"><?php echo($name); ?></th>
        </tr>
<?php foreach ($result as $item) : ?>
        <tr>
            <td><a href="<?php echo(sprintf('%s?type=city&name=%s', $baseURL, urlencode($item['CITY']))); ?>"><?php echo($item['CITY']); ?></a></td>
            <td><?php echo($item['SALEQUOTE'].'% '.getQuoteStatusString($item['SALESTATUS'])); ?></td>
            <td><?php echo(getPassedTimeString($item['TIME'])); ?></td>
        </tr>
<?php endforeach; ?>
<?php elseif ($type == 'city') : ?>
<?php foreach ($result as $row) : ?>
        <tr class="warning">
<?php if (strlen($name) == 0) : ?>
            <th><a href="<?php echo(sprintf('%s?type=city&name=%s', $baseURL, urlencode($row['NAME']))); ?>"><?php echo($row['NAME']); ?></a></th>
<?php else : ?>
            <th><?php echo($row['NAME']); ?></th>
<?php endif; ?>
            <th><?php echo($row['SALESTATUS']); ?></th>
            <th><?php echo(getPassedTimeString($row['TIME'])); ?></th>
        </tr>
<?php foreach ($row['ITEMS'] as $item) : ?>
        <tr>
            <td><a href="<?php echo(sprintf('%s?type=item&name=%s', $baseURL, urlencode($item['NAME']))); ?>"><?php echo($item['NAME']); ?></a></td>
            <td><?php echo($item['SALEQUOTE'].'% '.getQuoteStatusString($item['SALESTATUS'])); ?></td>
            <td><?php echo(getPassedTimeString($item['TIME'])); ?></td>
        </tr>
<?php endforeach; ?>
<?php endforeach; ?>
<?php else : ?>
<?php foreach ($result as $index => $row) : ?>
        <tr class="<?php echo(($index%2 == 0) ? 'active' : ''); ?>">
<?php if (strlen($name) == 0) : ?>
            <th><a href="<?php echo(sprintf('%s?type=city&name=%s', $baseURL, urlencode($row['NAME']))); ?>"><?php echo(sprintf('%s (%s)', $row['NAME'],  $row['COUNT'])); ?></a></th>
<?php else : ?>
            <th><?php echo($row['NAME']); ?></th>
<?php endif; ?>
            <th><?php echo($row['SALESTATUS']); ?></th>
            <th><?php echo(getPassedTimeString($row['TIME'])); ?></th>
        </tr>
<?php endforeach; ?>
<?php endif; ?>
    </table>
</div>
<?php endif; ?>
</body>
</html>
