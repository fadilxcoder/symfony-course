<?php


namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;

class Mailing
{
    private $mailer;
    private $em;
    private $twig;

    public function __construct(EntityManagerInterface $entityManager, Environment $environment, \Swift_Mailer $swift_Mailer)
    {
        $this->mailer = $swift_Mailer;
        $this->em = $entityManager;
        $this->twig = $environment;
    }

    public function notifyChanges($article)
    {
        $usersDetails = $this->em->getRepository(User::class)->findUsersByRole(User::ROLE);
        $arr = array();

        foreach ($usersDetails as $_usersDetails) {
            $arr[] = $_usersDetails->getEmail();
        }

        $subject = 'Symfony Course Notification';
        //$arr = array('fadil@xcoder.developer', 'user@system.app', 'admin@system.app');
        $mail = (new \Swift_Message($subject))
            ->setFrom([
                'no-reply@local.symfony.course' => "Symfony 4 Course"
            ])
            ->setTo($arr)
            ->setBody($this->twig->render('email/maj.html.twig', [
                'email' => $article
            ]), 'text/html');

        $this->mailer->send($mail);

    }
}