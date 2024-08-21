<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum BotTypeEnum: string
{
    use Values;

    case TEXT = 'General Chat Bot';
    case DOCUMENT = 'Document Chat Bot';
    case IMAGE = 'Image Generation Bot';
    case VIDEO = 'Video Generation Bot';
}
