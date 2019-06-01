
Folder sites helps to create multiple sites with the same gila cms installation.
You only have to create a new config file and the writable folders.
In order to work all subdomains and domains must point to the root folder.

** Example **
```
+--sites
  +--another-domain.com
  +--docs.mydomain.com
    +--config.php
    +--assets/
    +--tmp/
    +--log/
```

Note: You have to redirect the public folders(assets/tmp) of the new sites:
```
# from the root .htaccess redirect all domains except the main (localhost in this case)
RewriteCond %{HTTP_HOST} ^(.*)$ [NC]
RewriteCond %{HTTP_HOST} !localhost$ [NC]
RewriteRule ^assets/(.*)$ sites/%{HTTP_HOST}/assets/$1 [NC]
RewriteRule ^tmp/(.*)$ sites/%{HTTP_HOST}/tmp/$1 [NC]
```

The new sites will share the same source code, vendor packages and themes with the main site.
Although only from the main site he administrators can access and edit the files of the main and the other sites. The main site should keep its config.php and the rest folders on the root path.

** Add a new domain linking to a subdomain **
```
ln -s sites/sub.domain.com sites/newdomain.com
```
