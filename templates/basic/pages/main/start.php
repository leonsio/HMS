<?php 
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
?>
<div class="content-primary">
<?php 
include_once APP_ROOT."modules/widgets/weather/weather.php";
?>
</div>

<div class="content-secondary">
				<div data-theme="c" data-role="collapsible" data-collapsed="true"
					data-content-theme="c">
					
					<h3>Weitere Graphen</h3>

					<ul data-role="listview" data-filter="true" data-inset="true"
						data-theme="c" data-dividertheme="d">
                           <li data-theme="c" ><a href="'#">Aktuelles Wetter</a></li>
                          <li data-theme="c" ><a href="'#">Benzinpreise</a></li>
                          <li data-theme="c" ><a href="'#">Fenster und Türen</a></li>
                          <li data-theme="c" ><a href="'#">System Zustand</a></li> 
                          <li data-theme="c" ><a href="'#">irgendwas...</a></li> 
					</ul>
				</div>
</div>
