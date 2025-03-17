<?php

use Plib\View;

/**
 * @var View $this
 * @var string $actionUrl
 * @var string $basename
 * @var string $size
 * @var int $times
 */
?>

<form class="dlcounter" action="<?=$this->esc($actionUrl)?>" method="post">
    <input type="hidden" name="dlcounter" value="<?=$this->esc($basename)?>">
    <button class="dlcounter_button">
        <span><?=$this->text('label_download', $basename, $size)?></span>
        <span class="dlcounter_count"><?=$this->plural('label_dlcount', $times)?></span>
    </button>
</form>
