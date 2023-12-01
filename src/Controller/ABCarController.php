<?php

namespace App\Controller;

use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ABCarController extends AbstractController
{
    private SerializerInterface $serializer;
    private CarRepository $carRepository;


    public function __construct(SerializerInterface $serializer, CarRepository $carRepository)
    {
        $this->serializer = $serializer;
        $this->carRepository = $carRepository;
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

            return new JsonResponse(['status' => 'success', 'data' => $result, 'total' => count($result)]);
        } catch (ExceptionInterface $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/cars/{id}", methods={"GET"})
     */
    public function getCarById(int $id): JsonResponse
    {
        if (null === $car = $this->carRepository->find($id)) {
            return new JsonResponse(['status' => 'error', 'message' => 'Not found'], 404);
        }

        try {
            $result = $this->serializer->normalize($car);

            return new JsonResponse(['status' => 'success', 'data' => $result,'total' => count($result)]);
        } catch (ExceptionInterface $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
