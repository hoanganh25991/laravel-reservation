(function($, window, document, translate){
	let _ = translate || function(v){
			return v;
		};

	var pluginName = 'Calendar';

	var defaults = {
		default_year: null,
		default_month: null,
		weekStart: 1,
		msg_days: [_("Sun"), _("Mon"), _("Tue"), _("Wed"), _("Thu"), _("Fri"), _("Sat")],
		msg_months: [_("January"), _("February"), _("March"), _("April"), _("May"), _("June"), _("July"), _(
			"August"), _("September"), _("October"), _("November"), _("December")],
		msg_today: _('Today'),
		msg_events_header: _('Events Today'),
	};

	var template = '' +
		'<table class="calendarhead"  id="calendar" ><thead>' +
		'<th class="sel" id="last" title="Previous Month"><div class="arrow">&lsaquo;</i></div></th>' +
		'<th width="150"><h3 class="month"></h3>' +
		'<h1 class="year"></h1></th>' +
		'<th class="sel" id="next" title="Next Month"><div class="arrow">&rsaquo;</i></div></th>' +
		'</thead></table>' +
		'<table class="calendar" id="calendar">' +
		'<thead class="calendar-header"></thead>' +
		'<tbody class="calendar-body"></tbody>' +
		'<tfoot height="80" >' +

		'<th colspan="7" class="sel" id="current" title="Today\'s Date">' + _('Today') + '</th>' +

		'</tfoot>' +
		'</table>' +
		'';

	var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	var today = new Date();

	// The actual plugin constructor
	function Plugin(element, options){
		this.element = $(element);
		this.options = $.extend({}, defaults, options);
		this._defaults = defaults;
		this._name = pluginName;

		this.init();

		return this;
	}

	Plugin.prototype.init = function(){
		// Place initialization logic here
		// You already have access to the DOM element and
		// the options via the instance, e.g. this.element
		// and this.options
		this.weekStart = this.options.weekStart || 1;
		this.days = this.options.msg_days;
		this.months = this.options.msg_months;
		this.msg_today = this.options.msg_today;
		// this.msg_events_hdr = this.options.msg_events_header;
		this.events = this.options.events;
		this.calendar = $(template).appendTo(this.element).on({
			click: $.proxy(this.click, this)
		});

		this.live_date = new Date();

		var now = new Date();

		if(this.options.default_month == null){
			this.mm = now.getMonth();
		}else{
			this.mm = this.options.default_month;
		}

		if(this.options.default_year == null){
			this.yy = now.getFullYear();
		}else{
			this.yy = this.options.default_year;
		}

		var mon = new Date(this.yy, this.mm, 1);
		this.yp = mon.getFullYear();
		this.yn = mon.getFullYear();

		if(this.component){
			this.component.on('click', $.proxy(this.show, this));
		}else{
			this.element.on('click', $.proxy(this.show, this));
		}

		// this.renderCalendar(mon);
		this.renderCalendar(now);
	};


	Plugin.prototype.renderCalendar = function(date){
		var mon = new Date(this.yy, this.mm, 1);

		this.element.parent('div:first').find('.year').empty();
		this.element.parent('div:first').find('.month').empty();

		this.element.parent('div:first').find('.year').append(mon.getFullYear());
		this.element.parent('div:first').find('.month').append(this.months[mon.getMonth()]);

		if(this.isLeapYear(date.getYear())){
			daysInMonth[1] = 29;
		}else{
			daysInMonth[1] = 28;
		}

		this.calendar.find('.calendar-header').empty();
		this.calendar.find('.calendar-body').empty();

		// Render Days of Week
		this.renderDays();

		var fdom = mon.getDay(); // First day of month

		// Render days
		var dow = 0;
		var first = 0;
		var last = 0;
		for(var i = 0; i >= last; i++){

			var _html = "";

			for(var j = this.weekStart; j < this.days.length + this.weekStart; j++){

				let cls = "";
				let msg = "";
				let id = "";

				// Determine if we have reached the first of the month
				if(first >= daysInMonth[mon.getMonth()]){
					dow = 0;
				}else if(((dow % 7) > 0 && (first % 7) > 0) || ((j % 7) == (fdom % 7))){
					dow++;
					first++;
				}

				// Get last day of month
				if(dow == daysInMonth[mon.getMonth()]){
					last = daysInMonth[mon.getMonth()];
				}

				// Set class
				if(cls.length == 0){
					if(
						(today.getFullYear() == date.getFullYear()
						&& today.getMonth() == date.getMonth()
						&& today.getDate() > dow) ||
						(today.getFullYear() == date.getFullYear()
						&& today.getMonth() > date.getMonth()) ||
						(today.getFullYear() > date.getFullYear())

					){
						cls = "past";
					}else if(
						today.getDate() == date.getDate()
						&& dow == date.getDate()
						&& today.getMonth() == date.getMonth()
						&& today.getFullYear() == date.getFullYear()
					){
						cls = "day today";
					}else if(j % 7 == 0 || j % 7 == 6){
						cls = "day";
					}else{
						cls = "day";
					}
				}

				// Set ID
				id = "day_" + dow;
				let month_ = mon.getMonth() + 1;
				let year = mon.getFullYear();

				// Render HTML
				if(dow == 0){
					_html += '<td>&nbsp;</td>';
				}else if(msg.length > 0){
					_html += '<td class="' + cls + '" id="' + id + '" year="' + year + '" month="' + month_ + '" day="' + dow + '"><span class="weekday">' + dow + '</span></td>';
				}else{
					_html += '<td class="' + cls + '" id="' + id + '" year="' + year + '" month="' + month_ + '" day="' + dow + '">' + dow + '</td>';
				}

			}
			_html = "<tr>" + _html + "</tr>";
			this.calendar.find('.calendar-body').append(_html);
		}
	};

	Plugin.prototype.renderDays = function(){
		var html = '';
		for(var j = this.weekStart; j < this.weekStart + 7; j++){
			html += "<th>" + this.days[j % 7] + "</th>";
		}

		var _html = "<tr>" + html + "</tr>";
		this.calendar.find('.calendar-header').append(_html);
	};

	Plugin.prototype.click = function(e){
		e.stopPropagation();
		e.preventDefault();
		var target = $(e.target).closest('td, th');

		let element_name;
		try{
			element_name = target[0].nodeName.toLowerCase();
		}catch(e){
		}
		switch(element_name){
			case 'td':
				if(target.is('.day')){
					var day = parseInt(target.attr('day'), 10) || 1;
					var month = parseInt(target.attr('month'), 10) || 1;
					var year = parseInt(target.attr('year'), 10) || 1;
					//console.log(this.element);
					this.element.find('td.day').removeClass('day_selected');
					target.addClass('day_selected');
					//console.log(target);

					let nativeDom = this.element[0];

					var event = new CustomEvent("user-select-day", {
						detail: {day: `${year}-${month}-${day}`},
						bubbles: true,
						cancelable: true
					});

					nativeDom.dispatchEvent(event);
				}
				break;

			case 'th':
				if(target.is('.sel')){
					let action = '';
					let day = '';
					let shouldUpdate = true;

					switch(target.attr("id")){
						case 'last':
							action = 'prv';
							day = new Date(this.yp, this.mm, 1);
							break;
						case 'current':
							action = 'crt';
							day = new Date();
							break;
						case 'next':
							action = 'nxt';
							day = new Date(this.yn, this.mm, 1);
							break;
						default:
							shouldUpdate = false;
							break;
					}

					if(shouldUpdate){
						this.update_date(action);
						this.live_date = day;
						this.renderCalendar(day, this.events);
					}

				}
				break;
		}
	};

	Plugin.prototype.update_date = function(action){
		var now = new Date();

		switch(action){
			case 'prv':
				now = new Date(this.yy, this.mm - 1, 1);
				break;
			case 'nxt':
				now = new Date(this.yy, this.mm + 1, 1);
				break;
			case 'crt':
				break;
		}

		this.mm = now.getMonth();
		this.yy = now.getFullYear();

		var mon = new Date(this.yy, this.mm, 1);
		this.yp = mon.getFullYear();
		this.yn = mon.getFullYear();
	};

	Plugin.prototype.isLeapYear = function(year){
		return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0))
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function(options){
		return new Plugin(this, options);
	}

})($, window, document);



