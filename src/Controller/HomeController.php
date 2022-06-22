<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function  index(ManagerRegistry $doctrine): Response
    {
        $acceuil = $doctrine -> getRepository(Home::class)->findBy([],["nom" => "ASC"]);
        return $this->render('home/index.html.twig', [
            'acceuil' => $acceuil
        ]);
    }

    // public function AfficherSessionPasses()

    // {

    //     $now = new \DateTime();

    //     return $this->createQueryBuilder('s')

    //         ->andWhere('s.date_fin < :val')

    //         ->setParameter('val', $now)

    //         ->orderBy('s.date_debut', 'ASC')

    //         ->getQuery()

    //         ->getResult();

    // }


}
