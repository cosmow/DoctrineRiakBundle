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

namespace CosmoW\Bundle\RiakBundle\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractExtension;

/**
 * Form extension.
 *
 * @author Kris Wallsmith <kris@symfony.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class DoctrineRiakExtension extends AbstractExtension
{
    protected $registry = null;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    protected function loadTypes()
    {
        return array(
            new Type\DocumentType($this->registry),
        );
    }

    protected function loadTypeGuesser()
    {
        return new DoctrineRiakTypeGuesser($this->registry);
    }
}
