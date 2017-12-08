<?php
namespace Subito\Interfaces;

interface SubitoDateInterface
{
    public function setStartDate($date);

    public function setEndDate($date);

    public function isValidDate($date);

    public function diff();
}
