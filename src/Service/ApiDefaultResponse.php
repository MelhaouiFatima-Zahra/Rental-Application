<?php

namespace App\Service;

use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ApiDefaultResponse
{
    private SerializerInterface $serializer;
    private ApiResponse $apiResponse;

    public function __construct(SerializerInterface $serializer, ApiResponse $apiResponse)
    {
        $this->serializer = $serializer;
        $this->apiResponse = $apiResponse;
    }

    public function getAll(ObjectRepository $repository): JsonResponse
    {
        try {
            $result = [];
            foreach ($repository->findAll() as $entity) {
                $result[] = $this->serializer->normalize($entity);
            }

            return $this->apiResponse->success($result);
        } catch (ExceptionInterface $e) {
            return $this->apiResponse->errorException($e);
        }
    }

    public function getOneById(ObjectRepository $repository, int $id): JsonResponse
    {
        if (null === $entity = $repository->find($id)) {
            return $this->apiResponse->notFound();
        }

        try {
            $result = $this->serializer->normalize($entity);

            return $this->apiResponse->success($result);
        } catch (ExceptionInterface $e) {
            $this->apiResponse->errorException($e);
        }
    }
}
