<?php


namespace App\Controller\Trick;


use App\Controller\BaseController;
use App\Entity\Comment;
use App\Forms\CommentType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class DisplayTrickController extends BaseController
{
    protected Environment $templating;
    protected TrickRepository $trickRepository;
    protected EntityManagerInterface $entityManager;
    protected FlashBagInterface $flashBag;
    protected UrlGeneratorInterface $urlGenerator;

    public function __construct (
        Environment $templating,
        TrickRepository $trickRepository,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->templating = $templating;
        $this->trickRepository = $trickRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/tricks/{slug}", name="app_display_trick")
     */
    public function displayTrick($slug, Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($slug);

        $comment = new Comment();

        $form = $this->formFactory->create(CommentType::class, $comment)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //$comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'Ton commentaire a bien été enregistré !');

            return new RedirectResponse(
                $this->urlGenerator->generate(
                    'app_display_trick',
                    [
                        'slug' => $slug,
                        'form' => $form->createView()
                    ]
                )
            );
        }

        return new Response(
            $this->templating->render(
                'tricks/display_trick.html.twig',
                [
                    'trick' => $trick,
                    'form' => $form->createView()
                ]
            )
        );
    }
}