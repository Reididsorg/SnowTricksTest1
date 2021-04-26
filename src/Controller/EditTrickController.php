<?php


namespace App\Controller;


use App\Forms\TrickType;
use App\Repository\TrickRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class EditTrickController extends BaseController
{
    protected Environment $templating;
    protected FormFactoryInterface $formFactory;
    protected FlashBagInterface $flashBag;
    protected TrickRepository $trickRepository;

    public function __construct(
        Environment $templating,
        FormFactoryInterface $formFactory,
        FlashBagInterface $flashBag,
        TrickRepository $trickRepository
    )
    {
        $this->templating = $templating;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/edit/tricks/{slug}", name="app_edit_trick")
     */
    public function editTrick($slug, Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($slug);

        //dump($request);
        //dump($trick);
        //exit;

        /*$form = $this->formFactory->create(TrickType::class)
            ->handleRequest($request);*/

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);


        //dump($form);
        //exit;

        return new Response(
            $this->templating->render(
                'tricks/edit_trick.html.twig',
                [
                    'form' => $form->createView(),
                ]
            )
        );
    }
}