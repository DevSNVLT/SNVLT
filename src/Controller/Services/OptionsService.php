<?php

namespace App\Controller\Services;

use App\Repository\Admin\OptionRepository;

class OptionsService
{
    public function __construct(private OptionRepository $repository)
    {

    }

    public function findAll()
    {
        return $this->repository->findAllForTwig();
    }
    public function findValue(string $name)
    {
        return $this->repository->getValue($name);
    }
}