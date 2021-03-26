<?php

namespace Ldap\Form;

use Omeka\Form\Element\RoleSelect;
use Laminas\Form\Form;

class ConfigForm extends Form
{
    public function init()
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
