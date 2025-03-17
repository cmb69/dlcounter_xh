<?php

use Plib\View;

/**
 * @var View $this
 * @var string $version
 * @var list<array{class:string,label:string,stateLabel:string}> $checks
 */
?>

<h1>Dlcounter <?=$this->esc($version)?></h1>
<div>
  <h2><?=$this->text('synopsis_title')?></h2>
  <pre>{{{dlcounter('<?=$this->text('synopsis_filename')?>')}}}</pre>
</div>
<div>
  <h2><?php echo $this->text('syscheck_title')?></h2>
<?foreach ($checks as $check):?>
  <p class="<?php echo $this->esc($check['class'])?>"><?php echo $this->text('syscheck_message', $check['label'], $check['stateLabel'])?></p>
<?endforeach?>
</div>
