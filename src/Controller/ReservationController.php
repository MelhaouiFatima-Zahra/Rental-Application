<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\CarRepository;
use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ReservationController extends AbstractController
{
    private $reservationService;
    private $carRepository;

    public function __construct(ReservationService $reservationService, CarRepository $carRepository)
    {
        // Inject ReservationService and CarRepository into the controller through constructor
        $this->reservationService = $reservationService;
        $this->carRepository = $carRepository;
    }

    /**
     * @Route("/api/reservations", name="api_create_reservation", methods={"POST"})
     */
    public function createReservation(Request $request): JsonResponse
    {
        // Extract data from the JSON request
        $requestData = json_decode($request->getContent(), true);

        // Check if the expected keys are present in the JSON request
        if (
            !isset($requestData['user']) ||
            !isset($requestData['carId']) ||
            !isset($requestData['startDate']) ||
            !isset($requestData['endDate'])
        ) {
            return new JsonResponse(['error' => 'Invalid request data. Please provide user, carId, startDate, and endDate.'], 400);
        }

        // Retrieve data from the request
        $user = $requestData['user'];
        $carId = $requestData['carId'];
        $startDate = $requestData['startDate'];
        $endDate = $requestData['endDate'];

        // Retrieve the car from the CarRepository entity
        $car = $this->carRepository->find($carId);

        // Check if the car is found
        if (!$car) {
            return new JsonResponse(['error' => 'Car not found with the provided ID.'], 404);
        }

        // Call the service to create the reservation
        $message = $this->reservationService->createReservation($user, $carId, $startDate, $endDate);

        // Return the JSON response
        return new JsonResponse(['message' => $message]);
    }

    /**
     * @Route("/api/users/{id}/reservations", name="api_user_reservations", methods={"GET"})
     */
    public function getUserReservations($id)
    {
        // Logic to retrieve and return user reservations
        $userReservations = $this->reservationService->getUserReservations($id);

        // Convert reservations into an appropriate format (e.g., associative array)
        $formattedReservations = [];

        foreach ($userReservations as $reservation) {
            $car = $reservation->getCar();

            $formattedReservations[] = [
                'id' => $reservation->getId(),
                'startDate' => $reservation->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $reservation->getEndDate()->format('Y-m-d H:i:s'),
                'car' => [
                    'id' => $car->getId(),
                    'brand' => $car->getBrand(),
                    'model' => $car->getModel(),
                    // Add other car properties if necessary
                ],
            ];
        }

        return new JsonResponse(['reservations' => $formattedReservations]);
    }

    /**
     * @Route("/api/reservations/{id}", name="api_update_reservation", methods={"PUT"})
     */
    public function updateReservation(Request $request, $id): JsonResponse
    {
        // Extract data from the JSON request
        $requestData = json_decode($request->getContent(), true);

        // Check if the required keys are present
        if (!isset($requestData['startDate']) || !isset($requestData['endDate'])) {
            return new JsonResponse(['error' => 'Invalid request data. Please provide startDate and endDate.'], 400);
        }

        // Call the service to update the reservation
        $message = $this->reservationService->updateReservation($id, $requestData['newCarId'], $requestData['startDate'], $requestData['endDate']);

        // Return the JSON response
        return new JsonResponse(['message' => $message]);
    }

    /**
     * @Route("/api/reservations/{id}", name="api_cancel_reservation", methods={"DELETE"})
     */
    public function cancelReservation($id, ReservationService $reservationService)
    {
        try {
            // Call the service to cancel the reservation
            $message = $reservationService->cancelReservation($id);

            return new JsonResponse(['message' => $message]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
