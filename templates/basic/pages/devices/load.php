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
    echo "<pre>";
    $module = $device->getModule();
    $class = $module::init();
    echo $device->getName()."<br>";   
    print_r($class->getDeviceState($device)); 

echo "</pre>";
?>
			</div>
			<div class="content-secondary">

				<div data-theme="c" data-role="collapsible" data-collapsed="true"
					data-content-theme="c">
					
					<h3>Weitere Ger&auml;te</h3>

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
