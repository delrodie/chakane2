<?php

namespace App\Service;

use Symfony\Component\String\Slugger\AsciiSlugger;

class Utility
{
    public function slug(string $string)
    {
        return (new AsciiSlugger())->slug(strtolower($string));
    }

    public function notification(string $message, string $type=null): void
    {
        $notif = notyf()
            ->position('x', 'center')
            ->position('y', 'top');

        switch ($type){
            case 'Error':
                $notif->addError($message);
                break;

            case 'Warning':
                $notif->addWarning($message);

            default:
                $notif->addSuccess($message);
        }

    }
}