<?php

namespace App\Services;


class DisplayCategories
{
  public function displayArray($array)
  {
    $notes = array();
    foreach ($array as $n) {
      $notes[] = $n->getName();
    }

    return $notes;
  }
}
