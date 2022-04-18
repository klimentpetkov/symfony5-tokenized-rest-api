<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends AbstractFOSRestController
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

        $logger->info('Project controller initialized');
    }

    public function index(Request $request): Response
    {
        /**
         * @var ProjectRepository $repo
         * */
        $repo = $this->doctrine->getRepository(Project::class);
        $projects = $repo->findAllWithTasks();

        $data = [];
        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'title' => $project->getTitle(),
                'description' => $project->getDescription(),
                'status' => $project->getStatus(),
                'duration' => $project->getDuration(),
                'client' => $project->getClient(),
                'company' => $project->getCompany(),
                'deletedAt' => $project->getDeletedAt(),
                'tasks' => $project->getTasks()
            ];
        }

        return $this->json(['code' => Response::HTTP_OK, 'data' => $data]);
    }

    public function show(string $id) : Response
    {
        /**
         * @var ProjectRepository $repo
         * */
        $repo = $this->doctrine->getRepository(Project::class);
        $project = $repo->findWithTasks($id);

        if (!$project) {
            return $this->json(['code' => Response::HTTP_NOT_FOUND, 'message' => 'No project found for id: ' . $id]);
        }

        $data =  [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'description' => $project->getDescription(),
            'status' => $project->getStatus(),
            'duration' => $project->getDuration(),
            'client' => $project->getClient(),
            'company' => $project->getCompany(),
            'deletedAt' => $project->getDeletedAt(),
            'tasks' => $project->getTasks()
        ];

        return $this->json(['code' => Response::HTTP_OK, 'project' => $data]);
    }

    public function store(Request $request): Response
    {
        // $form = $this->buildForm(ProjectType::class);

        // $form->handleRequest($request);

        // if (!$form->isSubmitted() || !$form->isValid()) {
        //     $this->logger->error('Form data is not valid');
        //     return $this->json(['code' => Response::HTTP_BAD_REQUEST, $form]);
        // }

        // /** @var Project $project */
        // $project = $form->getData();
        $project = new Project();
        $project->setTitle($request->get('title'));
        $project->setDescription($request->get('description'));
        $project->setStatus($request->get('status'));
        $project->setDuration($request->get('duration'));
        $project->setClient($request->get('client'));
        $project->setCompany($request->get('company'));

        $this->doctrine->persist($project);
        $this->doctrine->flush();

        return $this->json(['code'=> Response::HTTP_OK, 'id' => $project->getId()]);
    }


    public function update(Request $request, string $id): Response
    {
        $project = $this->doctrine->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['code' => Response::HTTP_NOT_FOUND, 'message' => 'No project found for id: ' . $id]);
        }

        $project->setTitle($request->get('title'));
        $project->setDescription($request->get('description'));
        $project->setStatus($request->get('status'));
        $project->setDuration($request->get('duration'));
        $project->setClient($request->get('client'));
        $project->setCompany($request->get('company'));

        $this->doctrine->flush();

        $data =  [
            'id' => $project->getId(),
            'title' => $project->getTitle(),
            'description' => $project->getDescription(),
            'status' => $project->getStatus(),
            'duration' => $project->getDuration(),
            'client' => $project->getClient(),
            'company' => $project->getCompany(),
            'deletedAt' => $project->getDeletedAt()
        ];

        return $this->json(['code' => Response::HTTP_OK, 'project' => $data]);
    }

    public function delete(string $id): Response
    {
        $project = $this->doctrine->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->json(['code' => Response::HTTP_NOT_FOUND, 'message' => 'No project found for id: ' . $id]);
        }

        $this->doctrine->remove($project);
        $this->doctrine->flush();

        return $this->json(['code'=> Response::HTTP_OK, 'message' => 'Successfully deleted project  with id: ' . $id]);
    }
}