<?php

namespace App\Entity;

use App\Repository\ContactTopicRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactTopicRepository::class)]
class ContactTopic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $topicName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopicName(): ?string
    {
        return $this->topicName;
    }

    public function setTopicName(string $name): static
    {
        $this->topicName = $name;

        return $this;
    }
}
