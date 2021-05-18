<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *  * @ORM\Column(type="string", length=255)
     * 
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "Le titre de l'article doit comporter {{ limit }} caractères au minimum",
     *      maxMessage = "Le titre de l'article doit comporter {{ limit }} caractères au maximum"
     * )
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     *  @Assert\Length(
     *      min = 10,
     *      max = 500,
     *      minMessage = "L'article doit comporter {{ limit }} caractères au minimum",
     *      maxMessage = "L'article doit comporter {{ limit }} caractères au maximum"
     * 
     * 
     * 
     */
    private $contenu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
