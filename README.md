
<p align="center">
  <a href="https://github.com/GilaCMS/gila/releases">
    <img src="https://img.shields.io/github/release/gilacms/gila/all.svg">
  </a>
  <a href="https://github.com/GilaCMS/gila/commits/">
    <img src="https://img.shields.io/github/last-commit/gilacms/gila.svg">
  </a>
  <a href="https://gila-cms.readthedocs.io">
    <img src="https://readthedocs.org/projects/gila-cms/badge/?version=latest">
  </a>
  <a href="https://gitter.im/GilaCMS/Lobby">
    <img src="https://img.shields.io/gitter/room/nwjs/nw.js.svg">
  </a>
</p>

<p align="center">
  <img src="http://gilacms.com/assets/gila-logo.png" width="160px" />
</p>


Gila CMS
========
Gila CMS is a content management system made in PHP and MySql.
Built with MVC architecture, is very easy to develop on it any costumized solution.


Installation
============
1. Create a new database and a user with all privileges in MySql
2. Run /install in your browser
3. Fill all fields with the database credentials and the admin's data of the website
4. The installation is complete

Download
========
```
# with composer
composer create-project gilacms/gila

# with git
git clone https://github.com/GilaCMS/gila.git gila
```

Run with Docker
===============
Prepare the database
```
docker run --name mariadb1 -e MYSQL_ROOT_PASSWORD=rootpass -e MYSQL_DATABASE=g_db -e MYSQL_USER=g_user -e MYSQL_PASSWORD=password -d mariadb
```
Get the mariadb1 ip (use it as Database Hostname)
```
sudo docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' mariadb1
```
Run the container at http://localhost:8088 (DB Name: g_db, DB User: g_user, DB Password: password)
```
docker run  -d -p 8088:80 vzuburlis/gilacms
```

Useful Links
============
[Post: How to install gila cms](https://gilacms.com/blog/4/how-to-install-gila-cms)

[Documentation](https://gilacms.com/docs/)


Get Involved
============
You are welcome to be part of the development of Gila CMS.
First please read
[Code of Conduct](https://github.com/GilaCMS/gila/blob/master/CODE_OF_CONDUCT.md)

[How to contribute](https://github.com/GilaCMS/gila/blob/master/CONTRIBUTING.md)

For any question/feature proposal/help needed
[Make a new issue](https://github.com/GilaCMS/gila/issues/new)

[List of Contributors](https://github.com/GilaCMS/gila/blob/master/CONTRIBUTORS.md)

Gila CMS is using [Semantic Versioning](http://semver.org/)
