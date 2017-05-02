<form class="dlcounter" action="<?=$this->actionUrl()?>" method="post">
    <input type="hidden" name="dlcounter" value="<?=$this->basename()?>">
    <button>
        <img src="<?=$this->downloadImage()?>" alt="<?=$this->text('label_download')?>"
             title="<?=$this->basename()?> – <?=$this->size()?>">
    </button>
</form>
