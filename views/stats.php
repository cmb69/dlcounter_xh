<h1>Dlcounter – <?=$this->text('menu_main')?></h1>
<div id="dlcounter_stats">
    <h4><?=$this->text('label_totals')?></h4>
    <table id="dlcounter_summary_table" class="tablesorter">
        <thead>
            <tr>
                <th><?=$this->text('label_file')?></th>
                <th><?=$this->text('label_count')?></th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($totals as $filename => $count):?>
            <tr>
                <td><?=$filename?></td>
                <td><?=$count?></td>
            </tr>
<?php endforeach?>
        </tbody>
    </table>
    <h4><?=$this->text('label_individual')?></h4>
    <table id="dlcounter_details_table" class="tablesorter">
        <thead>
            <tr>
                <th><?=$this->text('label_date')?></th>
                <th><?=$this->text('label_file')?></th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($details as $rec):?>
            <tr>
                <td><?=date('Y-m-d H:i:s', (string) $rec->time)?></td>
                <td><?=$rec->name?></td>
            </tr>
<?php endforeach?>
        </tbody>
    </table>
</div>
