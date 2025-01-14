<?php

$query_create = "
CREATE TABLE `example` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,
    `text` longtext NOT NULL DEFAULT '',
    `pair` varchar(250) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE (`pair`),
    INDEX (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

$query_create = "

CREATE TABLE `z_0_signal` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_id` int UNSIGNED NOT NULL DEFAULT 0,
    `pair` varchar(250) NOT NULL DEFAULT '',
    `timeframe` varchar(250) NOT NULL DEFAULT '',
    `price` varchar(250) NOT NULL DEFAULT '',
    `indicator` varchar(250) NOT NULL DEFAULT '',
    `condition` varchar(250) NOT NULL DEFAULT '',
    `enable` int UNSIGNED NOT NULL DEFAULT 0,
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `positionSide` varchar(250) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `strat` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,
    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `text_in` longtext NOT NULL DEFAULT '',
    `text_out` longtext NOT NULL DEFAULT '',
    `json` longtext NOT NULL DEFAULT '',

    `saved` int UNSIGNED NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    UNIQUE (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `strat_history` (
    `id` bigint UNSIGNED NOT NULL DEFAULT 0,  # AUTO_INCREMENT
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,
    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `text_in` longtext NOT NULL DEFAULT '',
    `text_out` longtext NOT NULL DEFAULT '',
    `json` longtext NOT NULL DEFAULT '',

    `saved` int UNSIGNED NOT NULL DEFAULT 0,

  # PRIMARY KEY (`id`),
    INDEX (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `indiset` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  # `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `str` longtext NOT NULL DEFAULT '',
    `json` longtext NOT NULL DEFAULT '',

    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    UNIQUE (`str`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `indiset_combo` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  # `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    `enable` int UNSIGNED NOT NULL DEFAULT 0,

   #`indiset_id` int UNSIGNED NOT NULL DEFAULT 0,
   #`indiset_uuid` char(36) NOT NULL DEFAULT '',

    `indiset_ids` varchar(250) NOT NULL DEFAULT '',
  # `indiset_uuids` longtext NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    UNIQUE(`indiset_ids`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=11;

CREATE TABLE `indiset_combo_by_users` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  # `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    `enable` int UNSIGNED NOT NULL DEFAULT 0,
    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `indiset_combo_id` int UNSIGNED NOT NULL DEFAULT 0,
  # `indiset_combo_uuid` char(36) NOT NULL DEFAULT '',
    `pair` longtext NOT NULL DEFAULT '',

  # `takestop_combo_id` int UNSIGNED NOT NULL DEFAULT 0,
  # `takestop_combo_uuid` char(36) NOT NULL DEFAULT '',

    `side` varchar(250) NOT NULL DEFAULT '',
    `takestop_ids` longtext NOT NULL DEFAULT '',
  # `takestop_uuids` longtext NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    UNIQUE(`user_id`,`indiset_combo_id`),
    INDEX(`enable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#CREATE TABLE `indiset_combo_history` ######################################################

CREATE TABLE `takestop` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  # `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `take` varchar(250) NOT NULL DEFAULT '',
    `stop` varchar(250) NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    UNIQUE (`take`,`stop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

/*
##CREATE TABLE `takestop_combo` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  # `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

  # `takestop_id` int UNSIGNED NOT NULL DEFAULT 0,
  # `takestop_uuid` char(36) NOT NULL DEFAULT '',

    `takestop_ids` longtext NOT NULL DEFAULT '',
  # `takestop_uuids` longtext NOT NULL DEFAULT '',

    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
*/

