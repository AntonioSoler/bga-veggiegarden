/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * veggiegarden implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * veggiegarden.js
 *
 * veggiegarden user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {
    return declare("bgagame.veggiegarden", ebg.core.gamegui, {
        constructor: function(){
            console.log('veggiegarden constructor');
              
            if (!dojo.hasClass("ebd-body", "mode_3d")) {
            dojo.addClass("ebd-body", "mode_3d");
            dojo.addClass("ebd-body", "enableTransitions");
				$("globalaction_3d").innerHTML = "2D";   // controls the upper right button 
				this.control3dxaxis = 30;  // rotation in degrees of x axis (it has a limit of 0 to 80 degrees in the frameword so users cannot turn it upsidedown)
				this.control3dzaxis = 0;   // rotation in degrees of z axis
				this.control3dxpos = -100;   // center of screen in pixels
				this.control3dypos = 200;   // center of screen in pixels
				this.control3dscale = 1;   // zoom level, 1 is default 2 is double normal size, 
				this.control3dmode3d = true ;  			// is the 3d enabled	
				 //transform: rotateX(30deg) translate(200px, -100px) rotateZ(0deg) scale3d(1, 1, 1); min-width: 0px;

				$("game_play_area").style.transform = "rotatex(" + this.control3dxaxis + "deg) translate(" + this.control3dypos + "px," + this.control3dxpos + "px) rotateZ(" + this.control3dzaxis + "deg) scale3d(" + this.control3dscale + "," + this.control3dscale + "," + this.control3dscale + ")";
			}
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            this.gameconnections=new Array();
			
			
            // Setting up player boards
            for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // Setting up players boards if needed
                var player_board_div = $('player_board_'+player_id);
                dojo.place( this.format_block('jstpl_player_board', player ), player_board_div );
				dojo.byId("cardcount_p"+player_id).innerHTML=gamedatas.cardcount[player_id].amount;
				
            }
            
			for( var i in this.gamedatas.table )
				{
					var card = this.gamedatas.table[i];
					this.placecard('table',card['id'],card['type']);
					this.addtooltipcard ( card['id'],card['type'] );
				}
			
			for( var i in this.gamedatas.field )
				{
					var card = this.gamedatas.field[i];
					this.placecard('field'+card['location_arg'] ,card['id'],card['type']);
				}
			
			for( var i in this.gamedatas.hand )
				{
					var card = this.gamedatas.hand[i];
					this.placecard('hand',card['id'],card['type']);
				}

			for( var i in this.gamedatas.fence )
				{
					var card = this.gamedatas.fence[i];
					this.placetoken('fence'+card['location_arg'],card['id'],card['type']);
				}
			
			dojo.place( "<div id='groundhog' class='groundhog' ></div>" , "field"+this.gamedatas.groundhog, "last");
 
            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
            
            switch( stateName )
            {
            
            case 'playerpick':
			    
			    if (this.isCurrentPlayerActive() )
				{
					list =dojo.query( '#table .card' ).addClass( 'borderpulse' ) ;
					
					for (var i = 0; i < list.length; i++)
					{
						var thiselement = list[i];
						this.gameconnections.push( dojo.connect(thiselement, 'onclick' , this, 'pickcard'))
					}
					
				}
				
				break;
            case 'selectTarget':
			    
			    if (this.isCurrentPlayerActive() )
				{
					list = args.args.possiblemoves;
					
					
					for (var i = 0; i < list.length; i++)
					{
						var thiselement = list[i];
						thistarget=dojo.query("#"+thiselement+ " .card,#"+thiselement+".card ,#"+thiselement+">div" ).addClass( 'borderpulse' ) ;
						this.gameconnections.push( dojo.connect(thistarget[0], 'onclick' , this, 'selectTarget'))
					}
					
				}
				
				break;
           
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
            case 'playerpick':
			    
			    if (this.isCurrentPlayerActive() )
				{
					dojo.forEach(this.gameconnections, dojo.disconnect);
					dojo.query(".borderpulse").removeClass("borderpulse");
					this.gameconnections=new Array();
				}
            case 'selectTarget':
			    
			    if (this.isCurrentPlayerActive() )
				{
					dojo.forEach(this.gameconnections, dojo.disconnect);
					dojo.query(".borderpulse").removeClass("borderpulse");
					this.gameconnections=new Array();
				}
            case 'dummmy':
                break;
            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
/*               
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */
		attachToNewParentNoDestroy : function(mobile, new_parent) {
            if (mobile === null) {
                console.error("attachToNewParent: mobile obj is null");
                return;
            }
            if (new_parent === null) {
                console.error("attachToNewParent: new_parent is null");
                return;
            }
            if (typeof mobile == "string") {
                mobile = $(mobile);
            }
            if (typeof new_parent == "string") {
                new_parent = $(new_parent);
            }

            var src = dojo.position(mobile);
            dojo.style(mobile, "position", "absolute");
            dojo.place(mobile, new_parent, "last");
            var tgt = dojo.position(mobile);
            var box = dojo.marginBox(mobile);

            var left = box.l + src.x - tgt.x;
            var top = box.t + src.y - tgt.y;
            dojo.style(mobile, "top", top + "px");
            dojo.style(mobile, "left", left + "px");
            return box;
        },

        /**
         * This method is similar to slideToObject but works on object which do not use inline style positioning. It also attaches object to
         * new parent immediately, so parent is correct during animation
         */
        slideToObjectRelative : function(token, finalPlace, tlen, tdelay, onEnd) {
            this.resetPosition(token);

            var box = this.attachToNewParentNoDestroy(token, finalPlace);
            var anim = this.slideToObjectPos(token, finalPlace, box.l, box.t, tlen, tdelay);

            dojo.connect(anim, "onEnd", dojo.hitch(this, function(token) {
                this.stripPosition(token);
                if (onEnd) onEnd(token);
            }));

            anim.play();
        },
		
		stripPosition : function(token) {
            // console.log(token + " STRIPPING");
            // remove any added positioning style
            dojo.style(token, "display", null);
            dojo.style(token, "top", null);
            dojo.style(token, "left", null);
            dojo.style(token, "position", null);
        },
		
		resetPosition : function(token) {
            // console.log(token + " RESETING");
            // remove any added positioning style
            dojo.style(token, "display", null);
            dojo.style(token, "top", "0px");
            dojo.style(token, "left", "0px");
            dojo.style(token, "position", null);
        },
		
		placecard: function ( destination, card_id ,card_type )
		{
			xpos= -140*((card_type - 1 )%3 );
			ypos= -90*(Math.floor( (card_type -1 ) / 3 ));
			position= xpos+"px "+ ypos+"px ";
			
			//dojo.style('stile_back_'+location_arg , "background-position", position);
			
			dojo.place( "<div id='card_"+card_id+"' class='card' style='background-position:"+position+";'></div>" , destination, "last");			
		},
		
		addtooltipcard: function ( card_id ,card_type )
		{
			xpos= -180*((card_type - 1 )%3 );
			ypos= -240*(Math.floor( (card_type -1 ) / 3 ));
			position= xpos+"px "+ ypos+"px ";
			
			switch(card_type){
				case "1" :
				 tooltiptext=_("Bunny hops to a fence post and exchanges places with it");
				break;
				case "2" :
				 tooltiptext=_("Shift a row or column of veggies or fence posts (the groundhog blocks)");
				break;
				case "3" :
				 tooltiptext=_("Move the groundhog, then swap any two veggies opposite the groundhog");
				break;
				case "4" :
				 tooltiptext=_("Swap adjacent veggies of fence posts (the groundhog blocks)");
				break;
				case "5" :
				 tooltiptext=_("Exchange a card from your hand with one in the garden");
				break;
				case "6" :
				 tooltiptext=_("Discard a veggie from the garden and replace it with one from the harvest (the groundhog blocks)");
				break;
				}
			
			this.addTooltipHtml("card_"+card_id, "<div id='tooltipcard_"+card_id+"' class='tooltipcard' style='background-position:"+position+";'><span class='tooltiptext'>"+tooltiptext+"</span></div>" );			
		},
		
		placetoken: function ( destination, card_id ,card_type )
		{
			
			xpos= -60*((card_type - 1 ));
			
			position= xpos+"px ";
			
			//dojo.style('stile_back_'+location_arg , "background-position", position);
			if ( card_type== 0)
			{
				dojo.place( "<div id='token_"+card_id+"' class='rabbit' ></div>" , destination, "last");
			}
			else
			{
				dojo.place( "<div id='token_"+card_id+"' class='token' style='background-position:"+position+";'></div>" , destination, "last");
			}
			
		},


        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
		
		pickcard: function( evt )
        {
            // Stop this event propagation
			
            dojo.stopEvent( evt );
			if( ! this.checkAction( 'pickcard' ) )
            {   return; }

            // Get the cliqued pos and Player field ID
            var cardpicked = evt.currentTarget.id;
			var card_id = cardpicked.split('_')[1];
			
		/*	this.confirmationDialog( _('Are you sure you want to make this?'), dojo.hitch( this, function() {
            this.ajaxcall( '/mygame/mygame/makeThis.html', { lock:true }, this, function( result ) {} );
			} ) ); */
			
			
			dojo.toggleClass(cardpicked,"tileselected");

			
			dojo.forEach(this.gameconnections, dojo.disconnect);
			
			dojo.query(".borderpulse").removeClass("borderpulse");
		
            if( this.checkAction( 'pickcard' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/veggiegarden/veggiegarden/pickcard.html", {
                    card_id:card_id                    
                }, this, function( result ) {} );
            }            
        },    
		
 
		
		selectTarget: function( evt )
        {
            // Stop this event propagation
			
            dojo.stopEvent( evt );
			if( ! this.checkAction( 'selectTarget' ) )
            {   return; }

            // Get the cliqued pos and Player field ID
            var target = evt.currentTarget.id;
			
			
		/*	this.confirmationDialog( _('Are you sure you want to make this?'), dojo.hitch( this, function() {
            this.ajaxcall( '/mygame/mygame/makeThis.html', { lock:true }, this, function( result ) {} );
			} ) ); */

			dojo.toggleClass(target,"tileselected");  //TODO replace this with a notification
			
			
			dojo.forEach(this.gameconnections, dojo.disconnect);
			
			dojo.query(".borderpulse").removeClass("borderpulse");
		
            if( this.checkAction( 'selectTarget' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/veggiegarden/veggiegarden/selectTarget.html", {
                    target:target
                }, this, function( result ) {} );
            }            
        },    

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your veggiegarden.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            // dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            // this.notifqueue.setSynchronous( 'cardPlayed', 3000 );
            // 
			
			dojo.subscribe( 'movetoken', this, "notif_movetoken" );
			this.notifqueue.setSynchronous( 'movetoken', 2000 );
			dojo.subscribe( 'selectcard', this, "notif_selectcard" );
			this.notifqueue.setSynchronous( 'selectcard', 2000 );
			
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
		notif_movetoken: function( notif )
        {
            console.log( 'notif_movetoken' );
            console.log( notif );
            this.slideToObjectRelative (notif.args.card_id, notif.args.destination,1500)
        },
		
		notif_selectcard: function( notif )
        {
            console.log( 'notif_selectcard' );
            console.log( notif );
            dojo.toggleClass("card_"+notif.args.card_id,"tileselected");
        },
		
        /*
        Example:
        
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
            
            // Note: notif.args contains the arguments specified during you "notifyAllPlayers" / "notifyPlayer" PHP call
            
            // TODO: play the card in the user interface.
        },    
        
        */
   });             
});
