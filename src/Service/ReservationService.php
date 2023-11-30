<?php

namespace App\Service;

use App\Entity\Car;
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
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository,
        CarRepository $carRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
        $this->carRepository = $carRepository;
        $this->userRepository = $userRepository;
    }

    public function createReservation($userId, $carId, $startDate, $endDate)
    {
       // try {
            // Check if the car exists.
            $car = $this->carRepository->find($carId);
            if (!$car) {
                throw new \Exception('Car not found with the provided ID.');
            }

            // Check the car's availability during the reservation period.
            if (!$this->isCarAvailable($car, $startDate, $endDate)) {
                throw new \Exception('The car is not available for reservation during the specified period.');
            }

            $user = $this->userRepository->find($userId);
            if (!$user) {
                throw new \Exception('User not found with the provided ID.');
            }

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
//        } catch (\Exception $e) {
//            // Catch the exception and return the error message.
//            return $e->getMessage();
//        }
    }


    // Method to check the availability of the car during the reservation period
    public function isCarAvailable(Car $car, $startDate, $endDate)
    {
        $existingReservations = $car->getReservations();

        foreach ($existingReservations as $existingReservation) {
            $existingStartDate = $existingReservation->getStartDate();
            $existingEndDate = $existingReservation->getEndDate();

            if (
                ($startDate >= $existingStartDate && $startDate < $existingEndDate) ||
                ($endDate > $existingStartDate && $endDate <= $existingEndDate)
            ) {
                // The car is not available during the reservation period
                return false;
            }
        }

        // The car is available during the reservation period
        return true;
    }


    public function updateReservation($reservationId, $newCarId, $newStartDate, $newEndDate)
    {
//        try {
        // Fetch the reservation from the repository
        $reservation = $this->reservationRepository->find($reservationId);

        // Check if the reservation exists
        if (!$reservation) {
            throw new \Exception('Reservation not found with the provided ID.');
        }

        // Fetch the new car from the repository
        $newCar = $this->carRepository->find($newCarId);

        // Check if the new car exists
        if (!$newCar) {
            throw new \Exception('New car not found with the provided ID.');
        }

        // Check if the new car is available during the new reservation period
        if (!$this->isCarAvailable($newCar, $newStartDate, $newEndDate)) {
            throw new \Exception('The new car is not available for reservation during the specified period.');
        }

        // Update the reservation with the new car, start date, and end date
        $newStartDateObj = new \DateTime($newStartDate);
        $newEndDateObj = new \DateTime($newEndDate);
        if ($newEndDateObj <= $newStartDateObj) {
            throw new \Exception('Invalid reservation dates. The end date must be after the start date.');
        }
        $reservation->setCar($newCar);
        $reservation->setStartDate($newStartDateObj);
        $reservation->setEndDate($newEndDateObj);

        // Persist changes to the database
        $this->entityManager->flush();

        return 'Reservation updated successfully';
//        } catch (\Exception $e) {
//            // Catch the exception and return the error message.
//            return $e->getMessage();
//        }
    }

    public function getUserReservations($userId)
    {
        // Fetch all reservations for the user
        $userReservations = $this->reservationRepository->findBy(['user' => $userId]);

        // Filter out reservations that are canceled
        $activeReservations = array_filter($userReservations, function ($reservation) {
            return !$reservation->isIsCanceled();
        });

        return $activeReservations;
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
