CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers

 INTRODUCTION
------------

This module allow you to switch language based on the sub-domain. Mapping of
the domain is cofigurable. Sub-domain can be mapped with the language via
interface.

REQUIREMENTS
------------

This module requires the following modules:

 * Language (Present in core)

INSTALLATION
------------

 * Install as you would normally install a contributed Drupal module. Visit:
   https://drupal.org/documentation/install/modules-themes/modules-7
   for further information.

CONFIGURATION
-------------

  * Before doing any module configuration, add entry to virtual host and host file:

    - Add entry to virtual host (/etc/apache2/sites-available/hello.conf)

        <VirtualHost *:80>
          ServerAdmin admin@example.com
          ServerName hello.local
          ServerAlias *.hello.local
          DocumentRoot /var/www/html/hello
          ErrorLog ${APACHE_LOG_DIR}/error.log
          CustomLog ${APACHE_LOG_DIR}/access.log combined
        </VirtualHost>

    - Enable site: sudo a2ensite hello.conf


    - Add entry to host file (/etc/hosts)

      127.0.0.1 india.hello.local
      127.0.0.1 netherlands.hello.local
      127.0.0.1 world.hello.local

  * Configure user permissions in Administration » People » Permissions:

    - 'Administer language switch' should be given to the role to manage language
       and domain mapping.

  * Configure mapping of language and domain:

    - Once virtuahost and entry in the hosts file is made then this path can be used
      to manage mapping 'admin/config/regional/lswitch'.


MAINTAINERS
-----------

Current maintainers:
 * Nitesh Kumar (nit3ch) - creative2all@gmail.com - nit3ch.github.io
