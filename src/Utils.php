<?php

namespace S4mpp\Laragenius;

final class Utils
{
    /**
     * @param  array<int,string>  $uses
     */
    public static function mountUses(array $uses): string
    {
        $uses = array_unique($uses);

        usort($uses, fn ($a, $b): int => mb_strlen($a) - mb_strlen($b));

        return implode("\n", array_map(fn ($use): string => "use {$use};", $uses));
    }
}
