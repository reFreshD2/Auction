<?php

namespace App\Controller;

use App\Entity\Exception\CantComputeAuthKey;
use App\Repository\Exception\CantSaveUser;
use DateTime;
use Monolog\Logger;
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
     * @var Logger
     */
    private $logger;

    /**
     * IdentificationController constructor.
     *
     * @param UserRepository $userRepository
     * @param Logger $logger
     */
    public function __construct(UserRepository $userRepository, Logger $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/identification", name="identification")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request): Response
    {
        if ($request->request->has('signIn')) {
            $signIn = $this->signIn($request);
            $secure = $signIn['user']->getSecurity();
            if ($signIn['success']) {
                if ($secure == User::COMMON_USER || $secure == User::AUCTION_USER) {
                    return $this->redirectToRoute('user_page');
                } else {
                    // todo страница модерирования
                }
            } else {
                return $this->render('identification/index.html.twig', [
                    'message' => $signIn['message'],
                    'signIn' => true
                ]);
            }
        }

        if ($request->request->has('signUp')) {
            $signUp = $this->singUp($request);
            if ($signUp['success']) {
                setcookie('auth_key', $signUp['user']->getAuthKey(), 0, '/');
                $secure = $signUp['user']->getSecurity();
                if ($secure == User::COMMON_USER || $secure == User::AUCTION_USER) {
                    return $this->redirectToRoute('user_page');
                } else {
                    // todo страница модерирования
                }
            } else {
                return $this->render('identification/index.html.twig', [
                    'message' => $signUp['message'],
                    'signIn' => false
                ]);
            }
        }

        return $this->render('identification/index.html.twig', [
            'message' => '',
            'signIn' => true
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function signIn(Request $request): array
    {
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        if (!empty($login) && !empty($password)) {
            $user = $this->userRepository->findOneBy(['login' => $login, 'password' => $password]);
            if (isset($user)) {
                setcookie('auth_key', $user->getAuthKey(), 0, '/');
                return ['success' => true, 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Неверный логин и/или пароль'];
            }
        } else {
            return ['success' => false, 'message' => 'Заполните все поля'];
        }
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function singUp(Request $request): array
    {
        $login = $request->request->get('login');
        $password = $request->request->get('password');
        if (!empty($login) && !empty($password)) {
            $user = new User();
            $user->setLogin($login);
            $user->setPassword($password);
            try {
                $user->setAuthKey();
            } catch (CantComputeAuthKey $exception) {
                $this->logger->error($exception->getMessage(),
                    [
                        'login' => $login,
                        'password' => $password,
                        'trace' => $exception->getTraceAsString()
                    ]);
                return ['success' => false, 'message' => 'Упс, что то пошло не так'];
            }
            if ($request->request->has('birthday')) {
                $user->setBirthday(new DateTime($request->request->get('birthday')));
            }
            if ($request->request->has('personal')) {
                $user->setFIO($request->request->get('personal'));
            }
            if ($this->userRepository->findOneBy(['login' => $user->getLogin()]) !== null) {
                return ['success' => false, 'message' => 'Данный логин занят. Попробуйте: ' . $user->getLogin() . '1'];
            }
            try {
                $this->userRepository->save($user);
            } catch (CantSaveUser $exception) {
                $this->logger->error($exception->getMessage(), [
                    'user' => $user->toArray(),
                    'trace' => $exception->getTraceAsString()
                ]);
                return ['success' => false, 'message' => 'Упс, что то пошло не так'];
            }
            return ['success' => true, 'user' => $user];
        }
        return ['success' => false, 'message' => 'Заполните все основные поля'];
    }
}
