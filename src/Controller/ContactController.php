<?php

namespace App\Controller;

use App\Repository\ContactMessageRepository;
use App\Repository\ContactTitleRepository;
use App\Repository\ContactTopicRepository;

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
        name: 'create_contact_message',
        methods: ['POST'],
        priority: 0
    )]
    public function createContactMessage(
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        ContactTopicRepository $contactTopicRepository,
        ContactTitleRepository $contactTitleRepository,
        Request $request): Response
    {
        $headers = ['content-type' => 'application/json',
                   'Access-Control-Allow-Origin' => '*'];

        // Convert json string to php array
        $json = $request->getContent();
        $data = json_decode($json, true);

        // Check if all fields, we expect to be there, are present
        $keysToCheck = ['title', 'name', 'email', 'phone', 'topic', 'message'];
        $missingFields = array_diff($keysToCheck, array_keys($data));

        try {
            if ($missingFields) {
                throw new \RuntimeException(json_encode(['missingFields' => $missingFields]));
            }

            $title = $contactTitleRepository->findOneBy(
                ['titleName' => $data['title']]
            );

            if (!$title) {
                throw new \RuntimeException('Title not found.');
            }

            $topic = $contactTopicRepository->findOneBy(
                ['topicName' => $data['topic']]
            );

            if (!$topic) {
                throw new \RuntimeException('Topic not found.');
            }
        } catch (\Doctrine\DBAL\Exception $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_NOT_FOUND,
                $headers
            );
        }

        /*$title = $contactTitleRepository->findOneBy(
            ['titleName' => $data['title']]
        );
        $topic = $contactTopicRepository->findOneBy(
            ['topicName' => $data['topic']]
        );

        if (!$title || !$topic) {
            return new Response(
                'Title or topic not found',
                Response::HTTP_BAD_REQUEST,
                $headers
            );
        }*/

        // Create new entity and fill in the data
        $contactMessage = new ContactMessage();
        $contactMessage->setTitle($title->getId());

        $str = explode(" ", $data['name']);
        $contactMessage->setFirstName(($str[0]));
        $contactMessage->setLastName(($str[1]));

        $contactMessage->setEmail($data['email']);
        $contactMessage->setPhonenumber($data['phone']);
        $contactMessage->setTopic($topic->getId());
        $contactMessage->setMessage($data['message']);
        $contactMessage->setTimestamp(new \DateTime());

        // Validate entity's data
        $violations = $validator->validate($contactMessage);

        try {
            if ($violations->count()) {
                throw new \RuntimeException(json_encode(['violations' => $violations]));
            }

            // Tells doctrine that we eventually want to save this contact message
            $entityManager->persist($contactMessage);

            // Executes the queries
            $entityManager->flush();
        } catch (\Doctrine\DBAL\Exception $e) {
            return new Response(
                $e->getMessage(),
                Response::HTTP_BAD_REQUEST,
                $headers
            );
        }
        // Create new entity and fill in the data
        /*$contactMessage = new ContactMessage();
        $contactMessage->setTitle($title->getId());

        $str = explode(" ", $data['name']);
        $contactMessage->setFirstName(($str[0]));
        $contactMessage->setLastName(($str[1]));

        $contactMessage->setEmail($data['email']);
        $contactMessage->setPhonenumber($data['phone']);
        $contactMessage->setTopic($topic->getId());
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
        $entityManager->flush();*/

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
    public function getContactTopics(ContactTopicRepository $contactTopicRepository): Response
    {
        $topicEntities = $contactTopicRepository->findAll();
        $topicArray = array_map(fn($topicEntity) => $topicEntity->getTopicName(), $topicEntities);

        return new Response(
            json_encode($topicArray),
            Response::HTTP_OK,
            ['content-type' => 'application/json',
             'Access-Control-Allow-Origin' => '*']
        );
    }

    #[Route(
        '/api/contact/titles',
        name: 'get_contact_titles',
        methods: ['GET'],
        priority: 0
    )]
    public function getContactTitles(ContactTitleRepository $contactTitleRepository): Response
    {
        $titleEntities = $contactTitleRepository->findAll();
        $titleArray = array_map(fn($titleEntity) => $titleEntity->getTitleName(), $titleEntities);

        return new Response(
            json_encode($titleArray),
            Response::HTTP_OK,
            ['content-type' => 'application/json',
             'Access-Control-Allow-Origin' => '*']
        );
    }

    #[Route(
        '/api/contact/messages',
        name: 'get_contact_messages',
        methods: ['GET'],
        priority: 0
    )]
    public function getContactMessages(ContactMessageRepository $contactMessageRepository): Response
    {
        $messageEntities = $contactMessageRepository->findAll();
        $messageArray = array_map(fn($messageEntity) => $messageEntity->toArray(), $messageEntities);

        return new Response(
            json_encode($messageArray),
            Response::HTTP_OK,
            ['content-type' => 'application/json',
                'Access-Control-Allow-Origin' => '*']
        );
    }
}
