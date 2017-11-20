<form class="dlcounter" action="<?=$actionUrl?>" method="post">
    <input type="hidden" name="dlcounter" value="<?=$basename?>">
    <button class="dlcounter_button">
        <span><?=$this->text('label_download', $basename, $size)?></span>
        <span class="dlcounter_count"><?=$this->plural('label_dlcount', $times)?></span>
    </button>
</form>
