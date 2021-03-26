# Ldap module for Omeka S

Authentication with LDAP for Omeka S

## Installation

The module requires the php extension
[php-ldap](https://www.php.net/manual/en/book.ldap.php)
to be installed on the server.

### From the zip file

Download the zip file from the
[latest release](https://github.com/biblibre/omeka-s-module-Ldap/releases/latest)
and unzip it into the `modules` folder

### From source

```
cd /path/to/omeka-s/modules
git clone https://github.com/biblibre/omeka-s-module-Ldap.git Ldap
cd Ldap
composer install --no-dev
```

## Configuration

LDAP servers configuration should be done in Omeka S main configuration file
(`config/local.config.php`)

See https://docs.zendframework.com/zend-authentication/adapter/ldap/

Example:

```php
<?php
return [
    'ldap' => [
        'adapter_options' => [
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
];
```

## Usage

When a user is authenticated, a user is created inside the database with the
role defined in the config form. Once created, an administrator can update the
role.

## Development

When a user is created during the first connection, the events
`ldap.user.create.pre` and `ldap.user.create.post` are triggered so the user
can be updated by another module before or after being saved into the database.

The ldap user identity is stored in a user setting (`ldap_identity`).

## License

This module is published under the GNU General Public License (version 3 or later).
