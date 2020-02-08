<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\AMExtendeeToolsBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\CoreBundle\Controller\FormController;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\EmailBundle\Exception\EmailCouldNotBeSentException;
use Mautic\LeadBundle\MauticLeadBundle;
use Mautic\LeadBundle\Model\LeadModel;

class ExtendeeToolsController extends FormController
{
    /**
     * @param $objectId
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function sendToMailTesterAction($objectId)
    {
        $model = $this->getModel('email');
        $entity = $model->getEntity($objectId);

        // Prepare a fake lead
        /** @var \Mautic\LeadBundle\Model\FieldModel $fieldModel */
        $fieldModel = $this->getModel('lead.field');
        $fields = $fieldModel->getFieldList(false, false);
        array_walk(
            $fields,
            function (&$field) {
                $field = "[$field]";
            }
        );
        $fields['id'] = 0;

        $apiKeys = $this->get('mautic.helper.integration')->getIntegrationObject('EMailTester')->getKeys();
        if (empty($apiKeys['mailTesterUsername'])) {
            return new Response($this->translator->trans('plugin.extendee.mail.tester.username.not_blank'));
        }

        $mailTesterUsername = $apiKeys['mailTesterUsername'];

        $clientId = md5(
            $this->get('mautic.helper.user')->getUser()->getEmail() .
            $this->coreParametersHelper->getParameter('site_url')
        );
        $uniqueId = $mailTesterUsername . '-' . $clientId . '-' . time();
        $email = $uniqueId . '@mail-tester.com';

        $users = [
            [
                // Setting the id, firstname and lastname to null as this is a unknown user
                'id'        => '',
                'firstname' => '',
                'lastname'  => '',
                'email'     => $email,
            ],
        ];

        // send test email
        $model->sendSampleEmailToUser($entity, $users, $fields, [], [], false);

        // redirect to mail-tester
        return $this->postActionRedirect(
            [
                'returnUrl' => 'https://www.mail-tester.com/' . $uniqueId,
            ]
        );
    }

    /**
     * Segment rebuild action
     *
     * @param $objectId
     */
    public function segmentRebuildAction($objectId)
    {
        return $this->processJob('lead', 'segment', 'list', $objectId, 'm:s:r');
    }

    /**
     * Campaign rebuild action
     *
     * @param $objectId
     */
    public function campaignRebuildAction($objectId)
    {
        return $this->processJob('campaign', 'campaign', 'campaign', $objectId, ' m:c:r');
    }

    /**
     * Campaign trigger action
     *
     * @param $objectId
     */
    public function campaignTriggerAction($objectId)
    {
        return $this->processJob('campaign', 'campaign', 'campaign', $objectId, ' m:c:t');
    }

    /**
     * Email broadcast send
     *
     * @param $objectId
     */
    public function emailBroadcastSendAction($objectId)
    {
        return $this->processJob('email', 'email', 'email', $objectId, ' mautic:broadcasts:send');
    }

    /**
     * Process job
     *
     * @param $bundle
     * @param $entityName
     * @param $objectId
     * @param $command
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function processJob($bundle, $routeContext, $entityName, $objectId, $command)
    {

        $flashes = [];
        $model = $this->getModel($bundle . '.' . $entityName);
        $entity = $model->getEntity($objectId);
        $contentTemplate = 'Mautic' . ucfirst($bundle) . 'Bundle:' . ucfirst($entityName) . ':view';
        $activeLink = '#mautic_' . $routeContext . '_action';
        $mauticContent = $entityName;
        $returnUrl = $this->generateUrl(
            'mautic_' . $routeContext . '_action',
            ['objectAction' => 'view', 'objectId' => $entity->getId()]
        );
        $result = $this->get('mautic.plugin.extendee.helper')->execInBackground($command, $objectId);
        if ( ! empty($result)) {
            $flashes[] = [
                'type'    => 'notice',
                'msg'     => nl2br(trim($result)),
                'msgVars' => [
                    '%name%' => $entity,
                    '%id%'   => $objectId,
                ],
            ];
        }

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => [
                'objectAction' => 'view',
                'objectId'     => $entity->getId(),
            ],
            'contentTemplate' => $contentTemplate,
            'passthroughVars' => [
                'activeLink'    => $activeLink,
                'mauticContent' => $mauticContent,
            ],
        ];

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                [
                    'flashes' => $flashes,
                ]
            )
        );
    }
}
