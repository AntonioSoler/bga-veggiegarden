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
  * veggiegarden.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class veggiegarden extends Table
{
	function veggiegarden( )
	{
        	
 
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();self::initGameStateLabels( array( 
                "iterations" => 10,
				"max_iterations" =>11,
                "card_picked" => 12,
				"card_clicked" => 13,
				"token_clicked" => 14,
				"groundhog_pos" => 15
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );
		
		$this->cards = self::getNew( "module.common.deck" );
		$this->cards->init( "cards" );
		
		$this->tokens = self::getNew( "module.common.deck" );
		$this->tokens->init( "tokens" );
        
	}
	
    protected function getGameName( )
    {
		// Used for translations and stuff. Please do not modify.
        return "veggiegarden";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $default_colors = array( "ff0000", "008000", "0000ff", "ffa500", "773300" );
 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reattributeColorsBasedOnPreferences( $players, array(  "ff0000", "008000", "0000ff", "ffa500", "773300" ) );
        self::reloadPlayersBasicInfos();
        
        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // TODO: setup the initial game situation here
		
			
		$removed=mt_rand (1,6);  //remove 1 card type from the game
		$cards = array();
		for ( $i=1 ; $i <=6 ; $i++ )
        {
			$card = array( 'type' => $i , 'type_arg' => 0  , 'nbr' => 14 );
			if ( $i != $removed ) 
				{
					array_push($cards, $card);   
				}
        }
		$this->cards->createCards( $cards, 'deck' );
        
		$cards = array();
		
			
		for ( $i=1 ; $i <=3 ; $i++ )
        {
			$card = array( 'type' => $i , 'type_arg' => 0  , 'nbr' => 4 );
			array_push($cards, $card); 
		}
		
		$this->tokens->createCards( $cards, 'deck' );
		
		if  ( $removed != 1 )   // SHOULD WE PLACE THE BUNNY ?
		{
			$sql = "UPDATE tokens set card_type=0 where card_id=" . mt_rand (1,12) ;
			self::DbQuery( $sql );
		}
		
		
		//shuffle 
        $this->cards->shuffle( 'deck' );
        $this->tokens->shuffle( 'deck' );
		
		for ($x=0 ; $x<=3 ; $x++)
		{
			for ($y=0 ; $y<=3 ; $y++)
			{
				$PlayedCard = $this->cards->pickCardForLocation( 'deck', 'field', $x * 10 + $y );
			}
		}
		for ($x=0 ; $x<=3 ; $x++) 
		{
				$this->tokens->pickCardsForLocation( 1, 'deck' , 'fence', $x*10 , true );
				$this->tokens->pickCardsForLocation( 1, 'deck' , 'fence', $x*10+3 , true );
		}
		
	    for ($x=1 ; $x<=2 ; $x++) 
		{
				$this->tokens->pickCardsForLocation( 1, 'deck' , 'fence', $x , true );
				$this->tokens->pickCardsForLocation( 1, 'deck' , 'fence', 30+$x , true );
		}
	   
	    for ($x=1 ; $x<=4 ; $x++)
		{
			$PlayedCard = $this->cards->pickCardForLocation( 'deck', 'table', $x );
		}
		
		if  ( $removed != 3 )   // SHOULD WE PLACE THE groundhog ?
			{
			$random = mt_rand (0,3);
			$groundhog_pos = array ( 11 ,12 , 21 ,22 ) ;
			self::setGameStateInitialValue( 'groundhog_pos', $groundhog_pos[$random] );
			}
		else
			{
			self::setGameStateInitialValue( 'groundhog_pos', 0 );
			}

	    self::setGameStateInitialValue( 'iterations', 0 );	
		self::setGameStateInitialValue( 'max_iterations', sizeof( $players ) * 7 );
		self::setGameStateInitialValue( 'card_picked', 0 );	
		self::setGameStateInitialValue( 'card_clicked', 0 );	
		self::setGameStateInitialValue( 'token_clicked', 0 );	
		
		foreach( $players as $player_id => $player )
        {
            $this->cards->pickCardsForLocation( 2, 'deck' , 'hand', $player_id ); 
        }
		
		// Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array( 'players' => array() );
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
		
		$result['table'] = $this->cards->getCardsInLocation( 'table' );
		$result['field'] = $this->cards->getCardsInLocation( 'field' );
		$result['fence'] = $this->tokens->getCardsInLocation( 'fence' );
		$result['groundhog'] = self::getGameStateValue('groundhog_pos');
		
		$result['hand'] = $this->cards->getCardsInLocation( 'hand', $current_player_id );
		
		$sql = "SELECT card_location_arg, COUNT(*) amount FROM cards WHERE 1 GROUP BY card_location_arg ";
        $result['cardcount'] = self::getCollectionFromDb( $sql );
		
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).
  
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    
	function getGameProgression()
    {
        // TODO: compute and return the game progression
		
		$result = ( self::getGameStateValue('iterations') * 100 ) / ( self::getGameStateValue('max_iterations') + 4 );

        return $result ;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */



//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in veggiegarden.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} played ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */
	
	function pickcard( $card_id)
    {
	self::checkAction( 'pickcard' );
	$player_id = self::getActivePlayerId();
	$thiscard= $this->cards->getCard( $card_id );
	self::setGameStateValue( 'card_picked', $card_id );
	
	//$this->cards->moveCard( $card_id,'hand',$player_id );
	$thiscardtype=$thiscard['type'];
	self::notifyAllPlayers( "selectcard", clienttranslate( '${player_name} picks a ${thiscard_name} card' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'card_id' => $card_id ,  
						'thiscard_name' =>  $this->card_types[$thiscardtype]['name']
						) );	
			
	$this->gamestate->nextState( 'selectTarget' );
    }
	
	
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    */
    
	function argPossibleTargets()
    {   
		$player_id = self::getActivePlayerId();
        $cardpicked=self::getGameStateValue( 'card_picked');
		$groundhog_pos=self::getGameStateValue( 'groundhog_pos');
		$Ygroundhog_pos= $groundhog_pos % 10 ;
		$Xgroundhog_pos=  ($groundhog_pos - $groundhog_pos % 10) / 10; 
		$card=$this->cards->getCard( $cardpicked );
		$result=  array( 'possiblemoves' => array() );
        switch ($card['type'])
		{
		case "1":   // CARROTS Move BUNNY
		    $sql = "select card_location_arg from tokens where card_type=0";
			$bunnypos=self::getUniqueValueFromDB( $sql );
			array_push($result["possiblemoves"],"fence".$bunnypos);
			
		break;
		case "2":    //CABBAGE  Shift any column or row of cards or fence  (groundhog blocks row and column)
			for ($x=0 ; $x<= 3 ; $x++)
			{
				for ($y=0 ; $y<= 3 ;$y++)
				{
					if (( $Xgroundhog_pos != $x ) and ( $Ygroundhog_pos != $y ))
					{ 
						array_push($result["possiblemoves"],"field".($x*10+$y));
					}
				}	
			}
			for ($x=0 ; $x<=3 ; $x++) 
			{
				array_push($result["possiblemoves"],"fence".($x*10 ) );
				array_push($result["possiblemoves"],"fence".($x*10+3) );
			}
			for ($y=1 ; $y<=2 ; $y++)  
			{
				array_push($result["possiblemoves"],"fence".($y)  );
				array_push($result["possiblemoves"],"fence".(30+$y) );
			}
			break;
		case "3":    //PEAS    move the groundhog to other compost, select one card and it shifts position over the groundhog
			for ($x=1 ; $x<= 2; $x++)
				{
					for ($y=1 ; $y<= 2 ;$y++)
					{
						if ( $groundhog_pos != ($x*10 + $y ))
						{ 
							array_push($result["possiblemoves"],"field".($x*10+$y));
						}
					}	
				}
			break;
		case "4":     //PEPPERS  Swaps two cards (the groundhog blocks one card)
			for ($x=0 ; $x<= 3 ;$x++)
				{
					for ($y=0 ; $y<= 3; $y++)
					{
						if ( $groundhog_pos != ($x*10 + $y ))
						{ 
							array_push($result["possiblemoves"],"field".($x*10+$y));
						}
					}	
				}
			for ($x=0 ; $x<=3 ; $x++) 
				{
					array_push($result["possiblemoves"],"fence".($x*10 ) );
					array_push($result["possiblemoves"],"fence".($x*10+3) );
				}
				for ($y=1 ; $y<=2 ; $y++) 
				{
					array_push($result["possiblemoves"],"fence".($y)  );
					array_push($result["possiblemoves"],"fence".(30+$y) );
				}
			
			break;
		break;
		case "5":      // POTATO  Exchange one card from your hand with one on the table (the groundhog blocks one card)
			$hand = $this->cards->getCardsInLocation( 'hand', $player_id );
		    foreach( $hand as $thiscard => $hand )
			{
				array_push($result["possiblemoves"],"card_".$thiscard);
			}
		break;
		case "6":     //TOMATO   Discard a card from the table and replace it with one from the table.(the groundhog blocks one card)
					for ($x=0 ; $x<= 3 ;$x++)
					{
						for ($y=0 ; $y<= 3 ;$y++)
						{
							if ( $groundhog_pos !=( $x*10 + $y ))
							{ 
								array_push($result["possiblemoves"],"field".($x*10+$y));
							}
						}	
					}
		break;
		}
	     
        // return values:
        return $result ;
    }    
    

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */
	
	
    function ststartTurn()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        
		$this->gamestate->nextState( 'playerpick' );
    }

	function stplayerpick()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        // $this->gamestate->nextState( 'some_gamestate_transition' );
    }
	
	function stTarget()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        // $this->gamestate->nextState( 'some_gamestate_transition' );
    }
	
	function stDestination()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        // $this->gamestate->nextState( 'some_gamestate_transition' );
    }

	function stGameEndScoring()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        // $this->gamestate->nextState( 'some_gamestate_transition' );
    }

    
    	
//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] == "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }
            return;
        }

        if ($state['type'] == "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $sql = "
                UPDATE  player
                SET     player_is_multiactive = 0
                WHERE   player_id = $active_player
            ";
            self::DbQuery( $sql );

            $this->gamestate->updateMultiactiveOrNextState( '' );
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
    
///////////////////////////////////////////////////////////////////////////////////:
////////// DB upgrade
//////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */
    
    function upgradeTableDb( $from_version )
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345
        
        // Example:
//        if( $from_version <= 1404301345 )
//        {
//            $sql = "ALTER TABLE xxxxxxx ....";
//            self::DbQuery( $sql );
//        }
//        if( $from_version <= 1405061421 )
//        {
//            $sql = "CREATE TABLE xxxxxxx ....";
//            self::DbQuery( $sql );
//        }
//        // Please add your future database scheme changes here
//
//


    }    
}
