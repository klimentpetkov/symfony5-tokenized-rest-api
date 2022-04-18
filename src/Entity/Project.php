<?php declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 * @ApiResource()
 * @SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Project
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
     * @ORM\Column(name="title", type="string", length=190)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=190)
     */
    private $description;

    /**
     * @var smallint|null
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status; // 0 - backlog, 1 - in progress, 2 - done

    /**
     * @var integer|null
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration; // days

    /**
     * @var string|null
     *
     * @ORM\Column(name="client", type="string", length=190)
     */
    private $client;

    /**
     * @var string|null
     *
     * @ORM\Column(name="company", type="string", length=190)
     */
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="project", orphanRemoval=true)
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return smallint|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param smallint|null $status
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return integer|null
     */
    public function getDuration(): ?int
    {
        return $this->duration;
    }

    /**
     * @param integer|null $duration
     */
    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return string|null
     */
    public function getClient(): ?string
    {
        return $this->client;
    }

    /**
     * @param string|null $client
     */
    public function setClient(?string $client): void
    {
        $this->client = $client;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * Get all related tasks
     *
     * @return Collection|Task[]
     */
    public function getTasks() : Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task) : void
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setProject($this);
        }
    }

    public function removeTask(Task $task) : void
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getProject() === $this) {
                $task->setProject(null);
            }
        }
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $client = $this->getClient();
        $company = $this->getCompany();

        dump($client, $company);

        if ((null === $client ||  '' === trim($client)) && (null === $company || '' === trim($company)))
            $context->buildViolation('Client and Company fields are blank at least one of them is required!')
                ->atPath('client')
                ->addViolation();

        dd('project validation');
    }
}