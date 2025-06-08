<?php

namespace App\Model;

interface TimestampInterface
{
    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(\DateTimeInterface $createdAt);

    public function getUpdatedAt(): ? \DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt);
}