<?php

namespace CleaniqueCoders\LaravelMediaSecure\Enums;

use CleaniqueCoders\Traitify\Concerns\InteractsWithEnum;
use CleaniqueCoders\Traitify\Contracts\Enum;

enum MediaAccess: string implements Enum
{
    use InteractsWithEnum;

    case VIEW = 'view';
    case DOWNLOAD = 'download';
    case STREAM = 'stream';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function description(): string
    {
        return $this->label().' Media';
    }

    public static function acceptable(string $type): bool
    {
        foreach (self::cases() as $case) {
            if ($case->value === $type) {
                return true;
            }
        }

        return false;
    }
}
