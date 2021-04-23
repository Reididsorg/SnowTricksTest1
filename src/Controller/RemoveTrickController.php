<?php


namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class RemoveTrickController extends BaseController
{
    protected $templating;
    protected $trickRepository;
    protected $entityManager;
    protected $flashBag;

    public function __construct(
        Environment $templating,
        TrickRepository $trickRepository,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    )
    {
        $this->templating = $templating;
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/remove/tricks/{slug}", name="app_remove_trick")
     */
    public function removeTrick($slug)
    {
        //$trick = $this->trickRepository->findOneBy(array('id' => $slug));
        $trick = $this->trickRepository->findOneBySlug($slug);

        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'Le trick "' . $trick->getName() . '" a été correctement supprimé :)');

        return new RedirectResponse(
            $this->urlGenerator->generate('app_homepage')
        );
    }
}