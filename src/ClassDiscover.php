<?php

namespace Acme;

/**
 * Class ClassDiscover
 * @package Acme
 */
class ClassDiscover
{
    /**
     * @param string $pattern
     * @return string[]
     */
    public function getAllByPattern(string $pattern): array
    {
        $classes = [];
        $match = glob($pattern);

        if ($match) {
            foreach ($match as $file) {
                $classes[] = basename($file, '.php');
            }
        }

        return $classes;
    }
}