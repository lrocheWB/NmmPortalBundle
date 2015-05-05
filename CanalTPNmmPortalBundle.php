<?php

namespace CanalTP\NmmPortalBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CanalTPNmmPortalBundle extends Bundle
{
    public function getParent() {
        return 'CanalTPSamCoreBundle';
    }
}
