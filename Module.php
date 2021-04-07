<?php

namespace Ldap;

use Ldap\Form\ConfigForm;
use Omeka\Module\AbstractModule;
use Omeka\Module\Exception\ModuleCannotInstallException;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\ServiceManager\ServiceLocatorInterface;

class Module extends AbstractModule
{
    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        require_once __DIR__ . '/vendor/autoload.php';
    }

    public function install(ServiceLocatorInterface $serviceLocator)
    {
        if (!extension_loaded('ldap')) {
            throw new ModuleCannotInstallException('This module requires PHP ldap extension, which is not loaded');
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getConfigForm(PhpRenderer $renderer)
    {
        $services = $this->getServiceLocator();
        $settings = $services->get('Omeka\Settings');
        $form = $services->get('FormElementManager')->get(ConfigForm::class);
        $form->init();
        $form->setData([
            'ldap_role' => $settings->get('ldap_role'),
            'ldap_email_attribute' => $settings->get('ldap_email_attribute'),
            'ldap_name_attribute' => $settings->get('ldap_name_attribute'),
        ]);

        $output = $renderer->formCollection($form, false)
            . sprintf('<div><strong>%s:</strong> %s</div>',
                $renderer->translate('Important'),
                $renderer->translate('Configuration of LDAP servers should be done in the main configuration file. See README.md'));

        return $output;
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $services = $this->getServiceLocator();
        $settings = $services->get('Omeka\Settings');
        $form = $services->get('FormElementManager')->get(ConfigForm::class);
        $form->init();
        $form->setData($controller->params()->fromPost());
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }

        $formData = $form->getData();
        $settings->set('ldap_role', $formData['ldap_role']);
        $settings->set('ldap_email_attribute', $formData['ldap_email_attribute']);
        $settings->set('ldap_name_attribute', $formData['ldap_name_attribute']);

        return true;
    }
}
