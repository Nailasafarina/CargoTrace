<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Operator = 'operator';
    case Customer = 'customer';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => __('common.roles.admin'),
            self::Operator => __('common.roles.operator'),
            self::Customer => __('common.roles.customer'),
        };
    }
}
