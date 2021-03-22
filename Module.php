<?php declare(strict_types=1);

namespace Ldap;

use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\MvcEvent;
use Laminas\View\Renderer\PhpRenderer;
use Ldap\Form\ConfigForm;
use Omeka\Module\AbstractModule;

class Module extends AbstractModule
{
    public function onBootstrap(MvcEvent $event): void
    {
        parent::onBootstrap($event);

        require_once __DIR__ . '/vendor/autoload.php';
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
        ]);
        return $renderer->formCollection($form, false);
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
        return true;
    }
}
