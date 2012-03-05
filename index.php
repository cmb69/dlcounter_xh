<?php

/**
 * Front-end functionality of Dlcounter_XH.
 * Copyright (c) 2012 Christoph M. Becker (see license.txt)
 */
 

// utf-8-marker: äöüß


if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


define('DLCOUNTER_VERSION', '1dev1');


/**
 * Returns wether a log entry for the download was appended.
 *
 * @param string $file  The name of the file.
 * @return bool
 */
function dlcounter_log($file) {
    global $pth, $adm;
    // TODO: configurable data folder
    $rec = $adm ? '' : time()."\t".basename($file)."\n";
    $fn = $pth['folder']['plugins'].'dlcounter/data/downloads.dat';
    if (($fh = fopen($fn, 'a')) !== FALSE && fwrite($fh, $rec) !== FALSE) {
	$ok = TRUE;
    } else {
	$ok = FALSE;
	e('cntwriteto', 'file', $fn);
    }
    if ($fh !== FALSE) {fclose($fh);}
    return $ok;
}


/**
 * Delivers the download.
 *
 * @param string $fn  The name of the file.
 * @return void
 */
function dlcounter_download($fn) {
    global $pth, $plugin_cf;
    
    $pcf = $plugin_cf['dlcounter'];
    
    $fn = $pth['folder']['base'].$pcf['folder_downloads'].basename($fn);
    if (is_readable($fn)) {
	if (dlcounter_log($fn)) {
	    header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename='.basename($fn));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header('Content-Length: '.filesize($fn));
	    if (ob_get_level()) {ob_end_clean();}
	    readfile($fn);
	    exit;
	}
    } else {
	shead('404');
    }
}


/**
 * Returns the download form view.
 *
 * @param string $fn  The name of the file.
 */
function dlcounter($fn) {
    global $sn, $su, $pth, $plugin_tx;
    
    //return '<a href="'.$sn.'?&dlcounter='.$fn.'">'.tag('img src="'.$pth['folder']['plugins'].'dlcounter/images/download-button.png"').'</a>';
    return '<form class="dlcounter" action="'.$sn.'?'.$su.'" method="post">'."\n"
	    .tag('input type="hidden" name="dlcounter" value="'.$fn.'"')
	    .tag('input type="image" src="'.$pth['folder']['plugins'].'dlcounter/images/download-button.png"'
		    .' alt="'.$plugin_tx['dlcounter']['label_download'].'"'
		    .' title="'.$fn.'"')
	    .'</form>'."\n";
}


/**
 * Handles the download request.
 */
//if (isset($_GET['dlcounter']) && $_GET['dlcounter'] !== 'true') {
//    dlcounter_download(stsl($_GET['dlcounter']));
//}
if (!empty($_POST['dlcounter'])) {
    dlcounter_download(stsl($_POST['dlcounter']));
}

?>
