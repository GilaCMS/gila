{
  "short_name": "<?=gila::option('pwa.short_name','Gila')?>",
  "name": "<?=gila::option('pwa.name','Gila CMS')?>",
  "display": "standalone",
  "icons": [
    {
      "src": "<?=view::thumb($icon,'fav/48_',48)?>",
      "type": "image/png",
      "sizes": "48x48"
    },
    {
      "src": "<?=view::thumb($icon,'fav/96_',96)?>",
      "type": "image/png",
      "sizes": "96x96"
    },
    {
      "src": "<?=view::thumb($icon,'fav/192_',192)?>",
      "type": "image/png",
      "sizes": "192x192"
    }
  ],
  "start_url": "?homescreen=1"
}
