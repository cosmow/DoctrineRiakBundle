<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CosmoW\Bundle\RiakBundle\Command;

use Doctrine\ODM\Riak\Tools\Console\Command\Schema\UpdateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to update the database schema for a set of classes based on their
 * mappings.
 */
class UpdateSchemaDoctrineODMCommand extends UpdateCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('doctrine:riak:schema:update')
            ->addOption('dm', null, InputOption::VALUE_REQUIRED, 'The document manager to use for this command.')
            ->setHelp(<<<EOT
The <info>doctrine:riak:schema:update</info> command updates the default document manager's schema:

  <info>./app/console doctrine:riak:schema:update</info>

You can also optionally specify the name of a document manager to update the schema for:

  <info>./app/console doctrine:riak:schema:update --dm=default</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineODMCommand::setApplicationDocumentManager($this->getApplication(), $input->getOption('dm'));

        parent::execute($input, $output);
    }
}
