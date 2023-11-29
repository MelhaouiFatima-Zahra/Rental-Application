<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    private $entityManager;
    private $reservationRepository;
    private $carRepository;
    private $userRepository;  // Ajoutez cette ligne

    public function __construct(
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository,
        CarRepository $carRepository,
        UserRepository $userRepository  // Ajoutez cette ligne
    ) {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
        $this->carRepository = $carRepository;
        $this->userRepository = $userRepository;  // Ajoutez cette ligne
    }

    public function createReservation($userId, $carId, $startDate, $endDate)
    {
        // Vérifiez si la voiture existe
        $car = $this->carRepository->find($carId);
        if (!$car) {
            throw new \Exception('Car not found with the provided ID.');
        }

        // Vérifiez la disponibilité de la voiture pendant la période de réservation
        if (!$this->isCarAvailable($car, $startDate, $endDate)) {
            throw new \Exception('The car is not available for reservation during the specified period.');
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new \Exception('User not found with the provided ID.');
        }

        // Effectuez des vérifications supplémentaires si nécessaire, par exemple, l'autorisation de l'utilisateur, etc.
        $startDateObj = new \DateTime($startDate);
        $endDateObj = new \DateTime($endDate);

        if ($endDateObj <= $startDateObj) {
            throw new \Exception('Invalid reservation dates. The end date must be after the start date.');
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setCar($car);
        $reservation->setStartDate($startDateObj);
        $reservation->setEndDate($endDateObj);
        $reservation->setIsCanceled(false);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return 'Reservation created successfully';
    }

// Méthode pour vérifier la disponibilité de la voiture pendant la période de réservation
    private function isCarAvailable($car, $startDate, $endDate)
    {
        $existingReservations = $car->getReservations();

        foreach ($existingReservations as $existingReservation) {
            $existingStartDate = $existingReservation->getStartDate();
            $existingEndDate = $existingReservation->getEndDate();

            if (($startDate >= $existingStartDate || $startDate < $existingEndDate) ||
                ($endDate > $existingStartDate || $endDate <= $existingEndDate)) {
                // La voiture n'est pas disponible pendant la période de réservation
                return false;
            }
        }

        // La voiture est disponible pendant la période de réservation
        return true;
    }

    public function getUserReservations($userId)
    {
        // Logique pour récupérer les réservations d'un utilisateur
        $user = $this->userRepository->find($userId);

        if (!$user) {
            throw new \Exception('User not found with the provided ID.');
        }

        // Récupérer les réservations avec les détails de la voiture
        return $this->reservationRepository->findBy(['user' => $user], ['startDate' => 'ASC']);
    }

    public function updateReservation($id, $startDate, $endDate)
    {
        $reservation = $this->reservationRepository->find($id);

        // Check if the reservation exists
        if (!$reservation) {
            throw new \Exception('Reservation not found with the provided ID.');
        }

        // Perform additional checks if needed

        // Update the reservation
        $reservation->setStartDate(new \DateTime($startDate));
        $reservation->setEndDate(new \DateTime($endDate));

        $this->entityManager->flush();

        return 'Reservation updated successfully';
    }

    public function cancelReservation($id)
    {
        try {
            // Fetch the reservation from the repository
            $reservation = $this->reservationRepository->find($id);

            // Check if the reservation exists
            if (!$reservation) {
                throw new \Exception('Reservation not found with the provided ID.');
            }

            // Check if the reservation is already canceled
            if ($reservation->isIsCanceled()) {
                throw new \Exception('Reservation is already canceled.');
            }


            // Update the reservation status to canceled
            $reservation->setIsCanceled(true);

            // Persist changes to the database
            $this->entityManager->flush();

            return 'Reservation canceled successfully';
        } catch (\Exception $e) {
            throw $e; // You may handle the exception differently based on your requirements
        }
    }
}
