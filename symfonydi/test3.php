<?php 
require_once '../testclasses.php';

function __autoload($className)
{
	$className = ltrim($className, '\\');
	$fileName  = '';
	$namespace = '';
	if ($lastNsPos = strrpos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	require $fileName;
}

$file = './container_test6.php';

if (file_exists($file)) {
	require_once $file;
	$container = new ProjectServiceContainer();
} else {
	$container = new Symfony\Component\DependencyInjection\ContainerBuilder;

	$classes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
	for ($i = 0; $i < count($classes); $i++) {
		if (isset($classes[$i-1])) {
			$ref = [new Symfony\Component\DependencyInjection\Reference($classes[$i-1])];
		}
		else $ref = [];

		$definition = new Symfony\Component\DependencyInjection\Definition($classes[$i], $ref );
		$definition->setScope('prototype');
		$container->setDefinition($classes[$i], $definition);
	}

	$container->compile();

	$dumper = new Symfony\Component\DependencyInjection\Dumper\PhpDumper($container);
	file_put_contents($file, $dumper->dump());
}

//Trigger autoloader
$a = $container->get('J');
unset($a);

$t1 = microtime(true);

for ($i = 0; $i < 10000; $i++) {
	$a = $container->get('J');
}

$t2 = microtime(true);

$results = [
	'time' => $t2 - $t1,
	'files' => count(get_included_files()),
	'memory' => memory_get_peak_usage()/1024/1024
];

echo json_encode($results);