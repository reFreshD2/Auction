<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\Exception\CantSaveProduct;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use CURLFile;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $key;

    public function __construct(
        ProductRepository $productRepository,
        UserRepository $userRepository,
        Logger $logger,
        string $uri,
        string $key)
    {
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $cookie = $_COOKIE['auth_key'];
        $this->user = $userRepository->findOneBy(['auth_key' => $cookie]);
        $this->uri = $uri;
        $this->key = $key;
    }

    /**
     * @Route("/product_add", name="product_add", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        if ($request->request->has('isAdd')) {
            $product = new Product();
            if ($request && $request->request->has('name') && $request->request->has('category')
                && $request->request->has('price') && $request->request->has('description')
                && $request->files->has('photo')) {
                $product->setName($request->request->get('name'));
                $product->setCategory($request->request->get('category'));
                $product->setPrice($request->request->get('price'));
                $product->setDescription($request->request->get('description'));
                $product->setModerationStatus(Product::UNDER_MODERATION);
                $product->setCreatedAt(new \DateTime('now'));
                $product->setUser($this->user);
                $file = $request->files->get('photo');
                $file->move(dirname(__DIR__, 2) . '/tmp/', $file->getClientOriginalName());
                $ch = curl_init($this->uri);
                $data = [
                    'image' => new CURLFile('/var/www/html/client/мем.png', 'image/png', 'img.png'),
                    'key' => $this->key
                ];
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: multipart/form-data"));
                $response = curl_exec($ch);
                if ($response['success']) {
                    $product->setUrl($response['data']['url']);
                } else {
                    $this->logger->error("Ошибка загрузки изображения", [
                        'img' => $request->files->get('photo'),
                        'response' => $response,
                        'data' => $data,
                        'uri' => $this->uri
                    ]);
                }
                try {
                    $this->productRepository->save($product);
                    return $this->redirectToRoute('user_page');
                } catch (CantSaveProduct $exception) {
                    $this->logger->error("Ошибка сохранения товара", [
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTraceAsString()
                    ]);
                    return $this->render('product/add.html.twig', ['message' => "Упс, что-то пошло не так"]);
                }
            } else {
                return $this->render('product/add.html.twig', ['message' => "Заполните все поля"]);
            }
        } else {
            return $this->render('product/add.html.twig', ['message' => '']);
        }
    }
}
