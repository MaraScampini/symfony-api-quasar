<?php

namespace App\Services;


class DisplayNotes {
  public function displayArray($array){
    foreach ($array as $n) {
      $notes[] = $this->displayNote($n);
    }

    return $notes;
  }

  public function displayNote($note){
    $noteInfo = [
      'title' => $note->getTitle(),
      'description' => $note->getDescription(),
      'date' => $note->getDate(),
      'categories' => $this->getAndDisplayCategories($note)
    ];

    return $noteInfo;
  }

  public function getAndDisplayCategories($note)
  {
    $categories = $note->getCategories();
    $noteCats = array();
    foreach ($categories as $cats) {
      $noteCats[] = $cats->getName();
    }

    return $noteCats;
  }

}
