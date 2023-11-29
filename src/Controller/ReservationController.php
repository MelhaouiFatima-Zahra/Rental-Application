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
        $this->reservationService = $reservationService;
        $this->carRepository = $carRepository;
    }
    /**
     * @Route("/api/reservations", name="api_create_reservation", methods={"POST"})
     */
    public function createReservation(Request $request): JsonResponse
    {
        // Extrait les données de la demande JSON
        $requestData = json_decode($request->getContent(), true);

        // Vérifie si les clés attendues sont présentes dans la demande JSON
        if (
            !isset($requestData['user']) ||
            !isset($requestData['carId']) ||
            !isset($requestData['startDate']) ||
            !isset($requestData['endDate'])
        ) {
            return new JsonResponse(['error' => 'Invalid request data. Please provide user, carId, startDate, and endDate.'], 400);
        }

        // Récupère les données de la demande
        $user = $requestData['user'];
        $carId = $requestData['carId'];
        $startDate = $requestData['startDate'];
        $endDate = $requestData['endDate'];

        // Récupère la voiture à partir de l'entité CarRepository
        $car = $this->carRepository->find($carId);

        // Vérifie si la voiture a été trouvée
        if (!$car) {
            return new JsonResponse(['error' => 'Car not found with the provided ID.'], 404);
        }

        // Appelle le service pour créer la réservation
        $message = $this->reservationService->createReservation($user, $carId, $startDate, $endDate);

        // Retourne la réponse JSON
        return new JsonResponse(['message' => $message]);
    }


    // Dans ReservationController.php
    /**
     * @Route("/api/users/{id}/reservations", name="api_user_reservations", methods={"GET"})
     */
    public function getUserReservations($id)
    {
        // Logique pour récupérer et retourner les réservations d'un utilisateur
        $userReservations = $this->reservationService->getUserReservations($id);

        // Convertir les réservations en un format approprié (par exemple, tableau associatif)
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
                    // Ajoutez d'autres propriétés de la voiture si nécessaire
                ],
            ];
        }

        return new JsonResponse(['reservations' => $formattedReservations]);
    }


    /**
     * @Route("/api/reservations/{id}", name="api_update_reservation", methods={"PUT"})
     */
    public function updateReservation($id, Request $request, ReservationService $reservationService): JsonResponse
    {
        // Extrait les nouvelles données de la demande JSON
        $requestData = json_decode($request->getContent(), true);

        // Vérifiez si les clés attendues sont présentes dans la demande JSON
        if (!isset($requestData['startDate']) || !isset($requestData['endDate'])) {
            return new JsonResponse(['error' => 'Invalid request data. Please provide startDate and endDate.'], 400);
        }

        // Appelle le service pour mettre à jour la réservation
        $message = $reservationService->updateReservation($id, $requestData['startDate'], $requestData['endDate']);

        // Retourne la réponse JSON
        return new JsonResponse(['message' => $message]);
    }

    /**
     * @Route("/api/reservations/{id}", name="api_cancel_reservation", methods={"DELETE"})
     */
    public function cancelReservation($id, ReservationService $reservationService)
    {
        try {
            $message = $reservationService->cancelReservation($id);

            return new JsonResponse(['message' => $message]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }

}
