<?php

/**
 * Back-end functionality of Dlcounter_XH.
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */

// utf-8-marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Returns the content of the downloads database.
 *
 * @return array
 */
function dlcounter_read_db() {
    global $pth;
    static $data = NULL;
    
    if (!isset($data)) {
	$data = array();
	$fn = $pth['folder']['plugins'].'dlcounter/data/downloads.dat';
	$lines = file($fn);
	if ($lines !== FALSE) {
	    foreach ($lines as $line) {
		list($ts, $fn) = explode("\t", $line);
		$data[] = array(trim($ts), trim($fn));
	    }
	} else {
	    e('cntopen', 'file', $fn);
	}
    }
    return $data;
}


/**
 * Outputs the JS to initialize the tablesorter to <head>.
 *
 * @global string $hjs
 * @return void
 */
function dlcounter_include_js() {
    global $pth, $hjs;
    
    include_once $pth['folder']['plugins'].'jquery/jquery.inc.php';
    include_jQuery();
    include_jQueryPlugin('tablesorter', $pth['folder']['plugins'].'dlcounter/lib/jquery.tablesorter.js');
    $hjs .= <<<SCRIPT
<script type="text/javascript">
/* <![CDATA[ */
jQuery(function() {
    jQuery('table.tablesorter').tablesorter()
})
/* ]]> */
</script>

SCRIPT;
}


/**
 * Returns the plugin version information view.
 *
 * @return string  The (X)HTML.
 */
function dlcounter_version()
{
    global $pth;
    
    return '<h1>Dlcounter_XH</h1>'
	. tag('img class="dlcounter_plugin_icon" src="'
	    . $pth['folder']['plugins'] . 'dlcounter/dlcounter.png" width="128"'
	    . ' height="128" alt="Plugin Icon"')
	. '<p>Version: ' . DLCOUNTER_VERSION . '</p>'
	. '<p>Copyright &copy; 2012 <a href="http://3-magi.net">Christoph M. Becker</a></p>'
	. '<p class="dlcounter_license">This program is free software: you can redistribute it and/or modify'
	. ' it under the terms of the GNU General Public License as published by'
	. ' the Free Software Foundation, either version 3 of the License, or'
	. ' (at your option) any later version.</p>'
	. '<p class="dlcounter_license">This program is distributed in the hope that it will be useful,'
	. ' but WITHOUT ANY WARRANTY; without even the implied warranty of'
	. ' MERCHAN&shy;TABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the'
	. ' GNU General Public License for more details.</p>'
	. '<p class="dlcounter_license">You should have received a copy of the GNU General Public License'
	. ' along with this program.  If not, see'
	. ' <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>';
}


/**
 * Returns the requirements information view.
 *
 * @return string  The (X)HTML.
 */
function dlcounter_system_check() { // RELEASE-TODO
    global $pth, $tx, $plugin_tx, $plugin_cf;

    define('DLCOUNTER_PHP_VERSION', '4.2.0');
    $ptx = $plugin_tx['dlcounter'];
    $imgdir = $pth['folder']['plugins'].'dlcounter/images/';
    $ok = tag('img src="'.$imgdir.'ok.png" alt="ok"');
    $warn = tag('img src="'.$imgdir.'warn.png" alt="warning"');
    $fail = tag('img src="'.$imgdir.'fail.png" alt="failure"');
    $htm = '<h4>'.$ptx['syscheck_title'].'</h4>'
	    .(version_compare(PHP_VERSION, DLCOUNTER_PHP_VERSION) >= 0 ? $ok : $fail)
	    .'&nbsp;&nbsp;'.sprintf($ptx['syscheck_phpversion'], DLCOUNTER_PHP_VERSION)
	    .tag('br').tag('br')."\n";
    foreach (array('date') as $ext) {
	$htm .= (extension_loaded($ext) ? $ok : $fail)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_extension'], $ext).tag('br')."\n";
    }
    $htm .= tag('br').(strtoupper($tx['meta']['codepage']) == 'UTF-8' ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_encoding'].tag('br')."\n";
    $htm .= (!get_magic_quotes_runtime() ? $ok : $warn)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_magic_quotes'].tag('br')."\n";
    $htm .= (file_exists($pth['folder']['plugins'].'jquery/jquery.inc.php') ? $ok : $fail)
	    .'&nbsp;&nbsp;'.$ptx['syscheck_jquery'].tag('br').tag('br')."\n";
    foreach (array('config/', 'css/', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'].'dlcounter/'.$folder;
    }
    //$folders[] = advfrm_data_folder();
    foreach ($folders as $folder) {
	$htm .= (is_writable($folder) ? $ok : $warn)
		.'&nbsp;&nbsp;'.sprintf($ptx['syscheck_writable'], $folder).tag('br')."\n";
    }
    return $htm;
}


/**
 * Returns the statistics view.
 *
 * @return string  The (X)HTML.
 */
function dlcounter_admin_main() {
    global $plugin_tx;
    
    $ptx = $plugin_tx['dlcounter'];
    dlcounter_include_js();
    $data = dlcounter_read_db();
    
    $o = '<div id="dlcounter-stats">'."\n";
    
    $totals = array_count_values(array_map(create_function('$elt', 'return $elt[1];'), $data));
    $o .= '<h4 onclick="jQuery(this).next().toggle()">'.$ptx['label_totals'].'</h4>'."\n"
	    .'<table class="tablesorter">'."\n".'<thead>'."\n".'<tr>'
	    .'<th>File</th><th>Count</th>'
	    .'</tr>'."\n".'</thead>'."\n".'<tbody>'."\n";
    foreach ($totals as $file => $count) {
	$o .= '<tr><td>'.$file.'</td><td>'.$count.'</td></tr>'."\n";
    }
    $o .= '</tbody>'."\n".'</table>'."\n";
    
    $o .= '<h4 onclick="jQuery(this).next().toggle()">'.$ptx['label_individual'].'</h4>'."\n"
	    .'<table class="tablesorter">'."\n".'<thead>'."\n".'<tr>'
	    .'<th>Date</th><th>File</th>'
	    .'</tr>'."\n".'</thead>'."\n".'<tbody>'."\n";
    foreach ($data as $rec) {
	$o .= '<tr><td>'.date($ptx['format_date'], $rec[0]).'</td><td>'.$rec[1].'</td></tr>'."\n";
    }
    $o .= '</tbody>'."\n".'</table>'."\n";
    
    $o .= '</div>'."\n";
    
    return $o;
}


/**
 * Handles the plugin's administration.
 */
if (isset($dlcounter) && $dlcounter === 'true') {
    initvar('admin');
    initvar('action');
    
    $o .= print_plugin_admin('on');
    
    switch ($admin) {
	case '':
	    $o .= dlcounter_version().tag('hr').dlcounter_system_check();
	    break;
	case 'plugin_main':
	    $o .= dlcounter_admin_main();
	    break;
	default:
	    $o .= plugin_admin_common($action, $admin, $plugin);
    }
    
}

?>
