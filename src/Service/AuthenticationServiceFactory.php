<?php
namespace Ldap\Service;

use Interop\Container\ContainerInterface;
use Ldap\Authentication\Adapter\LdapAdapter;
use Omeka\Authentication\Adapter\KeyAdapter;
use Omeka\Authentication\Storage\DoctrineWrapper;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Adapter\Callback;
use Laminas\Authentication\Adapter\Ldap;
use Laminas\Authentication\Storage\NonPersistent;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create the authentication service.
     *
     * @return AuthenticationService
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $config = $serviceLocator->get('Config');
        $entityManager = $serviceLocator->get('Omeka\EntityManager');
        $logger = $serviceLocator->get('Omeka\Logger');
        $status = $serviceLocator->get('Omeka\Status');
        $settings = $serviceLocator->get('Omeka\Settings');
        $eventManager = $serviceLocator->get('EventManager');

        // Skip auth retrieval entirely if we're installing or migrating.
        if (!$status->isInstalled() ||
            ($status->needsVersionUpdate() && $status->needsMigration())
        ) {
            $storage = new NonPersistent;
            $adapter = new Callback(function () {
                return null;
            });
        } else {
            $userRepository = $entityManager->getRepository('Omeka\Entity\User');
            if ($status->isApiRequest()) {
                // Authenticate using key for API requests.
                $keyRepository = $entityManager->getRepository('Omeka\Entity\ApiKey');
                $storage = new DoctrineWrapper(new NonPersistent, $userRepository);
                $adapter = new KeyAdapter($keyRepository, $entityManager);
            } else {
                // Authenticate using user/password for all other requests.
                $storage = new DoctrineWrapper(new Session, $userRepository);
                $ldapAdapter = new Ldap($config['ldap']['adapter_options']);
                $adapter = new LdapAdapter($entityManager, $logger, $settings, $eventManager, $ldapAdapter);
            }
        }

        $authService = new AuthenticationService($storage, $adapter);
        return $authService;
    }
}
