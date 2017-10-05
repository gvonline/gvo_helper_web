<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo($title); ?></title>
<script src="https://code.jquery.com/jquery-3.2.1.slim.js" integrity="sha256-tA8y0XqiwnpwmOIl3SGAcFl2RvxHjA8qp0+1uCGmRmg=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style type="text/css">
.tooltip-inner {white-space: pre; text-align: left; max-width: none;}
</style>
</head>
<body>
<div class="container" style="padding-bottom: 10px">
    <h2><?php echo($title); ?></h2>
</div>
<div class="container" style="padding-bottom: 10px">
    <form action="<?php echo($baseURL); ?>" method="get" class="form-inline">
        <div class="form-group">
            <label class="radio-inline"><input type="radio" name="type" value="city"<?php echo(($type == 'city') ? 'checked' : '') ?>>도시명</label>
            <label class="radio-inline"><input type="radio" name="type" value="item"<?php echo(($type == 'item') ? 'checked' : '') ?>>교역품명</label>
            <input type="text" name="name" class="form-control" value="<?php echo($name); ?>" placeholder="검색어, 검색어, ...">
            <button type="submit" class="btn btn-primary">검색</button>
            <a class="btn btn-warning" href="<?php echo($orderURL); ?>" role="button"><?php echo($orderMessage); ?></a>
            <a class="btn btn-default" href="<?php echo($baseURL); ?>" role="button">초기화</a>
            <span style="padding: 0px 10px">&nbsp;</span>
            <a class="btn btn-info text-right" href="http://gvonline.ga" role="button">홈으로</a>
        </div>
    </form>
</div>
