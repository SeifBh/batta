<?php

namespace EspritEntreAide\ClubBundle\Controller;


use EspritEntreAide\ClubBundle\Entity\Club;
use EspritEntreAide\ClubBundle\Form\ClubType;
use EspritEntreAide\ClubBundle\Form\Recherchetype;
use Proxies\__CG__\EspritEntreAide\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Tests\Mapping\MemberMetadataTest;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
class gererclubController extends Controller
{
    public function AjoutAction(Request $request)
    {
        $cl = new Club();
        $form = $this->createForm(ClubType::class, $cl);
        $form->handleRequest($request);/*creation d'une session pr stocker les valeurs de l'input*/
        if ($form->isValid()) {
            $cl->setNbmemb(1);

            $em = $this->getDoctrine()->getManager();

            $u=$form->get('idUser')->getData();
            $cl->setIdUser($u->getId());
            $em->persist($cl);



            $em->flush();
        }
        return $this->render('admin/partial/ClubAdmin/ajoutClubAdmin.html.twig', array(
            'form' => $form->createView()
        ));
    }





    public function AfficheAction(Request $request)
    {
        $pclub = new Club();
        $cl = $this->getDoctrine()->getManager();
        $club = $cl->getRepository("ClubBundle:Club")->findAll();
        $form=$this->createForm(Recherchetype::class,$pclub);
        $form->handleRequest($request);

        if($request->isXmlHttpRequest() ){
            $ser=$request->get('s');
            $club=$cl->getRepository("ClubBundle:Club")->rechercheAjax($ser);

            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(2);
            $normalizer->setCircularReferenceHandler(function ($pclub) {
                return $pclub->getId();
            });
            $normalizers = array($normalizer);
            $serialzier = new Serializer(array($normalizer));
            $v = $serialzier->normalize($club);
            return new JsonResponse($v);
        }
        else {
            $club=$cl->getRepository("ClubBundle:Club")->findAll();
        }
        return $this->render('@Club/Default/afficheclub.html.twig', array(
            "club" => $club,
            "form" => $form->createView()
            // ...
        ));


    }

    public function  modifClubAction(Request $request,$id)
    {

        $clubs = $this->getDoctrine()->getManager()
            ->getRepository('ClubBundle:Club')
            ->find($id);
        $em = $this->getDoctrine()->getManager();
        $clubs->setNomC($request->get('nomc'));

        $clubs->setIdUser($request->get('iduser'));
        $clubs->setMailC($request->get('mailc'));
        $clubs->setDescC($request->get('descc'));
        $clubs->setImage($request->get('image'));

        $em->persist($clubs);
        $em->flush();
        return new JsonResponse("Modification terminé avec succes");


    }


    public function AllAction()
    {

        $club = $this->getDoctrine()->getManager();
        $con = $club->getConnection();
        $statement = $con->prepare("SELECT cl.*,u.username from club cl,user u where cl.id_user=u.id");
        $statement->execute();


        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($statement->fetchAll());
        return new JsonResponse($formatted);
    }


    public function findAction($id)
    {
        $clubs = $this->getDoctrine()->getManager()
            ->getRepository('ClubBundle:Club')
            ->find($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($clubs);
        return new JsonResponse($formatted);
    }


    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $club = new Club();
        $club->setIdUser($request->get('iduser'));
        $club->setMailC($request->get('mailc'));
        $club->setNomC($request->get('nomc'));
        $club->setDescC($request->get('descc'));
        $club->setDateCreation($request->get('date')) ;
        $club->setImage($request->get('img'));
       // $club->setMembres($request->get('iduser'));
        $club->setNbmemb($request->get('nbmemb'));

        $em->persist($club);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($club);
        return new JsonResponse($formatted);
    }




    public function ModifierAction(Request $request)
    {

        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $cl = $em->getRepository(club::class)->find($id);
        $form = $this->createForm(ClubType::class, $cl);
        $form->handleRequest($request);

        if($form->isValid()){
            $u=$form->get('idUser')->getData();
            $cl->setIdUser($u->getId());
            $em->persist($cl);

            $em->flush();
            return $this->redirectToRoute('club_affiche');
        }

        return $this->render('@Club/Default/modifclub.html.twig',array('form'=>$form->createView()));
    }

