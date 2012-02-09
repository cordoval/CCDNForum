<?php

/*
 * This file is part of the CCDN AdminBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 
 * 
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNForum\AdminBundle\Controller;

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
class CategoryController extends ContainerAware
{

	
	/**
	 * 
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
    public function indexAction()
    {
		/*
		 *	Invalidate this action / redirect if user should not have access to it
		 */
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$categories = $this->container->get('category.repository')->findAllJoinedToBoard();
		
		$topics_per_page = $this->container->getParameter('ccdn_forum_admin.board.topics_per_page');
		
		$crumb_trail = $this->container->get('crumb_trail')
			->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), 
				$this->container->get('router')->generate('cc_admin_forum_category_index'), "home");
		
		return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:index.html.' . $this->getEngine(), array(
			'user_profile_route' => $this->container->getParameter('ccdn_forum_admin.user.profile_route'),
			'crumbs' => $crumb_trail,
			'categories' => $categories,
			'topics_per_page' => $topics_per_page,
			));
    }


	/**
	 *
	 * @access public
	 * @return RedirectResponse|RenderResponse
	 */
	public function createAction()
	{
		/*
		 *	Invalidate this action / redirect if user should not have access to it
		 */
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$formHandler = $this->container->get('admin.category.form.insert.handler');
			
		$form = $formHandler->getForm();
		
		if ($formHandler->process())	
		{	
			$this->container->get('session')->setFlash('notice', 
				$this->container->get('translator')->trans('flash.category.create.success', array(), 'CCDNForumAdminBundle'));
										
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}
		else
		{
			// setup crumb trail.
			$crumb_trail = $this->container->get('crumb_trail')
				->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), 
					$this->container->get('router')->generate('cc_admin_forum_category_index'), "home")
				->add($this->container->get('translator')->trans('crumbs.category.create', array(), 'CCDNForumAdminBundle'), 
					$this->container->get('router')->generate('cc_admin_forum_category_create'), "edit");
					
			return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:create.html.' . $this->getEngine(), array(
				'crumbs' => $crumb_trail,
				'form' => $form->createView(),
			));
		}
	}
	
	
	/**
	 *
	 * @access public
	 * @param $category_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function editAction($category_id)
	{
		/*
		 *	Invalidate this action / redirect if user should not have access to it
		 */
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$user = $this->container->get('security.context')->getToken()->getUser();
		
		$category = $this->container->get('category.repository')->findOneById($category_id);

		if ( ! $category)
		{
			throw new NotFoundHTTPException('category not found!');
		}
		
		$formHandler = $this->container->get('admin.category.form.update.handler');		
		$formHandler->setOptions(array('category_entity' => $category));
		
		$form = $formHandler->getForm();
		
		if ($formHandler->process())	
		{
			$this->container->get('session')->setFlash('notice', 
				$this->container->get('translator')->trans('flash.category.edit.success', array(), 'CCDNForumAdminBundle'));
									
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}
		else
		{
			// setup crumb trail.
			$crumb_trail = $this->container->get('crumb_trail')
				->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), 
					$this->container->get('router')->generate('cc_admin_forum_category_index'), "home")
				->add($this->container->get('translator')->trans('crumbs.category.edit', array('%category_name%' => $category->getName()), 'CCDNForumAdminBundle'), 
					$this->container->get('router')->generate('cc_admin_forum_category_edit', array('category_id' => $category_id)), "edit");
					
			return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:edit.html.' . $this->getEngine(), array(
				'category' => $category,
				'crumbs' => $crumb_trail,
				'form' => $form->createView(),
			));
		}
	}
	
	
	/**
	 *
	 * @access public
	 * @param $category_id
	 * @return RedirectResponse|RenderResponse
	 */
	public function deleteAction($category_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
			
		$category = $this->container->get('category.repository')->findOneById($category_id);

		if ( ! $category)
		{
			throw new NotFoundHTTPException('category not found!');
		}

		// setup crumb trail.
		$crumb_trail = $this->container->get('crumb_trail')
			->add($this->container->get('translator')->trans('crumbs.category.index', array(), 'CCDNForumAdminBundle'), 
				$this->container->get('router')->generate('cc_admin_forum_category_index'), "home")
			->add($this->container->get('translator')->trans('crumbs.category.delete', array('%category_name%' => $category->getName()), 'CCDNForumAdminBundle'),
				$this->container->get('router')->generate('cc_admin_forum_category_delete', array('category_id' => $category->getId())), "trash");
		
		return $this->container->get('templating')->renderResponse('CCDNForumAdminBundle:Category:delete_category.html.' . $this->getEngine(), array(
			'category' => $category,
			'crumbs' => $crumb_trail,
		));
	}
	
	
	/**
	 *
	 * @access public
	 * @param $category_id
	 * @return RedirectResponse
	 */
	public function deleteConfirmedAction($category_id)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$doctrine = $this->container->get('doctrine');
		$category = $this->container->get('category.repository')->findOneById($category_id);

		if ( ! $category)
		{
			throw new NotFoundHTTPException('category not found!');
		}
			
		$this->container->get('admin.category.manager')->remove($category)->flushNow();
		
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.category.delete.success', array(), 'CCDNForumAdminBundle'));
			
		return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
	}
	
	
	/**
	 *
	 * @access public
	 * @param $category_id, $direction
	 * @return RedirectResponse|RenderResponse
	 */
	public function reorderAction($category_id, $direction)
	{
		if ( ! $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
			throw new AccessDeniedException('You do not have permission to access this page!');
		}
		
		$categories = $this->container->get('category.repository')->findCategoriesOrderedByPriority();
		
		if ( ! $categories)
		{
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}

		$category_count = count($categories);

		// if there is only 1 category, it cannot be re-ordered.
		if ($category_count < 2) {
			return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
		}
		
		$this->container->get('admin.category.manager')->reorder($categories, $category_id, $direction)->flushNow();
		
		$this->container->get('session')->setFlash('notice', 
			$this->container->get('translator')->trans('flash.category.reorder.success', array(), 'CCDNForumAdminBundle'));
			
		return new RedirectResponse($this->container->get('router')->generate('cc_admin_forum_category_index'));
	}
	
	
	/**
	 *
	 * @access public
	 * @return string
	 */
	protected function getEngine()
    {
        return $this->container->getParameter('ccdn_forum_admin.template.engine');
    }

}
