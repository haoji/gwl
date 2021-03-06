# Gaming with Lemons

Video game collection, wish list and backlog tracker.

## Credit

This software uses the following software and services, for which we are thankful for.

* [Giant Bomb API](http://www.giantbomb.com/api/)
* [CodeIgniter](http://ellislab.com/codeigniter)
* [Ignition](http://www.ignitionpowered.co.uk/)
* [PHP Markdown](http://michelf.ca/projects/php-markdown/)
* [Markdown library for CodeIgniter](http://blog.gauntface.co.uk/2014/03/17/codeigniter-markdown-libraries-hell/)
* [Bootstrap](http://getbootstrap.com/)
* [jQuery](http://jquery.com/)
* [jQuery autogrow textarea](https://github.com/jaz303/jquery-grab-bag)

## Installation

Prerequisites:

* PHP 5.5
* MySQL
* [Compass](http://compass-style.org/install/)
* [Giant Bomb API Key](http://www.giantbomb.com/api/)

Installation:

* Create a datebase with the schema in database.txt
* Set "base_url" in ignition_application/config/config.php
* Set "hostname", "username", "password" and "database" in ignition_application/config/database.php
* Set "gb_api_key" in ignition_application/config/gwl_config.php
* Navigate to project root in the terminal and run "compass compile" to build the CSS files. You can also run "compass watch" to have the CSS rebuilt everytime you modify a stylesheet.

## License

* Copyright 2014 [Joshua Marketis](http://www.clidus.com)
* Distributed under the [MIT License](http://creativecommons.org/licenses/MIT/)
