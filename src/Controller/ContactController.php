<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ContactMessage;

use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContactController extends AbstractController
{
    #[Route(
        '/api/contact',
        name: 'new_contact_message',
        methods: ['POST'],
        priority: 0
    )]
    public function newContactMessage(ValidatorInterface $validator,
        EntityManagerInterface $entityManager, Request $request): Response
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $contactMessage = new ContactMessage();
        $contactMessage->setName($data['name']);
        $contactMessage->setPhonenumber($data['phone']);
        $contactMessage->setEmail($data['email']);
        $contactMessage->setTopic($data['topic']);
        $contactMessage->setMessage($data['message']);
        $contactMessage->setTimestamp(new \DateTime());

        $violations = $validator->validate($contactMessage);
        if (!$violations->count()) {
            // Tells doctrine that we eventually want to save this contact message
            $entityManager->persist($contactMessage);

            // Executes the queries
            $entityManager->flush();
        }

        return new Response(
            //'Inserted at: '.$contactMessage->getId(),
            $json,
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
                'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
}
