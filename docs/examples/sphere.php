<?php

set_time_limit(0);

require_once('Image/3D.php');

$world = new Image_3D();
$world->setColor(new Image_3D_Color(255, 255, 255));

$light = $world->createLight(-20, -20, -20);
$light->setColor(new Image_3D_Color(255, 255, 255));

$sphere = $world->createObject('sphere', array('r' => 150, 'detail' => 4));
$sphere->setColor(new Image_3D_Color(0, 0, 255));

$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, false);
$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);

$world->createRenderer('perspectively');
$world->createDriver('GD');
$world->render(400, 400, 'Image_3D_Object_Sphere.png');

?>
