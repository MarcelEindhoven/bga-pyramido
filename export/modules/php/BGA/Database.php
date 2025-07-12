<?php
namespace NieuwenhovenGames\BGA;
/**
 * Access BGA game static and protected database methods
 * @see https://boardgamearena.com/doc/Main_game_logic:_yourgamename.game.php
 *------
 * BGA implementation : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 *
 */

interface Database {
    /** DbQuery */
    public function query(string $query) : void;
    /** getObjectFromDB */
    public function getObject(string $query) : array;
    /** getObjectListFromDB */
    public function getObjectList(string $query) : array;
    /** getCollectionFromDB */
    public function getCollection(string $query) : array;

    /*
    Protected or static methods that cannot be called directly

    DbQuery($query);
    getUniqueValueFromDB( $sql );
    getCollectionFromDB( $sql, $bSingleValue=false );
    getNonEmptyCollectionFromDB( $sql );
    getNonEmptyObjectFromDB( $sql );
    getObjectFromDB( $sql );
    getObjectListFromDB( $sql, $bUniqueValue=false );
    getDoubleKeyCollectionFromDB( $sql, $bSingleValue=false );
    DbGetLastId();
    DbAffectedRow();
    escapeStringForDB( $string );
    */
}
?>
