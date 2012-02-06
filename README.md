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

	ccdn_forum_forum:
	    user:
	        profile_route: cc_profile_show_by_id
	    template:
	        engine: twig
	        theme: CCDNForumForumBundle:Form:fields.html.twig
	    board:
	        topics_per_page: 40
	    topic:
	        posts_per_page: 10

	ccdn_forum_forum_admin:
	    user:
	        profile_route: cc_profile_show_by_id
	    template:
	        engine: twig
	        theme: CCDNForumForumAdminBundle:fields.html.twig
	    board:
	        topics_per_page: 40

	ccdn_forum_forum_moderator:
	    user:
	        profile_route: cc_profile_show_by_id
	    template:
	        engine: twig
	        theme: CCDNForumForumModeratorBundle:fields.html.twig
	    flag:
	        flags_per_page: 40
	    topic:
	        topics_per_page: 40
	        posts_per_page: 20
	    post:
	        posts_per_page: 40
	  
5) In your app/config/routing.yml add:  

	forum:  
	    resource: "@CCDNForumForumBundle/Resources/config/routing.yml"  
	    resource: "@CCDNForumForumAdminBundle/Resources/config/routing.yml"  
	    resource: "@CCDNForumForumModeratorBundle/Resources/config/routing.yml"  

6) Symlink assets to your public web directory by running this in the command line:

	php app/console assets:install --symlink web/
	
Then your done, if you need further help or want to contribute please join the community at www.codeconsortium.com where you can also see the forum in action.