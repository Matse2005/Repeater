<?php
declare(strict_types=1);

namespace MatseVH\Repeater\Block\Adminhtml\Widget\Repeater\Fields;

use MatseVH\Repeater\Block\Adminhtml\Widget\Repeater\AbstractField;

class Hr extends AbstractField
{
    protected const SHOW_LABEL = false;

    public function getTemplatePath(): ?string
    {
        return 'MatseVH_Repeater::widget/repeater/fields/hr.phtml';
    }
}
