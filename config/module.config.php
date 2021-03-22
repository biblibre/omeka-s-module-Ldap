<?php

namespace Ldap;

return [
    'form_elements' => [
        'invokables' => [
            Form\ConfigForm::class => Form\ConfigForm::class,
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
];
