<?php

namespace Ldap\Test;

use Omeka\Test\AbstractHttpControllerTestCase;

abstract class TestCase extends AbstractHttpControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setupHostname();
        $this->loginAsAdmin();
    }

    protected function getServiceLocator()
    {
        return $this->getApplication()->getServiceManager();
    }

    protected function api()
    {
        return $this->getServiceLocator()->get('Omeka\ApiManager');
    }

    protected function settings()
    {
        return $this->getServiceLocator()->get('Omeka\Settings');
    }

    protected function getEntityManager()
    {
        return $this->getServiceLocator()->get('Omeka\EntityManager');
    }

    protected function login($email, $password)
    {
        $serviceLocator = $this->getServiceLocator();
        $auth = $serviceLocator->get('Omeka\AuthenticationService');
        $adapter = $auth->getAdapter();
        $adapter->setIdentity($email);
        $adapter->setCredential($password);
        return $auth->authenticate();
    }

    protected function loginAsAdmin()
    {
        $this->login('admin@example.com', 'root');
    }

    protected function logout()
    {
        $serviceLocator = $this->getServiceLocator();
        $auth = $serviceLocator->get('Omeka\AuthenticationService');
        $auth->clearIdentity();
    }

    protected function setupHostname()
    {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $viewHelperManager->get('BasePath')->setBasePath('/');
        $serverUrlHelper = $viewHelperManager->get('ServerUrl');
        $serverUrlHelper->setHost('localhost');
        $_SERVER['SERVER_NAME'] = 'localhost';
    }
}
