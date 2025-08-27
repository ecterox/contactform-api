<?php

namespace App\Entity;

use App\Repository\ContactTitleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactTitleRepository::class)]
class ContactTitle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titleName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleName(): ?string
    {
        return $this->titleName;
    }

    public function setTitleName(string $titleName): static
    {
        $this->titleName = $titleName;

        return $this;
    }
}
