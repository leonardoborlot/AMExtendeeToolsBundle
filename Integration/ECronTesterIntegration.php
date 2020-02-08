<?php

namespace MauticPlugin\AMExtendeeToolsBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\AMExtendeeToolsBundle\Helper\ExtendeeToolsHelper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ECronTesterIntegration extends AbstractIntegration
{
    /**
     * @var ExtendeeToolsHelper
     */
    protected $extendeeHelper;

    public function __construct(ExtendeeToolsHelper $extendeeHelper)
    {
        $this->extendeeHelper = $extendeeHelper;
    }

    public function getName()
    {
        return 'ECronTester';
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
        return 'plugins/AMExtendeeToolsBundle/Assets/img/CronTester.png';
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
                'pathToMauticConsole',
                TextType::class,
                [
                    'label'       => 'plugin.extendee.cron.tester.form.path_to_mautic_console',
                    'attr'        => [
                        'class' => 'form-control',
                    ],
                    'required'    => true,
                    'data'        => empty($data['pathToMauticConsole']) ? $this->extendeeHelper->getDefaultConsolePath() : $data['pathToMauticConsole'],
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
                'template'   => 'AMExtendeeToolsBundle:Integration:cron-tester.html.php',
                'parameters' => [
                ],
            ];
        }

        return parent::getFormNotes($section);
    }
}
