<h1>Dlcounter</h1>
<img class="dlcounter_logo" src="<?=$logo?>" alt="<?=$this->text('alt_logo')?>">
<p>Version: <?=$version?></p>
<p>Copyright &copy; 2012-2017 Christoph M. Becker</p>
<p class="dlcounter_license">
    This program is free software: you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the Free
    Software Foundation, either version 3 of the License, or (at your option)
    any later version.
</p>
<p class="dlcounter_license">
    This program is distributed in the hope that it will be useful, but
    <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
    the GNU General Public License for more details.
</p>
<p class="dlcounter_license">
    You should have received a copy of the GNU General Public License along with
    this program. If not, see <a
    href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
<div>
    <h2><?=$this->text('synopsis_title')?></h2>
    <pre>{{{dlcounter('<?=$this->text('synopsis_filename')?>')}}}</pre>
</div>
<div class="dlcounter_syscheck">
    <h2><?php echo $this->text('syscheck_title')?></h2>
<?php foreach ($checks as $check):?>
    <p class="xh_<?php echo $check->getState()?>"><?php echo $this->text('syscheck_message', $check->getLabel(), $check->getStateLabel())?></p>
<?php endforeach?>
</div>
