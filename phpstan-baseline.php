<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\Config\\\\MakerConfig\\:\\:dtoProperties\\(\\) should return array\\<array\\{fieldName\\: string, type\\: string, nullable\\: bool\\}\\> but returns array\\<string, string\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Config/MakerConfig.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AddAttributeBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AddAttributeBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/AddAttributeBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AddEventsToEntityBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AddEventsToEntityBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/VO/Builder/AddEventsToEntityBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\CollectionBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\CollectionBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/CollectionBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\CommandBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\CommandBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/CommandBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ControllerBuilder\\:\\:buildSimple\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ControllerBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/ControllerBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ControllerBuilder\\:\\:buildWithForm\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ControllerBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/ControllerBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\DtoBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\DtoBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/DtoBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\EventBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\EventBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/EventBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ExceptionBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ExceptionBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/ExceptionBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\FromTemplateBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\FromTemplateBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/FromTemplateBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\InterfaceBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\InterfaceBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/InterfaceBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ListenerBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ListenerBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/ListenerBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\LoggerBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\LoggerBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/LoggerBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ServiceCommandBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ServiceCommandBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/ServiceCommandBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ServiceQueryBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\ServiceQueryBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/ServiceQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\TraitForEntityBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\TraitForEntityBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/TraitForEntityBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\TraitForObjectBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\TraitForObjectBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/TraitForObjectBuilder.php',
];
$ignoreErrors[] = [
	// identifier: offsetAccess.invalidOffset
	'message' => '#^Invalid array key type array\\<string, string\\>\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/src/VO/Builder/VoForEntityBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\VoForEntityBuilder\\:\\:build\\(\\) should return static\\(Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\VoForEntityBuilder\\) but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/VoForEntityBuilder.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\VoForObjectBuilder\\:\\:build\\(\\) should return Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\VoForObjectBuilder but returns Atournayre\\\\Bundle\\\\MakerBundle\\\\VO\\\\Builder\\\\AbstractBuilder\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/VO/Builder/VoForObjectBuilder.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
