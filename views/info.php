<h1>Dlcounter – <?=$this->text('info_title')?></h1>
<h4><?=$this->text('synopsis_title')?></h4>
<pre>{{{PLUGIN:dlcounter('<?=$this->text('synopsis_filename')?>');}}}</pre>
<div class="dlcounter_syscheck">
    <h4><?php echo $this->text('syscheck_title')?></h4>
<?php foreach ($this->checks as $check):?>
    <p class="xh_<?php echo $this->escape($check->state)?>"><?php echo $this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?php endforeach?>
</div>
<h4><?=$this->text('info_about')?></h4>
<img class="dlcounter_plugin_icon" src="<?=$this->logo()?>" width="128" height="128" alt="Plugin Icon">
<p>Version: <?=$this->version()?></p>
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
