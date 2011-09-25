# Fuel PHP - Oauth Provider

Initial development of an Oauth provider package for Fuel PHP. Apologies for this not being just a single package as there are models and login / permissions pages also added. I couldn't think of an easy way to abstract the database calls from the package that anyone would benefit from, so for now this ships as a full app package. 

If someone smarter than me (there should be a lot of you) wants to fork this and rework it into a single package by all means feel free.
Also there is a basic users system in place for testing login and access, this is by no means thorough although should be "secure" enough and uses bcrypt for password hashing.

## Prerequisites
* Requires Orm package.
* Database tables created.
* Create a table to hold your users if needed (see below).

## To do's
* Create the `block/'.$consumer->id` method, to blacklist consumers against a particular users request token.

## Basic usage
Create your database tables for:

### oauth_consumers

	CREATE TABLE `oauth_consumers` (
	  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	  `key` varchar(255) NOT NULL,
	  `secret` varchar(255) NOT NULL,
	  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
	  `name` varchar(255) NOT NULL,
	  `description` text,
	  `image` varchar(255) DEFAULT NULL,
	  `homepage` varchar(255) DEFAULT NULL,
	  `contact_email` varchar(255) DEFAULT NULL,
	  `author` varchar(255) DEFAULT NULL,
	  `platform` varchar(255) DEFAULT NULL,
	  `callback_url` varchar(255) DEFAULT NULL,
	  `user_id` bigint(20) unsigned DEFAULT NULL,
	  `date_created` datetime DEFAULT NULL,
	  `created_by` bigint(20) unsigned DEFAULT NULL,
	  `last_updated` datetime DEFAULT NULL,
	  `last_updated_by` bigint(20) unsigned DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

### oauth_consumer_nonces

	CREATE TABLE `oauth_consumer_nonces` (
	  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	  `consumer_id` bigint(20) unsigned NOT NULL,
	  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	  `nonce` varchar(255) NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;

### oauth_tokens

	CREATE TABLE `oauth_tokens` (
	  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	  `type` enum('request','access') NOT NULL DEFAULT 'request',
	  `consumer` bigint(20) unsigned NOT NULL,
	  `token` varchar(255) NOT NULL,
	  `token_secret` varchar(255) NOT NULL,
	  `callback` varchar(255) DEFAULT NULL,
	  `verifier` varchar(255) DEFAULT NULL,
	  `user` bigint(20) unsigned DEFAULT NULL,
	  `state` int(11) DEFAULT '0',
	  `date_created` datetime DEFAULT NULL,
	  `created_by` bigint(20) unsigned DEFAULT NULL,
	  `last_updated` datetime DEFAULT NULL,
	  `last_updated_by` bigint(20) unsigned DEFAULT NULL,
	  PRIMARY KEY (`id`),
	  KEY `date_created` (`date_created`)
	) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

### User table

	CREATE TABLE `users` (
	  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	  `display_name` varchar(255) DEFAULT NULL,
	  `full_name` varchar(255) DEFAULT NULL,
	  `email` varchar(255) DEFAULT NULL,
	  `state` int(11) DEFAULT NULL,
	  `password` varchar(255) DEFAULT NULL,
	  `date_created` datetime DEFAULT NULL,
	  `created_by` bigint(20) unsigned DEFAULT NULL,
	  `last_updated` datetime DEFAULT NULL,
	  `last_updated_by` bigint(20) unsigned DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;