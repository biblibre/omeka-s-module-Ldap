<?php declare(strict_types=1);

namespace Ldap\Form;

use Laminas\Form\Form;
use Omeka\Form\Element\RoleSelect;

class ConfigForm extends Form
{
    public function init(): void
    {
        $this->add([
            'name' => 'ldap_role',
            'type' => RoleSelect::class,
            'options' => [
                'label' => 'Role', // @translate
                'info' => 'Role given to created users', // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);
    }
}
