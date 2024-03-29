/**
Core script to handle the entire theme and core functions
**/

function getUrlParams(dParam) {
    var dPageURL = window.location.search.substring(1),
        dURLVariables = dPageURL.split('&'),
        dParameterName,
        i;

    for (i = 0; i < dURLVariables.length; i++) {
        dParameterName = dURLVariables[i].split('=');

        if (dParameterName[0] === dParam) {
            return dParameterName[1] === undefined ? true : decodeURIComponent(dParameterName[1]);
        }
    }
}

var Mophy = function(){
	
	/* Search Bar ============ */
	var screenWidth = $( window ).width();
	
	var homeSearch = function() {}
	
	var handleSelectPicker = function(){
		if(jQuery('.default-select').length > 0 ){
			jQuery('.default-select').selectpicker();
		}
	}
	
	var handleTheme = function(){
		$('#preloader').fadeOut(500);
		$('#main-wrapper').addClass('show');
	}
	
	var handleMetisMenu = function() {
		if(jQuery('#menu').length > 0 ){
			$("#menu").metisMenu();
		}
		jQuery('.metismenu > .mm-active ').each(function(){
			if(!jQuery(this).children('ul').length > 0)
			{
				jQuery(this).addClass('active-no-child');
			}
		});
	}
	
	var handleAllChecked = function() {
		$("#checkAll").on('change',function() {
			$("td input:checkbox, .custom-checkbox input:checkbox").prop('checked', $(this).prop("checked"));
		});
	}
	
	var handleNavigation = function() {
		$(".nav-control").on('click', function() {

			$('#main-wrapper').toggleClass("menu-toggle");

			$(".hamburger").toggleClass("is-active");
		});
	}
	
	var handleCurrentActive = function() {
		for (var nk = window.location,
			o = $("ul#menu a").filter(function() {
				return this.href == nk;
			})
			.addClass("mm-active")
			.parent()
			.addClass("mm-active");;) {
		// console.log(o)
		if (!o.is("li")) break;
			o = o.parent()
				.addClass("mm-show")
				.parent()
				.addClass("mm-active");
		}
	}
	
	var handleCustomFileInput = function() {
		$(".custom-file-input").on("change", function() {
			var fileName = $(this).val().split("\\").pop();
			$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});
	}
	
	var handleMiniSidebar = function() {
		$("ul#menu>li").on('click', function() {
			const sidebarStyle = $('body').attr('data-sidebar-style');
			if (sidebarStyle === 'mini') {
				console.log($(this).find('ul'))
				$(this).find('ul').stop()
			}
		})
	}
	
	// var handleMinHeight = function() {
	// 	var win_h = window.outerHeight;
	// 	var win_h = window.outerHeight;
	// 	if (win_h > 0 ? win_h : screen.height) {
	// 		$(".content-body").css("min-height", (win_h + 60) + "px");
	// 	};
	// }
	
	var handleDataAction = function() {
		$('a[data-action="collapse"]').on("click", function(i) {
			i.preventDefault(),
				$(this).closest(".card").find('[data-action="collapse"] i').toggleClass("mdi-arrow-down mdi-arrow-up"),
				$(this).closest(".card").children(".card-body").collapse("toggle");
		});

		$('a[data-action="expand"]').on("click", function(i) {
			i.preventDefault(),
				$(this).closest(".card").find('[data-action="expand"] i').toggleClass("icon-size-actual icon-size-fullscreen"),
				$(this).closest(".card").toggleClass("card-fullscreen");
		});



		$('[data-action="close"]').on("click", function() {
			$(this).closest(".card").removeClass().slideUp("fast");
		});

		$('[data-action="reload"]').on("click", function() {
			var e = $(this);
			e.parents(".card").addClass("card-load"),
				e.parents(".card").append('<div class="card-loader"><i class=" ti-reload rotate-refresh"></div>'),
				setTimeout(function() {
					e.parents(".card").children(".card-loader").remove(),
						e.parents(".card").removeClass("card-load")
				}, 2000)
		});
	}
	
	var handleHeaderHight = function() {
		const headerHight = $('.header').innerHeight();
		$(window).scroll(function() {
			if ($('body').attr('data-layout') === "horizontal" && $('body').attr('data-header-position') === "static" && $('body').attr('data-sidebar-position') === "fixed")
				$(this.window).scrollTop() >= headerHight ? $('.deznav').addClass('fixed') : $('.deznav').removeClass('fixed')
		});
	}
	
	var handledzScroll = function() {
		jQuery('.dz-scroll').each(function(){
		
			var scroolWidgetId = jQuery(this).attr('id');
			const ps = new PerfectScrollbar('#'+scroolWidgetId, {
			  wheelSpeed: 2,
			  wheelPropagation: true,
			  minScrollbarLength: 20
			});
		})
	}
	
	var handleMenuTabs = function() {
		if(screenWidth <= 991 ){
			jQuery('.menu-tabs .nav-link').on('click',function(){
				if(jQuery(this).hasClass('open'))
				{
					jQuery(this).removeClass('open');
					jQuery('.fixed-content-box').removeClass('active');
					jQuery('.hamburger').show();
				}else{
					jQuery('.menu-tabs .nav-link').removeClass('open');
					jQuery(this).addClass('open');
					jQuery('.fixed-content-box').addClass('active');
					jQuery('.hamburger').hide();
				}
				//jQuery('.fixed-content-box').toggleClass('active');
			});
			jQuery('.close-fixed-content').on('click',function(){
				jQuery('.fixed-content-box').removeClass('active');
				jQuery('.hamburger').removeClass('is-active');
				jQuery('#main-wrapper').removeClass('menu-toggle');
				jQuery('.hamburger').show();
			});
		}
	}
	
	var handleChatbox = function() {
		jQuery('.bell-link').on('click',function(){
			jQuery('.chatbox').addClass('active');
		});
		jQuery('.chatbox-close').on('click',function(){
			jQuery('.chatbox').removeClass('active');
		});
	}
	
	var handleBtnNumber = function() {
		$('.btn-number').on('click', function(e) {
			e.preventDefault();

			fieldName = $(this).attr('data-field');
			type = $(this).attr('data-type');
			var input = $("input[name='" + fieldName + "']");
			var currentVal = parseInt(input.val());
			if (!isNaN(currentVal)) {
				if (type == 'minus')
					input.val(currentVal - 1);
				else if (type == 'plus')
					input.val(currentVal + 1);
			} else {
				input.val(0);
			}
		});
	}
	
	var handledzChatUser = function() {
		jQuery('.dz-chat-user-box .dz-chat-user').on('click',function(){
			jQuery('.dz-chat-user-box').addClass('d-none');
			jQuery('.dz-chat-history-box').removeClass('d-none');
		}); 
		
		jQuery('.dz-chat-history-back').on('click',function(){
			jQuery('.dz-chat-user-box').removeClass('d-none');
			jQuery('.dz-chat-history-box').addClass('d-none');
		}); 
		
		jQuery('.dz-fullscreen').on('click',function(){
			jQuery('.dz-fullscreen').toggleClass('active');
		});
	}
	
	var handledzLoadMore = function() {
		$(".dz-load-more").on('click', function(e)
		{
			e.preventDefault();	//STOP default action
			$(this).append(' <i class="fa fa-refresh"></i>');
			
			var dzLoadMoreUrl = $(this).attr('rel');
			var dzLoadMoreId = $(this).attr('id');
			
			$.ajax({
				method: "POST",
				url: dzLoadMoreUrl,
				dataType: 'html',
				success: function(data) {
					$( "#"+dzLoadMoreId+"Content").append(data);
					$('.dz-load-more i').remove();
				}
			})
		});
	}
	
	var handledzFullScreen = function() {
		jQuery('.dz-fullscreen').on('click',function(e){
			if(document.fullscreenElement||document.webkitFullscreenElement||document.mozFullScreenElement||document.msFullscreenElement) { 
				/* Enter fullscreen */
				if(document.exitFullscreen) {
					document.exitFullscreen();
				} else if(document.msExitFullscreen) {
					document.msExitFullscreen(); /* IE/Edge */
				} else if(document.mozCancelFullScreen) {
					document.mozCancelFullScreen(); /* Firefox */
				} else if(document.webkitExitFullscreen) {
					document.webkitExitFullscreen(); /* Chrome, Safari & Opera */
				}
			} 
			else { /* exit fullscreen */
				if(document.documentElement.requestFullscreen) {
					document.documentElement.requestFullscreen();
				} else if(document.documentElement.webkitRequestFullscreen) {
					document.documentElement.webkitRequestFullscreen();
				} else if(document.documentElement.mozRequestFullScreen) {
					document.documentElement.mozRequestFullScreen();
				} else if(document.documentElement.msRequestFullscreen) {
					document.documentElement.msRequestFullscreen();
				}
			}		
		});
	}
	
  
  
  
	var handlePerfectScrollbar = function() {
		if(jQuery('.deznav-scroll').length > 0)
		{
			const qs = new PerfectScrollbar('.deznav-scroll');
		}
	}
	
	var handleheartBlast = function (){
		$(".heart").on("click", function() {
			$(this).toggleClass("heart-blast");
		});
	}	
	
	var handleshowPass = function (){
		jQuery('.show-pass').on('click',function(){
			jQuery(this).toggleClass('active');
			if(jQuery('#dz-password').attr('type') == 'password'){
				jQuery('#dz-password').attr('type','text');
			}else if(jQuery('#dz-password').attr('type') == 'text'){
				jQuery('#dz-password').attr('type','password');
			}
		});
	}
	
	var handleLightgallery = function (){
		if(jQuery('#lightgallery, .lightgallery').length>0) {
			$('#lightgallery, .lightgallery').lightGallery({
				thumbnail:true,
			});
		}
	}
  
  
  var reviewCarousel = function (){
    /*  testimonial one function by = owl.carousel.js */
    /*  testimonial one function by = owl.carousel.js */
    if($('.testimonial-one').length > 0) {
      $('.testimonial-one').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        center:true,
        dots: false,
        navText: ['<i class="fa fa-caret-left"></i>', '<i class="fa fa-caret-right"></i>'],
        responsive:{
          0:{
            items:2
          },
          400:{
            items:3
          },	
          700:{
            items:5
          },	
          991:{
            items:6
          },			
          
          1200:{
            items:4
          },
          1600:{
            items:5
          }
        }
      });
    }
  }

  var cardsCarousel = function (){
    
    if($('.cards').length > 0) {
      jQuery('.cards').owlCarousel({
				loop:true,
				margin:30,
				nav:true,
				autoWidth:true,
				center:true,
				dots: false,
        navText: ['', ''],
			})
    }
  }
	/* Function ============ */
	return {
		init:function(){
			handleSelectPicker();
			handleTheme();
			handleMetisMenu();	
			handleAllChecked();	
			handleNavigation();
			handleCurrentActive();
			handleCustomFileInput();
			handleMiniSidebar();
			// handleMinHeight();
			handleDataAction();
			handleHeaderHight();
			handledzScroll();
			handleMenuTabs();
			handleChatbox();
			handleBtnNumber();
			handledzChatUser();
			handledzLoadMore();
			handledzFullScreen();
			handlePerfectScrollbar();
			handleheartBlast();
			handleshowPass();
			handleLightgallery();
			reviewCarousel();
			cardsCarousel();
		},

		
		load:function(){
			handleSelectPicker();
			handleTheme();
		},
		
		resize:function(){
			
			
		},
    
	}
	
}();

/* Document.ready Start */	
jQuery(document).ready(function() {
    'use strict';
	Mophy.init();
  
  if($('#example-5').length > 0) {
      var table = $('#example-5').DataTable({
            searching: false,
            paging:true,
            select: false,
            //info: false,         
            lengthChange:false 
            
          });
  }
  if($('#example').length > 0) {
    $('#example tbody').on('click', 'tr', function () {
      var data = table.row( this ).data();
      
    });
  }
  
  if($('#smartwizard').length > 0) {
    $('#smartwizard').smartWizard(); 
  }
  $('[data-toggle="popover"]').popover()
});
/* Document.ready END */

/* Window Load START */
jQuery(window).on('load',function () {
	'use strict'; 
	Mophy.load();
	
});
/*  Window Load END */
/* Window Resize START */
jQuery(window).on('resize',function () {
	'use strict'; 
	Mophy.resize();
});
/*  Window Resize END */

jQuery(document).on('keypress',function(e) {
	'use strict';

    if(e.which == 13) {
        if ($('.swal2-confirm').length > 0) {
			$('.swal2-confirm').click();
		}
    }
});

function select_text(element) {
	if(element) {
		element.focus();
		element.select();
	}
}