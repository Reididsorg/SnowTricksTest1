<?php


namespace App\Controller\Trick;


use App\Controller\BaseController;
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
    protected string $targetDirectory;

    public function __construct(
        Environment $templating,
        TrickRepository $trickRepository,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        string $targetDirectory
    )
    {
        $this->templating = $templating;
        $this->trickRepository = $trickRepository;
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @Route("/remove/tricks/{slug}", name="app_remove_trick")
     */
    public function removeTrick($slug)
    {
        $trick = $this->trickRepository->findOneBySlug($slug);

        // Remove files
        foreach($trick->getImages() as $trickImage)
        {
            $filePath = $this->targetDirectory . '/' . $trickImage->getFileName();
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Remove trick
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'Le trick "' . $trick->getName() . '" a été correctement supprimé :)');

        return new RedirectResponse(
            $this->urlGenerator->generate('app_homepage')
        );
    }
}