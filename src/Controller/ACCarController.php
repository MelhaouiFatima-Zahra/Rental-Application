<?php

namespace App\Controller;

use App\Repository\CarRepository;
use App\Service\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ACCarController extends AbstractController
{
    private SerializerInterface $serializer;
    private CarRepository $carRepository;
    private ApiResponse $apiResponse;


    public function __construct(SerializerInterface $serializer, CarRepository $carRepository, ApiResponse $apiResponse)
    {
        $this->serializer = $serializer;
        $this->carRepository = $carRepository;
        $this->apiResponse = $apiResponse;
    }

    /**
     * @Route("/api/cars", methods={"GET"})
     */
    public function getAllCars(): JsonResponse
    {
        try {
            $result = [];
            foreach ($this->carRepository->findAll() as $car) {
                $result[] = $this->serializer->normalize($car);
            }

            return $this->apiResponse->success($result);
        } catch (ExceptionInterface $e) {
            $this->apiResponse->errorException($e);
        }
    }

    /**
     * @Route("/api/cars/{id}", methods={"GET"})
     */
    public function getCarById(int $id): JsonResponse
    {
        if (null === $car = $this->carRepository->find($id)) {
            return $this->apiResponse->notFound();
        }

        try {
            $result = $this->serializer->normalize($car);

            return $this->apiResponse->success($result);
        } catch (ExceptionInterface $e) {
            $this->apiResponse->errorException($e);
        }
    }
}
