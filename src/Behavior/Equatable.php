<?php

namespace App\Behavior;

/**
 * @template T of object
 */
interface Equatable
{
    /**
     * @param ?T $other
     */
    public function isEqualTo(?object $other): bool;
}
