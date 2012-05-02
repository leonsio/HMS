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
echo"<pre>";
foreach($devlist as $device)
{
    $module = $device->getModule();
    $class = $module::init();
    echo $device->getName()."<br>";   
    print_r($class->getDeviceState($device)); 
}
echo "</pre>";
?>
			</div>
			<div class="content-secondary">

				<div data-role="collapsible" data-collapsed="true" data-theme="c"
					data-content-theme="c">

					<h3>Weitere R&auml;ume</h3>

					<ul data-role="listview" data-filter="true" data-inset="true"
						data-theme="c" data-dividertheme="d">
<?php
    foreach ($rooms as $floor_name => $floor)
    {
        // Stockwerk ausgeben
        echo '<li data-role="list-divider">' . $floor_name . '</li>';
        // Räume ausgeben
        foreach ($floor as $room_id => $room_name)
        {
            echo '<li data-theme="c" ><a href="' . BASE_URL . 'rooms/load/' . $room_id . '">' . utf8_encode($room_name) . '</a></li>';
        }
    }
?>


					</ul>
				</div>
			</div>