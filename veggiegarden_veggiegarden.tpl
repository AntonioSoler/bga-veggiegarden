<html>
  <head>
    <meta name="generator"
    content="HTML Tidy for HTML5 (experimental) for Windows https://github.com/w3c/tidy-html5/tree/c63cc39" />
    <title></title>
  </head>
  <body>{OVERALL_GAME_HEADER} 
  <!-- 
========
== BGA framework: (c) Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
== veggiegarden implementation : (c) Antonio Soler Morgalad.es@gmail.com
== 
== This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
== See http://en.boardgamearena.com/#!doc/Studio for more information.
=======

    veggiegarden_veggiegarden.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->
<div id="playareascaler">
	<div id="playArea">
		<div id="table_wrap">
			<div id="deck">
			<div class="card_back"></div>
			<div class="card_back"></div>
			<div class="card_back"></div>
			<div class="card_back"></div>
			<div class="card_back"></div>
			<div class="card_back"></div>
			<div class="card_back"></div>
			<div class="card_back"></div>
			<div class="card_back"></div>
			</div>
			<div id="table" class="whiteblock table"></div>
		</div>
		<div id="boardwrapper">
			<div id="boardPanel" class="boarddiv">
				<div id="field0" class="field"></div>
				<div id="field1" class="field"></div>
				<div id="field2" class="field"></div>
				<div id="field3" class="field"></div>
				<div id="field10" class="field"></div>
				<div id="field11" class="field"></div>
				<div id="field12" class="field"></div>
				<div id="field13" class="field"></div>
				<div id="field20" class="field"></div>
				<div id="field21" class="field"></div>
				<div id="field22" class="field"></div>
				<div id="field23" class="field"></div>
				<div id="field30" class="field"></div>
				<div id="field31" class="field"></div>
				<div id="field32" class="field"></div>
				<div id="field33" class="field"></div>
				<div id="fence0" class="fence"></div>
				<div id="fence10" class="fence"></div>
				<div id="fence20" class="fence"></div>
				<div id="fence30" class="fence"></div>
				<div id="fence3" class="fence"></div>
				<div id="fence13" class="fence"></div>
				<div id="fence23" class="fence"></div>
				<div id="fence33" class="fence"></div>
				<div id="fence1" class="fence"></div>
				<div id="fence2" class="fence"></div>
				<div id="fence31" class="fence"></div>
				<div id="fence32" class="fence"></div>
				
			</div>
		</div>
		<div id="hand_wrap">
			<div id="hand" class="whiteblock table"></div>
		</div>
	</div>
</div>
<script type="text/javascript">

// Javascript HTML templates

/*
// Example:
var jstpl_some_game_item=&amp;#39;&amp;lt;div class=&amp;quot;my_game_item&amp;quot;
id=&amp;quot;my_game_item_${id}&amp;quot;&amp;gt;&amp;lt;\/div&amp;gt;&amp;#39;;

*/

var jstpl_player_board = '<br>\<div class="cp_board"></b>\<div id="cards_p${id}" class="cardcounter_board"> <div class="cardcounter"></div> <span id="cardcount_p${id}">0</span>\</div>';


</script> {OVERALL_GAME_FOOTER}</body>
</html>
