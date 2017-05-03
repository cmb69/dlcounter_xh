<form class="dlcounter" action="<?=$this->actionUrl()?>" method="post">
    <input type="hidden" name="dlcounter" value="<?=$this->basename()?>">
    <button class="dlcounter_button"><?=$this->text('label_download', $this->basename, $this->size)?></button>
</form>
