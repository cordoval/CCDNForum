CCDNForum Forum Bundle(s) README.
=================================


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

1. [PagerFanta](https://github.com/whiteoctober/Pagerfanta).
2. [CCDNComponent BBCodeBundle](https://github.com/codeconsortium/BBCodeBundle).
3. [CCDNComponent CrumbTrailBundle](https://github.com/codeconsortium/CrumbTrailBundle).
4. [CCDNComponent CommonBundle](https://github.com/codeconsortium/CommonBundle).
	  
Installation:
-------------
 
1) Download and install the dependencies.
   
2) Create the directory src/CCDNForum in your Symfony directory.
  
3) Add the ForumBundle to CCDNForurm directory.  

3) In your AppKernel.php add the following bundles to the registerBundles method array:  

	new CCDNForum\ForumBundle\CCDNForumForumBundle(),    
	new CCDNForum\ForumAdminBundle\CCDNForumForumAdminBundle(),
	new CCDNForum\ForumModeratorBundle\CCDNForumForumModeratorBundle(),
	
4) In your app/config/config.yml add (this is configs for all 3 forum bundles):    

	ccdn_forum_forum:
	    user:
	        profile_route: cc_profile_show_by_id
	    template:
	        engine: twig
	        theme: CCDNForumForumBundle:Form:fields.html.twig
	    category:
	        layout_templates:
	            index: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            show: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	    board:
	        topics_per_page: 40
	        layout_templates:
	            show: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	    topic:
	        posts_per_page: 10
	        layout_templates:
	            create: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            reply: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            show: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	    post:
	        layout_templates:
	            show: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            flag: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            edit_post: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            edit_topic: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            delete_post: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig

	ccdn_forum_forum_admin:
	    user:
	        profile_route: cc_profile_show_by_id
	    template:
	        engine: twig
	        theme: CCDNForumForumAdminBundle:fields.html.twig
	    category:
	        layout_templates:
	            create: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            delete_category: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            edit: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            index: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	    board:
	        topics_per_page: 40
	        layout_templates:
	            create: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            delete_board: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            edit: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig

	ccdn_forum_forum_moderator:
	    user:
	        profile_route: cc_profile_show_by_id
	    template:
	        engine: twig
	        theme: CCDNForumForumModeratorBundle:fields.html.twig
	    flag:
	        flags_per_page: 40
	        layout_templates:
	            flag_mark: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            show_flag: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            show_flagged: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	    topic:
	        topics_per_page: 40
	        posts_per_page: 20
	        layout_templates:
	            change_board: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            show_closed: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	            delete_topic: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	    post:
	        posts_per_page: 40
	        layout_templates:
	            show_locked: CCDNComponentCommonBundle:Layout:layout_body_left.html.twig
	  
5) In your app/config/routing.yml add:  

	forum:  
	    resource: "@CCDNForumForumBundle/Resources/config/routing.yml"  
	    resource: "@CCDNForumForumAdminBundle/Resources/config/routing.yml"  
	    resource: "@CCDNForumForumModeratorBundle/Resources/config/routing.yml"  

6) Symlink assets to your public web directory by running this in the command line:

	php app/console assets:install --symlink web/
	
Then your done, if you need further help/support, have suggestions or want to contribute please join the community at [www.codeconsortium.com](http://www.codeconsortium.com)
