<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Service\ApiDefaultResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ADCarController extends AbstractController
{

    private CarRepository $carRepository;
    private ApiDefaultResponse $apiDefaultResponse;


    public function __construct(ApiDefaultResponse $apiDefaultResponse, CarRepository $carRepository)
    {
        $this->apiDefaultResponse = $apiDefaultResponse;
        $this->carRepository = $carRepository;
    }

    /**
     * @Route("/api/cars", methods={"GET"})
     */
    public function getAllCars(): JsonResponse
    {
        return $this->apiDefaultResponse->getAll($this->carRepository);
    }

    /**
     * @Route("/api/cars/{id}", methods={"GET"})
     */
    public function getCarById(int $id): JsonResponse
    {
        return $this->apiDefaultResponse->getOneById($this->carRepository, $id);
    }
}
