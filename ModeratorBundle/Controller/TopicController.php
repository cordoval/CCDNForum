<?php

/*
 * This file is part of the CCDN ModeratorBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\ModeratorBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class TopicController extends ContainerAware
{

	/**
	 *
	 * Displays a list of closed topics (locked from posting new posts)
	 *
	 * @access public
	 * @param int $page
	 * @return RedirectResponse|RenderResponse
	 */
	public function showClosedAction($page)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$topics_paginated = $this->container->get('topic.repository')->findClosedTopicsForModeratorsPaginated();
			
		$topics_per_page = $this->container->getParameter('ccdn_forum_moderator.topic.topics_per_page');
		$topics_paginated->setMaxPerPage($topics_per_page);
		$topics_paginated->setCurrentPage($page, false, true);
				
		if ( ! $topics_paginated) {
			throw new NotFoundHttpException('no closed topics exist!');
		}
		
		$posts_per_page = $this->container->getParameter('ccdn_forum_moderator.topic.posts_per_page');
		
		// setup crumb trail.
		$crumb_trail = $this->container->get('crumb_trail')
			->add($this->container->get('translator')->trans('crumbs.topic.closed.index', array(), 'CCDNForumModeratorBundle'), 
				$this->container->get('router')->generate('cc_moderator_forum_show_all_closed_topics'), "home");
		
		return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:show_closed.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
			'user' => $user,
			'topics' => $topics_paginated,
			'crumbs' => $crumb_trail,
			'pager' => $topics_paginated,
			'posts_per_page' => $posts_per_page,
		));
	}
	
	
	/**
	 *
	 * Once a topic is locked, no posts can be added, deleted or edited!
	 *
	 * @access public
	 * @param int $topic_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function closeAction($topic_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();
			
		$topic = $this->container->get('topic.repository')->find($topic_id);

		if ( ! $topic) {
			throw new NotFoundHttpException('No such topic exists!');
		}
		
		$this->container->get('topic.manager')->close($topic, $user)->flushNow();
		
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.topic.close.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));
			
		return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', 
			array('topic_id' => $topic->getId()) ));
	}
	
	
	/**
	 *
	 * @access public
	 * @param int $topic_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function reopenAction($topic_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();
			
		$topic = $this->container->get('topic.repository')->find($topic_id);

		if ( ! $topic) {
			throw new NotFoundHttpException('No such topic exists!');
		}
		
		$this->container->get('topic.manager')->reopen($topic)->flushNow();
		
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.topic.reopen.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));
		
		return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', 
			array('topic_id' => $topic->getId()) ));
	}
	
	
	/**
	 *
	 * @access public
	 * @param int $topic_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function moveAction($topic_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have access to this section.');
		}

		$topic = $this->container->get('topic.repository')->find($topic_id);
		
		if ( ! $topic) {
			throw new NotFoundHttpException('No such topic exists!');
		}
		
		$formHandler = $this->container->get('topic.form.change_board.handler')->setOptions(array('topic' => $topic));

		if ($formHandler->process())
		{
			$this->container->get('session')->setFlash('notice', 
				$this->container->get('translator')->trans('flash.topic.move.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));
			
			return new RedirectResponse($this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId()) ));
		}
		else
		{
			$board = $topic->getBoard();
			$category = $board->getCategory();
			
			// setup crumb trail.
			$crumb_trail = $this->container->get('crumb_trail')
				->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), 
					$this->container->get('router')->generate('cc_forum_category_index'), "home")
				->add($category->getName(), 
					$this->container->get('router')->generate('cc_forum_category_show', array('category_id' => $category->getId())), "category")
				->add($board->getName(),
					$this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $board->getId())), "board")
				->add($topic->getTitle(), 
					$this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId())), "communication")
				->add($this->container->get('translator')->trans('crumbs.topic.change_board', array(), 'CCDNForumModeratorBundle'), 
					$this->container->get('router')->generate('cc_moderator_forum_topic_change_board', array('topic_id' => $topic->getId())), "edit");

			$form = $formHandler->getForm();
			
			return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:change_board.html.' . $this->getEngine(), array(
				'topic' => $topic,
				'crumbs' => $crumb_trail,
				'form' => $form->createView(),
			));
		}
	}
	
	
	/**
	 *
	 * @access public
	 * @param int $topic_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function restoreAction($topic_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
			throw new AccessDeniedException('You do not have permission to use this resource!');
		}

		$topic = $this->container->get('topic.repository')->find($topic_id);

		if ( ! $topic) {
			throw new NotFoundHttpException('No such topic exists!');
		}

		$this->container->get('topic.manager')->restore($topic)->flushNow();

		// set flash message
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.topic.restore.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

		// forward user
		return new RedirectResponse($this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $topic->getBoard()->getId()) ));		
	}
		
	
	/**
	 *
	 * @access public
	 * @param int $topic_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function deleteAction($topic_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR')) {
			throw new AccessDeniedException('You do not have permission to use this resource!');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();
			
		$topic = $this->container->get('topic.repository')->find($topic_id);
	
		if ( ! $topic) {
			throw new NotFoundHttpException('No such post exists!');
		}
		
		$board = $topic->getBoard();
		$category = $board->getCategory();
		
//		$confirmationMessage = 'topic.delete.question';
		$crumbDelete = $this->container->get('translator')->trans('crumbs.topic.delete', array(), 'CCDNForumForumBundle');
//		$pageTitle = $this->container->get('translator')->trans('title.topic.delete', array('%topic_title%' => $topic->getTitle()), 'CCDNForumForumBundle');
		
		// setup crumb trail.
		$crumb_trail = $this->container->get('crumb_trail')
			->add($this->container->get('translator')->trans('crumbs.forum_index', array(), 'CCDNForumForumBundle'), 
				$this->container->get('router')->generate('cc_forum_category_index'), "home")
			->add($category->getName(),	$this->container->get('router')->generate('cc_forum_category_show', array('category_id' => $category->getId())), "category")
			->add($board->getName(), $this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $board->getId())), "board")
			->add($topic->getTitle(), $this->container->get('router')->generate('cc_forum_topic_show', array('topic_id' => $topic->getId())), "communication")
			->add($crumbDelete, $this->container->get('router')->generate('cc_forum_topic_reply', array('topic_id' => $topic->getId())), "trash");
	
		return $this->container->get('templating')->renderResponse('CCDNForumModeratorBundle:Topic:delete_topic.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_forum_moderator.user.profile_route'),
//			'page_title' => $pageTitle,
//			'confirmation_message' => $confirmationMessage,
			'topic' => $topic,
			'crumbs' => $crumb_trail,
		));
	}
	
	
	/**
	 *
	 * @access public
	 * @param int $topic_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function deleteConfirmedAction($topic_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_MODERATOR'))
		{
			throw new AccessDeniedException('You do not have permission to use this resource!');
		}

		$user = $this->container->get('security.context')->getToken()->getUser();

		$topic = $this->container->get('topic.repository')->find($topic_id);

		if ( ! $topic) {
			throw new NotFoundHttpException('No such topic exists!');
		}

		$this->container->get('topic.manager')->softDelete($topic, $user)->flushNow();

		// set flash message
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.topic.delete.success', array('%topic_title%' => $topic->getTitle()), 'CCDNForumModeratorBundle'));

		// forward user
		return new RedirectResponse($this->container->get('router')->generate('cc_forum_board_show', array('board_id' => $topic->getBoard()->getId()) ));	
	}
		
	
	/**
	 *
	 * @access protected
	 * @return string
	 */
	protected function getEngine()
    {
        return $this->container->getParameter('ccdn_forum_moderator.template.engine');
    }

}