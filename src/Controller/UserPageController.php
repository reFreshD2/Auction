<?php

namespace App\Controller;

use App\Repository\Exception\CantSaveUser;
use App\Repository\UserRepository;
use App\Entity\User;
use Monolog\Logger;
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

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(UserRepository $userRepository, Logger $logger)
    {
        $cookie = $_COOKIE['auth_key'];
        $this->logger = $logger;
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
    public function changeData(Request $request)
    {
        if (isset($request)) {
            if ($request->request->has("password")) {
                $password = $request->request->get("password");
                $this->user->setPassword($password);
            }
            if ($request->request->has("personal")) {
                $FIO = $request->request->get("personal");
                $this->user->setFIO($FIO);
            }
            if ($request->request->has("birthday")) {
                $birthday = new \DateTime($request->request->get("birthday"));
                $this->user->setBirthday($birthday);
            }
            try {
                $this->userRepository->save($this->user);
            } catch (CantSaveUser $exception) {
                $this->logger->error($exception->getMessage(), [
                    'user' => $this->user->toArray(),
                    'trace' => $exception->getTraceAsString()
                ]);
            }
        }
        return $this->redirectToRoute('user_page');
    }

    /**
     * @Route("/payment", name="payment")
     * @param Request $request
     * @return Response
     */
    public function payment(Request $request): Response
    {
        if ($request->request->has('cash')) {
            $cash = $this->user->getCash();
            $cash += $request->request->get('cash');
            $this->user->setCash($cash);
            try {
                $this->userRepository->save($this->user);
                return $this->redirectToRoute('user_page');
            } catch (CantSaveUser $exception) {
                $this->logger->error($exception->getMessage(), [
                    'user' => $this->user->toArray(),
                    'trace' => $exception->getTraceAsString()
                ]);
            }
        }
        return $this->render('user_page/payment.html.twig');
    }

    /**
     * @Route ("/logout", name="logout")
     */
    public function logout()
    {
        setcookie("auth_key", "", time() - 3600, "/");
        return $this->redirectToRoute("identification");
    }
}
