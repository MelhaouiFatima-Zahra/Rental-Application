<?php

namespace App\Tests\Service;

use App\Repository\CarRepository;
use App\Service\CarService;
use PHPUnit\Framework\TestCase;

class CarServiceTest extends TestCase
{
    public function testGetListOfCars(): void
    {
        // Mock the CarRepository
        $carRepository = $this->getMockBuilder(CarRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set up the expected result when findAll is called
        $expectedCars = [
        ];

        $carRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedCars);

        // Create an instance of CarService with the mocked CarRepository
        $carService = new CarService($carRepository);

        // Call the method to get the list of cars
        $actualCars = $carService->getListOfCars();

        // Assert that the returned cars match the expected cars
        $this->assertEquals($expectedCars, $actualCars);
    }

    public function testGetCarDetailsById(): void
    {
        // Créez un double (mock) du CarRepository
        $carRepository = $this->createMock(CarRepository::class);

        // Configurez le mock pour retourner une voiture fictive lorsque la méthode find est appelée avec n'importe quel ID
        $fakeCar = new \App\Entity\Car();
        $carRepository->method('find')->willReturn($fakeCar);

        // Créez une instance de CarService en lui passant le mock de CarRepository
        $carService = new CarService($carRepository);

        // Appelez la méthode que vous voulez tester avec un ID quelconque
        $result = $carService->getCarDetailsById(123);

        // Assert que le résultat est égal à la voiture fictive que vous avez configurée dans le mock
        $this->assertEquals($fakeCar, $result);
    }
}
