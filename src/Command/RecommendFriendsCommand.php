<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;

class RecommendFriendsCommand extends Command
{
    protected static $defaultName = 'App:RecommendFriends';

    private $entity;

    public function __construct(EntityManagerInterface $entity)
    {
        parent::__construct();
        $this->entity = $entity;
    }


    protected function configure()
    {
        $this
            ->setDescription('Show recommend  friends')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $userRepo = $this->entity->getRepository(User::class);

        $table = new Table($output);
        $table->setHeaders(array('user_id', 'recommend_id', 'rate'));

        /** @var User $user */
        foreach ($userRepo->findAll() as $user) {
            $recommendFriends = $userRepo->getRecommendFriends($user);
            $table->addRow(array(
                $user->getId(),
                $recommendFriends[0][0]->getId(),
                $recommendFriends[0]['weight']

            ));
        }
        $table->render();


        $io->success('Список рекомендованных друзей построен');
    }
}
