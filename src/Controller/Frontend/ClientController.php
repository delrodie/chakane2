<?php

namespace App\Controller\Frontend;

use App\Entity\Adresse;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/client')]
class ClientController extends AbstractController
{
    public function __construct(
        private ClientRepository $clientRepository,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private Utility $utility,
        private EntityManagerInterface $entityManager
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

//        dd(Countries::getName());

        $client = new Client();
        $client->setNom(htmlentities(htmlspecialchars($request->get('nom'))));
        $client->setPrenom(htmlentities(htmlspecialchars($request->get('prenom'))));
        $client->setEmail(htmlentities(htmlspecialchars($request->get('email'))));
        $client->setTelephone(htmlentities(htmlspecialchars($request->get('telephone'))));
        $client->setPays(htmlentities(htmlspecialchars(Countries::getName($request->get('pays')))));
        $client->setVille(htmlentities(htmlspecialchars($request->get('ville'))));
        $client->setEtat(htmlentities(htmlspecialchars($request->get('region'))));
        $client->setUser($this->getUser());
        $client->setMatricule($this->utility->codeClient());

        $adresse = new Adresse();
        $adresse->setCodePays(htmlentities(htmlspecialchars($request->get('pays'))));
        $adresse->setPays(htmlentities(htmlspecialchars(Countries::getName($request->get('pays')))));
        $adresse->setVille(htmlentities(htmlspecialchars($request->get('ville'))));
        $adresse->setRegion(htmlentities(htmlspecialchars($request->get('region'))));
        $adresse->setZip(htmlentities(htmlspecialchars($request->get('zip'))));
        $adresse->setLieu(htmlentities(htmlspecialchars($request->get('adresse1'))));
        $adresse->setDetails(htmlentities(htmlspecialchars($request->get('detail'))));
        $adresse->setUser($this->getUser());

        $this->entityManager->persist($client);
        $this->entityManager->persist($adresse);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_frontend_panier_livraison',[], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add-adresse', name: 'app_frontend_client_add_adresse', methods: ['GET','POST'])]
    public function add(Request $request): Response
    {
        $submittedToken = $request->get('_csrf_token');

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('addAdresseToken', $submittedToken))){
            sweetalert()->addWarning("Veuillez reprendre l'enregistrement de votre adresse livraison");
            return $this->redirectToRoute('app_frontend_panier_livraison');
        }

        $adresse = new Adresse();
        $adresse->setCodePays(htmlentities(htmlspecialchars($request->get('pays'))));
        $adresse->setPays(htmlentities(htmlspecialchars(Countries::getName($request->get('pays')))));
        $adresse->setVille(htmlentities(htmlspecialchars($request->get('ville'))));
        $adresse->setRegion(htmlentities(htmlspecialchars($request->get('region'))));
        $adresse->setZip(htmlentities(htmlspecialchars($request->get('zip'))));
        $adresse->setLieu(htmlentities(htmlspecialchars($request->get('adresse1'))));
        $adresse->setDetails(htmlentities(htmlspecialchars($request->get('detail'))));
        $adresse->setUser($this->getUser());

        $this->entityManager->persist($adresse);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_frontend_panier_livraison',[], Response::HTTP_SEE_OTHER);
    }
}