<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Loading all the methods specified below in the correct order
        $this->loadCategories($manager);
        $this->loadUsers($manager);
        $this->loadNotes($manager);
    }

    public function loadUsers(ObjectManager $manager){
        // Creating a new user
        $user = new User();
        $user->setName('Mara');
        $user->setAge(30);
        $user->setEmail('mara@mara.com');
        $this->addReference('mara', $user);

        $manager->persist($user);

        $user = new User();
        $user->setName('Héctor');
        $user->setAge(30);
        $user->setEmail('hector@hector.com');
        $this->addReference('hector', $user);

        $manager->persist($user);

        // Saving the changes to the DB
        $manager->flush();
    }

    public function loadCategories(ObjectManager $manager){
        $cat = new Category();
        $cat->setName('House');
        $this->addReference('house', $cat);
        $manager->persist($cat);

        $cat = new Category();
        $cat->setName('School');
        $manager->persist($cat);
        $this->addReference('school', $cat);

        $cat = new Category();
        $cat->setName('Work');
        $manager->persist($cat);
        $this->addReference('work', $cat);

        $manager->flush();
    }

    public function loadNotes(ObjectManager $manager)
    {
        // Importing the references from the other tables
        $user_mara = $this->getReference('mara');
        $user_hector = $this->getReference('hector');
        $house_cat = $this->getReference('house');
        $school_cat = $this->getReference('school');
        $work_cat = $this->getReference('work');

        $note = new Note();
        $note->setTitle('Nota de prueba 1');
        $note->setDescription('Esto es una nota de prueba');
        $note->setDate(new DateTime('now'));

        // Direct setter for the ManyToOne relationship
        $note->setUser($user_mara);
        // Complex setter for the ManyToMany relationship
        $note->addCategory($house_cat);
        $note->addCategory($school_cat);

        $manager->persist($note);

        $note = new Note();
        $note->setTitle('Nota de prueba antigua');
        $note->setDescription('Esto es una nota de prueba');
        $note->setDate(new DateTime('2022-12-12'));
        $note->setUser($user_hector);
        $note->addCategory($house_cat);
        $note->addCategory($work_cat);
        $manager->persist($note);


        $manager->flush();
    }
}
