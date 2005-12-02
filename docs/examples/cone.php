<?php

set_time_limit(0);
require_once('Image/3D.php');

$world = new Image_3D();
$world->setColor(new Image_3D_Color(255, 255, 255));

$light = $world->createLight('Light', array(-200, -200, -200));
$light->setColor(new Image_3D_Color(0, 0, 255));

$light2 = $world->createLight('Light', array(200, -200, -200));
$light2->setColor(new Image_3D_Color(255, 0, 0));

$cone = $world->createObject('cone', array('detail' => 20));
$cone->setColor(new Image_3D_Color(255, 255, 255, 200));

$cone->transform($world->createMatrix('Scale', array(100, 400, 100)));
$cone->transform($world->createMatrix('Move', array(0, -80, 0)));
$cone->transform($world->createMatrix('Rotation', array(150, 30, 30)));

$world->createRenderer('perspectively');
$world->createDriver('GD');
$world->render(400, 400, 'Image_3D_Object_Cone.png');

echo $world->stats( );

