# Changelog
All notable changes to this project will be documented in this file.

## 0.27.7
- restore gd stuff for partlist

## 0.27.6
- correct rel info url

## 0.27.5
- make `NEWS_URL` configurable
- make `SERVERLIST_URL` configurable
- get GUI NEWS from Github

## 0.27.4
- set default `error_reporting` to `0` (can be changed with `PHP_INI_ERROR_REPORTING`) 
- set default `display_errors` to `Off` (can be changed with `PHP_INI_DISPLAY_ERRORS`) 

## 0.27.3
- allow tab selection in perma link

## 0.27.2
- fix permalink usage

## 0.27.1
- fix relinfo link builder

## 0.27.0
- add back a simplified RelInfo Icon in `downloads`, `uploads`, `share` and `search` view
- add `permalink` on top Bar
- load http files with `file_get_contents` instead of `fsockopen`
- refactor code and style of `/index.php` for better readability
- removed `minigui`

## 0.26.0
- document `ENV` variables for docker 
- remove phpaj `savebw` option
- remove phpaj downloads `autoclean` option
- remove now useless phpaj options from settings view
- allow progressbar configuration from ENV
- allow auto login in `top` frame

## 0.25.5
- add ability to handle multiple links 

## 0.25.4
- fix link exporter (AJL and BB-Code)

## 0.25.3
- switch from `fsockopen` to `curl` for all core requests (faster)

## 0.25.2
- remove PopUps on link export

## 0.25.1
- fix minigui for PHP 7.X

## 0.25.0
- import project into VCS
- make Code PHP 7.X compatible
- add Docker support
- remove outdated `appledocs` implementation
