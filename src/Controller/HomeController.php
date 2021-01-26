<?php

namespace App\Controller;

use App\Entity\House;
use App\Form\HouseType;
use App\Repository\HouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(
        HouseRepository $houseRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $sort = $request->query->get('sort', 'rental');
        $direction = $request->query->get('direction', 'ASC');

        if (
            $sort != 'rental' ||
            !in_array(strtoupper($direction), ['ASC', 'DESC'])
        ) {
            throw $this->createNotFoundException();
        }

        // $script = "<script>alert('toto'); </script>";
        $script = '<h1>Hello World !</h1>';

        return $this->render('home/index.html.twig', [
            'houses' => $houseRepository->findAllJoinCategory($sort, $direction),
            'script' => $script
        ]);
    }

    /**
     * @Route("/new", name="new_house")
     */
    public function newHouse(Request $request): Response
    {
        $house = new House();
        $form = $this->createForm(HouseType::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($house);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('home/new_house.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
