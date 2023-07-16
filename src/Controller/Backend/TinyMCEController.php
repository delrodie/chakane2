<?php

namespace App\Controller\Backend;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tinymce')]
class TinyMCEController extends AbstractController
{
    #[Route('/tinymce.js', name:'get_tinymce')]
    public function index(): Response
    {
        $file = file_get_contents(__DIR__. '/../../../vendor/tinymce/tinymce/tinymce.js');
//        dd($file);
        return new Response($file, 200, ['Content-Type' => 'text/javascript']);
    }
}