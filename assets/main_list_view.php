<?php if (count($result) != 0) : ?>
<div class="container">
    <table class="table table-bordered table-hover">
<?php foreach ($result as $index => $row) : ?>
        <tr class="<?php echo(($index%2 == 0) ? 'active' : ''); ?>">
<?php if (strlen($name) == 0) : ?>
            <th><a href="<?php echo(sprintf('%s?type=city&name=%s&order=%s', $baseURL, urlencode($row['NAME']), urlencode($order))); ?>"><?php echo(sprintf('%s (%s)', $row['NAME'],  $row['COUNT'])); ?></a></th>
<?php else : ?>
            <th><?php echo($row['NAME']); ?></th>
<?php endif; ?>
            <th><?php echo($row['SALESTATUS']); ?></th>
            <th><?php echo(getPassedTimeString($row['TIME'])); ?></th>
        </tr>
<?php endforeach; ?>
    </table>
</div>
<?php endif; ?>