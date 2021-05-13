<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Mailer
{
    protected Environment $engine;
    protected \Swift_Mailer $mailer;

    public function __construct(
        Environment $engine,
        \Swift_Mailer $mailer
    )
    {
        $this->engine = $engine;
        $this->mailer = $mailer;
    }

    public function sendMessage($from, $to, $subject, $body, $attachement = null)
    {
        $mail = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setReplyTo($from)
            ->setContentType('text/html');

        //$this->mailer->send($mail);
        if (!$this->mailer->send($mail, $errors)) {
            dump($errors);
            exit;
        }
        else {
            dump($errors);
            //exit;
        }
    }

    public function createBodyMail($view, array $parameters)
    {
//        dump($view);
//        dump($parameters);
//        exit;
        return $this->engine->render($view, $parameters);
    }
}