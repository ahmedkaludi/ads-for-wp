;( function( wp ) {
    var el = wp.element.createElement; // The wp.element.createElement() function to create elements.
    registerBlockType = wp.blocks.registerBlockType; // The registerBlockType() function to register blocks.
 
    registerBlockType( 'adsforwp/adsblock', {
            title: adsforwpGutenberg.default.adsforwp,
            //icon: 'chart-bar',
            category: 'common',
            attributes: {
                itemID: {
                    type: 'string',
                },
            },
            edit: function(props) {

                var itemID = props.attributes.itemID;
            
            /**
             * Update property on submit 
             */
            function setItemID( event ) {
                var selected = event.target.querySelector( 'option:checked' );
                props.setAttributes( { itemID: selected.value } );
                event.preventDefault();
            }
            

            // the form children elements
            var children = [];
            
            // argument list (in array form) for the children creation
            var args = [];
            var ads = [];
            var groups = [];
            var placements = [];
            
            args.push( 'select' );
            args.push( { value: itemID, onChange: setItemID } );
            args.push( el( 'option', null, adsforwpGutenberg.default['--empty--'] ) );
            
            
            for (i = 0; i < adsforwpGutenberg.ads.length; i++) {
                if ( 'undefined' == typeof adsforwpGutenberg.ads[i].id ) continue;
                ads.push( el( 'option', {value: 'ad_' + adsforwpGutenberg.ads[i].id}, adsforwpGutenberg.ads[i].title ) );
            }
            
            for (i = 0; i < adsforwpGutenberg.groups.length; i++) {
                if ( 'undefined' == typeof adsforwpGutenberg.groups[i].id ) continue;
                groups.push( el( 'option', {value: 'group_' + adsforwpGutenberg.groups[i]['id'] }, adsforwpGutenberg.groups[i]['name'] ) );
            }
            
            args.push( el( 'optgroup', {label: adsforwpGutenberg.default['adGroups']}, groups ) );
            
            args.push( el( 'optgroup', {label: adsforwpGutenberg.default['ads']}, ads ) );
            
            // add a <label /> first and style it.
            children.push( el( 'label', {style:{fontWeight:'bold',display:'block'}}, adsforwpGutenberg.default.adsforwp ) );
            
            // then add the <select /> input with its own children
            children.push( el.apply( null, args ) );
            
            if ( itemID && adsforwpGutenberg.default['--empty--'] != itemID ) {
                
                var url = '#';
                if ( 0 === itemID.indexOf( 'place_' ) ) {
                    url = adsforwpGutenberg.editLinks.placement;
                } else if ( 0 === itemID.indexOf( 'group_' ) ) {
                    url = adsforwpGutenberg.editLinks.group;
                } else if ( 0 === itemID.indexOf( 'ad_' ) ) {
                    var _adID = itemID.substr(3);
                    url = adsforwpGutenberg.editLinks.ad.replace( '%ID%', _adID );
                }
                
                children.push(
                    el(
                        'a',
                        {
                            class: 'dashicons dashicons-external',
                            style: {
                                'vetical-align': 'middle',
                                margin: 5,
                                'border-bottom': 'none'
                            },
                            href: url,
                            target: '_blank',
                        }
                    )
                );
                
            }
            // return the complete form
            return el( 'form', { onSubmit: setItemID }, children );
            },
            save: function(props) {
                // How our block renders on the frontend
                return null;
            },
        } 
    );
})(window.wp);