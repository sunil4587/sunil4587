// const djq = jQuery.noConflict();

// djq( document ).ready( function($) {

//     djq( '.variations_form' ).each( function() {
//         // when variation is found, do something
//         djq(this).on( 'found_variation', function( event, variation ) {
//             let prodWeight = variation.attributes.attribute_pa_weight.slice(0,-1);
//             let prodPrice = variation.display_price;
//             let singleWeight = prodPrice / prodWeight;
//             let roundedPrice = singleWeight.toFixed(2);
//             djq(this).parents('.product-element-top').next('.product-information').find('.price_weight').html("$" + roundedPrice + " /g");
//         });

//     });

// } );

// jQuery(function(){
//     jQuery(document).on( 'found_variation', 'form.cart', function( event, variation ) {
//        console.log('Form Variation');
//        console.log( event, variation);       
//     });
// })();