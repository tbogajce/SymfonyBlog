<?php


namespace App\Controller;


use App\Entity\User;
use App\Event\UserRegisterEvent;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="user_register")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function register(UserPasswordEncoderInterface $passwordEncoder,
                             Request $request,
                             EventDispatcherInterface $eventDispatcher) {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $userRegisterEvent = new UserRegisterEvent($user);

            $eventDispatcher->dispatch(
                $userRegisterEvent,
                UserRegisterEvent::NAME
            );

            return $this->redirectToRoute('micro_post_index');
        }

        // return response
        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}