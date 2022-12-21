<?php

namespace Ldap\Test\Authentication\Adapter;

use Laminas\Authentication\Result;
use Ldap\Authentication\Adapter\LdapAdapter;
use Ldap\Test\TestCase;

class LdapAdapterTest extends TestCase
{
    protected $adapter;

    public function setUp(): void
    {
        parent::setUp();

        $services = $this->getServiceLocator();
        $entityManager = $services->get('Omeka\EntityManager');
        $logger = $services->get('Omeka\Logger');
        $settings = $services->get('Omeka\Settings');
        $eventManager = $services->get('EventManager');

        $settings->set('ldap_email_attribute', 'mail');
        $settings->set('ldap_name_attribute', 'sn');

        $users = [
            'foo' => [
                'mail' => 'foo@example.com',
                'sn' => 'Foo',
            ],
        ];

        $ldapAdapter = $this->getMockBuilder(\Laminas\Authentication\Adapter\Ldap::class)
                            ->setMethods(['authenticate', 'getAccountObject'])
                            ->getMock();

        $ldapAdapter->method('authenticate')
                    ->willReturnCallback(function () use ($ldapAdapter, $users) {
                        $identity = $ldapAdapter->getIdentity();
                        if (isset($users[$identity])) {
                            return new Result(Result::SUCCESS, $identity);
                        }
                        return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, $identity, ['first message', 'second message']);
                    });

        $ldapAdapter->method('getAccountObject')
                    ->willReturnCallback(function () use ($ldapAdapter, $users) {
                        $identity = $ldapAdapter->getIdentity();
                        if (isset($users[$identity])) {
                            return (object) $users[$identity];
                        }
                        return false;
                    });

        $this->adapter = new LdapAdapter($entityManager, $logger, $settings, $eventManager, $ldapAdapter);
    }

    public function testSuccessfulLogin()
    {
        $this->adapter->setIdentity('foo');
        $this->adapter->setCredential('password');
        $result = $this->adapter->authenticate();

        $this->assertTrue($result->isValid());

        $user = $result->getIdentity();
        $this->assertInstanceOf(\Omeka\Entity\User::class, $user);

        $this->assertEquals('foo@example.com', $user->getEmail());
        $this->assertEquals('Foo', $user->getName());
        $this->assertTrue($user->isActive());
    }

    public function testFailedLogin()
    {
        $this->adapter->setIdentity('bar');
        $this->adapter->setCredential('password');
        $result = $this->adapter->authenticate();

        $this->assertFalse($result->isValid());

        $user = $result->getIdentity();
        $this->assertNull($user);

        $messages = $result->getMessages();
        $this->assertCount(1, $messages);
        $this->assertEquals('User not found.', $messages[0]);
    }
}
