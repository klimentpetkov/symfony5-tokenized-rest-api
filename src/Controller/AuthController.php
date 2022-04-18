<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class AuthController extends AbstractController
{
    /**
     * User repo
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * Security access
     *
     * @var Security $security
     */
    private $security;

    public function __construct(
        UserRepository $userRepository,
        Security $security
    )
    {
        $this->userRepository = $userRepository;
        $this->security = $security;

    }

    public function register(Request $request): Response
    {
        $jsonData = json_decode($request->getContent());
        $user = $this->userRepository->create($jsonData);

        return $this->json([
            'code' => Response::HTTP_CREATED,
            'user' => $user
        ]);
    }
}