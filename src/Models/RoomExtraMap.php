<?php
declare(strict_types=1);
include_once "../Models/BaseId.php";

class RoomExtraMap extends BaseId{
       private int $roomId;
       private ?int $extraId;

       public function getRoomId() : ?int {
        return $this->roomId;
       }

       public function setRoomId(int $value) : void {
        $this->roomId = $value;
       }

       public function getExtraId() : ?int {
        return $this->extraId;
       }

       public function setExtraId(int $value) : void {
        $this->extraId = $value;
       }
}