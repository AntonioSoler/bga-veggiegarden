<?php
/**
 *------
 * BGA framework: (c) Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * veggiegarden implementation : (c) Antonio Soler Morgalad.es@gmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * states.inc.php
 *
 * veggiegarden game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

 
$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 2 )
    ),
    
    // Note: ID=2 => your first state

    2 => array(
        "name" => "startTurn",
		"description" => clienttranslate('a new turn starts...'),
        "type" => "game",
        "action" => "ststartTurn",
        "updateGameProgression" => true, 
        "transitions" => array( "playerpick" => 3  ) ,
		
    ),
	
	3 => array(
        "name" => "playerpick",  
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} has to pick a veggie from the harvest'),
		"descriptionmyturn" => clienttranslate('${you} have to pick a veggie from the harvest'),
		"action" => "stplayerpick",	
		"possibleactions" => array( "pickcard" ),        
        "transitions" => array( "selectTarget" => 4 , "endTurn" => 6 , "selectDestination" => 5  , "gameEndScoring" => 90) 
    ),
	
	4 => array(
        "name" => "selectTarget",  
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} has to select a target for the card effect'),
		"descriptionmyturn" => clienttranslate('${you} have to select a target for the card effect'),
		"action" => "stTarget",
		"args" => "argPossibleTargets",
		"possibleactions" => array( "selectTarget" , "pickcard" ),
        "transitions" => array( "selectDestination" => 5 , "selectTarget" => 4 ) 
    ),
	
	5 => array(
        "name" => "selectDestination",  
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} has to select a destination for the card effect'),
		"descriptionmyturn" => clienttranslate('${you} have to select a destination for the card effect'),
		"action" => "stDestination",
		"args" => "argPossibleDestinations",
		"possibleactions" => array( "selectDestination", "cancel" ),
        "transitions" => array( "endTurn" => 6 , "playerpick" => 3) 
    ),
		
	6 => array(
        "name" => "endTurn",
		"description" => clienttranslate('end of the turn'),
        "type" => "game",
        "action" => "stendTurn",
        "updateGameProgression" => false, 
        "transitions" => array( "" => 2  ) ,
		
    ),	
    90 => array(
	   "description" => clienttranslate('Final Score'),
        "name" => "gameEndScoring",
        "type" => "game",
        "action" => "stGameEndScoring",
        "updateGameProgression" => true,
        "transitions" => array( "" => 99 )
    ),
	
    
/*
    Examples:
    
    2 => array(
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,   
        "transitions" => array( "endGame" => 99, "nextPlayer" => 10 )
    ),
    
    10 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "type" => "activeplayer",
        "possibleactions" => array( "playCard", "pass" ),
        "transitions" => array( "playCard" => 2, "pass" => 2 )
    ), 

*/    
   
    // Final state.
    // Please do not modify.
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



