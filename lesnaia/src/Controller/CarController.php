<?php

namespace App\Controller;

use App\Entity\Car;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\CarService;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Form\CarFormType;
use Psr\Log\LoggerInterface;

class CarController extends AbstractController
{

    #[Route('/')]
    public function index(LoggerInterface $logger): Response
    {
        $logger->info("Index page: user IP " . $_SERVER['REMOTE_ADDR']);
        return $this->render('car/index.html.twig', []);
    }

    #[Route('/cars', name: "display_cars")]
    public function displayCars(ManagerRegistry $doctrine, LoggerInterface $logger):Response
    {
        $car = new CarService($doctrine->getManager(), Car::class);
        $cars = $car->getAllCars();
        $logger->info("List of cars page: user IP " . $_SERVER['REMOTE_ADDR']);
        return $this->render('default/cars.html.twig', array
        ('cars' => $cars));
    }

    #[Route('/car', name: 'create_car')]
    public function createCar(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger):Response
    {
        $car = new Car();

        $form = $this->createForm(CarFormType::class, $car);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $car = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($car);
            $entityManager->flush();
            $logger->info("Create car action: user IP " . $_SERVER['REMOTE_ADDR']);
            return $this->redirectToRoute('display_cars');
        }
    
        $logger->info("Create car action: user IP " . $_SERVER['REMOTE_ADDR']);
        return $this->render('default/form.html.twig',array(
            'form'=>$form->createView()
        ));
    }

    #[Route('/car/{id}', name: 'display_car')]
    public function displayCar(int $id, ManagerRegistry $doctrine, LoggerInterface $logger):Response
    {    
        $car  = new CarService($doctrine->getManager(),Car::class);
        $car = $car->getCar($id);
        $logger->info("Car page: user IP " . $_SERVER['REMOTE_ADDR']);
        return $this->render('car/display.html.twig', array('car' =>$car));

    }

    #[Route('/car/update/{id}', name: 'update_car')]
    public function updateCar( Request $request, $id, ManagerRegistry $doctrine, LoggerInterface $logger):Response
    {
        $car = $doctrine->getRepository(Car::class)->find($id);

        $form = $this->createForm(CarFormType::class, $car);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $carService = new CarService($doctrine->getManager(), Car::class);
            $carService->addCar($car);
            $logger->info("Update car action: user IP " . $_SERVER['REMOTE_ADDR']);
            return $this->redirectToRoute('display_cars');
        }
        $logger->info("Update car action: user IP " . $_SERVER['REMOTE_ADDR']);
        return $this->render('default/form.html.twig',array(
            'form'=>$form->createView()
        ));
    }

    #[Route('/car/delete/{id}', name: 'delete_car')]
    public function deleteCar(Request $request, $id, ManagerRegistry $doctrine, LoggerInterface $logger):Response
    {
        $car = new CarService($doctrine->getManager(), Car::class);
        $car->deleteCar($id);
        $logger->info("Delete car action: user IP " . $_SERVER['REMOTE_ADDR']);
        return $this->redirectToRoute('display_cars');
    }
}
