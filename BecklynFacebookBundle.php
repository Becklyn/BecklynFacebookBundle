<?php

namespace Becklyn\FacebookBundle;

use Becklyn\FacebookBundle\DependencyInjection\BecklynFacebookExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class BecklynFacebookBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function getContainerExtension ()
    {
        return new BecklynFacebookExtension();
    }
}
