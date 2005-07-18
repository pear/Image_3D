#!/usr/bin/php
<?php

//    $make = 1;
	require_once('PEAR/PackageFileManager.php');

	$pkg = new PEAR_PackageFileManager;

	// directory that PEAR CVS is located in
	$cvsdir  = '/home/dotxp/dev/';
	$packagedir = $cvsdir . 'Image_3D/trunk/';
	
	// Filemanager settings
	$category = 'Image';
	$package = 'Image_3D';
	
	$version = '0.2.0';
	$state = 'alpha';
	
	$summary = 'This class allows the rendering of 3 dimensional images using PHP and ext/GD.';
	$description = <<<EOT
Image_3D is a highly object oriented PHP5 package 
that allows the creation of 3 dimensional images
using PHP and the GD extension, which is bundled
with PHP.

Image_3D currently supports:
* Creation of 3D objects like cubes, spheres, maps, text,...
* Own object definitions possible
* Own material definitions
* Import of 3DSMax files
* Unlimited number of light sources
* Saving all output formats supported by GD
EOT;

	$notes = <<<EOT
* New renderer interface, added a standard and an isometric renderer.
* Driver based output format, supports now GD and SVG.
* New shading API, supports modes "none", "flat" and "gauround" by now.
* Highly refactored.
EOT;
	
	$e = $pkg->setOptions(
		array('simpleoutput'      => true,
		      'baseinstalldir'    => '',
		      'summary'           => $summary,
		      'description'       => $description,
		      'version'           => $version,
              'license'           => 'LGPL',
	          'packagedirectory'  => $packagedir,
	          'pathtopackagefile' => $packagedir,
              'state'             => $state,
              'filelistgenerator' => 'file',
              'notes'             => $notes,
			  'package'           => $package,
			  'dir_roles' => array(
                    'docs' => 'doc'),
		      'ignore' => array('*.xml',
                                '*.tgz',
		                        'generate_package*',
                                ),
	));
	
	if (PEAR::isError($e)) {
    	echo $e->getMessage();
    	exit;
	}
	
	$e = $pkg->addMaintainer('toby', 'lead', 'Tobias Schlitt', 'toby@php.net');
	$e = $pkg->addMaintainer('kore', 'lead', 'Kore Nordmann', 'pear@kore-nordmann.de');
	
	if (PEAR::isError($e)) {
    	echo $e->getMessage();
    	exit;
	}

    $e = $pkg->addDependency('gd', null, 'has', 'ext');
    $e = $pkg->addDependency('php', '5.0.0', 'ge', 'php');

    $e = $pkg->addGlobalReplacement('package-info', '@package_version@', 'version');
    $e = $pkg->addGlobalReplacement('pear-config', '@data_dir@', 'data_dir');

	if (PEAR::isError($e)) {
    	echo $e->getMessage();
    	exit;
	}
	// hack until they get their shit in line with docroot role
	$pkg->addRole('tpl', 'php');
	$pkg->addRole('png', 'php');
	$pkg->addRole('gif', 'php');
	$pkg->addRole('jpg', 'php');
	$pkg->addRole('css', 'php');
	$pkg->addRole('js', 'php');
	$pkg->addRole('ini', 'php');
	$pkg->addRole('inc', 'php');
	$pkg->addRole('afm', 'php');
	$pkg->addRole('pkg', 'doc');
	$pkg->addRole('cls', 'doc');
	$pkg->addRole('proc', 'doc');
	$pkg->addRole('sh', 'doc');
	
	if (isset($make)) {
    	$e = $pkg->writePackageFile();
	} else {
    	$e = $pkg->debugPackageFile();
	}
	
	if (PEAR::isError($e)) {
    	echo $e->getMessage();
	}
/*	
	if (!isset($make)) {
    	echo '<a href="' . $_SERVER['PHP_SELF'] . '?make=1">Make this file</a>';
	}
*/
?>
