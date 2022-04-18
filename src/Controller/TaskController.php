<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractFOSRestController
{
    /**
     * Logger
     *
     * @var LoggerInterface $logger;
     */
    protected $logger;

    /**
     * Entity manager
     *
     * @var [type]
     */
    protected $doctrine;

    public function __construct(
        LoggerInterface $logger,
        ManagerRegistry $doctrine
    )
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine->getManager();
        $logger->info('Task controller initialized');
    }

    public function index(string $projectId, Request $request): Response
    {
        /**
         * @var TaskRepository $repo
         */
        $repo = $this->doctrine->getRepository(Task::class);
        $tasks = $repo->findAllWithProject($projectId);

        $data = [];
        foreach ($tasks as $task) {
           $data[] = [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'deletedAt' => $task->getDeletedAt(),
                'project' => $task->getProject()
           ];
        }

        return $this->json(['code' => Response::HTTP_OK, 'data' => $data]);
    }

    public function show(string $projectId, string $id) : Response
    {
        /**
         * @var TaskRepository $repo
         */
        $repo = $this->doctrine->getRepository(Task::class);
        $task = $repo->findWithProject($projectId, $id);

        if (!$task) {
            return $this->json(['code' => Response::HTTP_NOT_FOUND, 'message' => 'No task found for id: ' . $id]);
        }

        $data = [];
        $data =  [
            'id' => $task->getId(),
            'name' => $task->getName(),
            'project' => $task->getProject(),
            'deletedAt' => $task->getDeletedAt()
        ];

        return $this->json(['code' => Response::HTTP_OK, 'task' => $data]);
    }

    public function store(string $projectId, Request $request): Response
    {
        $project = $this->doctrine
                    ->getRepository(Project::class)
                    ->findOneBy(['id' => $projectId]);

        // @var Task $task
        $task = new Task();
        $task->setName($request->get('name'));

        $project->addTask($task);

        $this->doctrine->persist($task);
        $this->doctrine->persist($project);
        $this->doctrine->flush();

        return $this->json(['code'=> Response::HTTP_OK, 'id' => $task->getId()]);
    }


    public function update(Request $request, string $id): Response
    {
        $task = $this->doctrine
                    ->getRepository(Task::class)
                    ->find($id);

        if (!$task) {
            return $this->json(['code' => Response::HTTP_NOT_FOUND, 'message' => 'No task found for id: ' . $id]);
        }

        $task->setName($request->get('name'));
        $this->doctrine->persist($task);
        $this->doctrine->flush();

        $data =  [
            'id' => $task->getId(),
            'name' => $task->getName(),
            'deletedAt' => $task->getDeletedAt(),
            'project' => [
                'id' => $task->getProject()->getId(),
                'title' => $task->getProject()->getTitle(),
                'description' => $task->getProject()->getDescription(),
                'status' => $task->getProject()->getStatus(),
                'duration' => $task->getProject()->getDuration(),
                'client' => $task->getProject()->getClient(),
                'company' => $task->getProject()->getCompany(),
            ]
        ];

        return $this->json(['code' => Response::HTTP_OK, 'task' => $data]);
    }

    public function delete(string $projectId, string $id): Response
    {
        $project = $this->doctrine
                    ->getRepository(Project::class)
                    ->find($projectId);

        if (!$project) {
            return $this->json(['code' => Response::HTTP_NOT_FOUND, 'message' => 'No project found for id: ' . $projectId]);
        }

        $task = $this->doctrine
                    ->getRepository(Task::class)
                    ->find($id);

        if (!$task) {
            return $this->json(['code' => Response::HTTP_NOT_FOUND, 'message' => 'No task found for id: ' . $id]);
        }

        $project->removeTask($task);

        $this->doctrine->persist($project);
        $this->doctrine->persist($task);
        $this->doctrine->flush();

        return $this->json(['code'=> Response::HTTP_OK, 'message' => 'Successfully deleted task  with id: ' . $id]);
    }
}