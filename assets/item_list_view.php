<?php if (count($result) != 0) : ?>
<div class="container">
    <table class="table table-bordered table-hover">
<?php foreach ($result as $row) : ?>
        <tr class="warning">
            <th colspan="4"><?php echo(sprintf('%s&nbsp;(%s)&nbsp;&nbsp;%s', $row['NAME'], $row['TYPE'], getSaleCitiesString($row['SALE']))); ?></th>
        </tr>
<?php foreach ($row['CITYS'] as $city) : ?>
        <tr>
            <td>
                <a href="<?php echo(sprintf('%s?type=city&name=%s&order=%s', $baseURL, urlencode($city['DETAIL']['NAME']), urlencode($order))); ?>"><?php echo($city['DETAIL']['NAME']); ?></a>
                <?php echo(getResistStatusString($city['DETAIL']['NAME'], $row['RESIST'])); ?>
            </td>
            <td><?php echo($city['SALEQUOTE'].'% '.getQuoteStatusString($city['SALESTATUS'])); ?></td>
            <td><?php echo(getPassedTimeString($city['TIME'])); ?></td>
            <td><?php
            echo(
                (array_key_exists('TIME', $city['DETAIL']) &&  strlen($city['DETAIL']['TIME'] > 0))
                ? sprintf('%s&nbsp;(%s)', $city['DETAIL']['SALESTATUS'], getPassedTimeString($city['DETAIL']['TIME']))
                : '&nbsp;'
            ); ?></td>
        </tr>
<?php endforeach; ?>
<?php endforeach; ?>
    </table>
</div>
<?php endif; ?>