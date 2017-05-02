<h1>Dlcounter – <?=$this->text('menu_main')?></h1>
<div id="dlcounter_stats">
    <h4 onclick="jQuery(this).next().toggle()"><?=$this->text('label_totals')?></h4>
    <table id="dlcounter_summary_table" class="tablesorter">
        <thead>
            <tr>
                <th><?=$this->text('label_file')?></th>
                <th><?=$this->text('label_count')?></th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($this->totals as $filename => $count):?>
            <tr>
                <td><?=$this->escape($filename)?></td>
                <td><?=$this->escape($count)?></td>
            </tr>
<?php endforeach?>
        </tbody>
    </table>
    <h4 onclick="jQuery(this).next().toggle()"><?=$this->text('label_individual')?></h4>
    <table id="dlcounter_details_table" class="tablesorter">
        <thead>
            <tr>
                <th><?=$this->text('label_date')?></th>
                <th><?=$this->text('label_file')?></th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($this->details as $rec):?>
            <tr>
                <td><?=date('Y-m-d H:i:s', $rec[0])?></td>
                <td><?=$this->escape($rec[1])?></td>
            </tr>
<?php endforeach?>
        </tbody>
    </table>
</div>
