<?php

namespace EspritEntreAide\AnnonceBundle\Controller;

use EspritEntreAide\AnnonceBundle\Entity\Annonce;
use EspritEntreAide\AnnonceBundle\Form\AnnonceType;
use EspritEntreAide\AnnonceBundle\Form\ModifierAnnonceType;
use EspritEntreAide\AnnonceBundle\Form\ChercherAnnonceType;
use EspritEntreAide\AnnonceBundle\Form\ReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $an = $em->getRepository("AnnonceBundle:Annonce")->findAll();


        $an1 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("covoiturage");
        $an2 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("collocation");
        $an3 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("ObjetTrouve");
        $an4 = $em->getRepository("AnnonceBundle:Annonce")->countAnnonce("ObjetPerdu");





        return $this->render('AnnonceBundle:Default:index.html.twig', array(
            "annonce" => $an,"a1" => $an1,"a2"=>$an2,"a3"=>$an3,"a4"=>$an4
       ));
    }

    public function afficheAction()
    {
        $em = $this->getDoctrine()->getManager();
        $an = $em->getRepository("AnnonceBundle:Annonce")->findAll();
        return $this->render('AnnonceBundle::AfficherAnnonce.html.twig', array(
            "annonce" => $an
        ));

    }

    public function ajouterAnnonceAction(Request $request)
    {
        $an = new Annonce();
        $form = $this->createForm(AnnonceType::class, $an);
        $form->handleRequest($request);//creation d'une session pr stocker les valeurs de l'input
        if ($form->isValid()) {
            $an->setIdUser($this->getUser());
            $an->setDateA(new \DateTime());
            $an->setEtat("nonResolue");
            $em = $this->getDoctrine()->getManager();
            $em->persist($an);
            $em->flush();
            return $this->redirectToRoute('annonce_homepage');
        }
        return $this->render('AnnonceBundle::AjouterAnnonce.html.twig', array(
            'form' => $form->createView()
        ));
    }



    function chercherAnnonceAction(Request $request)
    {
        $an=new Annonce();
        $em=$this->getDoctrine()->getManager();
        $form=$this->createForm(ChercherAnnonceType::class,$an);
        $form->handleRequest($request);/*creation d'une session pr stocker les valeurs de l'input*/
        if($request->isXmlHttpRequest() ){
            $ser=$request->get('s');

            $far=$em->getRepository("AnnonceBundle:Annonce")->rechercheAjax($ser);

            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(2);
            $normalizer->setCircularReferenceHandler(function ($an) {
                return $an->getId();
            });
            $normalizers = array($normalizer);
            $serialzier = new Serializer(array($normalizer));
            $v = $serialzier->normalize($far);

            return new JsonResponse($v);

        }
        else{
            $far=$em->getRepository("AnnonceBundle:Annonce")->findAll();

        }

        if($form->isValid()){
            if (($an->getDateA()!="") and ($an->getTitreA()=="")){
                $em=$this->getDoctrine()->getManager();

                 $far=$em->getRepository("AnnonceBundle:Annonce")->findBy(array("dateA"=>$an->getDateA()));
                 return $this->render('AnnonceBundle::ChercherAnnonce.html.twig',array(
                    'form'=>$form->createView(),'annonce'=>$far));
            }

            if (($an->getTitreA()!="") and ($an->getDateA()=="")){
                $em=$this->getDoctrine()->getManager();

                $far=$em->getRepository("AnnonceBundle:Annonce")->findBy(array("titreA"=>$an->getTitreA()));
                return $this->render('AnnonceBundle::ChercherAnnonce.html.twig',array(
                    'form'=>$form->createView(),'annonce'=>$far));
            }


            if (($an->getTitreA()!="") and ($an->getDateA()!="")){
                $em=$this->getDoctrine()->getManager();

                //return new Response("titre no vide");
                $far=$em->getRepository("AnnonceBundle:Annonce")->findBy(array("titreA"=>$an->getTitreA(),"dateA"=>$an->getDateA()));
                return $this->render('AnnonceBundle::ChercherAnnonce.html.twig',array(
                    'form'=>$form->createView(),'annonce'=>$far));
            }


        }
        $far=$em->getRepository("AnnonceBundle:Annonce")->findAll();

        return $this->render('AnnonceBundle::ChercherAnnonce.html.twig',array(
            'form'=>$form->createView(),'annonce'=>$far));


    }

    function ChercherSansAjaxAction(Request $request)
    {
        $an=new Annonce();
        $em=$this->getDoctrine()->getManager();
        $form=$this->createForm(ChercherAnnonceType::class,$an);
        $form->handleRequest($request);/*creation d'une session pr stocker les valeurs de l'input*/
        if($form->isValid()){

            $an=$em->getRepository("AnnonceBundle:Annonce")->findBy(array('titreA'=>$an->getTitreA()));

        }else{
            $an=$em->getRepository("AnnonceBundle:Annonce")->findAll();

        }

        return $this->render('AnnonceBundle::ChercherAnnonceSansAjax.html.twig',array(
            'form'=>$form->createView(),'annonce'=>$an
        ));

    }


    function supprimerAnnonceAction(Request $request,$id)
    {

        $an=$this->getDoctrine()->getManager();
        $annonce=$an->getRepository("AnnonceBundle:Annonce")->find($id);
        if($this->getUser() == $annonce->getIdUser() )
        {
            $form=$this->createForm(AnnonceType::class,$annonce);
            $form->handleRequest($request);
            $an->remove($annonce);
            $an->flush();
        }

        return $this->redirectToRoute('_annonce_chercher1');


    }


    public function modifierAnnonceAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $an = $em->getRepository(Annonce::class)->find($id);
        if($this->getUser() == $an->getIdUser() )
        {
          $an->setDateModif();
          $form = $this->createForm(ModifierAnnonceType::class, $an);
          $form->handleRequest($request);



           //Save?
           if($form->isValid())
           {
             $em->persist($an);
             $em->flush();
             return $this->redirectToRoute('_annonce_chercher1');
           }

        // Recuperation des donnees
        //Remplir form
        return $this->render('AnnonceBundle::ModifierAnnonce.html.twig',array('form'=>$form->createView()));
        // .annonce..
        }
        return $this->redirectToRoute('_annonce_chercher1');


    }
