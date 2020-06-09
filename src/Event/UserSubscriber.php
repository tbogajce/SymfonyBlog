<?php


namespace App\Event;


use Swift_Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Environment
     */
    private $twig;

    /**
     * UserSubscriber constructor.
     * @param Swift_Mailer $mailer
     * @param Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        // we return an array because it can subscribe to many events
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event) {
        $body =$this->twig->render('email/registration.html.twig', [
            'user' => $event->getRegisteredUser()
        ]);

        $message = (new \Swift_Message())
            ->setSubject('Welcome to the micro-post app!')
            ->setFrom('micropost@micropost.com')
            ->setTo($event->getRegisteredUser()->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);
    }

}