<?php

namespace EspritEntreAide\StoreBundle\Controller;

use EspritEntreAide\StoreBundle\Entity\Store;
use EspritEntreAide\StoreBundle\Form\StoreType;
use Ivory\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class GererStoreController extends Controller
{

    public function indexAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $stores=$em->getRepository("StoreBundle:Store")->findAll();
        $paginator  = $this->get('knp_paginator');
        $stores = $paginator->paginate(
            $stores, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render("@Store/Default/index.html.twig",array(
            'stores'=>$stores
        ));
    }

    public function AjoutStoreAction(Request $request)
    {
        $st = new Store();
        $form = $this->createForm(StoreType::class, $st);
        $form->handleRequest($request);/*creation d'une session pr stocker les valeurs de l'input*/
        $em = $this->getDoctrine()->getManager();

        $listStoreResult=$em->getRepository("StoreBundle:Store")->createQueryBuilder("s")
            ->select("s.id")
            ->getQuery()
            ->getResult()
        ;
        $listStore=array();
        foreach ($listStoreResult as $l){
            array_push($listStore,$l['id']);
        }
        $listUsers=$em->getRepository("UserBundle:User")->createQueryBuilder("u")
            ->innerJoin("StoreBundle:store","s")
            ->where("u.roles like '%ROLE_RESPONSABLE_STORE%'")

            ->getQuery()
            ->getResult()
        ;
        if ($form->isValid()) {
            $storeUser=$em->getRepository("UserBundle:User")->find($request->request->get('storeUser'));
            $st->setUser($storeUser);
            $em->persist($st);
            $em->flush();
            $this->addFlash('success', 'Store ajouté avec succès');
        }
        return $this->render('@Store/Default/ajoutStore.html.twig', array(
            'form' => $form->createView(),
            'users'=>$listUsers
        ));
    }

    public function removeAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $store=$em->getRepository("StoreBundle:Store")->find($id);
        if ($store!=null){
            $em->remove($store);
            $em->flush();
        }
        return $this->redirectToRoute("store_homepage");
    }

    public function editAction($id,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $store=$em->getRepository("StoreBundle:Store")->find($id);
        $form=$this->createForm(StoreType::class,$store);
        $form->handleRequest($request);
        if ($request->isMethod("POST")){
            $em->flush();
        }

        return $this->render("StoreBundle:Default:editStore.html.twig",array(
           'form'=>$form->createView()
        ));
    }

    public function searchAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $storeName=$request->request->get('storeName');
        $storeManager=$request->request->get('storeManager');

        $stores=$em->getRepository("StoreBundle:Store")->createQueryBuilder("s")
            ->select("s.nomStore,s.id")
            ->innerJoin("s.user","u")
            ->where("s.nomStore like '%$storeName%'")
            ->AndWhere("u.username like '%$storeManager%'")
            ->getQuery()
            ->getResult()
        ;

        return new JsonResponse($stores);

    }

    //mobile
    public function AfficherStoreAction()
    {
        $stores = $this->getDoctrine()->getManager()
            ->getRepository("StoreBundle:Store")
            ->findAll();
        //var_dump($stores);
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($pstore) {
            return $pstore->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new \Symfony\Component\Serializer\Serializer($normalizers);
        $v = $serialzier->normalize($stores);
        return new JsonResponse($v);

    }

}
