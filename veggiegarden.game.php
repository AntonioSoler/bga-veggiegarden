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
				"card_target" => 13,
				"token_target" => 14,
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
        self::initStat( 'table', 'turns_number' , 0 );    // Init a table statistics
	/*	self::initStat( 'table', 'carrots_value', 0 );
		self::initStat( 'table', 'cabbage_value', 0 );
		self::initStat( 'table', 'peas_value'   , 0 );
		self::initStat( 'table', 'peppers_value', 0 );
		self::initStat( 'table', 'potato_value' , 0 );
		self::initStat( 'table', 'tomato_value' , 0 ); */
		
        self::initStat( 'player', 'turns_number'   , 0 );  // Init a player statistics (for all players)
	/*	self::initStat( 'player', 'carrots_picked' , 0 );
		self::initStat( 'player', 'cabbages_picked', 0 );
		self::initStat( 'player', 'peas_picked'    , 0 );
		self::initStat( 'player', 'peppers_picked' , 0 );
		self::initStat( 'player', 'potatos_picked' , 0 );
		self::initStat( 'player', 'tomatos_picked' , 0 ); */
		
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
			self::setGameStateInitialValue( 'groundhog_pos', -55 ); //NO Groundhog
			}

	    self::setGameStateInitialValue( 'iterations', 0 );	
		self::setGameStateInitialValue( 'max_iterations', sizeof( $players ) * 7 );
		self::setGameStateInitialValue( 'card_picked', 0 );	
		self::setGameStateInitialValue( 'card_target', 0 );	
		self::setGameStateInitialValue( 'token_target', 0 );	
		
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
		
		$sql = "SELECT card_location_arg, COUNT(*) amount FROM cards WHERE card_location='hand' GROUP BY card_location_arg ";
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
	self::notifyAllPlayers( "selectitem", clienttranslate( '${player_name} picks a ${thiscard_name} card' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'item' => 'card_'.$card_id ,  
						'thiscard_name' =>  $this->card_types[$thiscardtype]['name']
						) );	
			
	$this->gamestate->nextState( 'selectTarget' );
    }
	
	function selectTarget( $target)
    {
		self::checkAction( 'selectTarget' );
		$player_id = self::getActivePlayerId();
		$targettype=substr( $target, 0, 5 ) ; //  card_ field fence
		
		switch ($targettype){
			case "card_":
				$card_id = substr( $target, 5, 2 ) ; //  card_id
				self::setGameStateValue( 'card_target', $card_id );
				self::setGameStateValue( 'token_target', 0 );
				self::notifyAllPlayers( "selectitem", clienttranslate( '${player_name} picks a card to apply the effect' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'item' => 'card_'.$card_id   
						) );	
				$cardpicked=self::getGameStateValue( 'card_picked');		
				$card=$this->cards->getCard( $cardpicked );
				$target=$this->cards->getCard( $card_id );
				if ($card['type'] == 3)   // move the groundhog
				{
					self::setGameStateValue( 'groundhog_pos', $target['location_arg'] );
					
					$moveitems=  array( 'groundhog' => 'field'.$target['location_arg'] );
					
					self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} moves the groundhog' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'moveitems' => $moveitems
						) );	
					
				}
				break;
			case "token":
				$card_id = substr( $target, 6, 2 ) ; //  token_id
				self::setGameStateValue( 'card_target', 0 );
				self::setGameStateValue( 'token_target', $card_id );
				self::notifyAllPlayers( "selectitem", clienttranslate( '${player_name} picks a fence token to apply the effect' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'item' => 'token_'.$card_id 
						) );	
			break;
		}
		
			
	$this->gamestate->nextState( 'selectDestination' );
    }
	
	function selectDestination( $destination)
    {
		self::checkAction( 'selectDestination' );
		$player_id = self::getActivePlayerId();
		$targettype=substr( $destination, 0, 5 ) ; //  card_ field fence		
		self::notifyAllPlayers( "selectitem", clienttranslate( '${player_name} selects a destination for the card effect' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'item' => $destination 						
						) );	
		
        $cardpicked=self::getGameStateValue( 'card_picked');
		$cardtarget=self::getGameStateValue( 'card_target');
		$tokentarget=self::getGameStateValue( 'token_target');
		$groundhog_pos=self::getGameStateValue( 'groundhog_pos');
		$Ygroundhog_pos= $groundhog_pos % 10 ;
		$Xgroundhog_pos=  ($groundhog_pos - $groundhog_pos % 10) / 10; 
		$card=$this->cards->getCard( $cardpicked );		
        
		switch ($card['type']){
		case "1":   // CARROTS Move BUNNY
			$sql = "select card_location_arg from tokens where card_type=0";
			$bunnypos=self::getUniqueValueFromDB( $sql );
			$sql = "select card_id from tokens where card_type=0";
			$bunny_id=self::getUniqueValueFromDB( $sql );
			$token_id = substr( $destination, 6, 2 ) ; //  token_id
			$sql = "select card_location_arg from tokens where card_id=".$token_id;
			$tokenpos=self::getUniqueValueFromDB( $sql );
			self::DbQuery( "UPDATE tokens SET card_location_arg=".$tokenpos." WHERE card_type=0" );
			self::DbQuery( "UPDATE tokens SET card_location_arg=".$bunnypos." WHERE card_id=".$token_id );
			
					
			$moveitems=  array( 'token_'.$token_id => 'fence'.$bunnypos ,
						        'token_'.$bunny_id => 'fence'.$tokenpos 
							);
					
			self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} moves the bunny to other fence post' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'moveitems' => $moveitems
						) );
		
		break;
		case "2":    //CABBAGE  Shift any column or row of cards or fence  (groundhog blocks row and column)
			$targettype=substr( $destination, 0, 5 ) ; //  card_ or token?
			
			switch ($targettype){
				case "card_":
					$card_id = substr( $destination, 5, 2 ) ; //  card_id
					$sql = "select card_location_arg from cards where card_id=".$cardtarget;
					$targetpos=self::getUniqueValueFromDB( $sql );
					$sql = "select card_location_arg from cards where card_id=".$card_id;
			        $cardpos=self::getUniqueValueFromDB( $sql );
					
					$delta=$cardpos-$targetpos;
					
					$Y= $targetpos % 10 ;
		            $X=  ($targetpos - $targetpos % 10) / 10; 
					
					$shiftcards= array ();
					
					switch($delta)
					{
						case "1":
						    $shiftcards= array ($X*10+0,$X*10+1,$X*10+2,$X*10+3  );
							break;
						case "-1":
					        $shiftcards= array ($X*10+3,$X*10+2,$X*10+1,$X*10+0  );
							break;
						case "10":
					        $shiftcards= array (0+$Y,10+$Y,20+$Y,30+$Y  );
							break;
						case "-10":
					        $shiftcards= array (30+$Y,20+$Y,10+$Y,0+$Y  );
							break;	
					}
					$shiftcards_ids= array ();
					for ($c=0;$c<=3;$c++)
					{
						$sql = "select card_id from cards where (card_location='field') and (card_location_arg=".$shiftcards[$c].")";
						$shiftcards_ids[$c]=self::getUniqueValueFromDB( $sql );
					}
					
					$moveitems=  array();
					
					for ($c=1;$c<=4;$c++)
					{
						self::DbQuery( "UPDATE cards SET card_location_arg=".$shiftcards[$c%4]." WHERE card_id=".$shiftcards_ids[$c-1] );
						$moveitems[ 'card_'.$shiftcards_ids[$c-1]] = 'field'.$shiftcards[$c%4] ;
					}
					
					
					self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} shifts a row or column of cards ' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'moveitems' => $moveitems
								) );
					break;
				case "token":
					$token_id = substr( $destination, 6, 2 ) ; //  token_id
					$sql = "select card_location_arg from tokens where card_id=".$tokentarget;
					$targetpos=self::getUniqueValueFromDB( $sql );
					$sql = "select card_location_arg from tokens where card_id=".$token_id;
			        $tokenpos=self::getUniqueValueFromDB( $sql );
					
					$delta=$tokenpos-$targetpos;
					
					$Y= $targetpos % 10 ;
		            $X=  ($targetpos - $targetpos % 10) / 10; 
					
					$shiftcards= array ();
					
					switch($delta)
					{
						case "1":
						    $shiftcards= array ($X*10+1,$X*10+2  );
							break;
						case "-1":
					        $shiftcards= array ($X*10+2,$X*10+1  );
							break;
						case "10":
					        $shiftcards= array (0+$Y,10+$Y,20+$Y,30+$Y  );
							break;
						case "-10":
					        $shiftcards= array (30+$Y,20+$Y,10+$Y,0+$Y  );
							break;	
					}
					$shiftcards_ids= array ();
					for ($c=0;$c<sizeof($shiftcards);$c++)
					{
						$sql = "select card_id from tokens where (card_location='fence') and (card_location_arg=".$shiftcards[$c].")";
						$shiftcards_ids[$c]=self::getUniqueValueFromDB( $sql );
					}
					
					$moveitems=  array();
					
					for ($c=1;$c<=sizeof($shiftcards);$c++)
					{
						self::DbQuery( "UPDATE tokens SET card_location_arg=".$shiftcards[($c%sizeof($shiftcards))]." WHERE card_id=".$shiftcards_ids[$c-1] );
						$moveitems[ 'token_'.$shiftcards_ids[$c-1]] = 'fence'.$shiftcards[($c%sizeof($shiftcards))] ;
					}
					
					
					self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} shifts a row or column of tokens ' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'moveitems' => $moveitems,
							    
								) );
				break;
			}
		break;
		case "3":    //PEAS    move the groundhog to other compost, select one card and it shifts position over the groundhog
			        $card_id = substr( $destination, 5, 2 ) ; //  card_id
					$sql = "select card_location_arg from cards where card_id=".$cardtarget;
					$targetpos=self::getUniqueValueFromDB( $sql );
					$sql = "select card_location_arg from cards where card_id=".$card_id;
			        $cardpos=self::getUniqueValueFromDB( $sql );
					
					$opositepos=2*$targetpos - $cardpos;
					$sql = "select card_id from cards where (card_location='field') and (card_location_arg=".$opositepos.")";
			        $opositeid=self::getUniqueValueFromDB( $sql );
					
					
					self::DbQuery( "UPDATE cards SET card_location_arg=".$opositepos." WHERE card_id=".$card_id );
					self::DbQuery( "UPDATE cards SET card_location_arg=".$cardpos." WHERE card_id=".$opositeid );
							
					$moveitems=  array( 'card_'.$card_id => 'field'.$opositepos ,
										'card_'.$opositeid => 'field'.$cardpos 
									);
							
					self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} shifts two cards over the groundhog' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'moveitems' => $moveitems
								) );
		
		break;
		case "4":     //PEPPERS  Swaps two cards or tokens (the groundhog blocks one card)
			$targettype=substr( $destination, 0, 5 ) ; //  card_ or token?
			
			switch ($targettype){
				case "card_":
					$card_id = substr( $destination, 5, 2 ) ; //  card_id
					$sql = "select card_location_arg from cards where card_id=".$cardtarget;
					$targetpos=self::getUniqueValueFromDB( $sql );
					$sql = "select card_location_arg from cards where card_id=".$card_id;
			        $cardpos=self::getUniqueValueFromDB( $sql );
					
					self::DbQuery( "UPDATE cards SET card_location_arg=".$targetpos." WHERE card_id=".$card_id );
					self::DbQuery( "UPDATE cards SET card_location_arg=".$cardpos." WHERE card_id=".$cardtarget );
							
					$moveitems=  array( 'card_'.$card_id => 'field'.$targetpos ,
										'card_'.$cardtarget => 'field'.$cardpos 
									);
							
					self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} swaps two cards' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'moveitems' => $moveitems
								) );
					break;
				case "token":
					$token_id = substr( $destination, 6, 2 ) ; //  token_id
					$sql = "select card_location_arg from tokens where card_id=".$tokentarget;
					$targetpos=self::getUniqueValueFromDB( $sql );
					$sql = "select card_location_arg from tokens where card_id=".$token_id;
			        $tokenpos=self::getUniqueValueFromDB( $sql );
					
					self::DbQuery( "UPDATE tokens SET card_location_arg=".$targetpos." WHERE card_id=".$token_id );
					self::DbQuery( "UPDATE tokens SET card_location_arg=".$tokenpos." WHERE card_id=".$tokentarget );
					
					$moveitems=  array( 'token_'.$token_id => 'fence'.$targetpos ,
										'token_'.$tokentarget => 'fence'.$tokenpos 
									);
							
					self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} swaps two fence tokens' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'moveitems' => $moveitems
								) );
					
				break;
			}
		
		break;
		case "5":      // POTATO  Exchange one card from your hand with one on the table (the groundhog blocks one card)
			$card_id = substr( $destination, 5, 2 ) ; //  card_id
			$sql = "select card_location_arg from cards where card_id=".$cardtarget;
			$targetpos=self::getUniqueValueFromDB( $sql );
			$sql = "select card_location_arg from cards where card_id=".$card_id;
			$cardpos=self::getUniqueValueFromDB( $sql );
			
			self::DbQuery( "UPDATE cards SET card_location_arg=".$player_id.", card_location='hand' WHERE card_id=".$card_id );
			self::DbQuery( "UPDATE cards SET card_location_arg=".$cardpos." , card_location='field' WHERE card_id=".$cardtarget );
								
			self::notifyAllPlayers( "cardtohand", clienttranslate( '${player_name} picks a card from the garden' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'card_id' => $card_id
						) );
			
			$sql = "select card_type from cards where card_id=".$cardtarget;
			$target_type=self::getUniqueValueFromDB( $sql );
			
			self::notifyAllPlayers( "cardfromhand", clienttranslate( '${player_name} replaces the card with one from the hand' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'card_id' => $cardtarget,
						'card_pos' =>  $cardpos,
						'card_type' => $target_type
						) );			
			break;
		case "6":     //TOMATO Discard a veggie from the garden and replace it with one from the harvest (the groundhog blocks)
		    $card_id = substr( $destination, 5, 2 ) ; //  card_id
			$sql = "select card_location_arg from cards where card_id=".$cardtarget;
			$targetpos=self::getUniqueValueFromDB( $sql );
			$sql = "select card_location_arg from cards where card_id=".$card_id;
			$cardpos=self::getUniqueValueFromDB( $sql );
			
			self::DbQuery( "UPDATE cards SET card_location_arg=".$targetpos.", card_location='field' WHERE card_id=".$card_id );
			self::DbQuery( "UPDATE cards SET card_location_arg=".$player_id." , card_location='removed' WHERE card_id=".$cardtarget );
								
			self::notifyAllPlayers( "discard", clienttranslate( '${player_name} discards a card from the garden' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'card_id' => $cardtarget
						) );
									
			$moveitems=  array( 'card_'.$card_id => 'field'.$targetpos 	);
							
			self::notifyAllPlayers( "moveitems", clienttranslate( '${player_name} replaces the card wiht one from harvest' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'moveitems' => $moveitems
						) );			
			break;
			
		
		}

	self::DbQuery( "UPDATE cards SET card_location_arg=".$player_id.", card_location='hand' WHERE card_id=".$cardpicked );
			
								
	self::notifyAllPlayers( "cardtohand", clienttranslate( '${player_name} picks the selected card' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'card_id' => $cardpicked
						) );
		
	$this->gamestate->nextState( 'endTurn' );
	
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
    
	function argPossibleDestinations()
    {   
		$player_id = self::getActivePlayerId();
        $cardpicked=self::getGameStateValue( 'card_picked');
		$cardtarget=self::getGameStateValue( 'card_target');
		$tokentarget=self::getGameStateValue( 'token_target');
		$groundhog_pos=self::getGameStateValue( 'groundhog_pos');
		$Ygroundhog_pos= $groundhog_pos % 10 ;
		$Xgroundhog_pos=  ($groundhog_pos - $groundhog_pos % 10) / 10; 
		$card=$this->cards->getCard( $cardpicked );
		
		$result=  array( 'possibledestinations' => array() );
		
		
        switch ($card['type'])
		{
		case "1":   // CARROTS Move BUNNY
		    $sql = "select card_location_arg pos from tokens where card_type<>0";
			$notbunnypos=self::getCollectionFromDb( $sql );
			foreach( $notbunnypos as $thispos )
			{
				array_push($result["possibledestinations"],"fence".$thispos['pos']);
			}
		break;
		case "2":    //CABBAGE  Shift any column or row of cards or fence  (groundhog blocks row and column)
			if ($cardtarget >= 1 )
			{
				$target=$this->cards->getCard( $cardtarget );
				$Xtarget = ( $target['location_arg'] - $target['location_arg'] % 10) / 10; ;
				$Ytarget = $target['location_arg'] % 10 ;	
				
			  	for ($x=-1 ; $x<=1 ; $x+=2) 
				{	
					if ( (($Xtarget +$x) >= 0 ) AND ( ( $Xtarget + $x ) < 4 )   ) 
					{
						array_push($result["possibledestinations"],"field".(($Xtarget+$x)*10+$Ytarget));
					}	
				}		
				for ($y=-1 ; $y<=1 ; $y+=2) 
				{	
					if ( (($Ytarget +$y) >= 0 ) AND ( ( $Ytarget + $y ) < 4 )   ) 
					{
						array_push($result["possibledestinations"],"field".(($Xtarget)*10+$Ytarget+$y));
					}	
				}		
			}
			if ($tokentarget >= 1 )
				
			{
				$target=$this->tokens->getCard( $tokentarget );
				$Xtarget = ( $target['location_arg'] - $target['location_arg'] % 10) / 10; ;
				$Ytarget = $target['location_arg'] % 10 ;	
				
				if ( $Ytarget == 0 OR $Ytarget == 3)
				{
					for ($x=-1 ; $x<=1 ; $x+=2) 
					{	
						if ( (($Xtarget +$x) >= 0 ) AND ( ( $Xtarget + $x ) < 4 )   ) 
						{
							array_push($result["possibledestinations"],"fence".(($Xtarget+$x)*10+$Ytarget));
						}	
					}		
				}
				if ( $Ytarget == 1 OR $Ytarget == 2)
				{
					for ($y=-1 ; $y<=1 ; $y+=2) 
					{	
						if ( (($Ytarget +$y) >= 1 ) AND ( ( $Ytarget + $y ) < 3 )   ) 
						{
							array_push($result["possibledestinations"],"fence".(($Xtarget)*10+$Ytarget+$y));
						}	
					}		
				}
			}
			break;
		case "3":    //PEAS    move the groundhog to other compost, select one card and it shifts position over the groundhog
			$target=$this->cards->getCard( $cardtarget );
				$Xtarget = ( $target['location_arg'] - $target['location_arg'] % 10) / 10; ;
				$Ytarget = $target['location_arg'] % 10 ;	
				
				for ($y=-1 ; $y<=1 ; $y+=1) 
				{	
					for ($x=-1 ; $x<=1 ; $x+=1) 
					{	
						if ( (($Ytarget +$y) >= 0 ) AND ( ( $Ytarget + $y ) < 4 ) AND (($Xtarget +$x) >= 0 ) AND ( ( $Xtarget + $x ) < 4 )  AND ( $groundhog_pos != (($Xtarget+$x)*10+$Ytarget+$y))) 
						{
							array_push($result["possibledestinations"],"field".(($Xtarget+$x)*10+$Ytarget+$y));
						}	
					}
				}
		case "4":     //PEPPERS  Swaps two cards (the groundhog blocks one card)
			if ($cardtarget >= 1 )
			{
				$target=$this->cards->getCard( $cardtarget );
				$Xtarget = ( $target['location_arg'] - $target['location_arg'] % 10) / 10; ;
				$Ytarget = $target['location_arg'] % 10 ;	
				
			  	for ($y=-1 ; $y<=1 ; $y+=1) 
				{	
					for ($x=-1 ; $x<=1 ; $x+=1) 
					{	
						if ( (($Ytarget +$y) >= 0 ) AND ( ( $Ytarget + $y ) < 4 ) AND (($Xtarget +$x) >= 0 ) AND ( ( $Xtarget + $x ) < 4 )  AND ( $groundhog_pos != (($Xtarget+$x)*10+$Ytarget+$y))) 
						{
							array_push($result["possibledestinations"],"field".(($Xtarget+$x)*10+$Ytarget+$y));
						}	
					}
				}
			}
			if ($tokentarget >= 1 )	
			{
				$target=$this->tokens->getCard( $tokentarget );
				$Xtarget = ( $target['location_arg'] - $target['location_arg'] % 10) / 10; ;
				$Ytarget = $target['location_arg'] % 10 ;	
				
				if ( $Ytarget == 0 OR $Ytarget == 3)
				{
					for ($x=-1 ; $x<=1 ; $x+=2) 
					{	
						if ( (($Xtarget +$x) >= 0 ) AND ( ( $Xtarget + $x ) < 4 )   ) 
						{
							array_push($result["possibledestinations"],"fence".(($Xtarget+$x)*10+$Ytarget));
						}	
					}		
				}
				if ( $Ytarget == 1 OR $Ytarget == 2)
				{
					for ($y=-1 ; $y<=1 ; $y+=2) 
					{	
						if ( (($Ytarget +$y) >= 1 ) AND ( ( $Ytarget + $y ) < 3 )   ) 
						{
							array_push($result["possibledestinations"],"fence".(($Xtarget)*10+$Ytarget+$y));
						}	
					}		
				}
			}
			break;
		break;
		case "5":      // POTATO  Exchange one card from your hand with one on the table (the groundhog blocks one card)
			for ($x=0 ; $x<= 3 ;$x++)
				{
					for ($y=0 ; $y<= 3; $y++)
					{
						if ( $groundhog_pos != ($x*10 + $y ))
						{ 
							array_push($result["possibledestinations"],"field".($x*10+$y));
						}
					}	
				}
		break;
		case "6":     //TOMATO Discard a veggie from the garden and replace it with one from the harvest (the groundhog blocks)
					$sql = "select card_id from cards where card_location='table' and card_id<>".$card['id'];
					$cardsintable=self::getCollectionFromDb( $sql );
					foreach( $cardsintable as $thiscard )
					{
						array_push($result["possibledestinations"],"card_".$thiscard['card_id']);
					}
		break;
		}
	     
        // return values:
        return $result ;
    }


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
        self::setGameStateValue( 'card_picked', 0 );	
		self::setGameStateValue( 'card_target', 0 );	
		self::setGameStateValue( 'token_target', 0 );
		
		self::incStat( 1, $name, 'turns_number', self::getActivePlayerId() );
		
		for ($x=1 ; $this->cards->countCardInLocation( 'table' ) < 4 ; $x++)
		{
			$PlayedCard = $this->cards->pickCardForLocation( 'deck', 'table' );
			self::notifyAllPlayers( "drawcard", clienttranslate( '${player_name} draws a new card for the harvest' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'card_id' => $PlayedCard['id'],
						'card_type' => $PlayedCard['type']
						) );
			
			
		}
        
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
	
	function stendTurn()
    {
            $cardpicked=self::getGameStateValue( 'card_picked');
			
			
			
		$this->activeNextPlayer();
        $this->gamestate->nextState( );
    }

	function displayScores()
    {
        $players = self::loadPlayersBasicInfos();
		
		$carrots_value=self::getUniqueValueFromDB('SELECT sum(tokens.card_type) FROM tokens join cards on cards.card_location_arg=tokens.card_location_arg WHERE cards.card_type=1 and cards.card_location="field"');
		$cabbage_value=self::getUniqueValueFromDB('SELECT sum(tokens.card_type) FROM tokens join cards on cards.card_location_arg=tokens.card_location_arg WHERE cards.card_type=2 and cards.card_location="field"');
		$peas_value   =self::getUniqueValueFromDB('SELECT sum(tokens.card_type) FROM tokens join cards on cards.card_location_arg=tokens.card_location_arg WHERE cards.card_type=3 and cards.card_location="field"');
		$peppers_value=self::getUniqueValueFromDB('SELECT sum(tokens.card_type) FROM tokens join cards on cards.card_location_arg=tokens.card_location_arg WHERE cards.card_type=4 and cards.card_location="field"');
		$potato_value =self::getUniqueValueFromDB('SELECT sum(tokens.card_type) FROM tokens join cards on cards.card_location_arg=tokens.card_location_arg WHERE cards.card_type=5 and cards.card_location="field"');
		$tomato_value =self::getUniqueValueFromDB('SELECT sum(tokens.card_type) FROM tokens join cards on cards.card_location_arg=tokens.card_location_arg WHERE cards.card_type=6 and cards.card_location="field"');
		
		self::setStat( $carrots_value, 'carrots_value');
		self::setStat( $cabbage_value, 'cabbage_value');
		self::setStat( $peas_value   , 'peas_value'   );
		self::setStat( $peppers_value, 'peppers_value');
		self::setStat( $potato_value , 'potato_value' );
		self::setStat( $tomato_value , 'tomato_value' );
		      
        $table[] = array();
        
        //left hand col
        $table[0][] = array( 'str' => ' ', 'args' => array(), 'type' => 'header');
        $table[1][] = "<div class='carrots icon' ></div>=".$carrots_value;
        $table[2][] = "<div class='cabbage icon' ></div>=".$cabbage_value;
        $table[3][] = "<div class='peas icon' ></div>="   .$peas_value   ;
		$table[4][] = "<div class='peppers icon' ></div>=".$peppers_value;
		$table[5][] = "<div class='potato icon' ></div>=" .$potato_value ;
		$table[6][] = "<div class='tomato icon' ></div>=" .$tomato_value ;
        $table[7][] = clienttranslate($this->resources["score_window_title"]);
		
        foreach( $players as $player_id => $player )
        {
            $table[0][] = array( 'str' => '${player_name}',
                                 'args' => array( 'player_name' => $player['player_name'] ),
                                 'type' => 'header'
                               );
            
			$carrots_picked =self::getUniqueValueFromDB('SELECT count(*) FROM cards WHERE (card_type=1) and ( card_location="hand") and (card_location_arg = '.$player['player_id'].') ');
		    $cabbages_picked=self::getUniqueValueFromDB('SELECT count(*) FROM cards WHERE (card_type=2) and ( card_location="hand") and (card_location_arg = '.$player['player_id'].') ');
		    $peas_picked    =self::getUniqueValueFromDB('SELECT count(*) FROM cards WHERE (card_type=3) and ( card_location="hand") and (card_location_arg = '.$player['player_id'].') ');
		    $peppers_picked =self::getUniqueValueFromDB('SELECT count(*) FROM cards WHERE (card_type=4) and ( card_location="hand") and (card_location_arg = '.$player['player_id'].') ');
		    $potatos_picked =self::getUniqueValueFromDB('SELECT count(*) FROM cards WHERE (card_type=5) and ( card_location="hand") and (card_location_arg = '.$player['player_id'].') ');
		    $tomatos_picked =self::getUniqueValueFromDB('SELECT count(*) FROM cards WHERE (card_type=6) and ( card_location="hand") and (card_location_arg = '.$player['player_id'].') ');
		
			self::setStat( $carrots_picked , 'carrots_picked', $player['player_id'] );
			self::setStat( $cabbages_picked, 'cabbages_picked',$player['player_id'] );
			self::setStat( $peas_picked    , 'peas_picked',    $player['player_id'] );
			self::setStat( $peppers_picked , 'peppers_picked', $player['player_id'] );
			self::setStat( $potatos_picked , 'potatos_picked', $player['player_id'] );
			self::setStat( $tomatos_picked , 'tomatos_picked', $player['player_id'] );
			
			$table[1][] =$carrots_picked ;
            $table[2][] =$cabbages_picked;
			$table[3][] =$peas_picked    ;
			$table[4][] =$peppers_picked ;
            $table[5][] =$potatos_picked ;
			$table[6][] =$tomatos_picked ;
			
			$score = $carrots_picked * $carrots_value + $cabbages_picked * $cabbage_value + $peas_picked * $peas_value + $peppers_picked * $peppers_value + $potatos_picked * $potato_value + $tomatos_picked * $tomato_value ;
			
			$table[7][] = $score ;
			
			$sql = "UPDATE player SET player_score = ".$score." WHERE player_id=".$player['player_id'];
            self::DbQuery( $sql );    
        }
		
        $this->notifyAllPlayers( "notif_finalScore", '', array(
            "id" => 'finalScoring',
            "title" => $this->resources["score_window_title"],
            "table" => $table,            
			"closing" => clienttranslate( "OK" )
           
        ) ); 
    }

////////////////////////////////////////////////////////////////////////////

    function stGameEndScoring()
    {
        //stats for each player, we want to reveal how many gems they have in tent
        //In the case of a tie, check amounts of artifacts. Set auxillery score for this

        //stats first

        $this->displayScores();
		  
        $this->gamestate->nextState();
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
