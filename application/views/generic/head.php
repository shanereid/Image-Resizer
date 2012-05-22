<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html<?=isset($htmlClass)? ' class="'.$htmlClass.'"' : ''?>>
	<head>
		<title><?=isset($pageTitle)? $pageTitle : 'Image Resizer'?></title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<?php 
		    if(isset($javascripts) && is_array($javascripts)) {
		        foreach ($javascripts as $javascript) {
		            echo '<script type="text/javascript" src="'.$javascript.'"></script>';
		        }
		    }
		?>
		
		<?php 
		    if(isset($stylesheets) && is_array($stylesheets)) {
		        foreach ($stylesheets as $stylesheet) {
		            echo '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
		        }
		    }
		?>
		<link rel="stylesheet" href="assets/css/general.css?v=2" type="text/css" />
		<!--[if IE]>
		    <link rel="stylesheet" type="text/css" href="assets/css/general-ie.css?v=2" />
		<![endif]-->
		<?php if(!isset($_GET['ie7'])): ?>
		<!--[if IE 7]>
		    <link rel="stylesheet" type="text/css" href="assets/css/general-ie7.css" />
		<![endif]-->
		<?php else: ?>
		<link rel="stylesheet" type="text/css" href="assets/css/general-ie7.css" />
		<?php endif; ?>
		<script src="assets/js/general.js" type="text/javascript"></script>
	</head>
	<body>