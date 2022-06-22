<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\SessionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StagiaireController extends AbstractController
{
    /**
     * @Route("/stagiaire", name="app_stagiaire")
     */
    public function index(ManagerRegistry $doctrine): Response
    {

        $stagiaires = $doctrine -> getRepository(Stagiaire::class)->findBy([],["nom" => "ASC"]);
        
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    


    /**
     * @Route("/stagiaire/add", name="add_stagiaire")
     * @Route("/stagiaire/update/{id}", name="update_stagiaire")
     */
    public function add(ManagerRegistry $doctrine, Stagiaire $stagiaire = null, Request $request): Response{
        
        if(!$stagiaire) { 
            $stagiaire = new Stagiaire();  
        }


        $entityManager = $doctrine->GetManager();
        $form = $this-> createForm(StagiaireType::class, $stagiaire);
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            $stagiaire = $form -> getData();
            $entityManager -> persist($stagiaire);
            $entityManager -> flush();

            return $this->redirectToRoute('app_stagiaire');
        }

        return $this->render('stagiaire/add.html.twig', [
                'FormStagiaire'=> $form ->createView()
        ]);
    }




     /**
     * @Route("/stagiaire/delete/{id}", name="delete_stagiaire")
     */
    public function delete(ManagerRegistry $doctrine, Stagiaire $stagiaire)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($stagiaire);
        $entityManager->flush();
        return $this->redirectToRoute("app_stagiaire");
    }

    /**
     * @Route("/stagiaire/{id}", name="show_stagiaire")
     */
    public function show(Stagiaire $stagiaire, ManagerRegistry $doctrine): Response
    {

        

        return $this->render('stagiaire/show.html.twig', [
            'stagiaire' => $stagiaire,
            // 'sessions' => $sessions
        ]);
    }

}

