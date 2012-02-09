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

namespace CCDNForum\ModeratorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * 
 * @author Reece Fowell <reece@codeconsortium.com> 
 * @version 1.0
 */
class FlagUpdateType extends AbstractType
{
	
	
	/**
	 *
	 * @access protected
	 */
	protected $options;
	
	
	/**
	 *
	 * @access public
	 * @param Array() $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}
	
	
	/**
	 *
	 * @access public
	 * @param FormBuilder $builder, Array() $options
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		
		$builder->add('status', 'choice', array(
			'choices' => $this->options['flag_default_choices']->getStatusCodes()
		));
		$builder->add('moderators_note');
	}
	

	/**
	 *
	 * for creating and replying to topics
	 *
	 * @access public
	 * @param Array() $options
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'CCDNForum\ForumBundle\Entity\Flag',
			'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'intention'       => 'flag_item',
		);
	}


	/**
	 *
	 * @access public
	 * @return string
	 */
	public function getName()
	{
		return 'Flag';
	}
	
}
