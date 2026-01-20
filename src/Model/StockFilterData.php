<?php
namespace App\Model;

class StockFilterData
{
    public ?string $query = null;
    public ?int $minQuantity = null;
    public ?int $maxQuantity = null;
}