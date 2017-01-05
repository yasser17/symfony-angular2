<?php

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth
{
    public $manager;

    public function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function singnin($email, $password, $getHash = null)
    {
        $key = "clave-secreta";

        $user = $this->manager->getRepository('BackendBundle:User')->findOneBy([
            'email' => $email,
            'password' => $password
        ]);

        $singIn = false;

        if (is_object($user)) {
            $singIn = true;
        }

        if ($singIn) {
            $token = [
                'sub'       => $user->getId(),
                'email'     => $user->getEmail(),
                'name'      => $user->getName(),
                'surname'   => $user->getSurname(),
                'image'     => $user->getImage(),
                'iat'       => time(),
                'exp'       => time() + (7 * 24 * 60 * 60)
            ];

            $jwt = JWT::encode($token, $key, 'HS256');
            $decoded = JWT::decode($jwt, $key, ['HS256']);

            if ($getHash != null) {
                return $jwt;
            } else {
                return $decoded;
            }
        } else {
            return [
                'status' => 'error',
                'data' => 'Login failed'
            ];
        }
    }
}