<?php

namespace App\Service;

use App\Entity\Car;
use App\Repository\CarRepository;
use App\Entity\Reservation;

class CarService
{
    private $carRepository;

    public function __construct(CarRepository $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function getListOfCars()
    {
        // Use the CarRepository to fetch the list of cars
        return $this->carRepository->findAll();
    }

    public function getCarDetailsById($id)
    {
        // Use the CarRepository to fetch the details of a specific car by its ID
        return $this->carRepository->find($id);
    }


}
