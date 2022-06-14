-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 10.150.1.49
-- Erstellungszeit: 02. Jun 2022 um 17:08
-- Server-Version: 10.5.12-MariaDB-0+deb11u1
-- PHP-Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `kinoddr`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__address_validate`
--

CREATE TABLE `kino__address_validate` (
  `id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__announcement`
--

CREATE TABLE `kino__announcement` (
  `id` int(11) NOT NULL,
  `title` mediumtext NOT NULL,
  `content` longtext NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `creator` int(11) NOT NULL,
  `enable` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__cinemas`
--

CREATE TABLE `kino__cinemas` (
  `ID` int(11) NOT NULL,
  `POI_ID` int(11) NOT NULL,
  `cinemas` text NOT NULL,
  `Start` int(11) DEFAULT NULL,
  `End` int(11) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` datetime NOT NULL DEFAULT current_timestamp(),
  `source` text DEFAULT NULL,
  `points_received` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__cinemas_validate`
--

CREATE TABLE `kino__cinemas_validate` (
  `id` int(11) NOT NULL,
  `cinemas_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__cinema_types`
--

CREATE TABLE `kino__cinema_types` (
  `id` int(11) NOT NULL,
  `name` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `kino__cinema_types`
--

INSERT INTO `kino__cinema_types` (`id`, `name`) VALUES
(1, 'Festes Kino'),
(2, 'Betriebsspielstätte'),
(3, 'Armeespielstätte'),
(4, 'Aufführungstätte in Kultur- und Freizeiteinrichtungen'),
(5, 'Museumskino'),
(6, 'Freiluftkino'),
(7, 'Filmclub an Hochschulen'),
(8, 'Schulkino'),
(9, 'mobiles Kino / Wanderkino'),
(10, 'Zeltplatzkino / Camping'),
(11, 'Sonstiges');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__cinema_type_validate`
--

CREATE TABLE `kino__cinema_type_validate` (
  `id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__comments`
--

CREATE TABLE `kino__comments` (
  `comment_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__current_adr_validate`
--

CREATE TABLE `kino__current_adr_validate` (
  `id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__history_validate`
--

CREATE TABLE `kino__history_validate` (
  `id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__hist_adr`
--

CREATE TABLE `kino__hist_adr` (
  `ID` int(11) NOT NULL,
  `POI_ID` int(11) NOT NULL,
  `City` text DEFAULT NULL,
  `Postalcode` varchar(140) DEFAULT NULL,
  `Streetname` text DEFAULT NULL,
  `Housenumber` text DEFAULT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` datetime NOT NULL DEFAULT current_timestamp(),
  `source` text DEFAULT NULL,
  `points_received` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__names`
--

CREATE TABLE `kino__names` (
  `ID` int(11) NOT NULL,
  `POI_ID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Start` int(11) DEFAULT NULL,
  `End` int(11) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` datetime NOT NULL DEFAULT current_timestamp(),
  `source` text DEFAULT NULL,
  `points_received` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__name_validate`
--

CREATE TABLE `kino__name_validate` (
  `id` int(11) NOT NULL,
  `name_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__operators`
--

CREATE TABLE `kino__operators` (
  `ID` int(11) NOT NULL,
  `POI_ID` int(11) NOT NULL,
  `Operator` text NOT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` datetime NOT NULL DEFAULT current_timestamp(),
  `source` text DEFAULT NULL,
  `points_received` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__operator_validate`
--

CREATE TABLE `kino__operator_validate` (
  `id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__pois`
--

CREATE TABLE `kino__pois` (
  `poi_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `lng` double NOT NULL,
  `lat` double NOT NULL,
  `City` text COLLATE utf8_bin DEFAULT NULL,
  `Postalcode` varchar(140) COLLATE utf8_bin DEFAULT NULL,
  `Streetname` text COLLATE utf8_bin DEFAULT NULL,
  `Housenumber` text COLLATE utf8_bin DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `start` int(11) DEFAULT NULL,
  `end` int(11) DEFAULT NULL,
  `category` int(255) NOT NULL DEFAULT 0,
  `history` text COLLATE utf8_bin DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `duty` tinyint(4) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `creationDate` datetime NOT NULL DEFAULT current_timestamp(),
  `creator_timespan` int(11) DEFAULT NULL,
  `creationdate_timespan` datetime NOT NULL DEFAULT current_timestamp(),
  `creator_currentAddress` int(11) DEFAULT NULL,
  `creationdate_currentAddress` datetime NOT NULL DEFAULT current_timestamp(),
  `creator_history` int(11) DEFAULT NULL,
  `creatoiondate_history` datetime NOT NULL DEFAULT current_timestamp(),
  `creator_type` int(11) DEFAULT NULL,
  `creationdate_type` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `deletedPic` tinyint(4) NOT NULL DEFAULT 0,
  `blog` varchar(160) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__poi_pictures`
--

CREATE TABLE `kino__poi_pictures` (
  `id` int(11) NOT NULL,
  `picture_id` varchar(145) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `poiDel` tinyint(4) NOT NULL DEFAULT 0,
  `picDel` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__poi_pictures_validate`
--

CREATE TABLE `kino__poi_pictures_validate` (
  `id` int(11) NOT NULL,
  `link-id-poi-pic` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__poi_sources`
--

CREATE TABLE `kino__poi_sources` (
  `id` int(11) NOT NULL,
  `poiid` int(11) NOT NULL,
  `source` mediumtext NOT NULL,
  `typeid` int(11) NOT NULL,
  `relationid` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__poi_story`
--

CREATE TABLE `kino__poi_story` (
  `id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `story_token` varchar(140) NOT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `poiDel` tinyint(4) NOT NULL DEFAULT 0,
  `storyDel` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__poi_story_validate`
--

CREATE TABLE `kino__poi_story_validate` (
  `id` int(11) NOT NULL,
  `story_poi_link_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__seats`
--

CREATE TABLE `kino__seats` (
  `ID` int(11) NOT NULL,
  `POI_ID` int(11) NOT NULL,
  `seats` text NOT NULL,
  `Start` int(11) DEFAULT NULL,
  `End` int(11) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `creationdate` datetime NOT NULL DEFAULT current_timestamp(),
  `source` text DEFAULT NULL,
  `points_received` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__seats_validate`
--

CREATE TABLE `kino__seats_validate` (
  `id` int(11) NOT NULL,
  `seats_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__session`
--

CREATE TABLE `kino__session` (
  `ses_id` varchar(190) NOT NULL,
  `ses_time` int(11) NOT NULL,
  `ses_value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__source_relation`
--

CREATE TABLE `kino__source_relation` (
  `id` int(11) NOT NULL,
  `name` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `kino__source_relation`
--

INSERT INTO `kino__source_relation` (`id`, `name`) VALUES
(1, 'Allgemein'),
(2, 'Namen'),
(3, 'Adressen'),
(4, 'Sitzplätze'),
(5, 'Kinosäle'),
(6, 'Betreiber'),
(7, 'Betriebszeitraum'),
(8, 'Geschichte');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__source_type`
--

CREATE TABLE `kino__source_type` (
  `id` int(11) NOT NULL,
  `name` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `kino__source_type`
--

INSERT INTO `kino__source_type` (`id`, `name`) VALUES
(1, 'Literatur'),
(2, 'Webseite'),
(3, 'Zeitzeuge'),
(4, 'Quellenmaterial'),
(5, 'Sonstiges');

-- --------------------------------------------------------

--
-- Stellvertreter-Struktur des Views `kino__Statistics_Count_validated`
-- (Siehe unten für die tatsächliche Ansicht)
--
CREATE TABLE `kino__Statistics_Count_validated` (
`poi_id` int(11)
,`validateVal` decimal(32,0)
,`name` varchar(255)
);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__timespan_validate`
--

CREATE TABLE `kino__timespan_validate` (
  `id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__user-login`
--

CREATE TABLE `kino__user-login` (
  `id` int(11) NOT NULL,
  `name` varchar(190) NOT NULL,
  `password` longtext NOT NULL,
  `firstname` mediumtext DEFAULT NULL,
  `lastname` mediumtext DEFAULT NULL,
  `email` mediumtext DEFAULT NULL,
  `deaktivate` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__user_browserinfo`
--

CREATE TABLE `kino__user_browserinfo` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `browserName` varchar(140) NOT NULL,
  `browserVersion` varchar(140) NOT NULL,
  `plattform` varchar(140) NOT NULL,
  `userAgent` mediumtext NOT NULL,
  `realName` varchar(140) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__validate`
--

CREATE TABLE `kino__validate` (
  `id` int(11) NOT NULL,
  `poi_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kino__visitors`
--

CREATE TABLE `kino__visitors` (
  `id` int(11) NOT NULL,
  `ip` varchar(140) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(140) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur des Views `kino__Statistics_Count_validated`
--
DROP TABLE IF EXISTS `kino__Statistics_Count_validated`;

CREATE ALGORITHM=UNDEFINED DEFINER=`martin`@`%` SQL SECURITY DEFINER VIEW `kino__Statistics_Count_validated`  AS SELECT `A`.`poi_id` AS `poi_id`, `B`.`validateVal` AS `validateVal`, `A`.`name` AS `name` FROM (`kino__pois` `A` left join (select sum(`kino__validate`.`value`) AS `validateVal`,`kino__validate`.`poi_id` AS `poi_id` from `kino__validate` group by `kino__validate`.`poi_id`) `B` on(`B`.`poi_id` = `A`.`poi_id`)) ;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `kino__address_validate`
--
ALTER TABLE `kino__address_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-address-userid` (`uid`),
  ADD KEY `kino-address-adr-id` (`address_id`);

--
-- Indizes für die Tabelle `kino__announcement`
--
ALTER TABLE `kino__announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid-constraint-announcement` (`creator`);

--
-- Indizes für die Tabelle `kino__cinemas`
--
ALTER TABLE `kino__cinemas`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `kino-cinemas-poiid` (`POI_ID`),
  ADD KEY `kino-cinemas-userid` (`creator`);

--
-- Indizes für die Tabelle `kino__cinemas_validate`
--
ALTER TABLE `kino__cinemas_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-cinemas-val-id` (`uid`),
  ADD KEY `kino-cinemas-val-nameid` (`cinemas_id`);

--
-- Indizes für die Tabelle `kino__cinema_types`
--
ALTER TABLE `kino__cinema_types`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kino__cinema_type_validate`
--
ALTER TABLE `kino__cinema_type_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-type-poiid` (`poi_id`),
  ADD KEY `kino-type-userid` (`uid`);

--
-- Indizes für die Tabelle `kino__comments`
--
ALTER TABLE `kino__comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `POIID_COMMENT` (`poi_id`),
  ADD KEY `UID_COMMENT` (`user_id`);

--
-- Indizes für die Tabelle `kino__current_adr_validate`
--
ALTER TABLE `kino__current_adr_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-current_adr-poiid` (`poi_id`),
  ADD KEY `kino-current_adr-userid` (`uid`);

--
-- Indizes für die Tabelle `kino__history_validate`
--
ALTER TABLE `kino__history_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-history-poiid` (`poi_id`),
  ADD KEY `kino-history-userid` (`uid`);

--
-- Indizes für die Tabelle `kino__hist_adr`
--
ALTER TABLE `kino__hist_adr`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `userid` (`creator`),
  ADD KEY `poiid_hist_adr` (`POI_ID`);

--
-- Indizes für die Tabelle `kino__names`
--
ALTER TABLE `kino__names`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `kino-names-poiid` (`POI_ID`),
  ADD KEY `kino-names-userid` (`creator`);

--
-- Indizes für die Tabelle `kino__name_validate`
--
ALTER TABLE `kino__name_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-name-userid` (`uid`),
  ADD KEY `kino-name-nameid` (`name_id`);

--
-- Indizes für die Tabelle `kino__operators`
--
ALTER TABLE `kino__operators`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `kino-operators-poiid` (`POI_ID`),
  ADD KEY `kino-operators-userid` (`creator`);

--
-- Indizes für die Tabelle `kino__operator_validate`
--
ALTER TABLE `kino__operator_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-operator-userid` (`uid`),
  ADD KEY `kino-operator-operatorid` (`operator_id`);

--
-- Indizes für die Tabelle `kino__pois`
--
ALTER TABLE `kino__pois`
  ADD PRIMARY KEY (`poi_id`),
  ADD KEY `UserIdentificationPOITB` (`user_id`),
  ADD KEY `user_timespan` (`creator_timespan`),
  ADD KEY `user_current_Address` (`creator_currentAddress`),
  ADD KEY `user_history` (`creator_history`),
  ADD KEY `type_constraint` (`type`),
  ADD KEY `user_type_creator` (`creator_type`);

--
-- Indizes für die Tabelle `kino__poi_pictures`
--
ALTER TABLE `kino__poi_pictures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `picture_poi_unique` (`picture_id`,`poi_id`),
  ADD KEY `poi_id_id` (`poi_id`),
  ADD KEY `uid_creator` (`creator`);

--
-- Indizes für die Tabelle `kino__poi_pictures_validate`
--
ALTER TABLE `kino__poi_pictures_validate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `creator` (`creator`,`link-id-poi-pic`),
  ADD UNIQUE KEY `creator_2` (`creator`,`link-id-poi-pic`),
  ADD UNIQUE KEY `creator_3` (`creator`,`link-id-poi-pic`),
  ADD KEY `poi-pic-validate-constraint` (`link-id-poi-pic`);

--
-- Indizes für die Tabelle `kino__poi_sources`
--
ALTER TABLE `kino__poi_sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `poiid-source-constraint` (`poiid`),
  ADD KEY `creator-source-constraint` (`creator`),
  ADD KEY `sourcetype-source-constraint` (`typeid`),
  ADD KEY `relation-source-constraint` (`relationid`);

--
-- Indizes für die Tabelle `kino__poi_story`
--
ALTER TABLE `kino__poi_story`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `poi_id` (`poi_id`,`story_token`),
  ADD KEY `user_id_creator_poi_story` (`creator`);

--
-- Indizes für die Tabelle `kino__poi_story_validate`
--
ALTER TABLE `kino__poi_story_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-poi-story-userid` (`uid`),
  ADD KEY `kino-poi-story-id` (`story_poi_link_id`);

--
-- Indizes für die Tabelle `kino__seats`
--
ALTER TABLE `kino__seats`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `kino-seats-poiid` (`POI_ID`),
  ADD KEY `kino-seats-userid` (`creator`);

--
-- Indizes für die Tabelle `kino__seats_validate`
--
ALTER TABLE `kino__seats_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-seats-val-id` (`uid`),
  ADD KEY `kino-seats-val-nameid` (`seats_id`);

--
-- Indizes für die Tabelle `kino__session`
--
ALTER TABLE `kino__session`
  ADD PRIMARY KEY (`ses_id`);

--
-- Indizes für die Tabelle `kino__source_relation`
--
ALTER TABLE `kino__source_relation`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kino__source_type`
--
ALTER TABLE `kino__source_type`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kino__timespan_validate`
--
ALTER TABLE `kino__timespan_validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kino-timespan-poiid` (`poi_id`),
  ADD KEY `kino-timespan-uid` (`uid`);

--
-- Indizes für die Tabelle `kino__user-login`
--
ALTER TABLE `kino__user-login`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `kino__user_browserinfo`
--
ALTER TABLE `kino__user_browserinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `kino__validate`
--
ALTER TABLE `kino__validate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `poiid` (`poi_id`);

--
-- Indizes für die Tabelle `kino__visitors`
--
ALTER TABLE `kino__visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `kino__address_validate`
--
ALTER TABLE `kino__address_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__announcement`
--
ALTER TABLE `kino__announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__cinemas`
--
ALTER TABLE `kino__cinemas`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__cinemas_validate`
--
ALTER TABLE `kino__cinemas_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__cinema_types`
--
ALTER TABLE `kino__cinema_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT für Tabelle `kino__cinema_type_validate`
--
ALTER TABLE `kino__cinema_type_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__comments`
--
ALTER TABLE `kino__comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__current_adr_validate`
--
ALTER TABLE `kino__current_adr_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__history_validate`
--
ALTER TABLE `kino__history_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__hist_adr`
--
ALTER TABLE `kino__hist_adr`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__names`
--
ALTER TABLE `kino__names`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__name_validate`
--
ALTER TABLE `kino__name_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__operators`
--
ALTER TABLE `kino__operators`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__operator_validate`
--
ALTER TABLE `kino__operator_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__pois`
--
ALTER TABLE `kino__pois`
  MODIFY `poi_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__poi_pictures`
--
ALTER TABLE `kino__poi_pictures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__poi_pictures_validate`
--
ALTER TABLE `kino__poi_pictures_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__poi_sources`
--
ALTER TABLE `kino__poi_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__poi_story`
--
ALTER TABLE `kino__poi_story`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__poi_story_validate`
--
ALTER TABLE `kino__poi_story_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__seats`
--
ALTER TABLE `kino__seats`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__seats_validate`
--
ALTER TABLE `kino__seats_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__source_relation`
--
ALTER TABLE `kino__source_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `kino__source_type`
--
ALTER TABLE `kino__source_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `kino__timespan_validate`
--
ALTER TABLE `kino__timespan_validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__user-login`
--
ALTER TABLE `kino__user-login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__user_browserinfo`
--
ALTER TABLE `kino__user_browserinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__validate`
--
ALTER TABLE `kino__validate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `kino__visitors`
--
ALTER TABLE `kino__visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `kino__address_validate`
--
ALTER TABLE `kino__address_validate`
  ADD CONSTRAINT `kino-address-adr-id` FOREIGN KEY (`address_id`) REFERENCES `kino__hist_adr` (`ID`),
  ADD CONSTRAINT `kino-address-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__announcement`
--
ALTER TABLE `kino__announcement`
  ADD CONSTRAINT `uid-constraint-announcement` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__cinemas`
--
ALTER TABLE `kino__cinemas`
  ADD CONSTRAINT `kino-cinemas-poiid` FOREIGN KEY (`POI_ID`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-cinemas-userid` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__cinemas_validate`
--
ALTER TABLE `kino__cinemas_validate`
  ADD CONSTRAINT `kino-cinemas-val-id` FOREIGN KEY (`cinemas_id`) REFERENCES `kino__cinemas` (`ID`),
  ADD CONSTRAINT `kino-cinemas-val-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__cinema_type_validate`
--
ALTER TABLE `kino__cinema_type_validate`
  ADD CONSTRAINT `kino-type-poiid` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-type-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__comments`
--
ALTER TABLE `kino__comments`
  ADD CONSTRAINT `POIID_COMMENT` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `UID_COMMENT` FOREIGN KEY (`user_id`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__current_adr_validate`
--
ALTER TABLE `kino__current_adr_validate`
  ADD CONSTRAINT `kino-current_adr-poiid` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-current_adr-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__history_validate`
--
ALTER TABLE `kino__history_validate`
  ADD CONSTRAINT `kino-history-poiid` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-history-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__hist_adr`
--
ALTER TABLE `kino__hist_adr`
  ADD CONSTRAINT `poiid_hist_adr` FOREIGN KEY (`POI_ID`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `userid` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__names`
--
ALTER TABLE `kino__names`
  ADD CONSTRAINT `kino-names-poiid` FOREIGN KEY (`POI_ID`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-names-userid` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__name_validate`
--
ALTER TABLE `kino__name_validate`
  ADD CONSTRAINT `kino-name-nameid` FOREIGN KEY (`name_id`) REFERENCES `kino__names` (`ID`),
  ADD CONSTRAINT `kino-name-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__operators`
--
ALTER TABLE `kino__operators`
  ADD CONSTRAINT `kino-operators-poiid` FOREIGN KEY (`POI_ID`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-operators-userid` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__operator_validate`
--
ALTER TABLE `kino__operator_validate`
  ADD CONSTRAINT `kino-operator-operatorid` FOREIGN KEY (`operator_id`) REFERENCES `kino__operators` (`ID`),
  ADD CONSTRAINT `kino-operator-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__pois`
--
ALTER TABLE `kino__pois`
  ADD CONSTRAINT `UserIdentificationPOITB` FOREIGN KEY (`user_id`) REFERENCES `kino__user-login` (`id`),
  ADD CONSTRAINT `type_constraint` FOREIGN KEY (`type`) REFERENCES `kino__cinema_types` (`id`),
  ADD CONSTRAINT `user_current_Address` FOREIGN KEY (`creator_currentAddress`) REFERENCES `kino__user-login` (`id`),
  ADD CONSTRAINT `user_history` FOREIGN KEY (`creator_history`) REFERENCES `kino__user-login` (`id`),
  ADD CONSTRAINT `user_timespan` FOREIGN KEY (`creator_timespan`) REFERENCES `kino__user-login` (`id`),
  ADD CONSTRAINT `user_type_creator` FOREIGN KEY (`creator_type`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__poi_pictures`
--
ALTER TABLE `kino__poi_pictures`
  ADD CONSTRAINT `poi_id_id` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `uid_creator` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__poi_pictures_validate`
--
ALTER TABLE `kino__poi_pictures_validate`
  ADD CONSTRAINT `poi-pic-validate-constraint` FOREIGN KEY (`link-id-poi-pic`) REFERENCES `kino__poi_pictures` (`id`),
  ADD CONSTRAINT `poi-pic-validate-uid-constraint` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__poi_sources`
--
ALTER TABLE `kino__poi_sources`
  ADD CONSTRAINT `creator-source-constraint` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`),
  ADD CONSTRAINT `poiid-source-constraint` FOREIGN KEY (`poiid`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `relation-source-constraint` FOREIGN KEY (`relationid`) REFERENCES `kino__source_relation` (`id`),
  ADD CONSTRAINT `sourcetype-source-constraint` FOREIGN KEY (`typeid`) REFERENCES `kino__source_type` (`id`);

--
-- Constraints der Tabelle `kino__poi_story`
--
ALTER TABLE `kino__poi_story`
  ADD CONSTRAINT `poi_id_creator_poi_story` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `user_id_creator_poi_story` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__poi_story_validate`
--
ALTER TABLE `kino__poi_story_validate`
  ADD CONSTRAINT `kino-poi-story-id` FOREIGN KEY (`story_poi_link_id`) REFERENCES `kino__poi_story` (`id`),
  ADD CONSTRAINT `kino-poi-story-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__seats`
--
ALTER TABLE `kino__seats`
  ADD CONSTRAINT `kino-seats-poiid` FOREIGN KEY (`POI_ID`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-seats-userid` FOREIGN KEY (`creator`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__seats_validate`
--
ALTER TABLE `kino__seats_validate`
  ADD CONSTRAINT `kino-seats-val-id` FOREIGN KEY (`seats_id`) REFERENCES `kino__seats` (`ID`),
  ADD CONSTRAINT `kino-seats-val-userid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__timespan_validate`
--
ALTER TABLE `kino__timespan_validate`
  ADD CONSTRAINT `kino-timespan-poiid` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `kino-timespan-uid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);

--
-- Constraints der Tabelle `kino__validate`
--
ALTER TABLE `kino__validate`
  ADD CONSTRAINT `poiid` FOREIGN KEY (`poi_id`) REFERENCES `kino__pois` (`poi_id`),
  ADD CONSTRAINT `uid` FOREIGN KEY (`uid`) REFERENCES `kino__user-login` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
