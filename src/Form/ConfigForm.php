<?php

namespace Ldap\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Omeka\Form\Element\RoleSelect;

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
                'id' => 'ldap_role',
                'required' => true,
            ],
        ]);
        $this
            ->add([
                'name' => 'ldap_map_name',
                'type' => Element\Text::class,
                'options' => [
                    'label' => 'Ldap key to map user name', // @translate
                ],
                'attributes' => [
                    'id' => 'ldap_map_name',
                    'placeholder' => 'name',
                ],
            ]);
    }
}
