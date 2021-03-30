<?php

namespace App\Seeds;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\User;

class UserSeed extends Seed
{

    protected function configure()
    {
        //The seed won't load if this is not set
        //The resulting command will be {prefix}:country
        $this->setSeedName('adminSeed');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output)
    { 

        //Doctrine logging eats a lot of memory, this is a wrapper to disable logging
        $this->disableDoctrineLogging();

        $users = [
            [
                'nom' => 'admin',
                'prenom' => 'admin',
                'email' => 'admin@admin.com',
                'password' => 'admin',
                'type' => 'admin',
                'image' => 'assets/images/avatars/default.jpg'
            ],
        ];

        foreach ($users as $user){
            $userRepo = new User();
            $userRepo->setNom($user['nom']);
            $userRepo->setPrenom($user['prenom']);
            $userRepo->setMail($user['email']);
            $userRepo->setType($user['type']);
            $userRepo->setBan(false);
            $userRepo->setActive(true);
            $userRepo->setPassword(password_hash($user['password'], PASSWORD_DEFAULT));
            $userRepo->setImage('assets/images/avatars/default.jpg');
            $this->manager->persist($userRepo);
        }
        $this->manager->flush();
        $this->manager->clear();
        return 0; //Must return an exit code
    }
    
    public function unload(InputInterface $input, OutputInterface $output){
        //Clear the table
        $this->manager->getConnection()->exec('DELETE FROM user');
        return 0; //Must return an exit code
    }

    public function getOrder(): int 
    {
      return 0; 
    }
    

}