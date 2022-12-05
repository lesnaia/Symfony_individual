<?php 

namespace App\Service;

use Doctrine\ORM\EntityManager;


class CarService extends AbstractService
{

    public function __construct(EntityManager $em, $entityName)
    {
        $this->em = $em;
        $this->model = $em->getRepository($entityName);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getCar($car_id)
    {
        return $this->find($car_id);
    }

    public function getAllCars()
    {
        return $this->findAll();
    }

    public function addCar($car)
    {
        return $this->save($car);
    }

    public function deleteCar($id)
    {   
        return $this->delete($this->find($id));
    }

}