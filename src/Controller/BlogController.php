<?php


namespace App\Controller;


use App\Service\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/blog")
 */

class BlogController
{

    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(Environment $twig, SessionInterface $session, RouterInterface $router) {
        $this->session = $session;
        $this->twig = $twig;
        $this->router = $router;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index() {
        $html = $this->twig->render(
            'blog/index.html.twig',
            [
                'posts' => $this->session->get('posts')
            ]);

        return new Response($html);
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add() {
        $posts = $this->session->get('posts');

        $posts[uniqid()] = [
            'title' => 'A random title ' . rand(1, 500),
            'text' => 'A random text ' . rand(1, 500),
        ];

        $this->session->set('posts', $posts);

        return new RedirectResponse($this->router->generate('blog_index'));
    }

    /**
     * @Route("/show/{id}", name="blog_show")
     * @param $id
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show($id) {
        $posts = $this->session->get('posts');

        if (!$posts || !isset($posts[$id])) {
            throw new NotFoundHttpException('Post not found');
        }

        $html = $this->twig->render(
            'blog/post.html.twig',
            [
                'id' => $id,
                'post' => $posts[$id],
            ]);

        return new Response($html);
    }


}