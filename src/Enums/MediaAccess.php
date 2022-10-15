<?php

namespace Bekwoh\LaravelMediaSecure\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self view()
 * @method static self stream()
 * @method static self download()
 */
class MediaAccess extends Enum
{
    public static function acceptable(string $type)
    {
        return in_array($type, self::toArray());
    }
}
