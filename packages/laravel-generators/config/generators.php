<?php

declare(strict_types=1);

return [
    'namespaces' => [
        'controller' => 'Transport\\Http\\Controller',
        'model' => 'Domain\\Model',
        'request' => 'Transport\\Http\\Request',
        'resource' => 'Transport\\Http\\Resource',
        'provider' => 'Infrastructure\\Provider',
        'exception' => 'Domain\\Exception',
        'enum' => 'Domain\\Enum',
        'command' => 'Infrastructure\\Console\\Command',
        'service' => 'Application\\Service',
        'action' => 'Application\\Action',
        'doc' => 'Transport\\Http\\Doc',
    ],
];
