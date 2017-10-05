<?php if (count($result) != 0) : ?>
<div class="container">
    <table class="table table-bordered table-hover">
<?php foreach ($result as $row) : ?>
        <tr class="warning">
            <th><?php echo($row['NAME']); ?></th>
            <th><?php echo($row['SALESTATUS']); ?></th>
            <th><?php echo(getPassedTimeString($row['TIME'])); ?></th>
        </tr>
<?php foreach ($row['ITEMS'] as $item) : ?>
        <tr>
            <td><a href="<?php echo(sprintf('%s?type=item&name=%s&order=%s', $baseURL, urlencode($item['NAME']), urlencode($order))); ?>"><?php echo($item['NAME']); ?></a></td>
            <td><?php echo($item['SALEQUOTE'].'% '.getQuoteStatusString($item['SALESTATUS'])); ?></td>
            <td><?php echo(getPassedTimeString($item['TIME'])); ?></td>
        </tr>
<?php endforeach; ?>
<?php endforeach; ?>
    </table>
</div>
<?php endif; ?>