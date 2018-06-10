<?php

namespace EspritEntreAide\EvenementBundle\Controller;

use EspritEntreAide\EvenementBundle\Entity\Evenement;
use EspritEntreAide\EvenementBundle\Form\Evenement2Type;
use EspritEntreAide\EvenementBundle\Form\EvenementType;
use EspritEntreAide\EvenementBundle\Form\ModiferEvtType;
use EspritEntreAide\EvenementBundle\Form\RechercheClubType;
use EspritEntreAide\EvenementBundle\Form\RechercheDateType;
use EspritEntreAide\EvenementBundle\Form\RechercheNomType;
use EspritEntreAide\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $events= $em->getRepository(Evenement::class)->afficherFront();

        if ($request->isXmlHttpRequest()) {

            $search = $request->request->get('motcle');
            $events = $em->getRepository("EvenementBundle:Evenement")->rechercherEvenement($search);
            return $this->render('EvenementBundle:Default:indexxx.html.twig', array(
                "events"=>$events));
        }



        return $this->render('EvenementBundle:Default:index.html.twig', array(
            "events"=>$events));







    }



}
