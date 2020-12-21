<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;

class IdentificationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * IdentificationController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/identification", name="identification")
     */
    public function index(Request $request): Response
    {
        if ($request->request->has('singIn')) {
            $success = $this->signIn($request, $this->userRepository)['success'];
            if ($success) {
                switch ($success['secure']) {
                    case User::COMMON_USER:
                        return $this->render();
                    case User::AUCTION_USER:
                        return $this->render();
                    case User::MODERATOR:
                        return $this->render();
                }
            } else {
                return $this->render('identification/index.html.twig', ['message' => $success['message']]);
            }
        }

        return $this->render('identification/index.html.twig', [
            'message' => '',
        ]);
    }

    private function signIn(Request $request, UserRepository $userRepository): array
    {
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        if (!empty($login) && !empty($password)) {
            $user = $userRepository->findOneBy(['login' => $login, 'password' => $password]);
            if (isset($user)) {
                setcookie('auth_key', $user->getAuthKey(), 0, '/');
                return ['success' => true, 'secure' => $user->getSecurity()];
            } else {
                return ['success' => false, 'message' => 'Неверный логин и/или пароль'];
            }
        } else {
            return ['success' => false, 'message' => 'Заполните все поля'];
        }
    }
}
