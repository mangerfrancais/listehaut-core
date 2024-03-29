/* ----------------- Start Document ----------------- */
(function($){
"use strict";

$(document).ready(function(){

	var inputClicked = false;
/*----------------------------------------------------*/
	/*  Booking widget and confirmation form
	/*----------------------------------------------------*/
	$('a.booking-confirmation-btn').on('click', function(e){
		var button = $(this);
		button.addClass('loading');
		
		e.preventDefault();
		$('#booking-confirmation').submit();
		
	});

	$('a.book-now').on('click', function(e){
		var button = $(this);

		if(inputClicked == false){
			$('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').addClass('bounce');
		} else {
				button.addClass('loading');
		}
		e.preventDefault();

		var freeplaces = button.data('freeplaces');
		

	
		setTimeout(function() {
			  button.removeClass('loading');
			  $('.time-picker,.time-slots-dropdown,.date-picker-listing-rental').removeClass('bounce');
			  
		}, 3000);

		try {
			if ( freeplaces > 0 ) 
			{

					// preparing data for ajax
					var startDataSql = moment( $('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("DD-MM-YYYY");
					var endDataSql = moment( $('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("DD-MM-YYYY");
			
					var ajax_data = {
						'listing_type' : $('#listing_type').val(),
						'listing_id' : 	$('#listing_id').val()
						//'nonce': nonce		
					};

					if ( startDataSql ) ajax_data.date_start = startDataSql;
					if ( endDataSql ) ajax_data.date_end = endDataSql;
					if ( $('input#slot').val() ) ajax_data.slot = $('input#slot').val();
					if ( $('.time-picker').val() ) ajax_data._hour = $('.time-picker').val();
					if ( $('.adults').val() ) ajax_data.adults = $('.adults').val();
					if ( $('.childrens').val() ) ajax_data.childrens = $('.childrens').val();
					if ( $('#tickets').val() ) ajax_data.tickets = $('#tickets').val();
					var services = [];
 					$.each($("input[name='_service[]']:checked"), function(){            
                		services.push($(this).val());
            		});
            		ajax_data.services = services;
					$('input#booking').val( JSON.stringify( ajax_data ) );
					$('#form-booking').submit();

			} 
		} catch (e) {
			console.log(e);
		}

		if ( $('#listing_type').val() == 'event' )
		{
			
			var ajax_data = {
				'listing_type' : $('#listing_type').val(),
				'listing_id' : 	$('#listing_id').val(),
				'date_start' : $('.booking-event-date span').html(),
				'date_end' : $('.booking-event-date span').html(),
				//'nonce': nonce		
			};
			var services = [];
				$.each($("input[name='_service[]']:checked"), function(){            
        		services.push($(this).val());
    		});
    		ajax_data.services = services;
			
			// converent data
			ajax_data['date_start'] = moment(ajax_data['date_start'], wordpress_date_format.date).format('YYYY-MM-DD');
			ajax_data['date_end'] = moment(ajax_data['date_end'], wordpress_date_format.date).format('YYYY-MM-DD');
			if ( $('#tickets').val() ) ajax_data.tickets = $('#tickets').val();
			$('input#booking').val( JSON.stringify( ajax_data ) );
			
			$('#form-booking').submit();
			
		}
		
	});

	if(Boolean(listeo_core.clockformat)){
		var dateformat_even = wordpress_date_format.date+' HH:mm';
	} else {
		var dateformat_even = wordpress_date_format.date+' hh:mm A';
	}


	function updateCounter() {
	    var len = $(".bookable-services input[type='checkbox']:checked").length;
	    if(len>0){
	    	$(".booking-services span.services-counter").text(''+len+'');
	    	$(".booking-services span.services-counter").addClass('counter-visible');
	    } else{
	    	$(".booking-services span.services-counter").removeClass('counter-visible');
	    	$(".booking-services span.services-counter").text('0');
	    }
	}

	$('.single-service').on('click', function() {
		updateCounter();
		$(".booking-services span.services-counter").addClass("rotate-x");

		setTimeout(function() {
			$(".booking-services span.services-counter").removeClass("rotate-x");
		}, 300);
	});
	

	// $( ".input-datetime" ).each(function( index ) {
	// 	var $this = $(this);
	// 	var input = $(this).next('input');
	//   	var date =  parseInt(input.val());	
	//   	if(date){
	// 	  	var a = new Date(date);
	// 		var timestamp = moment(a);
	// 		$this.val(timestamp.format(dateformat_even));	
	//   	}
		
	// });
	
	//$('#_event_date').val(timestamp.format(dateformat_even));
	
	$('.input-datetime').daterangepicker({
		"opens": "left",
		// checking attribute listing type and set type of calendar
		singleDatePicker: true, 
		timePicker: true,
		autoUpdateInput: false,
		timePicker24Hour: Boolean(listeo_core.clockformat),
		//minDate: moment().subtract(0, 'days'),
		locale: {
			format 			: "YYYY-MM-DD HH:mm:ss",
			"firstDay"		: parseInt(wordpress_date_format.day),
			"applyLabel"	: listeo_core.applyLabel,
	        "cancelLabel"	: listeo_core.cancelLabel,
	        "fromLabel"		: listeo_core.fromLabel,
	        "toLabel"		: listeo_core.toLabel,
	        "customRangeLabel": listeo_core.customRangeLabel,
	        "daysOfWeek": [
	            listeo_core.day_short_su,
	            listeo_core.day_short_mo,
	            listeo_core.day_short_tu,
	            listeo_core.day_short_we,
	            listeo_core.day_short_th,
	            listeo_core.day_short_fr,
	            listeo_core.day_short_sa
	        ],
	  	},
	  	
	});

	$('.input-datetime').on('apply.daterangepicker', function(ev, picker) {
      	$(this).val(picker.startDate.format("YYYY-MM-DD HH:mm:ss"));
	});

	$('.input-datetime').on('cancel.daterangepicker', function(ev, picker) {
	    $(this).val('');
	});
	// $('.input-datetime').on( 'apply.daterangepicker', function(){
		
	// 	var picked_date = $(this).val();
	// 	var input = $(this).next('input');
	// 	input.val(moment(picked_date,dateformat_even).format('YYYY-MM-DD HH:MM:SS'));
	// } );


	$('#date-picker').daterangepicker({
		"opens": "left",
		// checking attribute listing type and set type of calendar
		singleDatePicker: ( $('#date-picker').attr('listing_type') == 'rental' ? false : true ), 
		timePicker: true,
		minDate: moment().subtract(0, 'days'),

		locale: {      
			format: "YYYY-MM-DD",
			"firstDay": parseInt(wordpress_date_format.day),
			"applyLabel"	: listeo_core.applyLabel,
	        "cancelLabel"	: listeo_core.cancelLabel,
	        "fromLabel"		: listeo_core.fromLabel,
	        "toLabel"		: listeo_core.toLabel,
	        "customRangeLabel": listeo_core.customRangeLabel,
	         "daysOfWeek": [
	            listeo_core.day_short_su,
	            listeo_core.day_short_mo,
	            listeo_core.day_short_tu,
	            listeo_core.day_short_we,
	            listeo_core.day_short_th,
	            listeo_core.day_short_fr,
	            listeo_core.day_short_sa
	        ],
		},

		isInvalidDate: function(date) {

			// working only for rental
			if ($('#listing_type').val() != 'rental' || typeof disabedDates == 'undefined' ) return false;

			if ( jQuery.inArray( date.format("YYYY-MM-DD"), disabedDates ) !== -1) return true;

		}

	});

	$('#date-picker').on('show.daterangepicker', function(ev, picker) {

        $('.daterangepicker').addClass('calendar-visible calendar-animated');
        $('.daterangepicker').removeClass('calendar-hidden');
    });
    $('#date-picker').on('hide.daterangepicker', function(ev, picker) {
    	
        $('.daterangepicker').removeClass('calendar-visible');
        $('.daterangepicker').addClass('calendar-hidden');
	});

	function calculate_price(){
		
		var ajax_data = {
			'action': 'calculate_price', 
			'listing_type' : $('#date-picker').attr('listing_type'),
			'listing_id' : 	$('input#listing_id').val(),
			'tickets' : 	$('input#tickets').val(),
			
			
			//'nonce': nonce		
		};
		var services = [];
			$.each($("input[name='_service[]']:checked"), function(){            
    		services.push($(this).val());
		});
		ajax_data.services = services;
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){

						$('#negative-feedback').fadeOut();
						$('a.book-now').removeClass('inactive');
						if(data.data.price > 0 ) {
							if(listeo_core.currency_position=='before'){
								$('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+data.data.price);	
							} else {
								$('.booking-estimated-cost span').html(data.data.price+' '+listeo_core.currency_symbol);	
							}
							
							$('.booking-estimated-cost').fadeIn();
						}
            }
        });
	}
	
	// function when checking booking by widget
	function check_booking() 
	{
		inputClicked = true;
		if ( is_open === false ) return 0;
		
		// if we not deal with services with slots or opening hours
		if ( $('#date-picker').attr('listing_type') == 'service' && 
		! $('input#slot').val() && ! $('.time-picker').val() ) 
		{
			$('#negative-feedback').fadeIn();
			
			return;
		}
		
		var startDataSql = moment( $('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
		var endDataSql = moment( $('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");

		
		// preparing data for ajax
		var ajax_data = {
			'action': 'check_avaliabity', 
			'listing_type' : $('#date-picker').attr('listing_type'),
			'listing_id' : 	$('input#listing_id').val(),
			'date_start' : startDataSql,
			'date_end' : endDataSql,
			//'nonce': nonce		
		};
		var services = [];
			$.each($("input[name='_service[]']:checked"), function(){            
    		services.push($(this).val());
		});
		ajax_data.services = services;
		
		if ( $('input#slot').val() ) ajax_data.slot = $('input#slot').val();
		if ( $('input.adults').val() ) ajax_data.adults = $('input.adults').val();
		if ( $('.time-picker').val() ) ajax_data.hour = $('.time-picker').val();
		

		// loader class
		$('a.book-now').addClass('loading');
		$('a.book-now-notloggedin').addClass('loading');
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){

				// loader clas
				if (data.success == true && ( ! $(".time-picker").length || is_open != false ) ) {
                   if ( data.data.free_places > 0) {
                   		$('a.book-now').data('freeplaces',data.data.free_places);
						$('.booking-error-message').fadeOut();
						$('a.book-now').removeClass('inactive');
						if(data.data.price > 0 ) {
							if(listeo_core.currency_position=='before'){
								$('.booking-estimated-cost span').html(listeo_core.currency_symbol+' '+data.data.price);	
							} else {
								$('.booking-estimated-cost span').html(data.data.price+' '+listeo_core.currency_symbol);	
							}
							
							$('.booking-estimated-cost').fadeIn();
						}
						

				   } else {
				   		$('a.book-now').data('freeplaces',0);
						$('.booking-error-message').fadeIn();
						
						$('.booking-estimated-cost').fadeOut();

						$('.booking-estimated-cost span').html('');

					}
                } else {
                	$('a.book-now').data('freeplaces',0);
					$('.booking-error-message').fadeIn();
					
					$('.booking-estimated-cost').fadeOut();
		   		}
		   		$('a.book-now').removeClass('loading');
		   		$('a.book-now-notloggedin').removeClass('loading');
            }
		});

	}

	var is_open = true;
	var lastDayOfWeek;




	// update slots and check hours setted to this day
	function update_booking_widget () 
	{

		// function only for services
		if ( $('#date-picker').attr('listing_type') != 'service') return;
		$('a.book-now').addClass('loading');
		$('a.book-now-notloggedin').addClass('loading');
		// get day of week
		var date = $('#date-picker').data('daterangepicker').endDate._d;
		var dayOfWeek = date.getDay() - 1;
		console.log(date.getDay() - 1);
	
		if(date.getDay() == 0){
			dayOfWeek = 6;
		}
		

		var startDataSql = moment( $('#date-picker').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
		var endDataSql = moment( $('#date-picker').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
			
		var ajax_data = {
			'action'		: 'update_slots', 
			'listing_id' 	: 	$('input#listing_id').val(),
			'date_start' 	: startDataSql,
			'date_end' 		: endDataSql,
			'slot'			: dayOfWeek
			//'nonce': nonce		
		};

		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
			
            success: function(data){
            	
				$('.time-slots-dropdown .panel-dropdown-scrollable').html(data.data);

				// reset values of slot selector
				if ( dayOfWeek != lastDayOfWeek)
				{
					
					$( '.panel-dropdown-scrollable .time-slot input' ).prop("checked", false);
					
					$('.panel-dropdown.time-slots-dropdown input#slot').val('');
					$('.panel-dropdown.time-slots-dropdown a').html( $('.panel-dropdown.time-slots-dropdown a').attr('placeholder') );
					$(' .booking-estimated-cost span').html(' ');

				}

				lastDayOfWeek = dayOfWeek;

				if ( ! $( '.panel-dropdown-scrollable .time-slot[day=\'' + dayOfWeek + '\']' ).length ) 
				{

					$( '.no-slots-information' ).show();
					$('.panel-dropdown.time-slots-dropdown a').html( $( '.no-slots-information' ).html() );

				}
					else  
				{

					// when we dont have slots for this day reset cost and show no slots
					$( '.no-slots-information' ).hide();
					$(' .booking-estimated-cost span').html(' ');
					

				}
				// show only slots for this day
				$( '.panel-dropdown-scrollable .time-slot' ).hide( );
				
				$( '.panel-dropdown-scrollable .time-slot[day=\'' + dayOfWeek + '\']' ).show( );
				$(".time-slot").each(function() {
					var timeSlot = $(this);
					$(this).find('input').on('change',function() {
						var timeSlotVal = timeSlot.find('strong').text();
						var slotArray = [timeSlot.find('strong').text(), timeSlot.find('input').val()];

						$('.panel-dropdown.time-slots-dropdown input#slot').val( JSON.stringify( slotArray ) );
				
						$('.panel-dropdown.time-slots-dropdown a').html(timeSlotVal);
						$('.panel-dropdown').removeClass('active');
						
						check_booking();
					});
				});
				$('a.book-now').removeClass('loading');
				$('a.book-now-notloggedin').removeClass('loading');
            }
        });
		

		// check if opening days are active
		if ( $(".time-picker").length ) {
			if(availableDays){


				if ( availableDays[dayOfWeek].opening == 'Closed' || availableDays[dayOfWeek].closing == 'Closed') 
				{

					$('#negative-feedback').fadeIn();

					//$('a.book-now').css('background-color','grey');
					
					is_open = false;
					console.log('zamkniete tego dnia' + dayOfWeek);
					return;
				}

				// converent hours to 24h format
				var opening_hour = moment( availableDays[dayOfWeek].opening, ["h:mm A"]).format("HH:mm");
				var closing_hour = moment( availableDays[dayOfWeek].closing, ["h:mm A"]).format("HH:mm");


				// get hour in 24 format
				var current_hour = $('.time-picker').val();


				// check if currer hour bar is open
				if ( current_hour >= opening_hour && current_hour <= closing_hour) 
				{

					is_open = true;
					$('#negative-feedback').fadeOut();
					$('a.book-now').attr('href','#').css('background-color','#f30c0c');
					check_booking()
					console.log('otwarte' + dayOfWeek);
					

				} else {
					
					is_open = false;
					$('#negative-feedback').fadeIn();
					//$('a.book-now').attr('href','#').css('background-color','grey');
					$('.booking-estimated-cost span').html('');
					console.log('zamkniete');

				}
			}
		}
	}

	// if slots exist update them
	if ( $( '.time-slot' ).length ) { update_booking_widget(); }
	
	// show only services for actual day from datapicker
	$( '#date-picker' ).on( 'apply.daterangepicker', update_booking_widget );


	// when slot is selected check if there are avalible bookings
	$( '#date-picker' ).on( 'apply.daterangepicker', check_booking );
	$( '#date-picker' ).on( 'cancel.daterangepicker', check_booking );
	
	$(document).on("change", 'input#slot,input.adults.count_per_guest, .form-booking-service input.bookable-service-checkbox,.form-booking-rental input.bookable-service-checkbox', function(event) {
		check_booking();
	}); 
	//$('input#slot').on( 'change', check_booking );
	
	$('input#tickets,.form-booking-event input.bookable-service-checkbox').on('change',function(e){
		//check_booking();
		calculate_price();
	});


	// hours picker
	if ( $(".time-picker").length ) {
		var time24 = false;
		
		if(listeo_core.clockformat){
			time24 = true;
		}
		$(".time-picker").flatpickr({
			enableTime: true,
			noCalendar: true,
			dateFormat: "H:i",
			time_24hr: time24,

			// check if there are free days on change and calculate price
			onChange: function(selectedDates, dateStr, instance) {
				update_booking_widget();
				check_booking();
			},

		});
	 
	}
	

	
/*----------------------------------------------------*/
/*  Bookings Dashboard Script
/*----------------------------------------------------*/

if ( $( "#booking-date-range" ).length ) {

	// to update view with bookin

	var bookingsOffset = 0;

	// here we can set how many bookings per page
	var bookingsLimit = 5;

	// function when checking booking by widget
	function listeo_bookings_manage(page=1) 
	{
		console.log($('#booking-date-range').data('daterangepicker'));
		if($('#booking-date-range').data('daterangepicker')){
			var startDataSql = moment( $('#booking-date-range').data('daterangepicker').startDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
			var endDataSql = moment( $('#booking-date-range').data('daterangepicker').endDate, ["MM/DD/YYYY"]).format("YYYY-MM-DD");
	
		} else {
			var startDataSql = '';
			var endDataSql = '';
		}

		
		// preparing data for ajax
		var ajax_data = {
			'action': 'listeo_bookings_manage', 
			'date_start' : startDataSql,
			'date_end' : endDataSql,
			'listing_id' : $('#listing_id').val(),
			'listing_status' : $('#listing_status').val(),
			'dashboard_type' : $('#dashboard_type').val(),
			'limit' : bookingsLimit,
			'offset' : bookingsOffset,
			'page' : page,
			//'nonce': nonce		
		};

		
		// display loader class
		$(".dashboard-list-box").addClass('loading');

		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){

				
				// display loader class
				$(".dashboard-list-box").removeClass('loading');

				if(data.data.html){
					$('#no-bookings-information').hide();
					$( "ul#booking-requests" ).html(data.data.html);	
					$( ".pagination-container" ).html(data.data.pagination);	
				} else {
					$( "ul#booking-requests" ).empty();
					$( ".pagination-container" ).empty();
					$('#no-bookings-information').show();
				}
				
            }
		});

	}

	// hooks for get bookings into view
	 $( '#booking-date-range' ).on( 'apply.daterangepicker', function(e){
		listeo_bookings_manage();
	 });
	 $( '#listing_id' ).on( 'change', function(e){
		listeo_bookings_manage();
	 });
	$( '#listing_status' ).on( 'change', function(e){
		listeo_bookings_manage();
	 });

	$( 'div.pagination-container').on( 'click', 'a', function(e) {
		e.preventDefault();
		
		var page   = $(this).parent().data('paged');

		listeo_bookings_manage(page);

		$( 'body, html' ).animate({
			scrollTop: $(".dashboard-list-box").offset().top
		}, 600 );

		return false;
	} );


	$(document).on('click','.reject, .cancel',function(e) {
		e.preventDefault();
		if (window.confirm(listeo_core.areyousure)) {
			var $this = $(this);
			$this.parents('li').addClass('loading');
			var status = 'confirmed';
			if ( $(this).hasClass('reject' ) ) status = 'cancelled';
			if ( $(this).hasClass('cancel' ) ) status = 'cancelled';

			// preparing data for ajax
			var ajax_data = {
				'action': 'listeo_bookings_manage', 
				'booking_id' : $(this).data('booking_id'),
				'status' : status,
				//'nonce': nonce		
			};
			$.ajax({
	            type: 'POST', dataType: 'json',
				url: listeo.ajaxurl,
				data: ajax_data,
				
	            success: function(data){
						
					// display loader class
					$this.parents('li').removeClass('loading');

					listeo_bookings_manage();
					
	            }
			});
		}
	});
	$(document).on('click','.approve',function(e) {
		e.preventDefault();
		var $this = $(this);
		$this.parents('li').addClass('loading');
		var status = 'confirmed';
		if ( $(this).hasClass('reject' ) ) status = 'cancelled';
		if ( $(this).hasClass('cancel' ) ) status = 'cancelled';

		// preparing data for ajax
		var ajax_data = {
			'action': 'listeo_bookings_manage', 
			'booking_id' : $(this).data('booking_id'),
			'status' : status,
			//'nonce': nonce		
		};
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
					
				// display loader class
				$this.parents('li').removeClass('loading');

				listeo_bookings_manage();
				
            }
		});

	});
	$(document).on('click','.mark-as-paid',function(e) {
		e.preventDefault();
		var $this = $(this);
		$this.parents('li').addClass('loading');
		var status = 'paid';
		
		// preparing data for ajax
		var ajax_data = {
			'action': 'listeo_bookings_manage', 
			'booking_id' : $(this).data('booking_id'),
			'status' : status,
			//'nonce': nonce		
		};
		$.ajax({
            type: 'POST', dataType: 'json',
			url: listeo.ajaxurl,
			data: ajax_data,
			
            success: function(data){
					
				// display loader class
				$this.parents('li').removeClass('loading');

				listeo_bookings_manage();
				
            }
		});

	});


	var start = moment().subtract(30, 'days');
    var end = moment();

    function cb(start, end) {
        $('#booking-date-range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    
    $('#booking-date-range-enabler').on('click',function(e){
    	e.preventDefault();
    	$(this).hide();
    	cb(start, end);
	    $('#booking-date-range').show().daterangepicker({
	    	"opens": "left",
		    "autoUpdateInput": false,
		    "alwaysShowCalendars": true,
	        startDate: start,
	        endDate: end,
	        ranges: {
	           'Today': [moment(), moment()],
	           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	           'This Month': [moment().startOf('month'), moment().endOf('month')],
	           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			},
			locale: {
				format: wordpress_date_format.date,
				"firstDay": parseInt(wordpress_date_format.day),
				"applyLabel"	: listeo_core.applyLabel,
		        "cancelLabel"	: listeo_core.cancelLabel,
		        "fromLabel"		: listeo_core.fromLabel,
		        "toLabel"		: listeo_core.toLabel,
		        "customRangeLabel": listeo_core.customRangeLabel,
		  	}
	    }, cb).trigger('click');
	    cb(start, end);
    })
   

    


    // Calendar animation and visual settings
    $('#booking-date-range').on('show.daterangepicker', function(ev, picker) {

        $('.daterangepicker').addClass('calendar-visible calendar-animated bordered-style');
        $('.daterangepicker').removeClass('calendar-hidden');
    });
    $('#booking-date-range').on('hide.daterangepicker', function(ev, picker) {
    	
        $('.daterangepicker').removeClass('calendar-visible');
        $('.daterangepicker').addClass('calendar-hidden');
	});
	
} // end if dashboard booking

   


	// $('a.reject').on('click', function() {
		
	// 	console.log(picker);
	
	// });
	});

})(this.jQuery);
