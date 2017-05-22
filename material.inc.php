<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * veggiegarden implementation : © <Your name here> <Your email address here>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * veggiegarden game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/

 $this->resources = array(
    "carrots"      => clienttranslate("carrots"    ),
	"cabbage"      => clienttranslate("cabbage"    ),
    "peas"         => clienttranslate("peas"       ),
    "peppers"      => clienttranslate("peppers"    ),
    "potato"      => clienttranslate("potato"    ),
    "tomato"      => clienttranslate("tomato"    ),
    "bunny"        => clienttranslate("bunny"      ),
	"groundhog"    => clienttranslate("groundhog"  ),
	"score_window_title" => clienttranslate('FINAL SCORE'),
	"win_condition" => clienttranslate('The player with the most points wins')
);

$this->card_types = array(
	1  => array( 'name' => $this->resources["carrots"], 'type_id' =>  1),
	2  => array( 'name' => $this->resources["cabbage"], 'type_id' =>  2),
	3  => array( 'name' => $this->resources["peas"   ], 'type_id' =>  3),
	4  => array( 'name' => $this->resources["peppers"], 'type_id' =>  4),
	5  => array( 'name' => $this->resources["potato" ], 'type_id' =>  5),
	6  => array( 'name' => $this->resources["tomato" ], 'type_id' =>  6)
);
                                                      
