<?php

set_time_limit(0);
ini_set('memory_limit', '24M');
set_include_path(realpath(dirname(__FILE__) . '/../..'));
require_once('Image/3D.php');

$world = new Image_3D();
$world->setColor(new Image_3D_Color(255, 255, 255));

$light1 = $world->createLight(-100, -100, -100);
$light1->setColor(new Image_3D_Color(255, 0, 0));

//$light2 = $world->createLight(100, -200, 0);
//$light2->setColor(new Image_3D_Color(0, 255, 0));

$p1 = $world->createObject('cube', array(80, 80, 80));
$p1->setColor(new Image_3D_Color(200, 200, 200));
$p1->transform($world->createMatrix('Rotation', array(45, 45, 0)));

$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, false);
$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);

$rotation = $world->createMatrix('Rotation', array(0, 5, 0));
$renderer = $world->createRenderer('perspectively');
$driver = $world->createDriver('ASCII');

while (1) {
	$world->transform($rotation);
	$driver->reset();
	$world->render(2 * 80, 6 * 30, 'php://stdout');
}

?>
