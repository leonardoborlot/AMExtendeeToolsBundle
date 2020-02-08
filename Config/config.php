<?php

return [
    'name'        => 'Extendee Tools',
    'description' => 'Extend your Mautic with awesome features',
    'author'      => 'Alan Mosko',
    'version'     => '1.0.0',
    'routes'      => [
        'main' => [
            'mautic_plugin_extendee' => [
                'path'       => '/extendee/tools/{objectAction}/{objectId}',
                'controller' => 'AMExtendeeToolsBundle:ExtendeeTools:execute',
            ],
        ],
    ],
    'services'    => [
        'events'       => [
            'mautic.plugin.extendee.button.subscriber' => [
                'class'     => \MauticPlugin\AMExtendeeToolsBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
        ],
        'others'       => [
            'mautic.plugin.extendee.helper' => [
                'class'     => \MauticPlugin\AMExtendeeToolsBundle\Helper\ExtendeeToolsHelper::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'mautic.helper.integration',
                ],
            ],
        ],
        'integrations' => [
            'mautic.integration.ECronTester' => [
                'class'     => \MauticPlugin\AMExtendeeToolsBundle\Integration\ECronTesterIntegration::class,
                'arguments' => [
                    'mautic.plugin.extendee.helper',
                ],
            ],
        ],
    ],
];
