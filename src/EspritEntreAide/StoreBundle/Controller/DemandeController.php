<?php

namespace EspritEntreAide\StoreBundle\Controller;

use EspritEntreAide\StoreBundle\Entity\Demande;
use EspritEntreAide\StoreBundle\Entity\Document;
use EspritEntreAide\StoreBundle\Entity\Store;
use EspritEntreAide\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DemandeController extends Controller{

    public function indexAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $demandes=$em->getRepository("StoreBundle:Demande")->findAll();
        $paginator  = $this->get('knp_paginator');
        $demandes = $paginator->paginate(
            $demandes, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render('StoreBundle:Demande:index.html.twig',array(
            'demandes'=>$demandes
        ));
    }

    public function indexStoreAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $demandes=$em->getRepository("StoreBundle:Demande")->findBy(array("idStore"=>$this->getUser()->getStore()));
        $countdemandes=$em->getRepository("StoreBundle:Demande")->createQueryBuilder("d")
            ->where("d.etatDemande = 'En attente'")
            ->getQuery()
            ->getResult()
        ;
        $countdemandes=count($countdemandes);
        $paginator  = $this->get('knp_paginator');
        $demandes = $paginator->paginate(
            $demandes, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render('StoreBundle:Demande:indexStore.html.twig',array(
            'demandes'=>$demandes,
            'countDemandes'=>$countdemandes
        ));
    }

    public function indexTeacherAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $demandes=$em->getRepository("StoreBundle:Demande")->findby(array("idUser"=>$this->getUser()));
        $paginator  = $this->get('knp_paginator');
        $demandes = $paginator->paginate(
            $demandes, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render('StoreBundle:Demande:indexTeacher.html.twig',array(
            'demandes'=>$demandes
        ));
    }

    public function newAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();

        $demande=new Demande();
        $document=new Document();
        $form=$this->createForm("EspritEntreAide\StoreBundle\Form\DemandeType",$demande);
        $formDocument=$this->createForm("EspritEntreAide\StoreBundle\Form\DocumentType",$document);
        $form->handleRequest($request);
        $formDocument->handleRequest($request);
        if ($request->isMethod("POST")){
            $documentArray=array();
            foreach ($document->getFile() as $f){
                $document=new Document();
                $document->setFilefileFile($f);
                $em->persist($document);
                $em->flush();
                array_push($documentArray,$document);
            }

            $demande->setDocument($documentArray);

                $demande->setIdUser($this->getUser());
                $em->persist($demande);

            $em->flush();
            $this->addFlash('success', 'Demande ajoutée avec succés');
            return $this->redirectToRoute("demande_new");
        }

        return $this->render('StoreBundle:Demande:new.html.twig',array(
            'form'=>$form->createView(),
            'formDocument'=>$formDocument->createView()
        ));
    }

    public function newAdminAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $demande=new Demande();
        $document=new Document();
        $form=$this->createForm("EspritEntreAide\StoreBundle\Form\DemandeType",$demande);
        $formDocument=$this->createForm("EspritEntreAide\StoreBundle\Form\DocumentType",$document);
        $form->handleRequest($request);
        $formDocument->handleRequest($request);
        if ($request->isMethod("POST")){
            $documentArray=array();
            foreach ($document->getFile() as $f){
                $document=new Document();
                $document->setFilefileFile($f);
                $em->persist($document);
                $em->flush();
                array_push($documentArray,$document);
            }

            $demande->setDocument($documentArray);

            $demande->setIdUser($this->getUser());
            $em->persist($demande);

            $em->flush();
            $this->addFlash('success', 'Demande ajoutée avec succés');
            return $this->redirectToRoute("demande_new_admin");
        }

        return $this->render('StoreBundle:Demande:newAdmin.html.twig',array(
            'form'=>$form->createView(),
            'formDocument'=>$formDocument->createView()
        ));
    }

    public function removeAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $demande=$em->getRepository("StoreBundle:Demande")->find($id);
        if ($demande!=null){
            $em->remove($demande);
            $em->flush();
        }

        return $this->redirectToRoute("demande_index");
    }

    public function adminSearchAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $date=$request->request->get('date');
        $store=$request->request->get('store');
        $teacher=$request->request->get('teacher');

        $demande=$em->getRepository("StoreBundle:Demande")->createQueryBuilder("d")
            ->select("d.nbrCopie,d.id,d.dateCreation,s.nomStore,u.username,d.etatDemande")
            ->innerJoin("d.idUser","u")
            ->innerJoin("d.idStore","s")
            ->where("s.nomStore like '%$store%'")
            ->AndWhere("u.username like '%$teacher%'")
            ->AndWhere("d.dateCreation like '%$date%'")
            ->getQuery()
            ->getResult()
        ;

        return new JsonResponse($demande);
    }

    public function storeSearchAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $date=$request->request->get('date');
        $teacher=$request->request->get('teacher');
        $store=$em->getRepository("StoreBundle:Store")->findOneBy(array("user"=>$this->getUser()));
        $store=$store->getId();
        $demande=$em->getRepository("StoreBundle:Demande")->createQueryBuilder("d")
            ->select("d.nbrCopie,d.id,d.dateCreation,s.nomStore,u.username,d.etatDemande")
            ->innerJoin("d.idUser","u")
            ->innerJoin("d.idStore","s")
            ->where("s.id = :storeId ")
            ->AndWhere("u.username like '%$teacher%'")
            ->AndWhere("d.dateCreation like '%$date%'")
            ->setParameter("storeId",$store)
            ->getQuery()
            ->getResult()
        ;

        return new JsonResponse($demande);
    }

    public function teacherSearchAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $date=$request->request->get('date');
        $store=$request->request->get('store');
        $teacherId=$this->getUser()->getId();
        $demande=$em->getRepository("StoreBundle:Demande")->createQueryBuilder("d")
            ->select("d.nbrCopie,d.id,d.dateCreation,s.nomStore,u.username")
            ->innerJoin("d.idUser","u")
            ->innerJoin("d.idStore","s")
            ->where("s.nomStore like '%$store%'")
            ->AndWhere("d.dateCreation like '%$date%'")
            ->AndWhere("u.id= :teacherId ")
            ->setParameter("teacherId",$teacherId)
            ->getQuery()
            ->getResult()
        ;

        return new JsonResponse($demande);
    }

    public function changeEtatAction($id,$etat)
    {
        $em=$this->getDoctrine()->getManager();
        $demande=$em->getRepository("StoreBundle:Demande")->find($id);
        $demande->setEtatDemande($etat);
        $em->flush();
        if($etat=="Prête"){
            $this->addFlash('success', "Un mail de confirmation a été envoyé à l'enseignant");
            $message = (new \Swift_Message('Demande prête'))
                ->setFrom('chaari.achref@gmail.com')
                ->setTo($demande->getIdUser()->getEmail())
                ->setBody(
                    "Votre demande est prête. Vous pouvez venir la récupérer"
                );
            $this->get('mailer')->send($message);
        }


        return $this->redirectToRoute("demande_index_store");
    }

    public function detailAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $demande=$em->getRepository("StoreBundle:Demande")->find($id);

        return $this->render("@Store/Demande/detail.html.twig",array(
            'demande'=>$demande
        ));
    }
