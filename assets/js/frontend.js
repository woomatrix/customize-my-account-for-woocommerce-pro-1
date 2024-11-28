var $vas = jQuery.noConflict();
(function( $vas ) {
    'use strict';

    $vas(function() {
        
      $vas(".wcmamtx_group").on('click',function(event5) {
          event5.preventDefault();

          var parentli = $vas(this).parents("li");


          if (parentli.hasClass("open")) {
             parentli.find("ul.wcmamtx_sub_level").hide();
             parentli.removeClass("open");
             parentli.addClass("closed");
             parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-up").addClass("fa-chevron-down");

          } else if (parentli.hasClass("closed")) {

             parentli.find("ul.wcmamtx_sub_level").show();
             parentli.removeClass("closed");
             parentli.addClass("open");
             parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-down").addClass("fa-chevron-up");
          }

          return false;
          
      });

        $vas(".wcmamtx_group_sub").on('click',function(event6) {
          event6.preventDefault();

          var parentli = $vas(this).parents("li");

          if (parentli.hasClass("open")) {
             parentli.find("ul.wcmamtx_sub_level").hide();
             parentli.removeClass("open");
             parentli.addClass("closed");
             parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-up").addClass("fa-chevron-down");

          } else if (parentli.hasClass("closed")) {

             $vas("li.wcmamtx_group_sub.open").find("ul.wcmamtx_sub_level").hide();
             $vas("li.wcmamtx_group_sub.open").find('.wcmamtx_group_fa').removeClass("fa-chevron-up").addClass("fa-chevron-down");
             $vas("li.wcmamtx_group_sub.open").removeClass("open").addClass("closed");
             


             parentli.find("ul.wcmamtx_sub_level").show();
             parentli.removeClass("closed");
             parentli.addClass("open");

             parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-down").addClass("fa-chevron-up");
          }

          return false;
          
      });

      if ( wcmamtxfrontend.ajax_navigation && wcmamtxfrontend.contentSelector.length ) {
         $vas( document ).on( 'click', 'div.wcmamtx_ajax_enabled a,li.wcmamtx_ajax_enabled a, table.woocommerce-orders-table a.view, .woocommerce-Addresses a.edit', function( ev ) {
            "use strict";
            ev.preventDefault();

            var destination = $vas( this ).attr( 'href' );

            $vas.ajax({
               url: destination,
               data: {},
               method: 'GET',
               dataType: 'html',
               beforeSend: function() {
                  
                  $vas( wcmamtxfrontend.contentSelector ).addClass( 'wcmamtx-loading' ).block( {
                     message: null,
                     overlayCSS: {
                        background: 'url(' + wcmamtxfrontend.ajax_loader + ') #fff no-repeat center',
                        opacity: 0.8,
                        cursor: 'none',
                        position: 'relative',
                        width: '100%',
                        height: '126%'
                     }
                  } );
               },
               error: function( jqXHR, textStatus, errorThrown ) {
                  if ( 404 == jqXHR.status ) {
                  // redirect to page on 404
                     window.location = destination;
                  }
                  console.log( jqXHR, textStatus, errorThrown );
                  $vas( wcmamtxfrontend.contentSelector ).unblock();
               },
               
               success: function( response ) {
               // replace
                  const new_content = $vas(response).find( wcmamtxfrontend.contentSelector ).first();
                  $vas( wcmamtxfrontend.contentSelector ).first().replaceWith( new_content );
                  
               // change url
                  if( window.location.pathname !== destination ) {
                     window.history.replaceState({url: "" + destination + ""}, "Title", destination);
                     document.title = $vas(response).filter('title').text();
                  }
                  $vas(window).scrollTop(0);



                  $vas(document).trigger( 'wcmamtx-myaccount-section-loaded' );

                  $vas('.wcmamtx_upload_avatar').on('click', function(event) {
                    event.preventDefault();
                    $vas('#mywcmamtx_modal').show();
                    return false;
                  });


                  $vas('.wcmamtx_modal_close').on('click', function() {
                   $vas('#mywcmamtx_modal').hide();
                  });


                  $vas(".wcmamtx_group").on('click',function(event2) {


                   event2.preventDefault();

                   var parentli = $vas(this).parents("li");

                   if (parentli.hasClass("open")) {
                      parentli.find("ul.wcmamtx_sub_level").hide();
                      parentli.removeClass("open");
                      parentli.addClass("closed");
                      parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-up").addClass("fa-chevron-down");

                   } else if (parentli.hasClass("closed")) {

                      $vas("li.wcmamtx_group2.open").find("ul.wcmamtx_sub_level").hide();
                      $vas("li.wcmamtx_group2.open").find('.wcmamtx_group_fa').removeClass("fa-chevron-up").addClass("fa-chevron-down");
                      $vas("li.wcmamtx_group2.open").removeClass("open").addClass("closed");



                      parentli.find("ul.wcmamtx_sub_level").show();
                      parentli.removeClass("closed");
                      parentli.addClass("open");

                      parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-down").addClass("fa-chevron-up");
                   }

                   return false;

                });


                  $vas(".wcmamtx_group_sub").on('click',function(event3) {
                    event3.preventDefault();

                    var parentli = $vas(this).parents("li");

                    if (parentli.hasClass("open")) {
                       parentli.find("ul.wcmamtx_sub_level").hide();
                       parentli.removeClass("open");
                       parentli.addClass("closed");
                       parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-up").addClass("fa-chevron-down");

                    } else if (parentli.hasClass("closed")) {

                       $vas("li.wcmamtx_group_sub.open").find("ul.wcmamtx_sub_level").hide();
                       $vas("li.wcmamtx_group_sub.open").find('.wcmamtx_group_fa').removeClass("fa-chevron-up").addClass("fa-chevron-down");
                       $vas("li.wcmamtx_group_sub.open").removeClass("open").addClass("closed");
                       


                       parentli.find("ul.wcmamtx_sub_level").show();
                       parentli.removeClass("closed");
                       parentli.addClass("open");

                       parentli.find("i.wcmamtx_group_fa").removeClass("fa-chevron-down").addClass("fa-chevron-up");
                    }

                    return false;
                    
                 });



               }
               
            });
         });
      }

        
    $vas('.wcmamtx_upload_avatar').on('click', function(event) {
       event.preventDefault();
       $vas('#mywcmamtx_modal').show();
       return false;
    });
    

    $vas('.wcmamtx_modal_close').on('click', function() {
       $vas('#mywcmamtx_modal').hide();
    });
        
    });

 
})( jQuery );