CCDN Forum README.
==================


Notes:  
------

This bundle is for the symfony framework and thusly requires Symfony 2.0.x and PHP 5.3.6
  
This project uses Doctrine 2.0.x and so does not require any specific database.
  

This file is part of the CCDNForum Bundle(s)

(c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 

Available on github <http://www.github.com/codeconsortium/>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.

Dependencies:
-------------

Download and install the pagerfanta bundle as it is required, 
ForumBundle will not function without it. Follow the pagerfanta install
instructions.  
	  
Installation:
-------------
    
1) Create the directory src/CCDNForum in your Symfony directory.
  
2) Add the ForumBundle and CrumbTrailBundle to CCDNForurm directory.  

3) In your AppKernel.php add the following bundles to the registerBundles method array:  

	new CCDNForum\ForumBundle\CCDNForumForumBundle(),    
	new CCDNForum\ForumAdminBundle\CCDNForumForumAdminBundle(),
	new CCDNForum\ForumModeratorBundle\CCDNForumForumModeratorBundle(),
	
4) In your app/config/config.yml add:    

	# for CodeConsortium ForumBundle Pagination      
	ccdn_forum_forum:  
	    topics_per_board_page: 40  
	    posts_per_topic_page: 20
	  
5) In your app/config/routing.yml add:  

	forum:  
	    resource: "@CCDNForumForumBundle/Resources/config/routing.yml"  
	    resource: "@CCDNForumForumAdminBundle/Resources/config/routing.yml"  
	    resource: "@CCDNForumForumModeratorBundle/Resources/config/routing.yml"  

6) Symlink assets to your public web directory by running this in the command line:

	php app/console assets:install --symlink web/