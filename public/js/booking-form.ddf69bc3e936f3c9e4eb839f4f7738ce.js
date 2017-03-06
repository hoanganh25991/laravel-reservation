let calendarHandler = function($){
	/**
	 * Sanity check
	 */
	try{
		let calendar = $.fn.Calendar;
		if(typeof calendar == 'undefined'){
			throw 'Need $ & f.fn.Calendar plugin'
		}
	}catch(e){
		throw e;
	}

	/**
	 * Store all needed div related to user pick day event
	 */
	let calendar = $('#calendar-box').Calendar();
	//label which change belongs to user pick day
	let label    = document.querySelector('#reservation_time');
	//call out api for availabel date
	let select   = document.querySelector('select[name="reservation_time"]');
	let sample_options = $('<option></option>');
	let form     = document.querySelector('#booking-form');
	

	let month_name = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

	// console.log(select);
	
	let changeLabel = function(e){
		let day_str = e.detail.day;
		let d    = new Date(day_str);

		label.innerText = `${month_name[d.getMonth()]} ${d.getDate()} ${d.getFullYear()}`;
	}
	
	let updateSelect = function(e){
		let day_str = e.detail.day;
		let d    = new Date(day_str);
		
		
		//ajax request
		//ask for available date base on form info
		let form_data = $(form).serializeArray();
		let data      = form_data.reduce((carry, item) => {
							carry[item.name] = item.value;

							return carry;
						}, {});
		
		$.ajax({
			method: 'POST',
			data,
			success(res){
				console.log(res);
			},
			error(res){
				console.log(res);
			}
		});
		
	}

	document.addEventListener('user-select-day', function(e){
		changeLabel(e);

		updateSelect(e);
	});





}

calendarHandler($);
