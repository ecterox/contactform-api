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
        $headers = ['content-type' => 'application/json',
                   'Access-Control-Allow-Origin' => '*'];

        // Convert json string to php array
        $json = $request->getContent();
        $data = json_decode($json, true);

        // Check if all fields, we expect to be there, are present
        $keysToCheck = ['title', 'name', 'email', 'phone', 'topic', 'message'];
        $missingFields = array_diff($keysToCheck, array_keys($data));

        if ($missingFields) {
            return new Response(
                json_encode(['missingFields' => $missingFields]),
                Response::HTTP_BAD_REQUEST,
                $headers
            );
        }

        // Create new entity and fill in the data
        $contactMessage = new ContactMessage();
        $contactMessage->setTitle($data['title']);

        $str = explode(" ", $data['name']);
        $contactMessage->setFirstName(($str[0]));
        $contactMessage->setLastName(($str[1]));

        $contactMessage->setEmail($data['email']);
        $contactMessage->setPhonenumber($data['phone']);
        $contactMessage->setTopic($data['topic']);
        $contactMessage->setMessage($data['message']);
        $contactMessage->setTimestamp(new \DateTime());

        // Validate entity's data
        $violations = $validator->validate($contactMessage);
        if ($violations->count()) {
            return new Response(
                json_encode(['violations' => $violations]),
                Response::HTTP_BAD_REQUEST,
                $headers
            );
        }

        // Tells doctrine that we eventually want to save this contact message
        $entityManager->persist($contactMessage);

        // Executes the queries
        $entityManager->flush();

        return new Response(
            //'Inserted at: '.$contactMessage->getId(),
            $json,
            Response::HTTP_OK,
            $headers
        );
    }

    #[Route(
        '/api/contact/topics',
        name: 'get_contact_topics',
        methods: ['GET'],
        priority: 0
    )]
    public function getContactTopics(EntityManagerInterface $entityManager): Response
    {
        return new Response(
            json_encode(['topics' => ['Allgemein', 'Konto', 'Bestellung', 'Zahlung', 'Sonstiges']]),
            Response::HTTP_OK,
            ['content-type' => 'application/json',
             'Access-Control-Allow-Origin' => '*']
        );
    }
}
