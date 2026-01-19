<?php
namespace App\Model;

class OrderFilterData
{
    public ?string $query = null;

    public ?string $status = null;

    public ?\DateTimeInterface $updatedAt = null;

    public ?\DateTimeInterface $createdAt = null;
}