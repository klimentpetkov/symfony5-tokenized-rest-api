<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation\SoftDeleteable;

/**
 * @ApiResource()
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 * @SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Task
{
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=190)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="tasks")
     * @ORM\JoinColumn(name="project", referencedColumnName="id", nullable=false)
     */
    private $project;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the related project
     *
     * @return Project|null
     */
    public function getProject() : ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project) : self
    {
        $this->project = $project;

        return $this;
    }
}