<<<<<<< HEAD
(function(b){var a={add:"ajaxAdd",del:"ajaxDel",dim:"ajaxDim",process:"process",recolor:"recolor"},c;c={settings:{url:ajaxurl,type:"POST",response:"ajax-response",what:"",alt:"alternate",altOffset:0,addColor:null,delColor:null,dimAddColor:null,dimDelColor:null,confirm:null,addBefore:null,addAfter:null,delBefore:null,delAfter:null,dimBefore:null,dimAfter:null},nonce:function(g,f){var d=wpAjax.unserialize(g.attr("href"));return f.nonce||d._ajax_nonce||b("#"+f.element+' input[name="_ajax_nonce"]').val()||d._wpnonce||b("#"+f.element+' input[name="_wpnonce"]').val()||0},parseClass:function(h,f){var i=[],d;try{d=b(h).attr("class")||"";d=d.match(new RegExp(f+":[\\S]+"));if(d){i=d[0].split(":")}}catch(g){}return i},pre:function(i,g,d){var f,h;g=b.extend({},this.wpList.settings,{element:null,nonce:0,target:i.get(0)},g||{});if(b.isFunction(g.confirm)){if("add"!=d){f=b("#"+g.element).css("backgroundColor");b("#"+g.element).css("backgroundColor","#FF9966")}h=g.confirm.call(this,i,g,d,f);if("add"!=d){b("#"+g.element).css("backgroundColor",f)}if(!h){return false}}return g},ajaxAdd:function(g,m){g=b(g);m=m||{};var h=this,l=c.parseClass(g,"add"),j,d,f,i,k;m=c.pre.call(h,g,m,"add");m.element=l[2]||g.attr("id")||m.element||null;if(l[3]){m.addColor="#"+l[3]}else{m.addColor=m.addColor||"#FFFF33"}if(!m){return false}if(!g.is('[id="'+m.what+'-add-submit"]')){return !c.add.call(h,g,m)}if(!m.element){return true}m.action="add-"+m.what;m.nonce=c.nonce(g,m);j=b("#"+m.element+" :input").not('[name="_ajax_nonce"], [name="_wpnonce"], [name="action"]');d=wpAjax.validateForm("#"+m.element);if(!d){return false}m.data=b.param(b.extend({_ajax_nonce:m.nonce,action:m.action},wpAjax.unserialize(l[4]||"")));f=b.isFunction(j.fieldSerialize)?j.fieldSerialize():j.serialize();if(f){m.data+="&"+f}if(b.isFunction(m.addBefore)){m=m.addBefore(m);if(!m){return true}}if(!m.data.match(/_ajax_nonce=[a-f0-9]+/)){return true}m.success=function(e){i=wpAjax.parseAjaxResponse(e,m.response,m.element);k=e;if(!i||i.errors){return false}if(true===i){return true}jQuery.each(i.responses,function(){c.add.call(h,this.data,b.extend({},m,{pos:this.position||0,id:this.id||0,oldId:this.oldId||null}))});h.wpList.recolor();b(h).trigger("wpListAddEnd",[m,h.wpList]);c.clear.call(h,"#"+m.element)};m.complete=function(e,n){if(b.isFunction(m.addAfter)){var o=b.extend({xml:e,status:n,parsed:i},m);m.addAfter(k,o)}};b.ajax(m);return false},ajaxDel:function(k,i){k=b(k);i=i||{};var j=this,d=c.parseClass(k,"delete"),h,g,f;i=c.pre.call(j,k,i,"delete");i.element=d[2]||i.element||null;if(d[3]){i.delColor="#"+d[3]}else{i.delColor=i.delColor||"#faa"}if(!i||!i.element){return false}i.action="delete-"+i.what;i.nonce=c.nonce(k,i);i.data=b.extend({action:i.action,id:i.element.split("-").pop(),_ajax_nonce:i.nonce},wpAjax.unserialize(d[4]||""));if(b.isFunction(i.delBefore)){i=i.delBefore(i,j);if(!i){return true}}if(!i.data._ajax_nonce){return true}h=b("#"+i.element);if("none"!=i.delColor){h.css("backgroundColor",i.delColor).fadeOut(350,function(){j.wpList.recolor();b(j).trigger("wpListDelEnd",[i,j.wpList])})}else{j.wpList.recolor();b(j).trigger("wpListDelEnd",[i,j.wpList])}i.success=function(e){g=wpAjax.parseAjaxResponse(e,i.response,i.element);f=e;if(!g||g.errors){h.stop().stop().css("backgroundColor","#faa").show().queue(function(){j.wpList.recolor();b(this).dequeue()});return false}};i.complete=function(e,l){if(b.isFunction(i.delAfter)){h.queue(function(){var m=b.extend({xml:e,status:l,parsed:g},i);i.delAfter(f,m)}).dequeue()}};b.ajax(i);return false},ajaxDim:function(h,n){if(b(h).parent().css("display")=="none"){return false}h=b(h);n=n||{};var i=this,m=c.parseClass(h,"dim"),g,d,f,k,j,l;n=c.pre.call(i,h,n,"dim");n.element=m[2]||n.element||null;n.dimClass=m[3]||n.dimClass||null;if(m[4]){n.dimAddColor="#"+m[4]}else{n.dimAddColor=n.dimAddColor||"#FFFF33"}if(m[5]){n.dimDelColor="#"+m[5]}else{n.dimDelColor=n.dimDelColor||"#FF3333"}if(!n||!n.element||!n.dimClass){return true}n.action="dim-"+n.what;n.nonce=c.nonce(h,n);n.data=b.extend({action:n.action,id:n.element.split("-").pop(),dimClass:n.dimClass,_ajax_nonce:n.nonce},wpAjax.unserialize(m[6]||""));if(b.isFunction(n.dimBefore)){n=n.dimBefore(n);if(!n){return true}}g=b("#"+n.element);d=g.toggleClass(n.dimClass).is("."+n.dimClass);f=c.getColor(g);g.toggleClass(n.dimClass);k=d?n.dimAddColor:n.dimDelColor;if("none"!=k){g.animate({backgroundColor:k},"fast").queue(function(){g.toggleClass(n.dimClass);b(this).dequeue()}).animate({backgroundColor:f},{complete:function(){b(this).css("backgroundColor","");b(i).trigger("wpListDimEnd",[n,i.wpList])}})}else{b(i).trigger("wpListDimEnd",[n,i.wpList])}if(!n.data._ajax_nonce){return true}n.success=function(e){j=wpAjax.parseAjaxResponse(e,n.response,n.element);l=e;if(!j||j.errors){g.stop().stop().css("backgroundColor","#FF3333")[d?"removeClass":"addClass"](n.dimClass).show().queue(function(){i.wpList.recolor();b(this).dequeue()});return false}};n.complete=function(e,o){if(b.isFunction(n.dimAfter)){g.queue(function(){var p=b.extend({xml:e,status:o,parsed:j},n);n.dimAfter(l,p)}).dequeue()}};b.ajax(n);return false},getColor:function(e){var d=jQuery(e).css("backgroundColor");return d||"#ffffff"},add:function(k,g){k=b(k);var i=b(this),d=false,j={pos:0,id:0,oldId:null},l,h,f;if("string"==typeof g){g={what:g}}g=b.extend(j,this.wpList.settings,g);if(!k.size()||!g.what){return false}if(g.oldId){d=b("#"+g.what+"-"+g.oldId)}if(g.id&&(g.id!=g.oldId||!d||!d.size())){b("#"+g.what+"-"+g.id).remove()}if(d&&d.size()){d.before(k);d.remove()}else{if(isNaN(g.pos)){l="after";if("-"==g.pos.substr(0,1)){g.pos=g.pos.substr(1);l="before"}h=i.find("#"+g.pos);if(1===h.size()){h[l](k)}else{i.append(k)}}else{if("comment"!=g.what||0===b("#"+g.element).length){if(g.pos<0){i.prepend(k)}else{i.append(k)}}}}if(g.alt){if((i.children(":visible").index(k[0])+g.altOffset)%2){k.removeClass(g.alt)}else{k.addClass(g.alt)}}if("none"!=g.addColor){f=c.getColor(k);k.css("backgroundColor",g.addColor).animate({backgroundColor:f},{complete:function(){b(this).css("backgroundColor","")}})}i.each(function(){this.wpList.process(k)});return k},clear:function(h){var g=this,f,d;h=b(h);if(g.wpList&&h.parents("#"+g.id).size()){return}h.find(":input").each(function(){if(b(this).parents(".form-no-clear").size()){return}f=this.type.toLowerCase();d=this.tagName.toLowerCase();if("text"==f||"password"==f||"textarea"==d){this.value=""}else{if("checkbox"==f||"radio"==f){this.checked=false}else{if("select"==d){this.selectedIndex=null}}}})},process:function(e){var f=this,d=b(e||document);d.delegate('form[class^="add:'+f.id+':"]',"submit",function(){return f.wpList.add(this)});d.delegate('a[class^="add:'+f.id+':"], input[class^="add:'+f.id+':"]',"click",function(){return f.wpList.add(this)});d.delegate('[class^="delete:'+f.id+':"]',"click",function(){return f.wpList.del(this)});d.delegate('[class^="dim:'+f.id+':"]',"click",function(){return f.wpList.dim(this)})},recolor:function(){var f=this,e,d;if(!f.wpList.settings.alt){return}e=b(".list-item:visible",f);if(!e.size()){e=b(f).children(":visible")}d=[":even",":odd"];if(f.wpList.settings.altOffset%2){d.reverse()}e.filter(d[0]).addClass(f.wpList.settings.alt).end().filter(d[1]).removeClass(f.wpList.settings.alt)},init:function(){var d=this;d.wpList.process=function(e){d.each(function(){this.wpList.process(e)})};d.wpList.recolor=function(){d.each(function(){this.wpList.recolor()})}}};b.fn.wpList=function(d){this.each(function(){var e=this;this.wpList={settings:b.extend({},c.settings,{what:c.parseClass(this,"list")[1]||""},d)};b.each(a,function(g,h){e.wpList[g]=function(i,f){return c[h].call(e,i,f)}})});c.init.call(this);this.wpList.process();return this}})(jQuery);
=======
(function($) {
var fs = {add:'ajaxAdd',del:'ajaxDel',dim:'ajaxDim',process:'process',recolor:'recolor'}, wpList;

wpList = {
	settings: {
		url: ajaxurl, type: 'POST',
		response: 'ajax-response',

		what: '',
		alt: 'alternate', altOffset: 0,
		addColor: null, delColor: null, dimAddColor: null, dimDelColor: null,

		confirm: null,
		addBefore: null, addAfter: null,
		delBefore: null, delAfter: null,
		dimBefore: null, dimAfter: null
	},

	nonce: function(e,s) {
		var url = wpAjax.unserialize(e.attr('href'));
		return s.nonce || url._ajax_nonce || $('#' + s.element + ' input[name="_ajax_nonce"]').val() || url._wpnonce || $('#' + s.element + ' input[name="_wpnonce"]').val() || 0;
	},

	parseData: function(e,t) {
		var d = [], wpListsData;

		try {
			wpListsData = $(e).attr('data-wp-lists') || '';
			wpListsData = wpListsData.match(new RegExp(t+':[\\S]+'));

			if ( wpListsData )
				d = wpListsData[0].split(':');
		} catch(r) {}

		return d;
	},

	pre: function(e,s,a) {
		var bg, r;

		s = $.extend( {}, this.wpList.settings, {
			element: null,
			nonce: 0,
			target: e.get(0)
		}, s || {} );

		if ( $.isFunction( s.confirm ) ) {
			if ( 'add' != a ) {
				bg = $('#' + s.element).css('backgroundColor');
				$('#' + s.element).css('backgroundColor', '#FF9966');
			}
			r = s.confirm.call(this, e, s, a, bg);

			if ( 'add' != a )
				$('#' + s.element).css('backgroundColor', bg );

			if ( !r )
				return false;
		}

		return s;
	},

	ajaxAdd: function( e, s ) {
		e = $(e);
		s = s || {};
		var list = this, data = wpList.parseData(e,'add'), es, valid, formData, res, rres;

		s = wpList.pre.call( list, e, s, 'add' );

		s.element = data[2] || e.attr( 'id' ) || s.element || null;

		if ( data[3] )
			s.addColor = '#' + data[3];
		else
			s.addColor = s.addColor || '#FFFF33';

		if ( !s )
			return false;

		if ( !e.is('[id="' + s.element + '-submit"]') )
			return !wpList.add.call( list, e, s );

		if ( !s.element )
			return true;

		s.action = 'add-' + s.what;

		s.nonce = wpList.nonce(e,s);

		es = $('#' + s.element + ' :input').not('[name="_ajax_nonce"], [name="_wpnonce"], [name="action"]');
		valid = wpAjax.validateForm( '#' + s.element );

		if ( !valid )
			return false;

		s.data = $.param( $.extend( { _ajax_nonce: s.nonce, action: s.action }, wpAjax.unserialize( data[4] || '' ) ) );
		formData = $.isFunction(es.fieldSerialize) ? es.fieldSerialize() : es.serialize();

		if ( formData )
			s.data += '&' + formData;

		if ( $.isFunction(s.addBefore) ) {
			s = s.addBefore( s );
			if ( !s )
				return true;
		}

		if ( !s.data.match(/_ajax_nonce=[a-f0-9]+/) )
			return true;

		s.success = function(r) {
			res = wpAjax.parseAjaxResponse(r, s.response, s.element);

			rres = r;

			if ( !res || res.errors )
				return false;

			if ( true === res )
				return true;

			jQuery.each( res.responses, function() {
				wpList.add.call( list, this.data, $.extend( {}, s, { // this.firstChild.nodevalue
					pos: this.position || 0,
					id: this.id || 0,
					oldId: this.oldId || null
				} ) );
			} );

			list.wpList.recolor();
			$(list).trigger( 'wpListAddEnd', [ s, list.wpList ] );
			wpList.clear.call(list,'#' + s.element);
		};

		s.complete = function(x, st) {
			if ( $.isFunction(s.addAfter) ) {
				var _s = $.extend( { xml: x, status: st, parsed: res }, s );
				s.addAfter( rres, _s );
			}
		};

		$.ajax( s );
		return false;
	},

	ajaxDel: function( e, s ) {
		e = $(e);
		s = s || {};
		var list = this, data = wpList.parseData(e,'delete'), element, res, rres;

		s = wpList.pre.call( list, e, s, 'delete' );

		s.element = data[2] || s.element || null;

		if ( data[3] )
			s.delColor = '#' + data[3];
		else
			s.delColor = s.delColor || '#faa';

		if ( !s || !s.element )
			return false;

		s.action = 'delete-' + s.what;

		s.nonce = wpList.nonce(e,s);

		s.data = $.extend(
			{ action: s.action, id: s.element.split('-').pop(), _ajax_nonce: s.nonce },
			wpAjax.unserialize( data[4] || '' )
		);

		if ( $.isFunction(s.delBefore) ) {
			s = s.delBefore( s, list );
			if ( !s )
				return true;
		}

		if ( !s.data._ajax_nonce )
			return true;

		element = $('#' + s.element);

		if ( 'none' != s.delColor ) {
			element.css( 'backgroundColor', s.delColor ).fadeOut( 350, function(){
				list.wpList.recolor();
				$(list).trigger( 'wpListDelEnd', [ s, list.wpList ] );
			});
		} else {
			list.wpList.recolor();
			$(list).trigger( 'wpListDelEnd', [ s, list.wpList ] );
		}

		s.success = function(r) {
			res = wpAjax.parseAjaxResponse(r, s.response, s.element);
			rres = r;

			if ( !res || res.errors ) {
				element.stop().stop().css( 'backgroundColor', '#faa' ).show().queue( function() { list.wpList.recolor(); $(this).dequeue(); } );
				return false;
			}
		};

		s.complete = function(x, st) {
			if ( $.isFunction(s.delAfter) ) {
				element.queue( function() {
					var _s = $.extend( { xml: x, status: st, parsed: res }, s );
					s.delAfter( rres, _s );
				}).dequeue();
			}
		};

		$.ajax( s );
		return false;
	},

	ajaxDim: function( e, s ) {
		if ( $(e).parent().css('display') == 'none' ) // Prevent hidden links from being clicked by hotkeys
			return false;

		e = $(e);
		s = s || {};

		var list = this, data = wpList.parseData(e,'dim'), element, isClass, color, dimColor, res, rres;

		s = wpList.pre.call( list, e, s, 'dim' );

		s.element = data[2] || s.element || null;
		s.dimClass =  data[3] || s.dimClass || null;

		if ( data[4] )
			s.dimAddColor = '#' + data[4];
		else
			s.dimAddColor = s.dimAddColor || '#FFFF33';

		if ( data[5] )
			s.dimDelColor = '#' + data[5];
		else
			s.dimDelColor = s.dimDelColor || '#FF3333';

		if ( !s || !s.element || !s.dimClass )
			return true;

		s.action = 'dim-' + s.what;

		s.nonce = wpList.nonce(e,s);

		s.data = $.extend(
			{ action: s.action, id: s.element.split('-').pop(), dimClass: s.dimClass, _ajax_nonce : s.nonce },
			wpAjax.unserialize( data[6] || '' )
		);

		if ( $.isFunction(s.dimBefore) ) {
			s = s.dimBefore( s );
			if ( !s )
				return true;
		}

		element = $('#' + s.element);
		isClass = element.toggleClass(s.dimClass).is('.' + s.dimClass);
		color = wpList.getColor( element );
		element.toggleClass( s.dimClass );
		dimColor = isClass ? s.dimAddColor : s.dimDelColor;

		if ( 'none' != dimColor ) {
			element
				.animate( { backgroundColor: dimColor }, 'fast' )
				.queue( function() { element.toggleClass(s.dimClass); $(this).dequeue(); } )
				.animate( { backgroundColor: color }, { complete: function() {
						$(this).css( 'backgroundColor', '' );
						$(list).trigger( 'wpListDimEnd', [ s, list.wpList ] );
					}
				});
		} else {
			$(list).trigger( 'wpListDimEnd', [ s, list.wpList ] );
		}

		if ( !s.data._ajax_nonce )
			return true;

		s.success = function(r) {
			res = wpAjax.parseAjaxResponse(r, s.response, s.element);
			rres = r;

			if ( !res || res.errors ) {
				element.stop().stop().css( 'backgroundColor', '#FF3333' )[isClass?'removeClass':'addClass'](s.dimClass).show().queue( function() { list.wpList.recolor(); $(this).dequeue(); } );
				return false;
			}
		};

		s.complete = function(x, st) {
			if ( $.isFunction(s.dimAfter) ) {
				element.queue( function() {
					var _s = $.extend( { xml: x, status: st, parsed: res }, s );
					s.dimAfter( rres, _s );
				}).dequeue();
			}
		};

		$.ajax( s );
		return false;
	},

	getColor: function( el ) {
		var color = jQuery(el).css('backgroundColor');

		return color || '#ffffff';
	},

	add: function( e, s ) {
		e = $( $.trim(e) ); // Trim leading whitespaces

		var list = $(this), old = false, _s = { pos: 0, id: 0, oldId: null }, ba, ref, color;

		if ( 'string' == typeof s )
			s = { what: s };

		s = $.extend(_s, this.wpList.settings, s);

		if ( !e.size() || !s.what )
			return false;

		if ( s.oldId )
			old = $('#' + s.what + '-' + s.oldId);

		if ( s.id && ( s.id != s.oldId || !old || !old.size() ) )
			$('#' + s.what + '-' + s.id).remove();

		if ( old && old.size() ) {
			old.before(e);
			old.remove();
		} else if ( isNaN(s.pos) ) {
			ba = 'after';

			if ( '-' == s.pos.substr(0,1) ) {
				s.pos = s.pos.substr(1);
				ba = 'before';
			}

			ref = list.find( '#' + s.pos );

			if ( 1 === ref.size() )
				ref[ba](e);
			else
				list.append(e);

		} else if ( 'comment' != s.what || 0 === $('#' + s.element).length ) {
			if ( s.pos < 0 ) {
				list.prepend(e);
			} else {
				list.append(e);
			}
		}

		if ( s.alt ) {
			if ( ( list.children(':visible').index( e[0] ) + s.altOffset ) % 2 ) { e.removeClass( s.alt ); }
			else { e.addClass( s.alt ); }
		}

		if ( 'none' != s.addColor ) {
			color = wpList.getColor( e );
			e.css( 'backgroundColor', s.addColor ).animate( { backgroundColor: color }, { complete: function() { $(this).css( 'backgroundColor', '' ); } } );
		}
		list.each( function() { this.wpList.process( e ); } );
		return e;
	},

	clear: function(e) {
		var list = this, t, tag;

		e = $(e);

		if ( list.wpList && e.parents( '#' + list.id ).size() )
			return;

		e.find(':input').each( function() {
			if ( $(this).parents('.form-no-clear').size() )
				return;

			t = this.type.toLowerCase();
			tag = this.tagName.toLowerCase();

			if ( 'text' == t || 'password' == t || 'textarea' == tag )
				this.value = '';
			else if ( 'checkbox' == t || 'radio' == t )
				this.checked = false;
			else if ( 'select' == tag )
				this.selectedIndex = null;
		});
	},

	process: function(el) {
		var list = this,
			$el = $(el || document);

		$el.delegate( 'form[data-wp-lists^="add:' + list.id + ':"]', 'submit', function(){
			return list.wpList.add(this);
		});

		$el.delegate( 'a[data-wp-lists^="add:' + list.id + ':"], input[data-wp-lists^="add:' + list.id + ':"]', 'click', function(){
			return list.wpList.add(this);
		});

		$el.delegate( '[data-wp-lists^="delete:' + list.id + ':"]', 'click', function(){
			return list.wpList.del(this);
		});

		$el.delegate( '[data-wp-lists^="dim:' + list.id + ':"]', 'click', function(){
			return list.wpList.dim(this);
		});
	},

	recolor: function() {
		var list = this, items, eo;

		if ( !list.wpList.settings.alt )
			return;

		items = $('.list-item:visible', list);

		if ( !items.size() )
			items = $(list).children(':visible');

		eo = [':even',':odd'];

		if ( list.wpList.settings.altOffset % 2 )
			eo.reverse();

		items.filter(eo[0]).addClass(list.wpList.settings.alt).end().filter(eo[1]).removeClass(list.wpList.settings.alt);
	},

	init: function() {
		var lists = this;

		lists.wpList.process = function(a) {
			lists.each( function() {
				this.wpList.process(a);
			} );
		};

		lists.wpList.recolor = function() {
			lists.each( function() {
				this.wpList.recolor();
			} );
		};
	}
};

$.fn.wpList = function( settings ) {
	this.each( function() {
		var _this = this;

		this.wpList = { settings: $.extend( {}, wpList.settings, { what: wpList.parseData(this,'list')[1] || '' }, settings ) };
		$.each( fs, function(i,f) { _this.wpList[i] = function( e, s ) { return wpList[f].call( _this, e, s ); }; } );
	} );

	wpList.init.call(this);

	this.wpList.process();

	return this;
};

})(jQuery);
>>>>>>> 3.5.1
