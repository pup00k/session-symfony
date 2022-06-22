<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\TypeSessionType;
use App\Repository\StagiaireRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SessionController extends AbstractController
{
    /**
     * @Route("/session/", name="app_session") // Route permet de cibler la redirection, et le name de pouvoir l'use dans le view 
     */
    public function index(ManagerRegistry $doctrine): Response 
    //ORM = Cpuche intermédiiare entre controller et base de donnée. 
    {


        $sessionactuelle = $doctrine -> getRepository(Session::class)->findCurrentSessions();  
        $sessionavenir = $doctrine -> getRepository(Session::class)->AfficherVenir();          
        $sessionpasse = $doctrine -> getRepository(Session::class)->AfficherSessionPasses();

        return $this->render('session/index.html.twig', [
            'sessionactuelle' => $sessionactuelle,
            'sessionavenir' => $sessionavenir,
            'sessionpasse' => $sessionpasse
        ]);
    }


       /**
     * @Route("/session/delete/{id}", name="delete_session")
     */
    public function delete(ManagerRegistry $doctrine, Session $session)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($session);
        $entityManager->flush();
        return $this->redirectToRoute("app_session");
    }

      /**
     * @Route("/session/deleteStagiaire/{idSession}/{idStagiaire}", name="delete_stagiaire_session")
     *
     * @ParamConverter("session", options={"mapping" = {"idSession" : "id"}})
     * @ParamConverter("stagiaire", options={"mapping" = {"idStagiaire" : "id"}})
     */
    public function deleteStagiaire(ManagerRegistry $doctrine, Stagiaire $stagiaire, Session $session)
    {
        $entityManager = $doctrine->getManager();
        $session->removeStagiaire($stagiaire);
        $entityManager->persist($session);
        $entityManager->flush();
        return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
    }

        // Param convert premiere chose -> Table / option mapping = lui dire que ce que tu créer  en premier c'est l'appelation et en deuxieme ce que tu cibles
        
      /**
     * @Route("/session/addStagiaire/{idSession}/{idStagiaire}", name="add_stagiaire_session")
     *
     * @ParamConverter("session", options={"mapping" = {"idSession" : "id"}})
     * @ParamConverter("stagiaire", options={"mapping" = {"idStagiaire" : "id"}})
     */
    public function programmeradd(ManagerRegistry $doctrine, Stagiaire $stagiaire, Session $session){

    if($session->getNbPlacesRestante() > 0){
    $session->addStagiaire($stagiaire);
    $doctrine->getManager()->flush();
    
    return $this->redirectToRoute("show_session", ['id' => $session->getId()]);
    }
}

/**
     * @Route("/session/add", name="add_session")
     * @Route("/session/update/{id}", name="update_session")
     */
    public function add(ManagerRegistry $doctrine, Session $session = null, Request $request): Response{
        
        if(!$session) { 
            $session = new Session();  
        }


        $entityManager = $doctrine->GetManager();
        $form = $this-> createForm(TypeSessionType::class, $session);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            $session = $form -> getData();
            $entityManager -> persist($session);
            $entityManager -> flush();

            return $this->redirectToRoute('app_session');
        }

        return $this->render('session/add.html.twig', [
                'FormSession'=> $form ->createView()
        ]);
    }

    /**
     * @Route("/session/{id}", name="show_session")
     */
    public function show(ManagerRegistry $doctrine,Session $session, StagiaireRepository $sta): Response
    {
        $nonInscrits = $sta ->findNonInscrit($session -> getId());



    
        return $this->render('session/show.html.twig', [
            'session' => $session,
            'nonInscrits'=> $nonInscrits
           
        ]);
    }
}
