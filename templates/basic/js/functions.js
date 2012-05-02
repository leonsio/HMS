 var IsiPhone = navigator.userAgent.indexOf("iPhone") != -1 ;
 var IsiPod = navigator.userAgent.indexOf("iPod") != -1 ;
 var IsiPad = navigator.userAgent.indexOf("iPad") != -1 ;

$(document).ready(function() {        
	// Enter necessary functions here
});

$(document).bind("mobileinit", function(){
	  $.mobile.touchOverflowEnabled = true;
	});