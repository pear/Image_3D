<?php

set_time_limit(0);
ini_set('memory_limit', '24M');

require_once('Image/3D.php');

$world = new Image_3D();
$world->setColor(new Image_3D_Color(240, 240, 240));

$light1 = $world->createLight(-20, -20, -20);
$light1->setColor(new Image_3D_Color(100, 100, 255));

$light2 = $world->createLight(20, 20, -20);
$light2->setColor(new Image_3D_Color(255, 100, 100));

$text = $world->createObject('3ds', 'docs/examples/models/Image_3D.3ds');
$text->setColor(new Image_3D_Color(255, 255, 255));
$text->transform($world->createMatrix('Rotation', array(120, 10, 0)));
$text->transform($world->createMatrix('Scale', array(7, 7, 7)));

$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, false);
$world->setOption(Image_3D::IMAGE_3D_OPTION_FILLED, true);

$world->createRenderer('perspectively');
$world->createDriver('GD');
$world->render(600, 200, 'Image_3D_Object_3ds.png');

?>
