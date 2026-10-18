<?php

namespace App\Enums;

enum ServiceType: string
{
    case Domain = 'domain';
    case Hosting = 'hosting';
    case Vps = 'vps';
    case Ssl = 'ssl';
    case Email = 'email';
    case Other = 'other';

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    public function label(): string
    {
        return match ($this) {
            self::Domain => 'Domain',
            self::Hosting => 'Hosting',
            self::Vps => 'VPS',
            self::Ssl => 'SSL',
            self::Email => 'Email',
            self::Other => 'Other',
        };
    }
}
