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
	<div data-role="navbar" data-inset="false">
		<ul>
			<li><a href="#" data-theme="c" class="ui-btn-active">Temperatur</a></li>
			<li><a href="#" data-theme="c">Feuchtigkeit</a></li>
		</ul>
	</div>
	<!-- /navbar -->
	<h1>Temperatur Wetterstation KS300</h1>

	<div id="placeholder"
		style="margin-right: 10px; width: 100%; height: 300px;"></div>
	<div id="overview"
		style="margin: 20px auto 10px auto; width: 90%; height: 50px"></div>
	<script>
$(function () {
    var d = [<?=implode(", ", $temp); ?>];
    // first correct the timestamps - they are recorded as the daily
    // midnights in UTC+0100, but Flot always displays dates in UTC
    // so we have to add one hour to hit the midnights in the plot
    for (var i = 0; i < d.length; ++i)
      d[i][0] += 60 * 60 * 1000;

    // helper for returning the weekends in a period
    function weekendAreas(axes) {
        var markings = [];
        var d = new Date(axes.xaxis.min);
        // go to the first Saturday
        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7))
        d.setUTCSeconds(0);
        d.setUTCMinutes(0);
        d.setUTCHours(0);
        var i = d.getTime();
        do {
            // when we don't set yaxis, the rectangle automatically
            // extends to infinity upwards and downwards
            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
            i += 7 * 24 * 60 * 60 * 1000;
        } while (i < axes.xaxis.max);

        return markings;
    }
    
    var options = {
        lines: { show: true, fill: true, fillColor: "rgba(255, 255, 255, 0.8)" },  	
    	xaxis: { mode: "time", tickLength: 5 },
        yaxis: { tickFormatter: function (v) { return v + " &deg;C"; } },
        selection: { mode: "x" },
        threshold: { below: 0, color: "rgb(200, 20, 30)" },
        grid: { markings: weekendAreas }
    };
    
    var plot = $.plot($("#placeholder"), [d], options);

    
    var overview = $.plot($("#overview"), [d], {
        series: {
            lines: { show: true, lineWidth: 1 },
            shadowSize: 0
        },
        xaxis: { ticks: [], mode: "time"},
        yaxis: { ticks: [], autoscaleMargin: 0.1 },
        grid: { color: "#999" },
        selection: { mode: "x" }
    });


    // now connect the two   
    $("#placeholder").bind("plotselected", function (event, ranges) {
        // do the zooming
        plot = $.plot($("#placeholder"), [d],
                      $.extend(true, {}, options, {
                          xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
                      }));

        // don't fire event on the overview to prevent eternal loop
        overview.setSelection(ranges, true);
    });
    
    $("#overview").bind("plotselected", function (event, ranges) {
        plot.setSelection(ranges);
    });
});
</script>

</div>
<div class="content-secondary">

	<div data-theme="c" data-role="collapsible" data-collapsed="true"
		data-content-theme="c">

		<h3>Weitere Graphen</h3>

		<ul data-role="listview" data-filter="true" data-inset="true"
			data-theme="c" data-dividertheme="d">
			<li data-theme="c"><a href="'#">Wetterstation Balkon</a></li>
		</ul>
	</div>
</div>
