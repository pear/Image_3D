<?php

set_time_limit(0);
ini_set('memory_limit',	'32M');

require_once('Image/3D.php');

$renderer = array(
	'pers' 		=> 'Perspectively',
	'iso' 		=> 'Isometric',
);

$driver = array(
	'gd.png' 	=> 'GD',
	'svg' 		=> 'SVG',
	'png' 		=> 'ZBuffer',
	'txt'		=> 'ASCII',
);

$shading = array(
	'none' 		=> Image_3D_Renderer::SHADE_NO,
	'flat' 		=> Image_3D_Renderer::SHADE_FLAT,
	'gauroud' 	=> Image_3D_Renderer::SHADE_GAUROUD,
	'phong' 	=> Image_3D_Renderer::SHADE_PHONG,
);

$bf = array(
    'bfculling' => true, 
    'nobfc'     => false
);

foreach ($renderer as $r_id => $r_class) {
	foreach ($driver as $d_id => $d_class) {
		foreach ($shading as $s_type) {
			foreach ($bf as $b_name => $b_status) {
				$s_name = array_search($s_type, $shading);
				$filename = dirname(__FILE__) . "/tests/TestImage_{$r_id},{$s_name},{$b_name}.{$d_id}";

				echo "Render image ({$r_class}, {$d_class}, {$s_name}, {$b_name}) ... ";
				createExample($filename, $r_class, $d_class, $s_type, $b_status);
				echo "OK\n";
			}
		}
	}
}

function createExample($filename, $renderer, $driver, $shading, $bfCulling) {
	$world = new Image_3D();
	$world->setColor(new Image_3D_Color(255, 255, 255));
	
	$blueLight = $world->createLight(-500, -500, -500);
	$blueLight->setColor(new Image_3D_Color(100, 100, 255));
	
	$redLight = $world->createLight(-500, 500, -500);
	$redLight->setColor(new Image_3D_Color(255, 100, 100));
	
	// Cubeframe
	for ($x = -225; $x <= 225; $x += 50) {
		$cube = $world->createObject('cube', array(30, 30, 30));
		$cube->setColor(new Image_3D_Color(150, 150, 150));
		$cube->transform($world->createMatrix('Move', array($x, 225, 0)));

		$cube = $world->createObject('cube', array(30, 30, 30));
		$cube->setColor(new Image_3D_Color(150, 150, 150));
		$cube->transform($world->createMatrix('Move', array($x, -225, 0)));

		$cube = $world->createObject('cube', array(30, 30, 30));
		$cube->setColor(new Image_3D_Color(150, 150, 150));
		$cube->transform($world->createMatrix('Move', array($x, $x, 0)));
	}
	
	// Sphere
	$sphere = $world->createObject('sphere', array('r' => 50, 'detail' => 3));
	$sphere->setColor(new Image_3D_Color(200, 200, 200));
	$sphere->transform($world->createMatrix('Move', array(100, 0, 0)));
	
	// 3ds
	$threeds = $world->createObject('3ds', 'docs/examples/models/cube.3ds');
	$threeds->setColor(new Image_3D_Color(255, 255, 255));
	$threeds->transform($world->createMatrix('Rotation', array(90, 0, 0)));
	$threeds->transform($world->createMatrix('Scale', array(5, 5, 5)));
	$threeds->transform($world->createMatrix('Move', array(-100, 0, 0)));

	// text
	$text = $world->createObject('text', 'Text');
	$text->setColor(new Image_3D_Color(0, 255, 0));
	$text->transform($world->createMatrix('Scale', array(10, 10, 10)));
	$text->transform($world->createMatrix('Move', array(-150, 100, 0)));
	
	// Karte
	$map = $world->createObject('map');
	$detail = 10; $size = 100; $height = 40;
	
	$raster = 1 / $detail;
	for ($x = -1; $x <= 1; $x += $raster) {
		$row = array();
		for ($y = -1; $y <= 1; $y += $raster) {
			$row[] = new Image_3D_Point($x * $size, $y * $size, sin($x * pi()) * sin($y * 2 * pi()) * $height);
		}
		$map->addRow($row);
	}
	$map->setColor(new Image_3D_Color(150, 150, 150, 100));
	$map->transform($world->createMatrix('Rotation', array(120, -20, -10)));
	$map->transform($world->createMatrix('Move', array(150, -100, 0)));
	
	$world->setOption(Image_3D::IMAGE_3D_OPTION_BF_CULLING, $bfCulling);
	
	$renderer = $world->createRenderer($renderer);
	$driver = $world->createDriver($driver);

	if (!in_array($shading, $driver->getSupportedShading())) return false;
	
	$renderer->setShading($shading);
	$world->render(500, 500, $filename);
}

?>
