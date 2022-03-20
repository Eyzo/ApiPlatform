<?php
namespace App\Interface;
use App\Entity\User;

interface UserOwnedInterface {
  public function getUser(): ?User;
  public function setUser(?User $user): self;
}
