<?php


namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @Route("/micro-post")
 */
class MicroPostController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var MicroPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * MicroPostController constructor.
     * @param Environment $twig
     * @param MicroPostRepository $microPostRepository
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     */
    public function __construct(Environment $twig,
                                MicroPostRepository $microPostRepository,
                                FormFactoryInterface $formFactory,
                                EntityManagerInterface $entityManager,
                                RouterInterface $router) {

        $this->twig = $twig;
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }


    /**
     * @Route("/", name="micro_post_index")
     */
    public function index() {
        $html = $this->twig->render('micro-post/index.html.twig', [
            'posts' => $this->microPostRepository->findAll()
        ]);

        return new Response($html);
    }


    /**
     * @Route("/add", name="micro_post_add")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function add(Request $request) {
        $microPost = new MicroPost();
        $microPost->setTime(new \DateTime());

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // telling entity manager that entity is ready to be persisted
            $this->entityManager->persist($microPost);
            // execute insert queries
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));
        }

        return new Response($this->twig->render('micro-post/add.html.twig', [
            'form' => $form->createView()
        ]));
    }

    // order of the routes is important because if we put this in front of add route
    // when we go to /add it would be passed as the id for the post route
    /**
     * @Route("/{id}", name="micro_post_post")
     * @param MicroPost $post
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    // Symfony ParamConverter instead of passing id to the function
    public function post(MicroPost $post) {

        // param converter is basically doing this and returning one record of type MicroPost
        // $post = $this->microPostRepository->find($id);

        return new Response($this->twig->render('micro-post/post.html.twig', [
            'post' => $post
        ]));
    }
}