    public function ModifierAdminAction(Request $request)
    {

        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $cl = $em->getRepository(club::class)->find($id);
        $form = $this->createForm(ClubType::class, $cl);
        $form->handleRequest($request);

        if($form->isValid()){
            $u=$form->get('idUser')->getData();
            $cl->setIdUser($u->getId());
            $em->persist($cl);

            $em->flush();
            return $this->redirectToRoute('afficheadmin');
        }

        return $this->render('admin/partial/ClubAdmin/modifClubAdmin.html.twig', array('form'=>$form->createView()));
    }





    public function ajouterMembresAction(Request $request)
    {

        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $cl = $em->getRepository("ClubBundle:Club")->find($id);
        $cl->setNbmemb($cl->getNbmemb()+1);
        $Membres=$this->getUser();
        $cl->addMembres($Membres);
        $em->persist($cl);
        $em->flush();
        return $this->redirectToRoute('club_affiche');
    }



    public function ajouterMembresmobileAction(Request $request)
    {

        $cid = $request->get('id');
        $uid = $request->get('idu');

        $em = $this->getDoctrine()->getManager();
        $cl = $em->getRepository("ClubBundle:Club")->find($cid);
        $mem = $em->getRepository("UserBundle:User")->find($uid);
        $cl->setNbmemb($cl->getNbmemb()+1);
        $cl->addMembres($mem);
        $em->persist($cl);
        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($cl)
        {
            return $cl->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $v = $serialzier->normalize($cl);
        return new JsonResponse($v);
        //  $serializer = new Serializer([new ObjectNormalizer()]);
       // $formatted = $serializer->normalize($cl);
        //return new JsonResponse($formatted);
    }











//lazmek t3adi l id ta3 l club w ta3 l user seulement
    public function retirerMembresMobileAction(Request $request)
    {
        $cid = $request->get('id');
        $uid = $request->get('idu');

        $em = $this->getDoctrine()->getManager();
        $cl = $em->getRepository("ClubBundle:Club")->find($cid);
        $mem = $em->getRepository("UserBundle:User")->find($uid);
        $cl->setNbmemb($cl->getNbmemb()-1);
        $cl->removeMembres($mem);
        $em->persist($cl);
        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($cl)
        {
            return $cl->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $v = $serialzier->normalize($cl);
        return new JsonResponse($v);
    }










    public function retirerMembresAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $cl = $em->getRepository("ClubBundle:Club")->find($id);
        $cl->setNbmemb($cl->getNbmemb()-1);

        $Membres=$this->getUser();
        $cl->removeMembres($Membres);
        $em->persist($cl);
        $em->flush();
        return $this->redirectToRoute('club_affiche');
    }



    public function AfficheAdminAction()
    {
        $cl = $this->getDoctrine()->getManager();
        $club = $cl->getRepository("ClubBundle:Club")->findAll();
        return $this->render('admin/partial/ClubAdmin/afficheClubAdmin.html.twig', array(
            "club" => $club
            // ...
        ));
    }


    function DeleteAction(Request $request,$id){
        $cl=$this->getDoctrine()->getManager();
        $club=$cl->getRepository("ClubBundle:Club")->find($id);
        $form=$this->createForm(ClubType::class,$club);
        $form->handleRequest($request);
        $cl->remove($club);
        $cl->flush();
        return $this->redirectToRoute('club_affiche');
    }




    function DeleteAdminAction(Request $request,$id){
        $cl=$this->getDoctrine()->getManager();
        $club=$cl->getRepository("ClubBundle:Club")->find($id);
        $form=$this->createForm(ClubType::class,$club);
        $form->handleRequest($request);
        $cl->remove($club);
        $cl->flush();
        return $this->redirectToRoute('afficheadmin');
    }

    public function SuppClubAction(Request $request,$id){
        $cl=new Club();

        $em = $this->getDoctrine()->getManager();

        $club = $em->getRepository("ClubBundle:Club")->find($id);

        $em->remove($club);

        $em->flush();

        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($pub);*/
        return new JsonResponse("Club supprimé avec succes");

    }




    public function checkMembreMobileAction(Request $request){
        $em=$this->getDoctrine()->getManager();
        $cl=$em->getRepository('ClubBundle:Club')->find($request->get('idc'));
        $us=$em->getRepository('UserBundle:User')->find($request->get('idu'));
        $var=$cl->checkMembre($us);
        if($var){return new Response('succes');}
        else return new Response('no');


    }

}
