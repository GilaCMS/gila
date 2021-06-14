<?php

Gila\Router::controller('blog', 'blog/controllers/BlogController');
Config::addList('menu.pages', ['blog', 'Blog']);
