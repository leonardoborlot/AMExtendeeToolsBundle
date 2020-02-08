<?php

namespace MauticPlugin\AMExtendeeToolsBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class EMailTesterIntegration extends AbstractIntegration
{
    public function getName()
    {
        // should be the name of the integration
        return 'EMailTester';
    }

    public function getAuthenticationType()
    {
        /* @see \Mautic\PluginBundle\Integration\AbstractIntegration::getAuthenticationType */
        return 'none';
    }

    /**
     * Get icon for Integration.
     *
     * @return string
     */
    public function getIcon()
    {
        return 'plugins/AMExtendeeToolsBundle/Assets/img/MailTester.png';
    }

    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ($formArea == 'keys') {
            $builder->add(
                'mailTesterUsername',
                TextType::class,
                [
                    'label'       => 'plugin.extendee.mail.tester.form.username',
                    'attr'        => [
                        'class' => 'form-control',
                    ],
                    'required'    => true,
                    'constraints' => [
                        new NotBlank(
                            ['message' => 'mautic.core.value.required']
                        ),
                    ],
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param $section
     *
     * @return string|array
     */
    public function getFormNotes($section)
    {
        if ('custom' === $section) {
            return [
                'template'   => 'AMExtendeeToolsBundle:Integration:mail-tester.html.php',
                'parameters' => [
                ],
            ];
        }

        return parent::getFormNotes($section);
    }
}
