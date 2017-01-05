<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    public function loginAction(Request $request)
    {
        $helpers = $this->get("app.helpers");
        $jwt_auth = $this->get("app.jwt_auth");

        $json = $request->get('json', null);

        if ($json != null) {
            $params = json_decode($json);

            $email  = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $getHash = (isset($params->getHash)) ? $params->getHash : null;

            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This email is not valid";
            $validator_email = $this->get("validator")->validate($email, $emailConstraint);

            if (count($validator_email) == 0 && $password != null) {
                if ($getHash == null) {
                    $singIn = $jwt_auth->singnin($email, $password);
                } else {
                    $singIn = $jwt_auth->singnin($email, $password, true);
                }
                
                return new JsonResponse($singIn);
            } else {
                return $helpers->json([
                    'status' => 'error',
                    'data' => 'login not valid'
                ]);
            }

        } else {
            return $helpers->json([
                'status' => 'error',
                'data' => 'Send json with post'
            ]);
        }
    }


    public function pruebasAction(Request $request)
    {
        $helpers = $this->get("app.helpers");
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BackendBundle:User')->findAll();
        return $helpers->json($users);
    }
}
