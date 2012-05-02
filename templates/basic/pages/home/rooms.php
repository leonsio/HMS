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
	<div data-theme="c" data-content-theme="c">
		<ul data-role="listview" data-mini="true" data-filter="true" data-inset="true" data-theme="c" data-dividertheme="d">
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
<div class="content-primary">
	<h1>H1 Heading</h1>
	<h2>H2 Heading</h2>
	<h3>H3 Heading</h3>
	<h4>H4 Heading</h4>
	<h5>H5 Heading</h5>
	<h6>H6 Heading</h6>
</div>