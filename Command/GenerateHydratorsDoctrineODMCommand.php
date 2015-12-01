<?php

/*
 * This file is part of the Doctrine RiakBundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CosmoW\Bundle\RiakBundle\Command;

use Doctrine\ODM\Riak\Tools\Console\Command\GenerateHydratorsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate the Doctrine ORM document hydrators to your cache directory.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class GenerateHydratorsDoctrineODMCommand extends GenerateHydratorsCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:riak:generate:hydrators')
            ->addOption('dm', null, InputOption::VALUE_OPTIONAL, 'The document manager to use for this command.')
            ->setHelp(<<<EOT
The <info>doctrine:riak:generate:hydrators</info> command generates hydrator classes for your documents:

  <info>./app/console doctrine:riak:generate:hydrators</info>

You can specify the document manager you want to generate the hydrators for:

  <info>./app/console doctrine:riak:generate:hydrators --dm=name</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineODMCommand::setApplicationDocumentManager($this->getApplication(), $input->getOption('dm'));

        return parent::execute($input, $output);
    }
}
