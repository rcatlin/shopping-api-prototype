<?php

namespace AppBundle;

use AppBundle\Command\GenerateCRUDControllerCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppBundle extends Bundle
{
    public function registerCommands(Application $application)
    {
        $application->add(new GenerateCRUDControllerCommand());
    }
}
