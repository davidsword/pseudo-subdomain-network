# network-pseduo-sub-domains #
**Contributors:**      davidsword  
**Donate link:**       https://wordpressfoundation.org/donate/  
**Tags:**              network, multisite, subdomain, rewrites  
**Requires at least:** 5.0  
**Tested up to:**      5.1.1  
**Stable tag:**        0.1.0  
**Requires PHP:**      7.1  
**License:**           GPLv2 or later  
**License URI:**       https://www.gnu.org/licenses/gpl-2.0.html  

Plugin - For subfolder networks, have the Create New Site form site the home and siteurl's to a subdomain

## Description ##

...

## Installation ##

The plugin should either be installed as a mu-plugin or network activated. It's a network plugin and therefore cannot be activated on individual sites on the network.

1. Ensure your site is a [WordPress Network setup](https://codex.wordpress.org/Create_A_Network)
	1. Using folders, not subdomain sub sites
	1. & The primary domain must not include "www."
1. Upload `/network-pseduo-sub-domains/` to `/wp-content/mu-plugins/` or `/wp-content/plugins/` directory
1. If uploaded to the latter, activate the plugin through the 'Network » Plugins' menu in WordPress

## Frequently Asked Questions ##

### Is a multisite setup the same as a network? ###

Yes. The verbiage is interchangable.

### What if my site is not a network setup? ###

Then this is not the plugin you're looking for.

### Why does this plugin require such a high version of PHP? ###

It doesn't, this plugin will run stable on >= 5.6. Flagging this requirment higher than needed
helps push WordPress users and hosts to upgrading to the safer and faster versions of PHP.

## Screenshots ##

### 1. @TODO ###
![@TODO](http://ps.w.org/network-pseduo-sub-domains/assets/screenshot-1.png)

### 2. @TODO ###
![@TODO](http://ps.w.org/network-pseduo-sub-domains/assets/screenshot-2.png)


## Changelog ##

### 1.0 ###
* Stable Release

### 0.1 ###
* Init commit

## Upgrade Notice ##

## Contributors ##

The following grunt tasks are avaliable during development:

* `i18n` containing `addtextdomain` and `makepot`
* `readme` containing `wp_readme_to_markdown`
* `build` run the two commands above
