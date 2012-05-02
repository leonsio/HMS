<?php
$url = "http://www.google.com/ig/api?hl=de&weather=";
$plz = "61352";

// Load Data
$data = file_get_contents($url . $plz);
$xml = iconv("iso-8859-1", "utf-8", $data);
$xml = simplexml_load_string($xml);

// Parse data
$information = $xml->xpath("/xml_api_reply/weather/forecast_information");
$current = $xml->xpath("/xml_api_reply/weather/current_conditions");
$forecast_list = $xml->xpath("/xml_api_reply/weather/forecast_conditions");
?>

<!-- 
http://demo.joomshaper.com/extensions/free-extensions/weather-module.html
-->
<div style="width:100%;height:100px;">
	<div style="float:left;width:100px">
		<img 
			src="<?= BASE_URL.str_replace(array('/ig/images/weather/', 'gif'), array('/templates/basic/images/weather/','png'), $current[0]->icon['data'])?>"
			title="<?= $current[0]->condition['data']?>"
			alt="<?= $current[0]->condition['data']?>"> <br />
		    <p ><?= $current[0]->temp_c['data'] ?>&deg;C</p>

	</div>
	<div style="float: left;">
		<h2 ><?= print $information[0]->city['data']; ?></h2>

		<div ><?= $current[0]->condition['data']?></div>

		<div ><?= $current[0]->humidity['data']?></div>
	</div>
</div>

<br/><br/>
<div>
<? foreach ($forecast_list as $forecast) : ?>
<div class="weather">
	<div style="float: left; width: 25%">
		<span><?= $forecast->day_of_week['data']; ?></span> <br /> <span><img
			src="<?= BASE_URL.str_replace(array('/ig/images/weather/', 'gif'), array('/templates/basic/images/weather/icons/','png'), $forecast->icon['data'])?>"
			alt="weather"></span> <br /> <span><?= $forecast->low['data'] ?>&deg;C&nbsp;|&nbsp;<?= $forecast->high['data'] ?>&deg; C</span>
		<br />
	</div>
</div>
<? endforeach ?>
</div>