//mobile
    public function AfficherAction()
    {
        $demandes = $this->getDoctrine()->getManager()
            ->getRepository("StoreBundle:Demande")
            ->findAll();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($pdemande) {
            return $pdemande->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $v = $serialzier->normalize($demandes);
        return new JsonResponse($v);

    }

    public function AjoutMobileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $demande = new Demande();
        $us = $request->get('iduser');
        $usr = $em->getRepository('EspritEntreAide\UserBundle\Entity\User')->find($us);
        $st = $request->get('idstore');
        $str = $em->getRepository('EspritEntreAide\StoreBundle\Entity\Store')->find($st);



        $demande->setIdUser($usr);
        $demande->setIdStore($str);
        $demande->setNbrCopie($request->get('nbrcopie'));
        $demande->setDeadline(\DateTime::createFromFormat('U',$request->get('deadline')));
        $demande->setDateCreation(new \DateTime());
        $demande->setEtatDemande("En attente");
        $doc = new Document();
        $doc->setFile($request->get('document'));
        $doc->setNomDoc($request->get('document'));
        $em->persist($doc);
        $em->flush();
        $demande->addDocument($doc);
        $em->persist($demande);
        $em->flush();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($pclub) {
            return $pclub->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $v = $serialzier->normalize($demande);
        return new JsonResponse($v);
    }

    public function ModifierEtatAction(Request $request){
        $demande = $this->getDoctrine()->getManager()->getRepository('StoreBundle:Demande')->find($request->get('id'));
        $em = $this->getDoctrine()->getManager();

        $em->persist($demande);
        $em->flush();

        return new JsonResponse("Modification terminée avec succes");
    }


}