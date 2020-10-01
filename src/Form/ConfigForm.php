<?php

namespace Ldap\Form;

use Omeka\Form\Element\RoleSelect;
use Zend\Form\Form;

class ConfigForm extends Form
{
    public function init()
    {
        $this->add([
            'name' => 'role',
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
