<?php


namespace App\Tests\Mailer;


use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use function foo\func;

class MailerTest extends TestCase
{
    public function testConfirmationEmail() {
        $user = new User();
        $user->setEmail('john@doe.com');

        // mock classes
        $swiftMailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $twigMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();

        // expected values
        $swiftMailer->expects($this->once())->method('send')
            ->with($this->callback(function ($subject) {
                $messageStr = (string)$subject;

                return strpos($messageStr, "From: me@domain.com")
                    && strpos($messageStr, "Content-Type: text/html; charset=utf-8")
                    && strpos($messageStr, "Subject: Welcome to the micro-post app!")
                    && strpos($messageStr, "To: john@doe.com");
            }));
        $twigMock->expects($this->once())->method('render')
            ->with('email/registration.html.twig', ['user' => $user])
            ->willReturn('This is a message body');

        $mailer = new Mailer($swiftMailer, $twigMock, 'me@domain.com');
        $mailer->sendConfirmationEmail($user);
    }
}