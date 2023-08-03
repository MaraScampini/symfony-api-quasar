<?php

namespace App\Services;

class DisplayUsers
{
  public function displayUser($data){
    $user = [
      'Name' => $data->getName(),
      'E-mail' => $data->getEmail(),
      'Age' => $data->getAge()
    ];

    return $user;
  }

  public function displayArrayUsers($array){
    foreach ($array as $a) {
      $users[] = [
        'Name' => $a->getName(),
        'E-mail' => $a->getEmail(),
        'Age' => $a->getAge()
      ];
    }

    return $users;
  }
}
