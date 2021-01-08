<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPageController extends AbstractController
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository, Request $request)
    {
        $cookie = $request->cookies->get('auth_key');
        $this->user = $userRepository->findOneBy(['auth_key' => $cookie]);
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user", name="user_page")
     */
    public function index(): Response
    {
        switch ($this->user->getSecurity()) {
            case User::COMMON_USER:
                return $this->render('user_page/common_user.html.twig',
                    ['user' => $this->user]);
            case User::AUCTION_USER:
                return $this->render();
        }
    }

    /**
     * @Route("/change", name="user_change")
     *
     * @param Request $request
     */
    protected function changeData(Request $request) {
        // todo сделать функцию изменения данных пользователя
    }
}
