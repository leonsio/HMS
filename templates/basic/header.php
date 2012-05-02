<?php 
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" class="ui-mobile" >

<head>
<title><?php $this->page_title(); ?></title>

<base href='<?php echo BASE_URL; ?>' /> 

<!-- Stylesheets + Favicon: -->
<link rel="stylesheet" href="./templates/basic/css/jquery.mobile-1.1.0.min.css" />
<link rel="stylesheet" href="./templates/basic/css/main.css" type="text/css" />
<!--  <link rel="shortcut icon" href="./images/favicon.ico" /> -->
<!-- /Stylesheets + Favicon: -->

<!-- JavaScript: -->
<script type="text/javascript" src="./templates/basic/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="./templates/basic/js/jquery.mobile-1.1.0.min.js"></script>
<script type="text/javascript" src="./templates/basic/js/functions.js"></script>
<!-- Flot Charts -->
<script type="text/javascript" src="./templates/basic/js/jquery.flot.min.js"></script>
<script type="text/javascript" src="./templates/basic/js/jquery.flot.resize.min.js"></script>
<script type="text/javascript" src="./templates/basic/js/jquery.flot.threshold.min.js"></script>
<script type="text/javascript" src="./templates/basic/js/jquery.flot.selection.min.js"></script> 
<!-- /JavaScript -->
 
<!-- Meta: -->
<meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1">
<!-- WebApp Support -->
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style"  content="black" />
<link rel="canonical" href="<?php echo BASE_URL; ?>" /> 
<!-- iOS Addons
<link rel="apple-touch-icon" href="apple-touch-icon-57x57.png" />
<link rel="apple-touch-icon" sizes="72x72" href="apple-touch-icon-72x72.png" />
<link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-114x114.png" />
<link rel="apple-touch-startup-image" href="apple-touch-startup-image-320x460.png" />
<link rel="apple-touch-startup-image" sizes="768x1004" href="apple-touch-startup-image-768x1004.png" />
-->
<!-- /Meta -->
</head>

<body>
<div class="type-interior" data-role="page" data-theme="c" data-add-back-btn="true" >
	<div data-role="header" data-theme="c"  data-position="fixed">
		<h1><?php $this->get_header(); ?></h1>
		<?php $this->get_header_menu(); ?>
	</div>
	<div data-role="content" role="main">
		<?php $this->get_msg(); ?>
