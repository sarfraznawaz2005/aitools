<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum BotTypeEnum: string
{
    use Values;

    case TEXT = 'Text Bot';
    case IMAGE = 'Image Bot';
    case VIDEO = 'Video Bot';
}
