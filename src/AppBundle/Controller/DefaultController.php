<?php

namespace AppBundle\Controller;

use EspritEntreAide\AnnonceBundle\Entity\Annonce;
use EspritEntreAide\SpottedBundle\Entity\Publication;
use EspritEntreAide\SpottedBundle\Entity\Rating;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function tryAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/try.html.twig');

    }


    public function homeAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/home.html.twig');

    }

    public function contentAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/content.html.twig');

    }

    public function compteAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/compte.html.twig');

    }

    public function createAnnonceAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/ajout_annonce.html.twig');

    }




    public function indexAction(Request $request)
    {
        $publication = new Publication();
        $rating = new Rating();
        $annonce = new Annonce();
        $em=$this->getDoctrine()->getManager();



        $rankingpub = $em->getRepository("SpottedBundle:Rating",$rating)->rankingspotted();
        $events= $em->getRepository("EvenementBundle:Evenement")->findAll();
        $nbrEvents= $em->getRepository("EvenementBundle:Evenement")->countEvents();
        $an1 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("covoiturage");
        $an2 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("collocation");
        $an3 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("ObjetTrouve");
        $an4 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("ObjetPerdu");
        $ce = $em->getRepository("EvenementBundle:Evenement")->countEvent();
        $liste_an = $em->getRepository("AnnonceBundle:Annonce")->findAnnonoces();
        $liste_ev = $em->getRepository("EvenementBundle:Evenement")->findE();
        $liste_sp = $em->getRepository("SpottedBundle:Publication")->findS();

        // replace this example code with whatever you need
        return $this->render(':default:index.html.twig',array(
            'liste_ev'=>$liste_ev,'ce'=>$ce,'liste_sp'=>$liste_sp,'liste_an'=>$liste_an,'rankingpub'=>$rankingpub, 'events'=>$events, 'nbr'=>$nbrEvents,"a1" => $an1,"a2"=>$an2,"a3"=>$an3,"a4"=>$an4

        ));

    }

    public function adminAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('admin/index.html.twig');

    }



    public function testAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/parenttest.html.twig');

    }
    public function annonceAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('version0/annonce.html.twig');

    }


    public function clubAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('version0/club.html.twig');

    }


    public function eventsAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('version0/events.html.twig');

    }

    public function spottedAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('version0/spotted.html.twig');

    }

    public function docsAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('version0/docs.html.twig');

    }

}
