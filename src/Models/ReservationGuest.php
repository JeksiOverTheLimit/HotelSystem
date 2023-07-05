<?php

declare(strict_types=1);

class ReservationGuest extends BaseId
{
    private int $reservationId;
    private int $guestId;

    public function getReservationId(): int
    {
        return $this->reservationId;
    }

    public function setReservationId(int $value): void
    {
        $this->reservationId = $value;
    }

    public function getGuestId(): int
    {
        return $this->guestId;
    }

    public function setGuestId(int $value): void
    {
        $this->guestId = $value;
    }
}
