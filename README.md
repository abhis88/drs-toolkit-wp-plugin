<!--- [![Build Status](https://travis-ci.org/NEU-Libraries/drs-toolkit-wp-plugin.svg?branch=develop)](https://travis-ci.org/NEU-Libraries/drs-toolkit-wp-plugin)

[![Coverage Status](https://coveralls.io/repos/github/NEU-Libraries/drs-toolkit-wp-plugin/badge.svg?branch=master)](https://coveralls.io/github/NEU-Libraries/drs-toolkit-wp-plugin?branch=master)
--->

Plugin that provides functionality and templates for bringing DRS data into WP for the CERES: Exhibit Toolkit.

Should be cloned into wp-content/plugins with
```
  git clone https://github.com/NEU-Libraries/drs-toolkit-wp-plugin.git drs-tk
```
Relies on Javascript being enabled and AJAX - Should work in IE10 and up, Chrome, Firefox, and Safari

[Corresponding theme](https://github.com/NEU-Libraries/drs-toolkit-wp-theme)

### Setup

1. Ask Karl for clean install of WP on correct server (will need to provide a directory name)

2. Log in to new Wordpress site as admin user

3. Install Quest theme (go to /wp-admin/theme-install.php and search for quest then click install)

4. SSH into correct directory on server and git clone this repo into wp-content/plugins

  ```
    git clone https://github.com/NEU-Libraries/drs-toolkit-wp-plugin.git drs-tk
  ```

5. Also clone the theme into wp-content/themes

  ```
    git clone https://github.com/NEU-Libraries/drs-toolkit-wp-theme.git quest-child
  ```

6. Register your site on google analytics and add your code to a git-ignored file called analytics.php

  ```
    cd /wp-content/themes/quest-child
    vi analytics.php
  ```

  Example file contents:

  ```
    <?php
      echo "<script>[YOUR GOOGLE ANALYTICS CODE]
      function add_google_tracking(){
        jQuery('.button').on('click', function() {
          ga('send', 'event', jQuery(this).data('label'), 'click', jQuery(this).data('pid'));
          console.log('send', 'event', jQuery(this).data('label'), 'click', jQuery(this).data('pid'));
        });
      }
      </script>
      ";
  ```

7. If you would like to override some of the functionality and styles or this child theme you may create a sub-directory named `overrides`.  This directory will be ignored by git and your changes won't be overwritten by future git pulls from the main repo.  Additionally, you can initialize this repository as a git-submodule and track your own changes in your own repo.

  ```
    cd wp-content/themes/quest-child
    mkdir overrides
    touch overrides/style.css
    echo "<?php //silence is golden" > overrides/functions.php
  ```

8. Delete extra themes to avoid user confusion

  ```
    cd wp-content/themes
    rm -rf twentyfifteen
    rm -rf twentyfourteen
    rm -rf twentythirteen
  ```

9. Go to /wp-admin/plugins.php in your browser. Install dependent plugins: Relevanssi, Page Builder by SiteOrigin, Black Studio TinyMCE Widget, and Widget Context and activate them.

10. Go to /wp-admin/plugins.php in your browser. Activate CERES: Exhibit Toolkit Plugin

11. Go to Settings > Reading and set Front Page Displays to a static page then choose a static page.

12. Go to Settings > Discussion and uncheck the box that says 'Allow people to post comments on new articles' to disable comments by default

13. Go to Settings > CERES: Exhibit Toolkit and enter collection URL and click Update

14. Go to Appearance > Themes and activate CERES: Exhibit Toolkit (Quest Child Theme)

15. Go to Appearance > Customize > Layout > Search Results. Change sidebar to none.

16. Go to Appearance > Customize > Colors > Global. Change Accent Color and  Accent Shade Color to #c00

17. Click the '<' arrow then Header. Change Secondary Header > Background Color to #494949, Text Color and Social Icon Color to #AFAFAF, Top Border Color to #3C3C3C and Social Icon Hover Color to #EFEFEF

18. Click the '<' arrow then Main Menu. Change Menu Items > Text Hover/Focus Color and SubMenu Items Hover/Focus Text Color to #c00.

19. Click the '<' arrow then Footer. Change Social Icon Hover Color and Social Icon Hover background color to #c00. Change Secondary Footer Background Color to #3C3C3C.

20. Go to Pages > Delete the 'Sample Page'. Go to Posts > Delete the 'Hello World'.

---
If you would like breadcrumbs on single pages/posts (not drs items) that reflect hierarchy, simply drag and drop the pages in the wp-admin pages screen to nest.

---
If you have trouble with the loading DRS content after initial install. Here are a few things to check:

* Check to make sure you have mod_rewrite enable (enable (`a2enmod rewrite`) and apache2/httpd restart(`sudo service apache2 restart`)). If you aren't sure if mod_rewrite is enabled, check `phpinfo()`

* Make sure the apache conf settings are correct. For your WP directory, you must have `Options FollowSymlinks` and `AllowOverride FileInfo` or `AllowOverride All`

* Make sure php5-curl library is installed. `sudo apt-get install php5-curl` and `sudo service apache2 restart`

* Check to make sure permalink settings are on and correct (ie. no index.php inside the path). If saving/resaving permalink settings does not work, make sure your .htaccess file in the root directory of your WP install is present and writable. For more on permalinks see [https://codex.wordpress.org/Using_Permalinks](https://codex.wordpress.org/Using_Permalinks).

* Check File permissions. In general WP directories should be 775 and files should be 664, with the exception of wp-config.php which should be 660. See [https://www.smashingmagazine.com/2014/05/proper-wordpress-filesystem-permissions-ownerships/](https://www.smashingmagazine.com/2014/05/proper-wordpress-filesystem-permissions-ownerships/) for more info.

---
Optional Steps for Updating

1. Install [Composer](https://getcomposer.org) ([Install Directions](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx))

2. Add a `composer.json` file to the root directory of your Wordpress install with the following content:

  ```
      {
    	"repositories":[
    		{
    			"type":"vcs",
    			"url":"https://github.com/NEU-Libraries/drs-toolkit-wp-plugin"
    		}
    	],
    	"require": {
    		"drs-tk/drs-tk":"dev-master"
    	},
    	"extra" : {
            	"installer-paths" : {
                		"wp-content/plugins/{$name}/": ["type:wordpress-plugin"]
            	}
        	}
    }
  ```
3. Update the plugin by running `php composer.phar update` in the root directory of your wordpress install.
