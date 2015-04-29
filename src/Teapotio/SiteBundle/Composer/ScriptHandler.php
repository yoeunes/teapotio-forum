<?php

namespace Teapotio\SiteBundle\Composer;

use Composer\Script\CommandEvent;

class ScriptHandler extends \Sensio\Bundle\DistributionBundle\Composer\ScriptHandler {
    /**
     * Call the demo command of the Acme Demo Bundle.
     *
     * @param $event CommandEvent A instance
     */
    public static function install(CommandEvent $event)
    {
        $options = self::getOptions($event);
        $consoleDir = self::getConsoleDir($event, 'set up database and forum');



        if (null === $consoleDir) {
            return;
        }

        $env = getenv('SYMFONY_ENV') ?: 'dev';

        if ($env !== 'dev') {
          $event->getIO()->write('Operations on database are only executed on dev. If cache clearing fails and throws a database exception, verify the integrity of your database.');
          return;
        }

        try {
          static::executeCommand($event, $consoleDir, 'doctrine:database:create -q', $options['process-timeout']);
        } catch (\RuntimeException $e) {
          $event->getIO()->write('The database was not created.');
        }

        try {
          static::executeCommand($event, $consoleDir, 'doctrine:schema:create -q', $options['process-timeout']);
        } catch (\RuntimeException $e) {
          $event->getIO()->write('The schema was not created.');
        }
    }
}
