
-- ------
-- BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
-- PyramidoCannonFodder implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

-- Each domino consists of two tiles
-- The type specifies the manufacturer order, which is also the position of the domino in the image
-- The type specifies the first tile, the type argument specifies the second tile
-- A type argument is composed of colour (6 possibilities) of first and second tile and the presence/absence of a jewel icon in each of the 4 corners
-- Location is 'deck', 'quarry', 'next', player ID
-- Location argument for quarry and next is the index, starting from 1
-- Location argument for player ID is the stage (0-4), horizontal and vertical of the first tile (0-19) and rotation (0-3)
-- Stage 4 has the special meaning that it is the last placed domino
CREATE TABLE IF NOT EXISTS `domino` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- The type specifies the colour of the first tile, the type argument specifies the colour of the second tile
-- Location is player ID
-- Location argument for player ID is the stage (0-4),
-- horizontal and vertical of the tile (0-19) and rotation (0-3) and side (0-1)
CREATE TABLE IF NOT EXISTS `resurfacing` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- The ID specifies the position of the Jewel marker in the image
-- The type specifies the marker
-- A type number is composed of colour (6 possibilities)
-- Location is player ID
-- Location argument for player ID is the stage (0-4), horizontal and vertical of the first tile (0-19)
CREATE TABLE IF NOT EXISTS `marker` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

