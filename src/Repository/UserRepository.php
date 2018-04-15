<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getRecommendFriends(User $user){

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(User::class, 'recommendUser');
        $rsm->addFieldResult('recommendUser','recommend_id','id');
        $rsm->addScalarResult('weight', 'weight');

       $query  = $this->getEntityManager()->createNativeQuery('
         select  users.id as ID , u2.user_id AS recommend_id , COUNT(u2.user_id) as weight from users
              left join users_friends u on users.id = u.friend_id
              left join users_friends u2 on u.user_id = u2.friend_id
            where users.id  = :User_id and users.id <> u.user_id and users.id <> u2.user_id
            group by u2.user_id
            order by weight DESC
            limit 1

       ',$rsm)->setParameter(':User_id',$user->getId())
       ;
       return $query->getResult();

    }

}
