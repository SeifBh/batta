<?php

namespace EspritEntreAide\UserBundle\Controller;

use EspritEntreAide\UserBundle\Entity\User;
use EspritEntreAide\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function indexAction()
    {

        return $this->render(':default:index.html.twig');
    }

    public function adminAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('admin/index.html.twig');

    }

    public function addUserAction(Request $request){
        $user = new User();


            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request); /*creation d'une session pr stocker les valeurs de l'input*/
            if ($form->isValid()) {
                $user->setEnabled(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }
            return $this->render('admin/partial/UserAdmin/addUser.html.twig', array(
                'form' => $form->createView()
            ));
        }

    public function updateUserAction(Request $request)
    {
        $id = $_GET['id'];

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isValid()){
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('club_affiche');
        }

        return $this->render(':admin/partial/UserAdmin:updateUser.html.twig',array('form'=>$form->createView()));
    }

    public function afficherUserAction(){
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('admin/partial/UserAdmin/afficherUser.html.twig',array('users'=>$users) );
    }

    public function supprimerUserAction()
    {
        $em = $this->getDoctrine()->getManager();
        $id = $_GET['id'];
        $user = $em->getRepository("UserBundle:User")->find($id);
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('_afficher_user');
    }
}
