<?php 
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
?>
			<div class="content-secondary">

				<div data-theme="c"
					data-content-theme="c">

					<ul data-role="listview" data-filter="true" data-inset="true"
						data-theme="c" data-dividertheme="d">
						<?php 
						foreach($devs as $module => $value)
						{
						    echo '<li data-role="list-divider">'.$module.'</li>';	    
						    foreach($value as $dev)
						    {
						        echo '<li data-theme="c" ><a href="'.BASE_URL.'devices/load/'.$module.'/'.$dev->getID().'">'.$dev->getName().'</a></li>';
						    }  
						}
						?>


					</ul>
				</div>
			</div>
			<div class="content-primary">
                Ger&auml;te &Uuml;bersicht
			</div>