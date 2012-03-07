<?php

/*
 * This file is part of the CCDN ForumBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\ForumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * PostRepository
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class PostRepository extends EntityRepository
{

	
	
	/**
	 *
	 * Finds the post requested for editing joined to its
	 * topic and also the post referenced as first post. This
	 * is so that if first_post matches the request post by ID
	 * then we know the post being edited is the topic/post, if
	 * not then we know we are only editing the post alone.
	 *
	 * By joining in this way we avoid doing 2 queries instead of
	 * just the one, as one would be needed to check if it was
	 * the first post after retrieval, which is a waste.
	 *
	 *
	 * @access public
	 * @param int $post_id
	 */
	public function findPostForEditing($post_id)
	{
		
		$query = $this->getEntityManager()
			->createQuery('	
				SELECT p, t, fp
				FROM CCDNForumForumBundle:Post p
				LEFT JOIN p.topic t
				LEFT JOIN t.first_post fp
				WHERE p.id = :id')
			->setParameter('id', $post_id);
			
		try {
	        return $query->getsingleResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}



	/**
	 *
	 * for moderator
	 *
	 * @access public
	 */
	public function findLockedPostsForModeratorsPaginated()
	{
		
		$query = $this->getEntityManager()
			->createQuery('	
				SELECT p, t
				FROM CCDNForumForumBundle:Post p
				LEFT JOIN p.topic t
				WHERE p.locked_by IS NOT NULL');
		
		try {
			return new Pagerfanta(new DoctrineORMAdapter($query));
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	}
	
	
	
	/**
	 *
	 *
	 */
	public function getPostCountForUserById($user_id)
	{
		
		$query = $this->getEntityManager()
			->createQuery('	
				SELECT COUNT(p.id) AS postCount
				FROM CCDNForumForumBundle:Post p
				LEFT JOIN p.topic t
				WHERE p.created_by = :id AND p.deleted_by IS NULL AND t.deleted_by IS NULL')
			->setParameter('id', $user_id);
			
		try {
	        return $query->getsingleResult();
	    } catch (\Doctrine\ORM\NoResultException $e) {
	        return null;
	    }
	
	}
	
	
}