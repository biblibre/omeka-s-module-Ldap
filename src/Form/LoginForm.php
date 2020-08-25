<?php

namespace Ldap\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function init()
    {
        $this->setAttribute('class', 'disable-unsaved-warning');
        $this->add([
            'name' => 'email',
            'type' => 'Text',
            'options' => [
                'label' => 'Email or username', // @translate
            ],
            'attributes' => [
                'required' => true,
                'id' => 'email',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'options' => [
                'label' => 'Password', // @translate
            ],
            'attributes' => [
                'required' => true,
                'id' => 'password',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Log in', // @translate
            ],
        ]);

        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'email',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'password',
            'required' => true,
        ]);
    }
}
