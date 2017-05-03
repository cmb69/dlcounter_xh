<form class="dlcounter" action="<?=$this->actionUrl()?>" method="post">
    <input type="hidden" name="dlcounter" value="<?=$this->basename()?>">
    <button class="dlcounter_button">
        <span><?=$this->text('label_download', $this->basename, $this->size)?></span>
        <span class="dlcounter_count"><?=$this->plural('label_dlcount', $this->times)?></span>
    </button>
</form>
