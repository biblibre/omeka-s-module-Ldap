# Ldap module for Omeka S

Authentication with LDAP for Omeka S

## Installation

```
cd /path/to/omeka-s/modules
git clone https://github.com/biblibre/omeka-s-module-Ldap.git Ldap
cd Ldap
composer install --no-dev
```

## Configuration

LDAP servers configuration is done in Omeka S config/local.config.php

See https://docs.zendframework.com/zend-authentication/adapter/ldap/

Example:

```php
<?php
return [
    'ldap' => [
        'adapter_options' => [
            [
                'server1' => [
                    'host' => 'localhost',
                    'username' => 'cn=admin,dc=example,dc=com',
                    'password' => '*******',
                    'bindRequiresDn' => true,
                    'baseDn' => 'ou=People,dc=example,dc=com',
                    'accountFilterFormat' => '(&(objectClass=posixAccount)(uid=%s))',
                    'accountCanonicalForm' => 4,
                    'accountDomainName' => 'example.com',
                ],
            ],
        ],
    ],
];
```
