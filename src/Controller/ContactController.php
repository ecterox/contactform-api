<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ContactMessage;

final class ContactController extends AbstractController
{
    #[Route(
        '/api/contact',
        name: 'new_contact_message',
        priority: 0
    )]
    public function newContactMessage(EntityManagerInterface $entityManager, Request $request): Response
    {
        $contactmessage = new ContactMessage();
        $contactmessage->setName('Max Mustermann');
        $contactmessage->setPhonenumber('01234567890');
        $contactmessage->setEmail('max@mustermann.de');
        $contactmessage->setTopic('Konto');
        $contactmessage->setMessage('Dies ist eine Testnachricht!');

        // Tells doctrine that we eventually want to save this contact message
        $entityManager->persist($contactmessage);

        // Executes the queries
        $entityManager->flush();

        $data = json_decode($request->getContent(), true);

        return new Response(
            //'Inserted at: '.$contactmessage->getId(),
            $data,
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
}
