<?php

namespace App\Service;

use Symfony\Component\String\Slugger\AsciiSlugger;

class Utility
{
    public function slug(string $string)
    {
        return (new AsciiSlugger())->slug(strtolower($string));
    }
}