<?php

namespace App\Seeds;

use Evotodi\SeedBundle\Command\Seed;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Setting;

class SettingSeed extends Seed
{

    protected function configure()
    {
        //The seed won't load if this is not set
        //The resulting command will be {prefix}:country
        $this->setSeedName('settingSeed');

        parent::configure();
    }

    public function load(InputInterface $input, OutputInterface $output)
    { 

        //Doctrine logging eats a lot of memory, this is a wrapper to disable logging
        $this->disableDoctrineLogging();

        $settings = [
            [
                'visit_number' => 0,
                'booking_block' => false,
            ],
        ];

        foreach ($settings as $setting){
            $settingRepo = new Setting();
            $settingRepo->setVisitNumber($setting['visit_number']);
            $settingRepo->setBookingBlock($setting['booking_block']);
            $this->manager->persist($settingRepo);
        }
        $this->manager->flush();
        $this->manager->clear();
        return 0; //Must return an exit code
    }
    
    public function unload(InputInterface $input, OutputInterface $output){
        //Clear the table
        $this->manager->getConnection()->exec('DELETE FROM setting');
        return 0; //Must return an exit code
    }

    public function getOrder(): int 
    {
      return 0; 
    }
    

}