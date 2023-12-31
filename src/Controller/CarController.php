<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Service\CarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private $carService;

    public function __construct(CarService $carService)
    {
        // Inject the CarService into the controller through constructor
        $this->carService = $carService;
    }

    /**
     * @Route("/api/cars", name="api_cars_list", methods={"GET"})
     */
    public function listCars()
    {
        // Use the CarService to get the list of cars
        $cars = $this->carService->getListOfCars();
        $carDetails = [];
        foreach ($cars as $car) {

            $carDetails[] = [
                'id' => $car->getId(),
                'brand' => $car->getBrand(),
                'model' => $car->getModel(),
                'fuelType' => $car->getFuelType(),
                'color' => $car->getColor(),
                'dailyRentalPrice' => $car->getDailyRentalPrice(),
                'mileage' => $car->getMileage(),
                'numberOfSeats' => $car->getNumberOfSeats(),
                'transmission' => $car->getTransmission(),
                'image' => $car->getImage(),
                'yearOfManufacture' => $car->getYearOfManufacture(),
            ];
        }
        // Return the list of cars as a JSON response
        return new JsonResponse(['cars' => $carDetails]);
    }

    /**
     * @Route("/api/cars/{id}", name="api_car_details", methods={"GET"})
     */
    public function carDetails($id)
    {
        // Use the CarService to get the details of the car by ID
        $carDetails = $this->carService->getCarDetailsById($id);

        // Check if the car is found
        if (!$carDetails) {
            // Handle the case where the car is not found
            return new JsonResponse(['error' => 'Car not found'], 404);
        }

        // Transform the car details into an array or use a serializer if needed
        $formattedCarDetails = [
            'id' => $carDetails->getId(),
            'brand' => $carDetails->getBrand(),
            'model' => $carDetails->getModel(),
            'fuelType' => $carDetails->getFuelType(),
            'color' => $carDetails->getColor(),
            'dailyRentalPrice' => $carDetails->getDailyRentalPrice(),
            'mileage' => $carDetails->getMileage(),
            'numberOfSeats' => $carDetails->getNumberOfSeats(),
            'transmission' => $carDetails->getTransmission(),
            'image' => $carDetails->getImage(),
            'yearOfManufacture' => $carDetails->getYearOfManufacture(),
        ];

        // Return the details of the car as JSON response
        return new JsonResponse(['car' => $formattedCarDetails]);
    }
}
