<?php

namespace App\Controller\Frontend;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\Utility;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/client')]
class ClientController extends AbstractController
{
    public function __construct(
        private ClientRepository $clientRepository,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private Utility $utility
    )
    {
    }

    #[Route('/create', name: 'app_frontend_client_create', methods: ['GET','POST'])]
    public function create(Request $request): Response
    {
        $submittedToken = $request->get('_csrf_token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('adresseToken', $submittedToken))){
            sweetalert()->addWarning("Veuillez reprendre l'enregistrement de votre adresse livraison");
            return $this->redirectToRoute('app_frontend_panier_livraison');
        }

//        $code = $this->utility->codeClient();

        $client = new Client();
        $client->setNom(htmlentities(htmlspecialchars($request->get('nom'))));
        $client->setPrenom(htmlentities(htmlspecialchars($request->get('prenom'))));
        $client->setEmail(htmlentities(htmlspecialchars($request->get('email'))));
        $client->setTelephone(htmlentities(htmlspecialchars($request->get('telephone'))));
        $client->setPays(htmlentities(htmlspecialchars($request->get('pays'))));
        $client->setVille(htmlentities(htmlspecialchars($request->get('ville'))));
        $client->setEtat(htmlentities(htmlspecialchars($request->get('region'))));
        $client->setUser($this->getUser());
        $client->setMatricule($this->utility->codeClient());
        dd($client);
//        dd(htmlentities(htmlspecialchars($request->get('nom'))));
    }
}