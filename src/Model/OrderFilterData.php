<?php

namespace App\Model;

class OrderFilterData
{
    public ?string $query = null;

    public ?string $status = null;

    // Champ texte pour l'affichage du range picker
    public ?string $updatedAtRange = null;
    public ?\DateTimeInterface $updatedAtStart = null;
    public ?\DateTimeInterface $updatedAtEnd = null;

    public ?string $createdAtRange = null;
    public ?\DateTimeInterface $createdAtStart = null;
    public ?\DateTimeInterface $createdAtEnd = null;
}
