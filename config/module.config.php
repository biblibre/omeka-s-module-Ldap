<?php declare(strict_types=1);

namespace Ldap;

return [
    'form_elements' => [
        'invokables' => [
            'Omeka\Form\LoginForm' => Form\LoginForm::class,
        ],
    ],
    'ldap' => [
        // This should be configured in Omeka config/local.config.php
        // See https://docs.zendframework.com/zend-authentication/adapter/ldap/
        'adapter_options' => [
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Omeka\AuthenticationService' => Service\AuthenticationServiceFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
];
