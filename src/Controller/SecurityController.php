<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SecurityController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * SecurityController constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }


    /**
     * @Route("/login", name="security_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function login(AuthenticationUtils $authenticationUtils) {
        return new Response($this->twig->render(
           'security/login.html.twig',
           [
               'last_username' => $authenticationUtils->getLastUsername(),
               'error' => $authenticationUtils->getLastAuthenticationError()
           ]
        ));
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout() {
        // this is an empty method
        // it just needs to be defined in order for Symfony to handle logout
    }
}