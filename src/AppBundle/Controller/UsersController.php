<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * Users controller.
 *
 * @Route("/user")
 */
class UsersController extends Controller
{
    /**
     * @Rest\Get("/all/")
     * la liste de tous les Users
     */
    public function getAction() {
        $response = new JsonResponse();

        $allUsers = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if (!$allUsers) {
            return new Response("User not found", Response::HTTP_NOT_FOUND);
        }

        $serializer = $this->get('jms_serializer');
        $allUsers = $serializer->serialize($allUsers, 'json');

        return new Response($allUsers);


    }


    /**
     * @Rest\Post ("/add/")
     * inscription de nouveau User
     */
    public function postAction(Request $request) {

        $response = new JsonResponse();

        $username = $request->get('username');
        $password = $request->get('password');
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');
        $email = $request->get('email');
        $role = $request->get('role');

        if (empty($username)) {
            return new Response("username NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);}
        if (empty($password)) {
            return new Response("password NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);}
        if (empty($firstname)) {
            return new Response("firstname NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);}

        $dataUser = new User;

        $dataUser->setPassword($password);
        $dataUser->setUsername($username);
        $dataUser->setFirstname($firstname);
        $dataUser->setLastname($lastname);
        $dataUser->setEmail($email);
        $dataUser->setRole($role);

        $em = $this->getDoctrine()->getManager();
        $em->persist($dataUser);

        try {
            $em->flush();
        } catch (\Exception $e) {
            return new Response($e, Response::HTTP_NOT_ACCEPTABLE);
        }

        return new Response("This User Was Created", Response::HTTP_OK);
    }


    /**
     * @Route("/delete/{id}")
     * @Method("DELETE")
     */
    public function deleteAction($id) {



        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneById($id);
        if (!$user) {
            throw $this->createNotFoundException(sprintf(
                'No user found with id "%s"',
                $id
            ));
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return new Response("User Was Deleted", Response::HTTP_OK);
    }
    /**
     * @Rest\Get("/one/{id}")
     * Afficher les informations du user id
     */
    public function getidAction($id) {
        $response = new JsonResponse();
        $singleUser = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if (!$singleUser) {
            return new Response("User  not found", Response::HTTP_NOT_FOUND);
        }

        $serializer = $this->get('jms_serializer');
        $singleUser = $serializer->serialize($singleUser, 'json');

        return new Response($singleUser);

    }
    /**
     * @Rest\Put("/user/{id}")
     */
    public function updateAction($id,Request $request)
    {
        $data = new User;
        $role = $request->get('role');
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');
        $password = $request->get('password');
        $lastname = $request->get('role');
        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($user)) {
            return new View("user not found", Response::HTTP_NOT_FOUND);
        }
        elseif(!empty($name) && !empty($role)){
            $user->setName($name);
            $user->setRole($role);
            $sn->flush();
            return new View("User Updated Successfully", Response::HTTP_OK);
        }
        elseif(empty($name) && !empty($role)){
            $user->setRole($role);
            $sn->flush();
            return new View("role Updated Successfully", Response::HTTP_OK);
        }
        elseif(!empty($name) && empty($role)){
            $user->setName($name);
            $sn->flush();
            return new View("User Name Updated Successfully", Response::HTTP_OK);
        }
        else return new View("User name or role cannot be empty", Response::HTTP_NOT_ACCEPTABLE);
    }
}
