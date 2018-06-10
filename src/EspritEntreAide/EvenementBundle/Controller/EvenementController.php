<?php

namespace EspritEntreAide\EvenementBundle\Controller;

use CyberJaw\GoogleMapsBundle\DependencyInjection\GoogleMapsExtension;
use CyberJaw\GoogleMapsBundle\Form\Type\GoogleMapsType;
use CyberJaw\GoogleMapsBundle\GoogleMapsBundle;
use EspritEntreAide\EvenementBundle\Entity\Evenement;
use EspritEntreAide\EvenementBundle\Form\Evenement2Type;
use EspritEntreAide\EvenementBundle\Form\EvenementAdminType;
use EspritEntreAide\EvenementBundle\Form\EvenementType;
use EspritEntreAide\EvenementBundle\Form\ModiferAdresseEvtType;
use EspritEntreAide\EvenementBundle\Form\ModiferEvtType;
use EspritEntreAide\EvenementBundle\Form\ModiferImgEvtType;
use EspritEntreAide\EvenementBundle\Form\RechercheClubType;
use EspritEntreAide\EvenementBundle\Form\RechercheDateType;
use EspritEntreAide\EvenementBundle\Form\RechercheNomType;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Event\Event;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlay\Marker;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderAddressRequest;
use Ivory\GoogleMap\Service\Place\Base\Place;
use Ivory\GoogleMapBundle\Form\Type\PlaceAutocompleteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EvenementController extends Controller
{
    public function ajoutAction(Request $request)
    {
        $evt = new Evenement();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')
            or $this->get('security.authorization_checker')->isGranted('ROLE_RESPONSABLE_SUPER_ADMIN')
            or $this->get('security.authorization_checker')->isGranted('ROLE_RESPONSABLE_CLUB')
            or $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')
        )
        {
            $evt->setEtat(0);

            $form = $this->createForm(EvenementType::class, $evt);
            $form->handleRequest($request); /*creation d'une session pr stocker les valeurs de l'input*/
            if ($form->isValid()) {
                $evt->setIdUser($this->getUser());
                $evt->setUsrRole('ADMIN');
                $em = $this->getDoctrine()->getManager();
                $em->persist($evt);
                $em->flush();
                return $this->redirectToRoute('_afficher_events');
            }
            return $this->render('@Evenement/Evenement/ajout.html.twig', array(
                'form' => $form->createView()
            ));
        }
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ETUDIANT'))
        {
            $evt->setEtat(1);
            $evt->setUsrRole("Etudiant") ;
            $form = $this->createForm(Evenement2Type::class, $evt);
            $form->handleRequest($request); /*creation d'une session pr stocker les valeurs de l'input*/

            if ($form->isValid()) {
                $evt->setIdUser($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($evt);
                $em->flush();
                return $this->redirectToRoute('_afficher_events');
            }
            return $this->render('@Evenement/Evenement/ajout.html.twig', array(
                'form' => $form->createView()//'map'=>$map
            ));
        }
        if ( $this->get('security.authorization_checker')->isGranted('ROLE_ENSEIGNANT'))
        {
            $evt->setEtat(1);
            $evt->setUsrRole("Enseignant") ;
            $form = $this->createForm(Evenement2Type::class, $evt);
            $form->handleRequest($request); /*creation d'une session pr stocker les valeurs de l'input*/
            if ($form->isValid()) {
                $evt->setIdUser($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($evt);
                $em->flush();
            }
            return $this->render('@Evenement/Evenement/ajout.html.twig', array(
                'form' => $form->createView()
            ));
        }


        return $this->render('@Evenement/Evenement/ajout.html.twig');

    }

    public function adminAjoutAction(Request $request)
    {
        $evt = new Evenement();
            $evt->setEtat(0);
            $form = $this->createForm(EvenementAdminType::class, $evt);
            $form->handleRequest($request); /*creation d'une session pr stocker les valeurs de l'input*/
            if ($form->isValid()) {
                $evt->setIdUser($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($evt);
                $em->flush();
                return $this->redirectToRoute("_admin_afficher_events");
            }
            return $this->render('admin/partial/EvenementAdmin/ajouterEvenementAdmin.html.twig', array(
                'form' => $form->createView()
            ));

    }

    public function afficherEventsPasseAction()
    {
        $em = $this->getDoctrine()->getManager();

        $evt = $em->getRepository("EvenementBundle:Evenement")->affichereventspasserFront();
        $nbrEvents= $em->getRepository(Evenement::class)->countEvents();
        return $this->render('@Evenement/Evenement/afficherpasser.html.twig', array(
            "evts" => $evt, "nbr"=>$nbrEvents

        ));

    }


    public function modifierAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id=$request->get('id');
            $titre=$request->get('titre');
            $description=$request->get('description');
            $date=$request->get('date');
            $type=$request->get('type');
            $em=$this->getDoctrine()->getManager();
            $evts=$em->getRepository('EvenementBundle:Evenement')->find($id);
            $evts->setTitreE($titre);
            $evts->setDescE($description);
            $evts->setDateE(new \DateTime($date));
            $evts->setTypeE($type);


            $em->persist($evts);
            $em->flush();
            return new Response("Succes!");

        }
        return new Response("Requête invalide",400);
    }

    public function adminModifierImgEventAction(Request $request)
    {
        $id=$_GET['id'];
        $evts=$this->getDoctrine()->getManager()->getRepository('EvenementBundle:Evenement')->find($id);
        $form=$this->createForm(ModiferImgEvtType::class,$evts);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($evts);
            $em->flush();
            return $this->redirectToRoute('_admin_afficher_events');
        }
        return $this->render("admin/partial/EvenementAdmin/modifierimgeventadmin.html.twig",array('evt'=>$evts,'form'=>$form->createView()));
    }

    public function ModifierImgEventAction(Request $request)
    {
        $id=$_GET['id'];
        $evts=$this->getDoctrine()->getManager()->getRepository('EvenementBundle:Evenement')->find($id);
        $form=$this->createForm(ModiferImgEvtType::class,$evts);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($evts);
            $em->flush();
            return $this->redirect('http://localhost/PIDEV-final/web/app_dev.php/Events_a/afficherunevent?id='.$id);
        }
        return $this->render("@Evenement/Evenement/modifierimg.html.twig",array("evt"=>$evts,'form'=>$form->createView()));
    }


    public function adminModifierAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $id=$request->get('id');

            $titre=$request->get('titre');
            $description=$request->get('description');
            $date=$request->get('date');
            $type=$request->get('type');
            $em=$this->getDoctrine()->getManager();
            $evts=$em->getRepository('EvenementBundle:Evenement')->find($id);

            $evts->setTitreE($titre);
            $evts->setDescE($description);
            $evts->setDateE(new \DateTime($date));
            $evts->setTypeE($type);


                $em->persist($evts);
                $em->flush();
                return new Response("Succes!");

        }
            return new Response("Requête invalide",400);

    }

    public function supprimerAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $em = $this->getDoctrine()->getManager();

            $evt = $em->getRepository("EvenementBundle:Evenement")->find($id);
            $em->remove($evt);
            $em->flush();
            return new Response("Succes!");
        }
        return new Response("Requête invalide",400);
    }


    public function adminSupprimerAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->get('id');
            $em = $this->getDoctrine()->getManager();

            $evt = $em->getRepository("EvenementBundle:Evenement")->find($id);
            $em->remove($evt);
            $em->flush();
            return new Response("Succes!");
        }
        return new Response("Requête invalide",400);
    }


    public function afficherAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $evt = $em->getRepository("EvenementBundle:Evenement")->afficherFront();
        $nbrEvents= $em->getRepository(Evenement::class)->countEvents();
        return $this->render('@Evenement/Evenement/afficher.html.twig', array(
            "evts" => $evt, "nbr"=>$nbrEvents

        ));

    }

    public function afficherUnEventAction()
    {


        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $evt = $em->getRepository("EvenementBundle:Evenement")->find($id);
        $coor=new Coordinate($evt->getGooglemaps()['lat'],$evt->getGooglemaps()['lng']);
        $marker = new Marker($coor);
        $map = new Map();
        $map->getOverlayManager()->addMarker($marker);
        $map->setMapOption('zoom',12);
        $map->setCenter($coor);
        return $this->render('@Evenement/Evenement/afficherUnEvent.html.twig', array(
            "evt" => $evt,'map'=>$map
        ));

    }




    public function adminAfficherAction()
    {
        $em = $this->getDoctrine()->getManager();
        $evt = $em->getRepository("EvenementBundle:Evenement")->findAll();

        return $this->render('admin/partial/EvenementAdmin/afficheEvenementAdmin.html.twig', array(
            "evts" => $evt
        ));

    }



    public function traiterPropositionsEvtAction()
    {
        $em = $this->getDoctrine()->getManager();
        $evt = $em->getRepository("EvenementBundle:Evenement")->findBy(array('etat'=>1));

        return $this->render('admin/partial/EvenementAdmin/TraiterPropostitionsEvt.html.twig', array("evts" => $evt
        ));
    }


    public function accepterPropositionsEvtAction()
    {
        $id=$_GET['id'];
        $evt=$this->getDoctrine()->getRepository('EvenementBundle:Evenement')->find($id);
        $evt->setEtat(0);
        $em=$this->getDoctrine()->getManager();
        $em->persist($evt);
        $em->flush();
        return $this->redirectToRoute('admin_traiter_demande_events');
    }

    public function refuserPropositionsEvtAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id=$_GET['id'];
        $evt = $em->getRepository("EvenementBundle:Evenement")->find($id);
        $em->remove($evt);
        $em->flush();
        return $this->redirectToRoute('admin_traiter_demande_events');

    }

    public function participerEventAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id=$_GET['id'];
        $evt = $em->getRepository("EvenementBundle:Evenement")->find($id);

        $participant=$this->getUser();
        $evt->addParticipants($participant);
        $em->persist($evt);
        $em->flush();
        return $this->redirectToRoute('_afficher_events');
    }


    public function nePasParticiperEventAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id=$_GET['id'];
        $evt = $em->getRepository("EvenementBundle:Evenement")->find($id);

        $participant=$this->getUser();
        $evt->removeParticipants($participant);
        $em->persist($evt);
        $em->flush();
        return $this->redirectToRoute('_afficher_events');
    }


    public function afficherTriAction()
    {
        $em = $this->getDoctrine()->getManager();
        $evt = $em->getRepository("EvenementBundle:Evenement")->trier();
        return $this->render('@Evenement/Evenement/afficher.html.twig', array(
            "evts" => $evt
        ));
    }

    public function mapAction()
    {
        $coor=new Coordinate(36.8991287,10.1896075);

        $marker = new Marker($coor);
        $marker->setOption('draggable',true);
        $map = new Map();
        $map->getOverlayManager()->addMarker($marker);

        $map->getEventManager()->addEvent(new Event(
            $marker->getVariable(),
            'dragend',
            'function() {alert("Marker dragged!");}',
            true,
            $coor=$marker->getPosition()
        ));
        $coor=$marker->getPosition();

        $map->setMapOption('zoom',15);
        $map->setCenter($coor);
        /*$coor=new Coordinate(36.8983963,10.1875433);

        $map = new Map();
        $map->setCenter($coor);
        $map->setMapOption('zoom',15);
        $marker = new Marker($coor);
        // Configure your marker options
        //$marker->setPrefixJavascriptVariable('marker_');

        $map->getOverlayManager()->addMarker($marker);*/
        //$marker->setAnimat
        // Requests the ivory google map geocoder service
      //  $geocoder = $this->get('ivory.google_map.geocoder');

// Geocode an address
       // $request = new GeocoderAddressRequest('petite ariana, tunis, tunisia');
       // $response = $this->container->get('ivory.google_map.geocoder')->geocode($request);

// Get the result corresponding to your address
        //foreach($response->getResults() as $result) {
          //  var_dump($result->getGeometry()->getLocation());
       // }
        return $this->render('@Evenement/Evenement/map.html.twig',array("map"=>$map));
    }


    public function adminModifierAdresseEventAction(Request $request)
    {
        $id=$_GET['id'];
        $evts=$this->getDoctrine()->getManager()->getRepository('EvenementBundle:Evenement')->find($id);
        $form=$this->createForm(ModiferAdresseEvtType::class,$evts);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($evts);
            $em->flush();
            return $this->redirectToRoute('_admin_afficher_events');
        }
        return $this->render("admin/partial/EvenementAdmin/modifieradressevtadmin.html.twig",array('form'=>$form->createView()));
    }
    public function modifierAdresseEventAction(Request $request)
    {
        $id=$_GET['id'];
        $evts=$this->getDoctrine()->getManager()->getRepository('EvenementBundle:Evenement')->find($id);
        $form=$this->createForm(ModiferAdresseEvtType::class,$evts);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($evts);
            $em->flush();
            return $this->redirect('http://localhost/PIDEV-final/web/app_dev.php/Events_a/afficherunevent?id='.$id);
        }
        return $this->render("@Evenement/Evenement/modifieradressevt.html.twig",array('form'=>$form->createView()));
    }

    public function afficherParticipationsAction(){
        return $this->render("@Evenement/Evenement/afficherParticipations.html.twig");
    }


    // La partie mobile //

    public function afficherMobileAction(){



        $events = $this->getDoctrine()->getManager()->getRepository('EvenementBundle:Evenement')->findAll();
      /* var_dump($events);
        die();*/
        $normalizer = new ObjectNormalizer();
        //$normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($P_an) {
            return $P_an;
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $formatted = $serialzier->normalize($events);
        return new JsonResponse($formatted);


    }

    public function ajoutMobileAction(Request $request){
        $em=$this->getDoctrine()->getManager();
        $event = new Evenement();
        $event->setTitreE($request->get('titre'));
        $event->setDescE($request->get('desc'));
        $event->setDateE(\DateTime::createFromFormat("U", $request->get('date')) );
        $event->setDateModif(new \DateTime());
        $event->setEtat(1);
        $user = $em->getRepository('UserBundle:User')->find($request->get('idu'));
        $event->setIdUser($user);

        $event->setUsrRole($request->get('userrole'));
        $event->setTypeE($request->get('type'));
        $event->setImage($request->get('image'));
        $club = $em->getRepository('ClubBundle:Club')->find(1);
        $event->setIdClub($club);
        $event->setGooglemaps($request->get('map'));
        $em->persist($event);
        $em->flush();

       /* $serializer = new Serializer([new ObjectNormalizer()]);

        $formatted= $serializer->normalize($event);
        return new JsonResponse($formatted);*/
       return new Response('succes');

    }

    public function modifierMobileAction(Request $request){
        $em=$this->getDoctrine()->getManager();
        $event = $em->getRepository('EvenementBundle:Evenement')->find($request->get('id'));
        $event->setTitreE($request->get('titre'));
        $event->setDescE($request->get('desc'));
        $event->setDateE(\DateTime::createFromFormat("U", $request->get('date')) );
        $event->setDateModif(new \DateTime());

        $event->setTypeE($request->get('type'));
        $event->setImage($request->get('image'));

        $event->setGooglemaps($request->get('map'));
        $em->persist($event);
        $em->flush();

        /* $serializer = new Serializer([new ObjectNormalizer()]);

         $formatted= $serializer->normalize($event);
         return new JsonResponse($formatted);*/
        return new Response('succes');

    }

    public function supprimerMobileAction(Request $request){
        $em=$this->getDoctrine()->getManager();
        $event= $em->getRepository('EvenementBundle:Evenement')->find($request->get('id'));
        $event->setEtat(3);
        $em->persist($event);
        $em->flush();
        return new Response('succes');
    }

    public function participerMobileAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        $event= $em->getRepository('EvenementBundle:Evenement')->find($request->get('id'));
        $user= $em->getRepository('UserBundle:User')->find($request->get('idu'));
        $event->addParticipants($user);
        $em->persist($event);
        $em->flush();
        return new Response('succes');
    }
    public function noParticiperMobileAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        $event= $em->getRepository('EvenementBundle:Evenement')->find($request->get('id'));
        $user= $em->getRepository('UserBundle:User')->find($request->get('idu'));
        $event->removeParticipants($user);
        $em->persist($event);
        $em->flush();
        return new Response('succes');
    }

    public function checkParticipationMobileAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        $event= $em->getRepository('EvenementBundle:Evenement')->find($request->get('id'));
        $user= $em->getRepository('UserBundle:User')->find($request->get('idu'));
        $res= $event->checkParticipant($user);
        if($res){return new Response('succes');}
        else return new Response('no');
    }
    public function countAction(Request $request){
        $em= $this->getDoctrine()->getManager();
        $event= $em->getRepository('EvenementBundle:Evenement')->find($request->get('id'));
        $res= $event->countParticipant();
        if($res){return new Response($res);}
        else return new Response('0');
    }
}
