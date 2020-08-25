<?php
namespace Ldap\Authentication\Adapter;

use Doctrine\ORM\EntityManager;
use Omeka\Authentication\Adapter\PasswordAdapter;
use Omeka\Entity\User;
use Omeka\Entity\UserSetting;
use Omeka\Permissions\Acl;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Adapter\Ldap;
use Zend\Authentication\Result;
use Zend\Log\Logger;

class LdapAdapter extends AbstractAdapter
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $config;

    public function __construct(EntityManager $entityManager, Logger $logger, array $config = [])
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function authenticate()
    {
        $options = $this->config['adapter_options'] ?? [];
        $ldapAdapter = new Ldap($options, $this->identity, $this->credential);
        $result = $ldapAdapter->authenticate();
        if ($result->isValid()) {
            $query = $this->entityManager->createQuery('SELECT s FROM Omeka\Entity\UserSetting s WHERE s.id = :settingName AND s.value = :settingValue');
            $query->setParameter('settingName', 'ldap_identity');
            $query->setParameter('settingValue', json_encode($result->getIdentity()));
            $settings = $query->getResult();
            $setting = reset($settings);
            $user = $setting ? $setting->getUser() : null;
            if (!$user) {
                $user = new User();
                $user->setName($result->getIdentity());
                $user->setEmail('');
                $user->setRole(Acl::ROLE_RESEARCHER);
                $user->setIsActive(true);

                $userSetting = new UserSetting();
                $userSetting->setId('ldap_identity');
                $userSetting->setUser($user);
                $userSetting->setValue($result->getIdentity());

                $this->entityManager->persist($user);
                $this->entityManager->persist($userSetting);
                $this->entityManager->flush();
            }

            return new Result($result->getCode(), $user, $result->getMessages());
        }

        // LDAP authentication failed, log error message and try password authentication
        $messages = $result->getMessages();
        $this->logger->err(sprintf('Ldap: %s', $messages[1]));

        $passwordAdapter = new PasswordAdapter($this->entityManager->getRepository('Omeka\Entity\User'));
        $passwordAdapter->setIdentity($this->identity);
        $passwordAdapter->setCredential($this->credential);
        $result = $passwordAdapter->authenticate();

        return $result;
    }
}
