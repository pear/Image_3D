<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
// resize image according to this factor
$factor = 5;

$world = new Image_3D();
$world->setColor(new Image_3D_Color(255, 255, 255));

$light = $world->createLight('Light', array(-4 * $factor, -4 * $factor, 0));
$light->setColor(new Image_3D_Color(255, 255, 255, 100));
$lightSphere = $world->createObject('sphere', array('r' => $factor, 'detail' => 0));
$lightSphere->transform($world->createMatrix('Move', array(-4 * $factor, -4 * $factor, 0)));
$lightSphere->setColor(new Image_3D_Color(255, 255, 255, 100));

$light = $world->createLight('Light', array(4 * $factor, -4 * $factor, 0));
$light->setColor(new Image_3D_Color(255, 255, 255, 100));
$lightSphere = $world->createObject('sphere', array('r' => $factor, 'detail' => 0));
$lightSphere->transform($world->createMatrix('Move', array(4 * $factor, -4 * $factor, 0)));
$lightSphere->setColor(new Image_3D_Color(255, 255, 255, 100));

$p = array();
$bottom = $world->createObject('polygon', array(
    new Image_3D_Point(-5 * $factor, 3 * $factor, 5 * $factor),
    new Image_3D_Point(-5 * $factor, 3 * $factor, -5 * $factor),
    new Image_3D_Point(5 * $factor, 3 * $factor, -5 * $factor),
    new Image_3D_Point(5 * $factor, 3 * $factor, 5 * $factor),
));
$bottom->setColor(new Image_3D_Color(200, 200, 200, 0, .6));

$top = $world->createObject('polygon', array(
    new Image_3D_Point(-5 * $factor, 5 * $factor, 5 * $factor),
    new Image_3D_Point(5 * $factor, 5 * $factor, 5 * $factor),
    new Image_3D_Point(5 * $factor, -5 * $factor, 5 * $factor),
    new Image_3D_Point(-5 * $factor, -5 * $factor, 5 * $factor),
));
$top->setColor(new Image_3D_Color(200, 200, 200, 0, .5));

$redPlane = $world->createObject('polygon', array(
    new Image_3D_Point(-5 * $factor, 1 * $factor, 2 * $factor),
    new Image_3D_Point(-5 * $factor, 1 * $factor, -2 * $factor),
    new Image_3D_Point(-1 * $factor, -1 * $factor, -2 * $factor),
    new Image_3D_Point(-1 * $factor, -1 * $factor, 2 * $factor),
));
$redPlane->setColor(new Image_3D_Color(255, 0, 0, 100, 0));

$bluePlane = $world->createObject('polygon', array(
    new Image_3D_Point(5 * $factor, 1 * $factor, 2 * $factor),
    new Image_3D_Point(5 * $factor, 1 * $factor, -2 * $factor),
    new Image_3D_Point(1 * $factor, -1 * $factor, -2 * $factor),
    new Image_3D_Point(1 * $factor, -1 * $factor, 2 * $factor),
));
$bluePlane->setColor(new Image_3D_Color(100, 100, 255, 0, 0));

$world->transform($world->createMatrix('Rotation', array(10, 0, 0)));

if (!@$argv[1]) {
    // Create normal GD picture with projection
    echo "Render with projection.\n";
    
    $renderer = $world->createRenderer('perspectively');
    $driver = $world->createDriver('GD');
    $world->render(15 * $factor, 15 * $factor, 'Image_3D_No_Raytrace.png');
} else {
    // Raytrace advanced crazy picture
    echo "RAYTRACE!\n";

    $renderer = $world->createRenderer('Raytrace');

    // Define the cameras position
    $renderer->setCameraPosition(new Image_3D_Coordinate(0, 0, -50 * $factor));

    // define antialiasing level
    $renderer->setRaysPerPixel(2);

    // Set recursive scan depth
    $renderer->scanDepth(3);

    // Enable shadows
    $renderer->enableShadows(true);

    $world->render(15 * $factor, 15 * $factor, 'Image_3D_Raytrace.png');
}

echo $world->stats();

