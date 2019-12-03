<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class SecurityController extends FOSRestController
{
    /**
     * @Rest\Post("/login")
     */
    public function loginAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, JWTEncoderInterface $encoder)
    {
        $login = $request->get('username');
        $password = $request->get('password');
        $user = $this
            ->getDoctrine()
            ->getRepository("AppBundle:User")
            ->findOneBy([
                'username' => $login
            ]);
        if (!$user) {
            $user = $this
                ->getDoctrine()
                ->getRepository("AppBundle:User")
                ->findOneBy([
                    'email' => $login
                ]);
        }
        if(!$user) {
            return new View('User not found',Response::HTTP_NOT_FOUND);
        }

        $isValid = $passwordEncoder
            ->isPasswordValid($user, $password);

        if (!$isValid) {
            return new View('User password not correct',Response::HTTP_NOT_FOUND);
        }

        $token = $encoder->encode(
            [
                'username' => $user->getUsername(),
                'exp' => time() + 3600,
            ]
        );

        return array(
            'user' => $user,
            'token' => $token,
            'success' => Response::HTTP_OK
        );
    }
}
