<?php

namespace EspritEntreAide\SpottedBundle\Controller;

use Doctrine\ORM\Mapping\Id;
use EspritEntreAide\SpottedBundle\Entity\Rating;
use EspritEntreAide\SpottedBundle\Form\PublicationImageType;
use EspritEntreAide\SpottedBundle\Repository;
use EspritEntreAide\SpottedBundle\Entity\Commentaire;
use EspritEntreAide\SpottedBundle\Entity\Publication;
use EspritEntreAide\SpottedBundle\Form\CommentaireType;
use EspritEntreAide\SpottedBundle\Form\PublicationType;
use EspritEntreAide\SpottedBundle\Form\RechercheType;
use EspritEntreAide\SpottedBundle\SpottedBundle;
use EspritEntreAide\UserBundle\Entity\User;
use FOS\UserBundle\FOSUserBundle;
use FOS\UserBundle\Model\User as Seif;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{

    // =============================== Front Office ====================================
    public function indexAction()
    {
        $publication = new Publication();
        $rating = new Rating();
        $em=$this->getDoctrine()->getManager();


        $rankingpub = $em->getRepository("SpottedBundle:Rating",$rating)->rankingspotted();



        return $this->render(":default:index.html.twig",array(
            'rankingpub'=>$rankingpub
        ));

    }

    public function ChooseTypeAction()
    {
        return $this->render("@Spotted/CRUD/type_pub.html.twig",array());

    }
    public function fooAction()
    {

        return new Response("abcdfd");
    }
    public  function anonymeImageRatingAction(Request $request,$entity){
        $em= $this->getDoctrine()->getManager();
        var_dump($entity);
        $current_user_id_bySeif = $this->getUser()->getId();
        $rating = new Rating();
        $rating->setIdUser($this->getUser());
        //return new Response($publication->getId());
        $rating->setIdPublication(34);
        $em->persist($rating);
        $em->flush();

        $em2 = $this->getDoctrine()->getManager();
        return $this->redirect($request->getUri());



    }
    public function anonymeAction(Request $request, $id)
    {

        $publication = new Publication();
        $rating = new Rating();

        $em=$this->getDoctrine()->getManager();

        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array('id'=>$id));
        $lst = $em->getRepository("SpottedBundle:Publication",$publication)->findOneBy(['id' => $id]);

        $commentaire = new Commentaire();
        $em=$this->getDoctrine()->getManager();

        $lstcomments = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->findBy(array('idPublication'=>$id));


        $listeRate = $em->getRepository("SpottedBundle:Rating",$rating)->findOneBy([
            'idPublication' => $id,


            'idUser' => $this->getUser()->getId(),
        ]);
        //var_dump($listeRate);
        //return new Response($this->getUser()->getId());
        //return new Response($this->getUser()->getId());
        $commentaire = new Commentaire();
        $em= $this->getDoctrine()->getManager();
        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\CommentaireType",$commentaire);
        $form->handleRequest($request);
        $form6=$this->createForm("EspritEntreAide\SpottedBundle\Form\Rating2Type",$rating);
        $form6->handleRequest($request);
        if ($form6->isSubmitted())
        {


            $current_user_id_bySeif = $this->getUser()->getId();

            $rating->setIdUser($this->getUser());


            $spot = $em->getRepository("SpottedBundle:Publication",$publication)->findOneBy(array('id'=>$rating->getIdPublication()));

            $rating->setIdPublication($spot);
            $em->persist($rating);
            $em->flush();


            return $this->redirect($request->getUri());


        }

        if ($form->isValid())
        {
            $commentaire->setIdUser($this->getUser());
            $commentaire->setIdPublication($lst);


            $commentaire->setDateModif(new \DateTime());

            $em->persist($commentaire);
            $em->flush();

            $em2 = $this->getDoctrine()->getManager();
            return $this->redirect($request->getUri());


        }





        return $this->render("@Spotted/Default/anonyme_pub.html.twig",array(
            'listspotted'=>$listspotted,
            'form'=>$form->createView(),
            'form6'=>$form6->createView(),
            'listerate'=>$listeRate,
            'ido'=>$id,
            'comments'=>$lstcomments
        ));

    }
    public function Aff2Action()
    {

        $em=$this->getDoctrine()->getManager();
        $cb=$em->getRepository(\EspritEntreAide\SpottedBundle\Repository\CommentaireRepository::class);

        return $this->render('@MyAppBanque/Banque/cb.html.twig',array(
            'cb'=>$cb
        ));
    }

    public function anonymeImageAction(Request $request)
    {
        $error_taken = null;
        $publication = new Publication();
        $commentaire = new Commentaire();
        $rating = new Rating();
        $em= $this->getDoctrine()->getManager();


        $countcommentstable = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->countComments();




        $nbreimage = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublicationSecrete($this->getUser()->getId());
        $nbrepublication = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublicationImage($this->getUser()->getId());

        $form_recherche_ajax=$this->createForm("EspritEntreAide\SpottedBundle\Form\RechercheSpottedType",$publication);
        $form_recherche_ajax->handleRequest($request);

        $form_ajout_publication_secrete=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationType",$publication);
        $form_ajout_publication_secrete->handleRequest($request);

        $form_ajout_publication_image=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationImageType",$publication);
        $form_ajout_publication_image->handleRequest($request);

        $form_filter_categorie=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationFilterType",$publication);
        $form_filter_categorie->handleRequest($request);

        $listeRate = $em->getRepository("SpottedBundle:Rating",$rating)->findAll();


        $nbre_publication = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublication();



        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array(), array('id' => 'DESC'));


        if ($form_filter_categorie->isSubmitted())
        {
            if ($form_filter_categorie->getClickedButton()->getName() === "All"){

                //return new Response("All");
                $publication = new Publication();
                $em= $this->getDoctrine()->getManager();
                $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array(), array('id' => 'DESC'));
                $nbre_publication = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublication();

                return $this->render("@Spotted/Default/anonyme_publication_image.html.twig",array(
                    'listspotted'=>$listspotted,
                    'form_recherche_ajax'=>$form_recherche_ajax->createView(),
                    'form_ajout_publication_secrete'=>$form_ajout_publication_secrete->createView(),
                    'form_ajout_publication_image'=>$form_ajout_publication_image->createView(),
                    'form_filter_categorie'=>$form_filter_categorie->createView(),
                    'error_taken'=>$error_taken,
                    'nbr_publication'=>$nbrepublication,
                    'nbr_image'=>$nbreimage,
                    'listerate'=>$listeRate,
                    'countcommentstable'=>$countcommentstable,

                    'nbre_publication'=>$nbre_publication

                ));

            }
            else if ($form_filter_categorie->getClickedButton()->getName() === "publicationsecrete"){
                //return new Response("Publication secrete");
                $vide = " ";
                $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array('image'=>NULL),array('id' => 'DESC'));
                $nbre_publication = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublicationSecrete2();

                return $this->render("@Spotted/Default/anonyme_publication_image.html.twig",array(
                    'listspotted'=>$listspotted,
                    'form_recherche_ajax'=>$form_recherche_ajax->createView(),
                    'form_ajout_publication_secrete'=>$form_ajout_publication_secrete->createView(),
                    'form_ajout_publication_image'=>$form_ajout_publication_image->createView(),
                    'form_filter_categorie'=>$form_filter_categorie->createView(),
                    'error_taken'=>$error_taken,
                    'nbr_publication'=>$nbrepublication,
                    'nbr_image'=>$nbreimage,
                    'listerate'=>$listeRate,
                    'countcommentstable'=>$countcommentstable,

                    'nbre_publication'=>$nbre_publication

                ));

            }
            else{
                $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array('descP'=>NULL),array('id' => 'DESC'));
                //return new Response("Publication Image");
                $nbre_publication = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublicationImage2();

                return $this->render("@Spotted/Default/anonyme_publication_image.html.twig",array(
                    'listspotted'=>$listspotted,
                    'form_recherche_ajax'=>$form_recherche_ajax->createView(),
                    'form_ajout_publication_secrete'=>$form_ajout_publication_secrete->createView(),
                    'form_ajout_publication_image'=>$form_ajout_publication_image->createView(),
                    'form_filter_categorie'=>$form_filter_categorie->createView(),
                    'error_taken'=>$error_taken,
                    'nbr_publication'=>$nbrepublication,
                    'nbr_image'=>$nbreimage,
                    'listerate'=>$listeRate,
                    'countcommentstable'=>$countcommentstable,

                    'nbre_publication'=>$nbre_publication

                ));

            }


        }


        $afk = $this->getUser()->getId();
        $iduser1 = $this->getUser()->getId();
        /*
                $nbre_commentaire = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->seif();
                var_dump($nbre_commentaire);
        */

        foreach ($listspotted as $idp)
        {
            $valid = $idp->getId();
            //var_dump($valid);

        }


        $badWords = array("bad1","bad2","bad3","bad4","bad5","bad6","bad6");


        if ($form_ajout_publication_secrete->isSubmitted())
        {



            $current_user_id_bySeif = $this->getUser()->getId();

            $publication->setIdUser($this->getUser());
            $newdt = new \DateTime();
            $newdt->format('y:m:d');
            $publication->setDateP( $newdt);
            //return new Response($publication->getDateP()->format('y:m:d'));
            $em->persist($publication);
            $em->flush();

            $em2 = $this->getDoctrine()->getManager();
            return $this->redirect($request->getUri());


        }

        if ($form_ajout_publication_image->isSubmitted())
        {


            $current_user_id_bySeif = $this->getUser()->getId();

            $publication->setIdUser($this->getUser());
            $em->persist($publication);
            $em->flush();

            $em2 = $this->getDoctrine()->getManager();
            return $this->redirect($request->getUri());


        }


        return $this->render("@Spotted/Default/anonyme_publication_image.html.twig",array(
            'listspotted'=>$listspotted,
            'form_recherche_ajax'=>$form_recherche_ajax->createView(),
            'form_ajout_publication_secrete'=>$form_ajout_publication_secrete->createView(),
            'form_ajout_publication_image'=>$form_ajout_publication_image->createView(),
            'form_filter_categorie'=>$form_filter_categorie->createView(),
            'error_taken'=>$error_taken,
            'listerate'=>$listeRate,
            'nbre_publication'=>$nbre_publication,
            'nbr_image'=>$nbreimage,
            'countcommentstable'=>$countcommentstable,


            'nbr_publication'=>$nbrepublication
        ));


    }
    public function ajoutAction(Request $request)
    {
        //return new Response($this->getUser());
        //return new Response($this->getUser()->getId());
        $publication = new Publication();
        $em= $this->getDoctrine()->getManager();
        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationType",$publication);
        $form->handleRequest($request);


        if ($form->isValid())
        {


            $current_user_id_bySeif = $this->getUser()->getId();

            $publication->setIdUser($this->getUser());
            $em->persist($publication);
            $em->flush();

            $em2 = $this->getDoctrine()->getManager();
            $this->redirectToRoute('_list_spotted');


        }
        return $this->render("@Spotted/CRUD/ajout.html.twig",array(
            'form'=>$form->createView()
        ));
    }
    public function ajoutPictureAction(Request $request)
    {
        //return new Response($this->getUser());
        //return new Response($this->getUser()->getId());
        $publication = new Publication();
        $em= $this->getDoctrine()->getManager();
        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationImageType",$publication);
        $form->handleRequest($request);


        if ($form->isValid())
        {



            $current_user_id_bySeif = $this->getUser()->getId();

            $publication->setIdUser($this->getUser());
            $em->persist($publication);
            $em->flush();

            $em2 = $this->getDoctrine()->getManager();
            $this->redirectToRoute('_list_spotted');

        }
        return $this->render("@Spotted/CRUD/ajoutImage.html.twig",array(
            'form'=>$form->createView()
        ));
    }
    public function listSpottedAction(Request $request)
    {
        $usr = new User();
        $afk = $this->getUser()->getId();
        $iduser1 = $this->getUser()->getId();
        $publication = new Publication();
        $em=$this->getDoctrine()->getManager();

        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array('idUser'=>$afk));


        return $this->render("@Spotted/CRUD/listspotted.html.twig",array(
            'listspotted'=>$listspotted

        ));
    }

    public function listAllSpottedAction(Request $request)
    {
        $usr = new User();
        $afk = $this->getUser()->getId();
        $iduser1 = $this->getUser()->getId();
        $publication = new Publication();
        $em=$this->getDoctrine()->getManager();

        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findAll();


        return $this->render("@Spotted/CRUD/listspotted.html.twig",array(
            'listspotted'=>$listspotted

        ));
    }


    public function modifSpottedAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();

        $publication = $em->getRepository("SpottedBundle:Publication")->find($id);
        $urlimg = $publication->getImage();
        //return new Response($publication->getImage());
        // $publication = $em->getRepository("SpottedBundle:Publication")->findBy(array('id'=>$id,"image"=>NULL));
        // return new Response($pb->getId());

        if ($publication->getImage() == NULL){
            $Form = $this->createForm(PublicationType::class, $publication);

        }
        if ($publication->getImage() != NULL){
            $Form = $this->createForm(PublicationImageType::class, $publication);

        }


        //$Form = $this->createForm(PublicationType::class, $publication);

        $Form->handleRequest($request);

        if ($Form->isSubmitted()) {
            $publication->setDateModif( new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $publication->setDateModif(new \DateTime());
            $em->persist($publication);
            $em->flush();

            return $this->redirectToRoute('_test_anonyme_image');

        }
        return $this->render('@Spotted/Default/anonyme_modif.html.twig', array(
            "form"=>$Form->createview(),
            "url"=>$urlimg
        ));
    }

    public function contentSpottedAction(Request $request,$id)
    {


        $publication = new Publication();
        $em=$this->getDoctrine()->getManager();

        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array('id'=>$id));
        $lst = $em->getRepository("SpottedBundle:Publication",$publication)->findOneBy(['id' => $id]);


        $commentaire = new Commentaire();
        $em=$this->getDoctrine()->getManager();

        $lstcomments = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->findBy(array('idPublication'=>$id));



        //return new Response($this->getUser());
        //return new Response($this->getUser()->getId());
        $commentaire = new Commentaire();
        $em= $this->getDoctrine()->getManager();
        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\CommentaireType",$commentaire);
        $form->handleRequest($request);


        if ($form->isValid())
        {
            $commentaire->setIdUser($this->getUser());
            $commentaire->setIdPublication($lst);



            $em->persist($commentaire);
            $em->flush();

            $em2 = $this->getDoctrine()->getManager();


        }



        return $this->render("@Spotted/CRUD/content.html.twig",array(
            'listspotted'=>$listspotted,
            'form'=>$form->createView(),
            'comments'=>$lstcomments

        ));
    }


    public function editCommentSpottedAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository("SpottedBundle:Commentaire")->find($id);
        $Form = $this->createForm(CommentaireType::class, $commentaire);

        $Form->handleRequest($request);
        if ($Form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $commentaire->setDateModif(new \DateTime());
            $em->persist($commentaire);
            $em->flush();
            return $this->redirectToRoute('_test_anonyme_image');

        }
        return $this->render('@Spotted/CRUD/modifcomment.html.twig', array(
            "form"=>$Form->createview(),

        ));
    }


    public function deleteSpottedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository("SpottedBundle:Publication")->find($id);
        $em->remove($publication);
        $em->flush();
        return $this->redirectToRoute('_test_anonyme_image');
    }


    public function deleteCommentSpottedAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $commentaire = $em->getRepository("SpottedBundle:Commentaire")->find($id);
        $em->remove($commentaire);
        $em->flush();
        return $this->redirectToRoute('_test_anonyme_image');
    }

    public function RechercheAction(Request $request){
        $voiture=new Publication();
        $em=$this->getDoctrine()->getManager();
        $Form=$this->createForm(RechercheType::class,$voiture);
        $Form->handleRequest($request);
        if($request->isXmlHttpRequest() ){

            $ser=$request->get('s');

            $voiture=$em->getRepository("SpottedBundle:Publication")->findBy(array('titreP'=>$ser));
            $serialzier = new Serializer(array(new ObjectNormalizer()));
            $v = $serialzier->normalize($voiture);

            return new JsonResponse($v);

        }
        else{
            $voiture=$em->getRepository("SpottedBundle:Publication")->findAll();

        }
        return $this->render("SpottedBundle:Default:rechercher.html.twig",array(
            'Form'=>$Form->createView(),
            'voitures'=>$voiture
        ));

    }
    public function RechercheSpottedAction(Request $request){
        $publication=new Publication();

        $em=$this->getDoctrine()->getManager();

        $Form0=$this->createForm("EspritEntreAide\SpottedBundle\Form\RechercheSpottedType",$publication);
        $Form0->handleRequest($request);

        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationType",$publication);
        $form->handleRequest($request);


        $form2=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationImageType",$publication);
        $form2->handleRequest($request);

        $form3=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationFilterType",$publication);
        $form3->handleRequest($request);

        if($request->isXmlHttpRequest() ){


            $ser=$request->get('s');
            if ($ser != null)
            {
                $listspotted=$em->getRepository("SpottedBundle:Publication")->rechercheAjax($ser);

            }

            else
            {
                $listspotted=$em->getRepository("SpottedBundle:Publication")->findAll();

            }
            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceLimit(2);
            // Add Circular reference handler
            $normalizer->setCircularReferenceHandler(function ($publication) {
                return $publication->getId();
            });
            $normalizers = array($normalizer);
            $serialzier = new Serializer(array($normalizer));
            $v = $serialzier->normalize($listspotted);

            return new JsonResponse($v);

        }
        else{
            $listspotted=$em->getRepository("SpottedBundle:Publication")->findAll();

        }
        $nbre_publication = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublication();

        return $this->render("@Spotted/Default/rechercheajax.html.twig",array(
            'form0'=>$Form0->createView(),
            'publication'=>$listspotted


        ));

    }

    public  function carrouselAction(Request $request){
        $publication = new Publication();
        $rating = new Rating();
        $em=$this->getDoctrine()->getManager();


        $rankingpub = $em->getRepository("SpottedBundle:Rating",$rating)->rankingspotted();


        return $this->render(":default:carrousel.html.twig",array(
            'rankingpub'=>$rankingpub
        ));
    }

    //================================ Back Office ==============================================

    public  function adminListSpottedAction(Request $request)
    {



        $publication = new Publication();
        $commentaire = new Commentaire();
        $rating = new Rating();
        $em= $this->getDoctrine()->getManager();



        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array(), array('id' => 'DESC'));


        $countcommentstable = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->countComments();




        return $this->render(":admin/partial/SpottedAdmin:listspotted.html.twig",array(
            'listspotted'=>$listspotted,
            'countcommentstable'=>$countcommentstable

        ));
    }

    public  function deleteAllAction(Request $request)
    {

        $publication = new Publication();
        $commentaire = new Commentaire();
        $rating = new Rating();
        $em= $this->getDoctrine()->getManager();



        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array(), array('id' => 'DESC'));

        foreach ($listspotted as $me)
        {
            $em->remove($me);

        }
        $em->flush();
        return $this->redirect($request->getUri());
        return $this->render(":admin/partial/SpottedAdmin:listspotted.html.twig",array(
            'listspotted'=>$listspotted

        ));
    }

    public  function ajoutAdminAction(Request $request)
    {

        $publication = new Publication();
        $em= $this->getDoctrine()->getManager();

        $form_ajout_publication=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationType",$publication);
        $form_ajout_publication->handleRequest($request);

        $form_ajout_image=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationImageType",$publication);
        $form_ajout_image->handleRequest($request);

        if ($form_ajout_publication->isSubmitted())
        {
            $current_user_id_bySeif = $this->getUser()->getId();
            $publication->setIdUser($this->getUser());
            $publication->setDateP( new \DateTime());
            $em->persist($publication);
            $em->flush();
            $em2 = $this->getDoctrine()->getManager();
            return $this->redirect($request->getUri());
        }
        if ($form_ajout_image->isSubmitted())
        {
            $current_user_id_bySeif = $this->getUser()->getId();
            $publication->setIdUser($this->getUser());
            $em->persist($publication);
            $em->flush();
            $em2 = $this->getDoctrine()->getManager();
            return $this->redirect($request->getUri());
        }
        return $this->render(":admin/partial/SpottedAdmin:ajoutAdmin.html.twig",array(
            'form_ajout_publication'=>$form_ajout_publication->createView(),
            'form_ajout_image'=>$form_ajout_image->createView()

        ));
    }

    public  function RechercheAjaxAction(Request $request)
    {
        $publication=new Publication();
        $em=$this->getDoctrine()->getManager();
        $Form0=$this->createForm("EspritEntreAide\SpottedBundle\Form\RechercheSpottedType",$publication);
        $Form0->handleRequest($request);

        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationType",$publication);
        $form->handleRequest($request);


        $form2=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationImageType",$publication);
        $form2->handleRequest($request);

        $form3=$this->createForm("EspritEntreAide\SpottedBundle\Form\PublicationFilterType",$publication);
        $form3->handleRequest($request);

        if($request->isXmlHttpRequest() ){


            $ser=$request->get('s');
            $oi = '%'.$ser.'%';



            $listspotted=$em->getRepository("SpottedBundle:Publication")->rechercheAjax($ser);


            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceLimit(2);
// Add Circular reference handler
            $normalizer->setCircularReferenceHandler(function ($publication) {
                return $publication->getId();
            });
            $normalizers = array($normalizer);
            $serialzier = new Serializer(array($normalizer));
            $v = $serialzier->normalize($listspotted);

            return new JsonResponse($v);

        }
        else{
            $listspotted=$em->getRepository("SpottedBundle:Publication")->findAll();

        }
        $nbre_publication = $em->getRepository("SpottedBundle:Publication",$publication)->countAllPublication();

        return $this->render(":admin/partial/SpottedAdmin:rechercheajaxadmin.html.twig",array(
            'form0'=>$Form0->createView(),
            'listspotted'=>$listspotted



        ));
    }

    public function autoAction(Request $request)
    {
        return $this->render("@Spotted/Default/auto.html.twig");
    }


    public  function loadAction(Request $request)
    {
        $publication=new Publication();

        $em=$this->getDoctrine()->getManager();

        $mdate = $request->get('maxdate');

        if($request->isXmlHttpRequest() ){
            //return new JsonResponse($mdate);
            //$date = new DateTime();

            ///return new JsonResponse(var_dump($date));

            //return new JsonResponse($mdate);

            //$newformat = date('y-d-m',$mdate);
            //return new JsonResponse($newformat);
            //return new JsonResponse("new m date  : = ".$newformat);

            //return new JsonResponse("newformat date  : = ".$newformat);
            //return new JsonResponse(gettype(date($newformat,$time)));
            //$s = $mdate;
            //$date = strtotime($s);

            //return new JsonResponse($date);

            /*$datetime = $mdate;
            $newDate = $datetime->createFromFormat('Y-d-m', $mdate)->format('Y-d-m H:i:s');*/

            //eturn $objects;
            //return new JsonResponse($mdate);
            $fara = date("Y-m-d H:i:s", strtotime($mdate));
            //return new JsonResponse($fara);
            //return new JsonResponse($fara);
            $listspotted=$em->getRepository("SpottedBundle:Publication")->newposts($fara);

            /* foreach ($listspotted as $idp)
             {


                 $bd = $idp->getDateP();
                 //$fara = date("Y-m-d H:i:s", $bd);
                 if ($fara < $idp->getDateP())
                 {
                     $listspotted=$em->getRepository("SpottedBundle:Publication")->newposts($idp->getDateP());

                 }

             }*/

            // $listspotted=$em->getRepository("SpottedBundle:Publication")->newposts($bd);

            $normalizer = new ObjectNormalizer();

            $normalizer->setCircularReferenceLimit(1);
// Add Circular reference handler
            $normalizer->setCircularReferenceHandler(function ($publication) {
                return $publication->getId();
            });
            $normalizers = array($normalizer);
            $serialzier = new Serializer(array($normalizer));
            $v = $serialzier->normalize($listspotted);

            return new JsonResponse( $v);



        }
        return new JsonResponse("hello world 0");

    }

    public function modifSpottedAdminAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();

        $publication = $em->getRepository("SpottedBundle:Publication")->find($id);
        $urlimg = $publication->getImage();
        //return new Response($publication->getImage());
        // $publication = $em->getRepository("SpottedBundle:Publication")->findBy(array('id'=>$id,"image"=>NULL));
        // return new Response($pb->getId());

        if ($publication->getImage() == NULL){
            $Form = $this->createForm(PublicationType::class, $publication);

        }
        if ($publication->getImage() != NULL){
            $Form = $this->createForm(PublicationImageType::class, $publication);

        }


        //$Form = $this->createForm(PublicationType::class, $publication);

        $Form->handleRequest($request);

        if ($Form->isSubmitted()) {
            $publication->setDateModif( new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $publication->setDateModif(new \DateTime());
            $em->persist($publication);
            $em->flush();
            return $this->redirectToRoute('_test_anonyme_image');

        }
        return $this->render(':admin/partial/SpottedAdmin:modifspotted.html.twig', array(
            "form"=>$Form->createview(),
            "url"=>$urlimg
        ));
    }

    public function supprimerSpottedAdminAction(Request $request,$id)
    {

        $publication=new Publication();

        $em = $this->getDoctrine()->getManager();
        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array(), array('id' => 'DESC'));


        $publication = $em->getRepository("SpottedBundle:Publication")->find($id);

        $em->remove($publication);

        $em->flush();
        return new Response("deleted with succes !");
    }


    public function contentSpottedAdminAction(Request $request,$id){

        $publication = new Publication();
        $rating = new Rating();

        $em=$this->getDoctrine()->getManager();

        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array('id'=>$id));
        $lst = $em->getRepository("SpottedBundle:Publication",$publication)->findOneBy(['id' => $id]);


        $commentaire = new Commentaire();
        $em=$this->getDoctrine()->getManager();

        $lstcomments = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->findBy(array('idPublication'=>$id));


        $listeRate = $em->getRepository("SpottedBundle:Rating",$rating)->findOneBy([
            'idPublication' => $id,
            'idUser' => $this->getUser()->getId(),
        ]);
        //var_dump($listeRate);
        //return new Response($this->getUser()->getId());
        //return new Response($this->getUser()->getId());
        $commentaire = new Commentaire();
        $em= $this->getDoctrine()->getManager();
        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\CommentaireType",$commentaire);
        $form->handleRequest($request);
        $form6=$this->createForm("EspritEntreAide\SpottedBundle\Form\Rating2Type",$rating);
        $form6->handleRequest($request);
        if ($form6->isSubmitted())
        {


            $current_user_id_bySeif = $this->getUser()->getId();

            $rating->setIdUser($this->getUser());


            $spot = $em->getRepository("SpottedBundle:Publication",$publication)->findOneBy(array('id'=>$rating->getIdPublication()));

            $rating->setIdPublication($spot);
            $em->persist($rating);
            $em->flush();


            return $this->redirect($request->getUri());


        }

        if ($form->isValid())
        {
            $commentaire->setIdUser($this->getUser());
            $commentaire->setIdPublication($lst);

            $commentaire->setDateModif(new \DateTime());


            $em->persist($commentaire);
            $em->flush();

            $em2 = $this->getDoctrine()->getManager();
            return $this->redirect($request->getUri());


        }





        return $this->render(":admin/partial/SpottedAdmin:contentspotted.html.twig",array(
            'listspotted'=>$listspotted,
            'form'=>$form->createView(),
            'form6'=>$form6->createView(),
            'listerate'=>$listeRate,
            'ido'=>$id,
            'comments'=>$lstcomments
        ));

    }
    public function clearCommentsAdminAction(Request $request,$id){
        $commentaire = new Commentaire();
        $em= $this->getDoctrine()->getManager();


        $spot = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->findBy(array('idPublication'=>$id));

        foreach ($spot as $me)
        {
            $em->remove($me);

        }
        $em->flush();

        $publication = new Publication();
        $rating = new Rating();

        $em=$this->getDoctrine()->getManager();

        $listspotted = $em->getRepository("SpottedBundle:Publication",$publication)->findBy(array('id'=>$id));
        $lst = $em->getRepository("SpottedBundle:Publication",$publication)->findOneBy(['id' => $id]);


        $commentaire = new Commentaire();
        $em=$this->getDoctrine()->getManager();

        $lstcomments = $em->getRepository("SpottedBundle:Commentaire",$commentaire)->findBy(array('idPublication'=>$id));


        $listeRate = $em->getRepository("SpottedBundle:Rating",$rating)->findOneBy([
            'idPublication' => $id,
            'idUser' => $this->getUser()->getId(),
        ]);
        //var_dump($listeRate);
        //return new Response($this->getUser()->getId());
        //return new Response($this->getUser()->getId());
        $commentaire = new Commentaire();
        $em= $this->getDoctrine()->getManager();
        $form=$this->createForm("EspritEntreAide\SpottedBundle\Form\CommentaireType",$commentaire);
        $form->handleRequest($request);
        $form6=$this->createForm("EspritEntreAide\SpottedBundle\Form\Rating2Type",$rating);
        $form6->handleRequest($request);


        return $this->render(":admin/partial/SpottedAdmin:contentspotted.html.twig",array(
            'listspotted'=>$listspotted,
            'form'=>$form->createView(),
            'form6'=>$form6->createView(),
            'listerate'=>$listeRate,
            'ido'=>$id,
            'comments'=>$lstcomments
        ));



    }


    /*--------------------------------CRUD WEB SERVICE------------------------------------*/

    public function jsonTestAction(){
        return new JsonResponse("test me");

    }

    public function affichePublicationAction(){

        //return new JsonResponse("Hello ");
        $publications = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Publication')->findBy(array(),array('id'=>'DESC'));
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($listp) {
            return $listp->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $l = $serialzier->normalize($publications);
        // var_dump($l);
        return new JsonResponse($l);

    }

    public function affichePublicationByImageAction(){

        $publications = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Publication')->imageT();
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($listp) {
            return $listp->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $l = $serialzier->normalize($publications);
        // var_dump($l);
        return new JsonResponse($l);

    }

    public function affichePublicationByPublicationAction(){

        $publications = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Publication')->texto();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($listp) {
            return $listp->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $l = $serialzier->normalize($publications);
        // var_dump($l);
        return new JsonResponse($l);

    }

    public function findPublicationAction($id){

        $publications = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Publication')->find($id);
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($listp) {
            return $listp->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $l = $serialzier->normalize($publications);
        return new JsonResponse($l);

    }


    public function ajoutPublicationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $pub = new Publication();
        //$pub->setIdUser($this->getUser());
        //$pub->setIdUser($this->getUser());
        $user5 = $this->getDoctrine()->getManager()->getRepository('UserBundle:User')->find(26);


        $pub->setIdUser($user5);
        $pub->setImage($request->get('image'));

        //$pub->setTags("acd");
        $pub->setDateP(new \DateTime());
        $pub->setDateModif(new \DateTime());
        $pub->setDescP($request->get('desc_p'));
        $pub->setTags($request->get('tags'));
        //$pub->setIdUser($request->get('idUser'));



        $em->persist($pub);
        $em->flush();


        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($pub);*/
        return new JsonResponse("ajout terminé avec succes");


        /*
                $normalizer = new ObjectNormalizer();
                $normalizer->setCircularReferenceLimit(1);

                $normalizers = array($normalizer);
                $serialzier = new Serializer($normalizers);
                $l = $serialzier->normalize($pub);
                return new JsonResponse($l);
        */


    }

    public function modifPublicationAction(Request $request,$id)
    {
        $pub = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Publication')->find($id);

        $em = $this->getDoctrine()->getManager();

        //$pub->setIdUser($this->getUser());
        //$pub->setIdUser($this->getUser());
        // $pub->setIdUser(26);
        //$pub->setImage("");
        //$pub->setTags("acd");
        $pub->setDateP(new \DateTime());
        $pub->setDateModif(new \DateTime());
        $pub->setDescP($request->get('desc_p'));
        $pub->setTags($request->get('tags'));
        //$pub->setIdUser($request->get('idUser'));
        $em->persist($pub);
        $em->flush();



        return new JsonResponse("Modification terminé avec succes");


    }

    public function suppPublicationAction(Request $request, $id){
        $pub=new Publication();

        $em = $this->getDoctrine()->getManager();

        $publication = $em->getRepository("SpottedBundle:Publication")->find($id);

        $em->remove($publication);

        $em->flush();

        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($pub);*/
        return new JsonResponse("Publication supprimé avec succes");

    }

    public function suppressionPublicationAction(Request $request, $id){
        $pub=new Publication();

        $em = $this->getDoctrine()->getManager();

        $publication = $em->getRepository("SpottedBundle:Publication")->find($id);

        $em->remove($publication);

        $em->flush();

        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($pub);*/
        return new JsonResponse("Publication test avec succes");

    }


    public function suppCommentaireAction(Request $request, $id){
        $pub=new Publication();

        $em = $this->getDoctrine()->getManager();

        $publication = $em->getRepository("SpottedBundle:Commentaire")->find($id);

        $em->remove($publication);

        $em->flush();

        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($pub);*/
        return new JsonResponse("Publication test avec succes");

    }



    public function ajoutCommentaireAction(Request $request , $id){
        $em = $this->getDoctrine()->getManager();
        $com = new Commentaire();
        //$pub->setIdUser($this->getUser());
        //$pub->setIdUser($this->getUser());
        $com->setIdUser(26);
        $com->setIdPublication($id);
        $com->setContentComm($request->get('content_comm'));
        //$com->setContentComm("seif test");
        $com->setDateCreation(new \DateTime());
        $com->setDateModif(new \DateTime());

        //$pub->setIdUser($request->get('idUser'));



        $em->persist($com);
        $em->flush();


        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($pub);*/
        return new JsonResponse("ajout terminé avec succes");


    }


    public function afficheCommentaireAction(Request $request , $id){

        $publications = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Commentaire')->findBy(array("idPublication"=>$id),array('id' => 'DESC'));
        //$publications = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Commentaire')->findAll();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($listp) {
            return $listp->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $l = $serialzier->normalize($publications);
        //var_dump($l);
        return new JsonResponse($l);

    }


    public function modifCommentaireAction(Request $request,$id)
    {
        $pub = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Commentaire')->find($id);

        $em = $this->getDoctrine()->getManager();

        //$pub->setIdUser($this->getUser());
        //$pub->setIdUser($this->getUser());
        // $pub->setIdUser(26);
        //$pub->setImage("");
        //$pub->setTags("acd");
        $pub->setContentComm($request->get('content_comm'));
        $pub->setDateModif(new \DateTime());
        $pub->setDateCreation(new \DateTime());

        $em->persist($pub);
        $em->flush();



        return new JsonResponse("Modification terminé avec succes");


    }



    public function ajoutRatingAction(Request $request ){
        $em = $this->getDoctrine()->getManager();
        $rating = new Rating();
        //$pub->setIdUser($this->getUser());
        //$pub->setIdUser($this->getUser());
        $rating->setIdUser($request->get('iduser'));
        $rating->setIdPublication($request->get('idpublication'));
        $rating->setCreated(new \DateTime());


        $rating->setRating($request->get("rating"));


        //$pub->setIdUser($request->get('idUser'));



        $em->persist($rating);
        $em->flush();


        /*$serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($pub);*/
        return new JsonResponse("ajout rating terminé avec succes");


    }

    public function afficheRatingAction(){

        $publications = $this->getDoctrine()->getManager()->getRepository('SpottedBundle:Rating')->findAll();
        //var_dump($publications);
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($listp) {
            return $listp->getId();
        });
        $normalizers = array($normalizer);
        $serialzier = new Serializer($normalizers);
        $l = $serialzier->normalize($publications);
        // var_dump($l);
        return new JsonResponse($l);

    }


}
