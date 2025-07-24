<?php

namespace CleaniqueCoders\LaravelMediaSecure\Enums;

use CleaniqueCoders\Traitify\Concerns\InteractsWithEnum;
use CleaniqueCoders\Traitify\Contracts\Enum;

enum MediaAccess: string implements Enum
{
    use InteractsWithEnum;

    CASE VIEW = 'view';
    CASE DOWNLOAD = 'download';
    CASE STREAM = 'stream';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function description(): string
    {
        return $this->label() . ' Media';
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
