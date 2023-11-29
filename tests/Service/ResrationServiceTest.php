<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ResrationServiceTest extends TestCase
{
    public function testCancelReservation()
    {
        // Create mocks for the dependencies of the ReservationService constructor
        $reservationRepository = $this->createMock(ReservationRepository::class);
        $carRepository = $this->createMock(CarRepository::class);
        $userRepository = $this->createMock(UserRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Create a mock for the Reservation class
        $mockedReservation = $this->createMock(Reservation::class);

        // Configure the mock to return the mocked reservation when 'find' is called
        $reservationRepository->method('find')->willReturn($mockedReservation);

        // Create a mock for the EntityManagerInterface
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Create an instance of ReservationService by injecting the mocks
        $reservationService = new ReservationService(
            $entityManager,
            $reservationRepository,
            $carRepository,
            $userRepository
        );

        // Set up the expectation that the 'find' method will be called with the correct ID
        $reservationRepository->expects($this->once())
            ->method('find')
            ->with(5)
            ->willReturn($mockedReservation);

        // Set up the expectation that the 'flush' method will be called
        $entityManager->expects($this->once())
            ->method('flush');

        // Call the cancelReservation method with an appropriate ID
        $result = $reservationService->cancelReservation(5);

        // Perform assertions on the result or the expected state
        $this->assertEquals('Reservation canceled successfully', $result);
    }
    public function testGetUserReservations()
    {
        // Create mocks for the dependencies of the ReservationService constructor
        $reservationRepository = $this->createMock(ReservationRepository::class);
        $carRepository = $this->createMock(CarRepository::class);
        $userRepository = $this->createMock(UserRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        // Create an instance of ReservationService with the mock repository
        $reservationService = new ReservationService(
            $entityManager,
            $reservationRepository,
            $carRepository,
            $userRepository
        );
        // Set up an example user ID
        $userId = 1;

        // Set up the expected result from the repository
        $expectedReservations = [
            // Create some Reservation objects for testing
            $this->createMock(Reservation::class),
            $this->createMock(Reservation::class),

        ];

        // Configure the mock repository to return the expected reservations
        $reservationRepository->method('findBy')
            ->with(['user' => $userId])
            ->willReturn($expectedReservations);

        // Call the getUserReservations method
        $userReservations = $reservationService->getUserReservations($userId);

        // Assert that the result matches the expected reservations
        $this->assertEquals($expectedReservations, $userReservations);
    }

}
