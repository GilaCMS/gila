#!/bin/bash
# update dependencies of Gila CMS using package.json
npm install

cp node_modules/gilajs/dist/gila.min.js lib/gila.min.js
cp node_modules/gilajs/dist/gila.min.css lib/gila.min.css

cp node_modules/g-vue-editor/vue-editor.js lib/vue/vue-editor.js
cp node_modules/g-vue-editor/vue-editor.css lib/vue/vue-editor.css

cp node_modules/vue/dist/vue.min.js lib/vue/vue.min.js