/**************************************************************************/

    public function ajouterAnnonceAdminAction(Request $request)
    {
        $an = new Annonce();
        $form = $this->createForm(AnnonceType::class, $an);
        $form->handleRequest($request);//creation d'une session pr stocker les valeurs de l'input
        if ($form->isValid()) {
            $an->setIdUser($this->getUser());
            $an->setDateA(new \DateTime());
            $an->setEtat("nonResolue");
            $em = $this->getDoctrine()->getManager();
            $em->persist($an);
            $em->flush();
            return $this->redirectToRoute('_annonce_ajouter_admin');
        }
        return $this->render('admin/partial/AnnonceAdmin/ajouterAnnonceAdmin.html.twig', array(
            'form' => $form->createView()
        ));
    }



    public function indexAdminAction(Request $request)
    {
        $an = new Annonce();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ChercherAnnonceType::class, $an);
        $form->handleRequest($request);/*creation d'une session pr stocker les valeurs de l'input*/
        if ($form->isValid()) {

            $an = $em->getRepository("AnnonceBundle:Annonce")->findBy(array('titreA' => $an->getTitreA()));

        } else {
            $an = $em->getRepository("AnnonceBundle:Annonce")->findAll();

        }

        return $this->render('admin/partial/AnnonceAdmin/afficheAnnonceAdmin.html.twig', array(
            'form' => $form->createView(), 'annonce' => $an
        ));
    }


    function supprimerAnnonceAdminAction(Request $request,$id)
    {

        $an=$this->getDoctrine()->getManager();
        $annonce=$an->getRepository("AnnonceBundle:Annonce")->find($id);
        if($this->getUser() == $annonce->getIdUser() )
        {
            $form=$this->createForm(AnnonceType::class,$annonce);
            $form->handleRequest($request);
            $an->remove($annonce);
            $an->flush();
        }

        return $this->redirectToRoute('annonce_homepage_admin');


    }

    public function modifierAnnonceAdminAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $an = $em->getRepository(Annonce::class)->find($id);


            $an->setDateModif();
            $form = $this->createForm(ModifierAnnonceType::class, $an);
            $form->handleRequest($request);



            //Save?
            if($form->isValid())
            {
                $em->persist($an);
                $em->flush();
                return $this->redirectToRoute('annonce_homepage_admin');
            }

            // Recuperation des donnees
            //Remplir form
            return $this->render('admin/partial/AnnonceAdmin/modifierAnnonceAdmin.html.twig',array('form'=>$form->createView()));
            // .annonce..
        return $this->redirectToRoute('annonce_homepage_admin');


    }

}
