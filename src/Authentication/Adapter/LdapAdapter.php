<?php declare(strict_types=1);
namespace Ldap\Authentication\Adapter;

use Doctrine\ORM\EntityManager;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Adapter\Ldap;
use Laminas\Authentication\Result;
use Laminas\EventManager\EventManager;
use Laminas\Log\Logger;
use Omeka\Authentication\Adapter\PasswordAdapter;
use Omeka\Entity\User;
use Omeka\Entity\UserSetting;
use Omeka\Permissions\Acl;
use Omeka\Settings\Settings;

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

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @var EventManager
     */
    protected $eventManager;

    public function __construct(EntityManager $entityManager, Logger $logger, Settings $settings, EventManager $eventManager, array $config = [])
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->config = $config;
        $this->settings = $settings;
        $this->eventManager = $eventManager;
    }

    public function authenticate()
    {
        $options = $this->config['adapter_options'] ?? [];
        $ldapAdapter = new Ldap($options, $this->identity, $this->credential);
        $result = $ldapAdapter->authenticate();
        if ($result->isValid()) {
            $identity = $result->getIdentity();
            $query = $this->entityManager->createQuery('SELECT s FROM Omeka\Entity\UserSetting s WHERE s.id = :settingName AND s.value = :settingValue');
            $query->setParameter('settingName', 'ldap_identity');
            $query->setParameter('settingValue', json_encode($identity));
            $settings = $query->getResult();
            $setting = reset($settings);
            $user = $setting ? $setting->getUser() : null;
            if (!$user) {
                $userRepository = $this->entityManager->getRepository('Omeka\Entity\User');
                $user = $userRepository->findOneBy(['email' => $identity]);
                if (!$user) {
                    $user = new User();
                    $user->setName($identity);
                    $user->setEmail($identity);
                    $user->setRole($this->settings->get('ldap_role', Acl::ROLE_RESEARCHER));
                    $user->setIsActive(true);

                    $this->eventManager->setIdentifiers([self::class]);
                    $this->eventManager->trigger('ldap.user.create.pre', $user);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    $this->eventManager->trigger('ldap.user.create.post', $user);
                }

                $userSetting = new UserSetting();
                $userSetting->setId('ldap_identity');
                $userSetting->setUser($user);
                $userSetting->setValue($identity);

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
