# Arcadia

Current Version: `5.0.x`  
Guidelines: `1.0`

## Requirements

* [PHP 7+](http://php.net)
* [Node](http://nodejs.com)
* [FontForge](https://fontforge.github.io/en-US/)
* [AdvancedCustomFields 5.7.0+](https://www.advancedcustomfields.com/)

## Prerequisites

It is required that the following packages be installed globally:

```
npm install -g gulp-cli browser-sync eslint-config-airbnb@^13.0.0 eslint@^3.9.1 eslint-plugin-jsx-a11y@^2.2.3 eslint-plugin-import@^2.1.0 eslint-plugin-react@^6.6.0
```

## Getting started

1. First you will need to install some packages using the following `npm install`
1. Update `src/scss/style.scss` with your project details.
1. Update `gulpfile.js` config `url` to the root of your local WordPress install if not set by launcher.
1. Windows users refer to section below.
1. And finally run `gulp`

## Windows Users

1. You will need FontForge added to your system PATH.

## Sublime Users

1. If you experience issues with partial SCSS files being unreadable. Add "atomic_save": true to your preferences. It will increase SCSS compilation times but will resolve the issue.
