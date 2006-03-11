<?php

set_time_limit(0);
require_once('Image/3D.php');

$world = new Image_3D();
$world->setColor(new Image_3D_Color(255, 255, 255));

$light = $world->createLight('Light', array(-2000, -2000, -2000));
$light->setColor(new Image_3D_Color(255, 255, 255));

$redLight = $world->createLight('Light', array(90, 0, 50));
$redLight->setColor(new Image_3D_Color(255, 0, 0));

$sphere = $world->createObject('bezier', array( 'x_detail' => 50, 
                                                'y_detail' => 15,
                                                'points' => array(
        array(  array(-100, -100, 0),
                array(0, -100, -100),
                array(100, -100, -30),
            ),
        array(  array(-100, 0, 70),
                array(0, 0, 0),
                array(100, 0, 100),
            ),
        array(  array(-100, 100, -200),
                array(0, 100, 30),
                array(100, 100, 20),
            ),
    )));
$sphere->setColor(new Image_3D_Color(150, 150, 150));

$renderer = $world->createRenderer('perspectively');

$world->createDriver('GD');
$world->render(400, 400, 'example.png');

echo $world->stats();

