<?php

use Plib\View;

if (!isset($this)) {http_response_code(403); exit;}

/**
 * @var View $this
 * @var array<string,int> $totals
 * @var list<object{name:string,time:string}> $details
 */
?>

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
<?foreach ($totals as $filename => $count):?>
      <tr>
        <td><?=$filename?></td>
        <td><?=$count?></td>
      </tr>
<?endforeach?>
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
<?foreach ($details as $rec):?>
      <tr>
        <td><?=date('Y-m-d H:i:s', (int) $rec->time)?></td>
        <td><?=$rec->name?></td>
      </tr>
<?endforeach?>
    </tbody>
  </table>
</div>
<script>
  jQuery(function($) {
    $("#dlcounter_stats h4").click(function () {
      $(this).next().toggle();
    });
    $("#dlcounter_stats .tablesorter").tablesorter();
  });
</script>
