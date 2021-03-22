Ldap module for Omeka S
=======================

This tool is an LDAP authentication module for [Omeka S].


Installation
------------

The module requires the php extension "[php-ldap]" to be installed on the server.
Furthermore, it requires the version 7.3 or higher for php.

The module uses an external library [laminas/ldap], so use the release zip to
install it, or use and init the source.

* From the zip

Download the last release [Ldap.zip] from the list of releases (the master does
not contain the dependency), and uncompress it in the `modules` directory.

* From the source and for development:

If the module was installed from the source, rename the name of the folder of
the module to `Ldap`, and go to the root module, and run:

```sh
cd /path/to/omeka-s/modules
git clone https://github.com/biblibre/omeka-s-module-Ldap.git Ldap
cd Ldap
composer install --no-dev
```

Then install it like any other Omeka module.


Configuration
-------------

LDAP servers configuration should be done in Omeka S main config file "config/local.config.php".

See https://docs.zendframework.com/zend-authentication/adapter/ldap/

Example:

```php
<?php
return [
    'ldap' => [
        'adapter_options' => [
            'server1' => [
                'host' => 'localhost',
                'username' => 'CN=admin,DC=example,DC=com',
                'password' => '*******',
                'bindRequiresDn' => true,
                'baseDn' => 'OU=People,DC=example,DC=com',
                'accountFilterFormat' => '(&(objectClass=posixAccount)(uid=%s))',
                'accountCanonicalForm' => 4,
                'accountDomainName' => 'example.com',
            ],
        ],
    ],
];
```


Usage
-----

When a user is authenticated, a user is created inside the database with the
role defined in the config form. Once created, an administrator can update the
role.


Development
-----------

When a user is created during the first connection, the events `ldap.user.create.pre`
and `ldap.user.create.post` are triggered so the user can be updated by another
module before flushing.

The ldap user data are cached in user settings (`ldap_identity`).


TODO
----

- [ ] Manage the mapping of roles.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online [issues].


License
-------

This module is published under [GNU/GPL] v3 or later.

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


Copyright
---------

* Copyright BibLibre, 2020 (see [BibLibre])
* Copyright Daniel Berthereau, 2021 (see [Daniel-KM])


[Omeka S]: https://omeka.org/s
[php-ldap]: https://www.php.net/manual/en/book.ldap.php
[laminas/ldap]: https://docs.laminas.dev/laminas-ldap/
[Ldap.zip]: https://gitlab.com/biblibre/Omeka-S-module-Ldap/releases
[issues]: https://github.com/biblibre/Omeka-S-module-Ldap/issues
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[BibLibre]: https://gitlab.com/biblibre
[Daniel-KM]: https://gitlab.com/Daniel-KM "Daniel Berthereau"
