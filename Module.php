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
        $forms = $this->getServiceLocator()->get('FormElementManager');
        $settings = $this->getServiceLocator()->get('Omeka\Settings');
        $form = $forms->get(ConfigForm::class);
        $form->setData([
            'role' => $settings->get('ldap_role'),
        ]);

        return $renderer->render('ldap/config-form', ['form' => $form]);
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $params = $controller->params()->fromPost();
        $controller->settings()->set('ldap_role', $params['role']);
    }
}
