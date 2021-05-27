<?php


namespace App\Controller\Trick;


use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{

   protected TrickRepository $trickRepository;
   protected ImageRepository $imageRepository;

    public function __construct(TrickRepository $trickRepository, ImageRepository $imageRepository)
    {
        $this->trickRepository = $trickRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function showHomepage()
    {
        $tricks = $this->trickRepository->getTricks(0, 5);
        //$allTricks = $this->trickRepository->getAllTricks();
        $images = $this->imageRepository->findAll();

        return $this->render('tricks/homepage.html.twig', [
            'tricks' => $tricks,
            'images' => $images,
        ]);
    }

    /**
     * @Route("/seemoretricks/{offset}", name="app_see_more_tricks")
     */
    public function seeMoreTricks($offset)
    {
        $moreTricks = $this->trickRepository->getTricks($offset, 15);

        return $this->render('tricks/see_more_tricks.html.twig', [
            'tricks' => $moreTricks,
        ]);

    }

}