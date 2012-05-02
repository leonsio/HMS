<?php
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
// Liste besorgen
$file = trim(file_get_contents('http://data.iana.org/TLD/tlds-alpha-by-domain.txt'));
// Liste erstellen und bereinigen
$list = explode("\n", $file);
unset($list[0]);
file_put_contents('./tld_list', serialize($list));
?>