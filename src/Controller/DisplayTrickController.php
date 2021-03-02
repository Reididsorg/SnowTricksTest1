<?php


namespace App\Controller;


use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DisplayTrickController extends BaseController
{
    protected $templating;
    protected $trickRepository;

    public function __construct(
        Environment $templating,
        TrickRepository $trickRepository
    )
    {
        $this->templating = $templating;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/tricks/{slug}", name="app_displayTrick")
     */
    public function displayTrick($slug)
    {
        $trick = $this->trickRepository->findOneBySlug($slug);

        return $this->render('tricks/display_trick.html.twig', [
            'trick' => $trick,
        ]);
    }
}