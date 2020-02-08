<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\AMExtendeeToolsBundle\Helper;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\PluginBundle\Helper\IntegrationHelper;

/**
 * Class ExtendeeHelper.
 */
class ExtendeeToolsHelper
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    public function __construct(CoreParametersHelper $coreParametersHelper, IntegrationHelper $integrationHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * Return path to console
     *
     * @return string
     */
    private function getConsolePath()
    {
        $keys = $this->integrationHelper->getIntegrationObject('ECronTester')->getKeys();
        if ( ! empty($keys['pathToMauticConsole'])) {
            return $keys['pathToMauticConsole'];
        }

        return $this->getDefaultConsolePath();
    }

    /**
     * @return string
     */
    public function getDefaultConsolePath()
    {
        return $this->coreParametersHelper->getParameter('kernel.root_dir') . '/console';
    }

    /**
     * Execute command line in background
     *
     * @param      $command
     * @param      $objectId
     * @param bool $inBackground
     *
     * @return int|string
     */
    public function execInBackground($command, $objectId = null, $inBackground = false)
    {
        $cmd = 'php ' . $this->getConsolePath() . ' ' . $command;
        if ($objectId) {
            $cmd .= ' -i ' . $objectId;
        }
        @set_time_limit(9999);
        if (false === $inBackground) {
            return shell_exec($cmd);
        } else {
            if (substr(php_uname(), 0, 7) == "Windows") {
                return pclose(popen("start /B " . $cmd, "r"));
            } else {
                return exec($cmd . " > /dev/null &");
            }
        }
    }
}
