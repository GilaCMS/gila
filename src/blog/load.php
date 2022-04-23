<?php

Gila\Router::controller('blog', 'blog/controllers/BlogController');
Gila\Config::addList('menu.pages', ['blog', 'Blog']);
