<<<<<<< HEAD
(function(e){var b=Math.abs,a=Math.max,d=Math.min,c=Math.round;function f(){return e("<div/>")}e.imgAreaSelect=function(s,X){var az=e(s),Z,av=f(),ai=f(),K=f().add(f()).add(f()).add(f()),ab=f().add(f()).add(f()).add(f()),O=e([]),V,n,q,aC={left:0,top:0},Q,j,C,P={left:0,top:0},D=0,ag="absolute",T,S,ad,ac,L,E,U,W,am,Y,N,A,aD,z,aB,y={x1:0,y1:0,x2:0,y2:0,width:0,height:0},p=document.documentElement,l,au,ap,aj,af,aq,x;function J(h){return h+aC.left-P.left}function I(h){return h+aC.top-P.top}function H(h){return h-aC.left+P.left}function B(h){return h-aC.top+P.top}function ao(h){return h.pageX-P.left}function al(h){return h.pageY-P.top}function G(h){var o=h||ad,i=h||ac;return{x1:c(y.x1*o),y1:c(y.y1*i),x2:c(y.x2*o),y2:c(y.y2*i),width:c(y.x2*o)-c(y.x1*o),height:c(y.y2*i)-c(y.y1*i)}}function ah(i,w,h,o,aE){var aG=aE||ad,aF=aE||ac;y={x1:c(i/aG||0),y1:c(w/aF||0),x2:c(h/aG||0),y2:c(o/aF||0)};y.width=y.x2-y.x1;y.height=y.y2-y.y1}function ar(){if(!az.width()){return}aC={left:c(az.offset().left),top:c(az.offset().top)};Q=az.innerWidth();j=az.innerHeight();aC.top+=(az.outerHeight()-j)>>1;aC.left+=(az.outerWidth()-Q)>>1;E=c(X.minWidth/ad)||0;U=c(X.minHeight/ac)||0;W=c(d(X.maxWidth/ad||1<<24,Q));am=c(d(X.maxHeight/ac||1<<24,j));if(e().jquery=="1.3.2"&&ag=="fixed"&&!p.getBoundingClientRect){aC.top+=a(document.body.scrollTop,p.scrollTop);aC.left+=a(document.body.scrollLeft,p.scrollLeft)}P=/absolute|relative/.test(C.css("position"))?{left:c(C.offset().left)-C.scrollLeft(),top:c(C.offset().top)-C.scrollTop()}:ag=="fixed"?{left:e(document).scrollLeft(),top:e(document).scrollTop()}:{left:0,top:0};n=J(0);q=I(0);if(y.x2>Q||y.y2>j){ay()}}function aa(h){if(!N){return}av.css({left:J(y.x1),top:I(y.y1)}).add(ai).width(af=y.width).height(aq=y.height);ai.add(K).add(O).css({left:0,top:0});K.width(a(af-K.outerWidth()+K.innerWidth(),0)).height(a(aq-K.outerHeight()+K.innerHeight(),0));e(ab[0]).css({left:n,top:q,width:y.x1,height:j});e(ab[1]).css({left:n+y.x1,top:q,width:af,height:y.y1});e(ab[2]).css({left:n+y.x2,top:q,width:Q-y.x2,height:j});e(ab[3]).css({left:n+y.x1,top:q+y.y2,width:af,height:j-y.y2});af-=O.outerWidth();aq-=O.outerHeight();switch(O.length){case 8:e(O[4]).css({left:af>>1});e(O[5]).css({left:af,top:aq>>1});e(O[6]).css({left:af>>1,top:aq});e(O[7]).css({top:aq>>1});case 4:O.slice(1,3).css({left:af});O.slice(2,4).css({top:aq})}if(h!==false){if(e.imgAreaSelect.keyPress!=aw){e(document).unbind(e.imgAreaSelect.keyPress,e.imgAreaSelect.onKeyPress)}if(X.keys){e(document)[e.imgAreaSelect.keyPress](e.imgAreaSelect.onKeyPress=aw)}}if(e.browser.msie&&K.outerWidth()-K.innerWidth()==2){K.css("margin",0);setTimeout(function(){K.css("margin","auto")},0)}}function u(h){ar();aa(h);A=J(y.x1);aD=I(y.y1);z=J(y.x2);aB=I(y.y2)}function ak(h,i){X.fadeSpeed?h.fadeOut(X.fadeSpeed,i):h.hide()}function F(i){var h=H(ao(i))-y.x1,o=B(al(i))-y.y1;if(!x){ar();x=true;av.one("mouseout",function(){x=false})}L="";if(X.resizable){if(o<=X.resizeMargin){L="n"}else{if(o>=y.height-X.resizeMargin){L="s"}}if(h<=X.resizeMargin){L+="w"}else{if(h>=y.width-X.resizeMargin){L+="e"}}}av.css("cursor",L?L+"-resize":X.movable?"move":"");if(V){V.toggle()}}function an(h){e("body").css("cursor","");if(X.autoHide||y.width*y.height==0){ak(av.add(ab),function(){e(this).hide()})}e(document).unbind("mousemove",ae);av.mousemove(F);X.onSelectEnd(s,G())}function t(h){if(h.which!=1){return false}ar();if(L){e("body").css("cursor",L+"-resize");A=J(y[/w/.test(L)?"x2":"x1"]);aD=I(y[/n/.test(L)?"y2":"y1"]);e(document).mousemove(ae).one("mouseup",an);av.unbind("mousemove",F)}else{if(X.movable){T=n+y.x1-ao(h);S=q+y.y1-al(h);av.unbind("mousemove",F);e(document).mousemove(g).one("mouseup",function(){X.onSelectEnd(s,G());e(document).unbind("mousemove",g);av.mousemove(F)})}else{az.mousedown(h)}}return false}function r(h){if(Y){if(h){z=a(n,d(n+Q,A+b(aB-aD)*Y*(z>A||-1)));aB=c(a(q,d(q+j,aD+b(z-A)/Y*(aB>aD||-1))));z=c(z)}else{aB=a(q,d(q+j,aD+b(z-A)/Y*(aB>aD||-1)));z=c(a(n,d(n+Q,A+b(aB-aD)*Y*(z>A||-1))));aB=c(aB)}}}function ay(){A=d(A,n+Q);aD=d(aD,q+j);if(b(z-A)<E){z=A-E*(z<A||-1);if(z<n){A=n+E}else{if(z>n+Q){A=n+Q-E}}}if(b(aB-aD)<U){aB=aD-U*(aB<aD||-1);if(aB<q){aD=q+U}else{if(aB>q+j){aD=q+j-U}}}z=a(n,d(z,n+Q));aB=a(q,d(aB,q+j));r(b(z-A)<b(aB-aD)*Y);if(b(z-A)>W){z=A-W*(z<A||-1);r()}if(b(aB-aD)>am){aB=aD-am*(aB<aD||-1);r(true)}y={x1:H(d(A,z)),x2:H(a(A,z)),y1:B(d(aD,aB)),y2:B(a(aD,aB)),width:b(z-A),height:b(aB-aD)};aa();X.onSelectChange(s,G())}function ae(h){z=/w|e|^$/.test(L)||Y?ao(h):J(y.x2);aB=/n|s|^$/.test(L)||Y?al(h):I(y.y2);ay();return false}function R(h,i){z=(A=h)+y.width;aB=(aD=i)+y.height;e.extend(y,{x1:H(A),y1:B(aD),x2:H(z),y2:B(aB)});aa();X.onSelectChange(s,G())}function g(h){A=a(n,d(T+ao(h),n+Q-y.width));aD=a(q,d(S+al(h),q+j-y.height));R(A,aD);h.preventDefault();return false}function aA(){e(document).unbind("mousemove",aA);ar();z=A;aB=aD;ay();L="";if(!ab.is(":visible")){av.add(ab).hide().fadeIn(X.fadeSpeed||0)}N=true;e(document).unbind("mouseup",at).mousemove(ae).one("mouseup",an);av.unbind("mousemove",F);X.onSelectStart(s,G())}function at(){e(document).unbind("mousemove",aA).unbind("mouseup",at);ak(av.add(ab));ah(H(A),B(aD),H(A),B(aD));if(!(this instanceof e.imgAreaSelect)){X.onSelectChange(s,G());X.onSelectEnd(s,G())}}function m(h){if(h.which!=1||ab.is(":animated")){return false}ar();T=A=ao(h);S=aD=al(h);e(document).mousemove(aA).mouseup(at);return false}function v(){u(false)}function ax(){Z=true;M(X=e.extend({classPrefix:"imgareaselect",movable:true,parent:"body",resizable:true,resizeMargin:10,onInit:function(){},onSelectStart:function(){},onSelectChange:function(){},onSelectEnd:function(){}},X));av.add(ab).css({visibility:""});if(X.show){N=true;ar();aa();av.add(ab).hide().fadeIn(X.fadeSpeed||0)}setTimeout(function(){X.onInit(s,G())},0)}var aw=function(w){var h=X.keys,aE,o,i=w.keyCode;aE=!isNaN(h.alt)&&(w.altKey||w.originalEvent.altKey)?h.alt:!isNaN(h.ctrl)&&w.ctrlKey?h.ctrl:!isNaN(h.shift)&&w.shiftKey?h.shift:!isNaN(h.arrows)?h.arrows:10;if(h.arrows=="resize"||(h.shift=="resize"&&w.shiftKey)||(h.ctrl=="resize"&&w.ctrlKey)||(h.alt=="resize"&&(w.altKey||w.originalEvent.altKey))){switch(i){case 37:aE=-aE;case 39:o=a(A,z);A=d(A,z);z=a(o+aE,A);r();break;case 38:aE=-aE;case 40:o=a(aD,aB);aD=d(aD,aB);aB=a(o+aE,aD);r(true);break;default:return}ay()}else{A=d(A,z);aD=d(aD,aB);switch(i){case 37:R(a(A-aE,n),aD);break;case 38:R(A,a(aD-aE,q));break;case 39:R(A+d(aE,Q-H(z)),aD);break;case 40:R(A,aD+d(aE,j-B(aB)));break;default:return}}return false};function k(h,o){for(var i in o){if(X[i]!==undefined){h.css(o[i],X[i])}}}function M(h){if(h.parent){(C=e(h.parent)).append(av.add(ab))}e.extend(X,h);ar();if(h.handles!=null){O.remove();O=e([]);ap=h.handles?h.handles=="corners"?4:8:0;while(ap--){O=O.add(f())}O.addClass(X.classPrefix+"-handle").css({position:"absolute",fontSize:0,zIndex:D+1||1});if(!parseInt(O.css("width"))>=0){O.width(5).height(5)}if(aj=X.borderWidth){O.css({borderWidth:aj,borderStyle:"solid"})}k(O,{borderColor1:"border-color",borderColor2:"background-color",borderOpacity:"opacity"})}ad=X.imageWidth/Q||1;ac=X.imageHeight/j||1;if(h.x1!=null){ah(h.x1,h.y1,h.x2,h.y2);h.show=!h.hide}if(h.keys){X.keys=e.extend({shift:1,ctrl:"resize"},h.keys)}ab.addClass(X.classPrefix+"-outer");ai.addClass(X.classPrefix+"-selection");for(ap=0;ap++<4;){e(K[ap-1]).addClass(X.classPrefix+"-border"+ap)}k(ai,{selectionColor:"background-color",selectionOpacity:"opacity"});k(K,{borderOpacity:"opacity",borderWidth:"border-width"});k(ab,{outerColor:"background-color",outerOpacity:"opacity"});if(aj=X.borderColor1){e(K[0]).css({borderStyle:"solid",borderColor:aj})}if(aj=X.borderColor2){e(K[1]).css({borderStyle:"dashed",borderColor:aj})}av.append(ai.add(K).add(V).add(O));if(e.browser.msie){if(aj=ab.css("filter").match(/opacity=(\d+)/)){ab.css("opacity",aj[1]/100)}if(aj=K.css("filter").match(/opacity=(\d+)/)){K.css("opacity",aj[1]/100)}}if(h.hide){ak(av.add(ab))}else{if(h.show&&Z){N=true;av.add(ab).fadeIn(X.fadeSpeed||0);u()}}Y=(au=(X.aspectRatio||"").split(/:/))[0]/au[1];az.add(ab).unbind("mousedown",m);if(X.disable||X.enable===false){av.unbind("mousemove",F).unbind("mousedown",t);e(window).unbind("resize",v)}else{if(X.enable||X.disable===false){if(X.resizable||X.movable){av.mousemove(F).mousedown(t)}e(window).resize(v)}if(!X.persistent){az.add(ab).mousedown(m)}}X.enable=X.disable=undefined}this.remove=function(){M({disable:true});av.add(ab).remove()};this.getOptions=function(){return X};this.setOptions=M;this.getSelection=G;this.setSelection=ah;this.cancelSelection=at;this.update=u;l=az;while(l.length){D=a(D,!isNaN(l.css("z-index"))?l.css("z-index"):D);if(l.css("position")=="fixed"){ag="fixed"}l=l.parent(":not(body)")}D=X.zIndex||D;if(e.browser.msie){az.attr("unselectable","on")}e.imgAreaSelect.keyPress=e.browser.msie||e.browser.safari?"keydown":"keypress";if(e.browser.opera){V=f().css({width:"100%",height:"100%",position:"absolute",zIndex:D+2||2})}av.add(ab).css({visibility:"hidden",position:ag,overflow:"hidden",zIndex:D||"0"});av.css({zIndex:D+2||2});ai.add(K).css({position:"absolute",fontSize:0});s.complete||s.readyState=="complete"||!az.is("img")?ax():az.one("load",ax);if(!Z&&e.browser.msie&&e.browser.version>=7){s.src=s.src}};e.fn.imgAreaSelect=function(g){g=g||{};this.each(function(){if(e(this).data("imgAreaSelect")){if(g.remove){e(this).data("imgAreaSelect").remove();e(this).removeData("imgAreaSelect")}else{e(this).data("imgAreaSelect").setOptions(g)}}else{if(!g.remove){if(g.enable===undefined&&g.disable===undefined){g.enable=true}e(this).data("imgAreaSelect",new e.imgAreaSelect(this,g))}}});if(g.instance){return e(this).data("imgAreaSelect")}return this}})(jQuery);
=======
/*
 * imgAreaSelect jQuery plugin
 * version 0.9.10
 *
 * Copyright (c) 2008-2013 Michal Wojciechowski (odyniec.net)
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 * http://odyniec.net/projects/imgareaselect/
 *
 */

(function($) {

/*
 * Math functions will be used extensively, so it's convenient to make a few
 * shortcuts
 */
var abs = Math.abs,
    max = Math.max,
    min = Math.min,
    round = Math.round;

/**
 * Create a new HTML div element
 *
 * @return A jQuery object representing the new element
 */
function div() {
    return $('<div/>');
}

/**
 * imgAreaSelect initialization
 *
 * @param img
 *            A HTML image element to attach the plugin to
 * @param options
 *            An options object
 */
$.imgAreaSelect = function (img, options) {
    var
        /* jQuery object representing the image */
        $img = $(img),

        /* Has the image finished loading? */
        imgLoaded,

        /* Plugin elements */

        /* Container box */
        $box = div(),
        /* Selection area */
        $area = div(),
        /* Border (four divs) */
        $border = div().add(div()).add(div()).add(div()),
        /* Outer area (four divs) */
        $outer = div().add(div()).add(div()).add(div()),
        /* Handles (empty by default, initialized in setOptions()) */
        $handles = $([]),

        /*
         * Additional element to work around a cursor problem in Opera
         * (explained later)
         */
        $areaOpera,

        /* Image position (relative to viewport) */
        left, top,

        /* Image offset (as returned by .offset()) */
        imgOfs = { left: 0, top: 0 },

        /* Image dimensions (as returned by .width() and .height()) */
        imgWidth, imgHeight,

        /*
         * jQuery object representing the parent element that the plugin
         * elements are appended to
         */
        $parent,

        /* Parent element offset (as returned by .offset()) */
        parOfs = { left: 0, top: 0 },

        /* Base z-index for plugin elements */
        zIndex = 0,

        /* Plugin elements position */
        position = 'absolute',

        /* X/Y coordinates of the starting point for move/resize operations */
        startX, startY,

        /* Horizontal and vertical scaling factors */
        scaleX, scaleY,

        /* Current resize mode ("nw", "se", etc.) */
        resize,

        /* Selection area constraints */
        minWidth, minHeight, maxWidth, maxHeight,

        /* Aspect ratio to maintain (floating point number) */
        aspectRatio,

        /* Are the plugin elements currently displayed? */
        shown,

        /* Current selection (relative to parent element) */
        x1, y1, x2, y2,

        /* Current selection (relative to scaled image) */
        selection = { x1: 0, y1: 0, x2: 0, y2: 0, width: 0, height: 0 },

        /* Document element */
        docElem = document.documentElement,

        /* User agent */
        ua = navigator.userAgent,

        /* Various helper variables used throughout the code */
        $p, d, i, o, w, h, adjusted;

    /*
     * Translate selection coordinates (relative to scaled image) to viewport
     * coordinates (relative to parent element)
     */

    /**
     * Translate selection X to viewport X
     *
     * @param x
     *            Selection X
     * @return Viewport X
     */
    function viewX(x) {
        return x + imgOfs.left - parOfs.left;
    }

    /**
     * Translate selection Y to viewport Y
     *
     * @param y
     *            Selection Y
     * @return Viewport Y
     */
    function viewY(y) {
        return y + imgOfs.top - parOfs.top;
    }

    /*
     * Translate viewport coordinates to selection coordinates
     */

    /**
     * Translate viewport X to selection X
     *
     * @param x
     *            Viewport X
     * @return Selection X
     */
    function selX(x) {
        return x - imgOfs.left + parOfs.left;
    }

    /**
     * Translate viewport Y to selection Y
     *
     * @param y
     *            Viewport Y
     * @return Selection Y
     */
    function selY(y) {
        return y - imgOfs.top + parOfs.top;
    }

    /*
     * Translate event coordinates (relative to document) to viewport
     * coordinates
     */

    /**
     * Get event X and translate it to viewport X
     *
     * @param event
     *            The event object
     * @return Viewport X
     */
    function evX(event) {
        return event.pageX - parOfs.left;
    }

    /**
     * Get event Y and translate it to viewport Y
     *
     * @param event
     *            The event object
     * @return Viewport Y
     */
    function evY(event) {
        return event.pageY - parOfs.top;
    }

    /**
     * Get the current selection
     *
     * @param noScale
     *            If set to <code>true</code>, scaling is not applied to the
     *            returned selection
     * @return Selection object
     */
    function getSelection(noScale) {
        var sx = noScale || scaleX, sy = noScale || scaleY;

        return { x1: round(selection.x1 * sx),
            y1: round(selection.y1 * sy),
            x2: round(selection.x2 * sx),
            y2: round(selection.y2 * sy),
            width: round(selection.x2 * sx) - round(selection.x1 * sx),
            height: round(selection.y2 * sy) - round(selection.y1 * sy) };
    }

    /**
     * Set the current selection
     *
     * @param x1
     *            X coordinate of the upper left corner of the selection area
     * @param y1
     *            Y coordinate of the upper left corner of the selection area
     * @param x2
     *            X coordinate of the lower right corner of the selection area
     * @param y2
     *            Y coordinate of the lower right corner of the selection area
     * @param noScale
     *            If set to <code>true</code>, scaling is not applied to the
     *            new selection
     */
    function setSelection(x1, y1, x2, y2, noScale) {
        var sx = noScale || scaleX, sy = noScale || scaleY;

        selection = {
            x1: round(x1 / sx || 0),
            y1: round(y1 / sy || 0),
            x2: round(x2 / sx || 0),
            y2: round(y2 / sy || 0)
        };

        selection.width = selection.x2 - selection.x1;
        selection.height = selection.y2 - selection.y1;
    }

    /**
     * Recalculate image and parent offsets
     */
    function adjust() {
        /*
         * Do not adjust if image has not yet loaded or if width is not a
         * positive number. The latter might happen when imgAreaSelect is put
         * on a parent element which is then hidden.
         */
        if (!imgLoaded || !$img.width())
            return;

        /*
         * Get image offset. The .offset() method returns float values, so they
         * need to be rounded.
         */
        imgOfs = { left: round($img.offset().left), top: round($img.offset().top) };

        /* Get image dimensions */
        imgWidth = $img.innerWidth();
        imgHeight = $img.innerHeight();

        imgOfs.top += ($img.outerHeight() - imgHeight) >> 1;
        imgOfs.left += ($img.outerWidth() - imgWidth) >> 1;

        /* Set minimum and maximum selection area dimensions */
        minWidth = round(options.minWidth / scaleX) || 0;
        minHeight = round(options.minHeight / scaleY) || 0;
        maxWidth = round(min(options.maxWidth / scaleX || 1<<24, imgWidth));
        maxHeight = round(min(options.maxHeight / scaleY || 1<<24, imgHeight));

        /*
         * Workaround for jQuery 1.3.2 incorrect offset calculation, originally
         * observed in Safari 3. Firefox 2 is also affected.
         */
        if ($().jquery == '1.3.2' && position == 'fixed' &&
            !docElem['getBoundingClientRect'])
        {
            imgOfs.top += max(document.body.scrollTop, docElem.scrollTop);
            imgOfs.left += max(document.body.scrollLeft, docElem.scrollLeft);
        }

        /* Determine parent element offset */
        parOfs = /absolute|relative/.test($parent.css('position')) ?
            { left: round($parent.offset().left) - $parent.scrollLeft(),
                top: round($parent.offset().top) - $parent.scrollTop() } :
            position == 'fixed' ?
                { left: $(document).scrollLeft(), top: $(document).scrollTop() } :
                { left: 0, top: 0 };

        left = viewX(0);
        top = viewY(0);

        /*
         * Check if selection area is within image boundaries, adjust if
         * necessary
         */
        if (selection.x2 > imgWidth || selection.y2 > imgHeight)
            doResize();
    }

    /**
     * Update plugin elements
     *
     * @param resetKeyPress
     *            If set to <code>false</code>, this instance's keypress
     *            event handler is not activated
     */
    function update(resetKeyPress) {
        /* If plugin elements are hidden, do nothing */
        if (!shown) return;

        /*
         * Set the position and size of the container box and the selection area
         * inside it
         */
        $box.css({ left: viewX(selection.x1), top: viewY(selection.y1) })
            .add($area).width(w = selection.width).height(h = selection.height);

        /*
         * Reset the position of selection area, borders, and handles (IE6/IE7
         * position them incorrectly if we don't do this)
         */
        $area.add($border).add($handles).css({ left: 0, top: 0 });

        /* Set border dimensions */
        $border
            .width(max(w - $border.outerWidth() + $border.innerWidth(), 0))
            .height(max(h - $border.outerHeight() + $border.innerHeight(), 0));

        /* Arrange the outer area elements */
        $($outer[0]).css({ left: left, top: top,
            width: selection.x1, height: imgHeight });
        $($outer[1]).css({ left: left + selection.x1, top: top,
            width: w, height: selection.y1 });
        $($outer[2]).css({ left: left + selection.x2, top: top,
            width: imgWidth - selection.x2, height: imgHeight });
        $($outer[3]).css({ left: left + selection.x1, top: top + selection.y2,
            width: w, height: imgHeight - selection.y2 });

        w -= $handles.outerWidth();
        h -= $handles.outerHeight();

        /* Arrange handles */
        switch ($handles.length) {
        case 8:
            $($handles[4]).css({ left: w >> 1 });
            $($handles[5]).css({ left: w, top: h >> 1 });
            $($handles[6]).css({ left: w >> 1, top: h });
            $($handles[7]).css({ top: h >> 1 });
        case 4:
            $handles.slice(1,3).css({ left: w });
            $handles.slice(2,4).css({ top: h });
        }

        if (resetKeyPress !== false) {
            /*
             * Need to reset the document keypress event handler -- unbind the
             * current handler
             */
            if ($.imgAreaSelect.onKeyPress != docKeyPress)
                $(document).unbind($.imgAreaSelect.keyPress,
                    $.imgAreaSelect.onKeyPress);

            if (options.keys)
                /*
                 * Set the document keypress event handler to this instance's
                 * docKeyPress() function
                 */
                $(document)[$.imgAreaSelect.keyPress](
                    $.imgAreaSelect.onKeyPress = docKeyPress);
        }

        /*
         * Internet Explorer displays 1px-wide dashed borders incorrectly by
         * filling the spaces between dashes with white. Toggling the margin
         * property between 0 and "auto" fixes this in IE6 and IE7 (IE8 is still
         * broken). This workaround is not perfect, as it requires setTimeout()
         * and thus causes the border to flicker a bit, but I haven't found a
         * better solution.
         *
         * Note: This only happens with CSS borders, set with the borderWidth,
         * borderOpacity, borderColor1, and borderColor2 options (which are now
         * deprecated). Borders created with GIF background images are fine.
         */
        if (msie && $border.outerWidth() - $border.innerWidth() == 2) {
            $border.css('margin', 0);
            setTimeout(function () { $border.css('margin', 'auto'); }, 0);
        }
    }

    /**
     * Do the complete update sequence: recalculate offsets, update the
     * elements, and set the correct values of x1, y1, x2, and y2.
     *
     * @param resetKeyPress
     *            If set to <code>false</code>, this instance's keypress
     *            event handler is not activated
     */
    function doUpdate(resetKeyPress) {
        adjust();
        update(resetKeyPress);
        x1 = viewX(selection.x1); y1 = viewY(selection.y1);
        x2 = viewX(selection.x2); y2 = viewY(selection.y2);
    }

    /**
     * Hide or fade out an element (or multiple elements)
     *
     * @param $elem
     *            A jQuery object containing the element(s) to hide/fade out
     * @param fn
     *            Callback function to be called when fadeOut() completes
     */
    function hide($elem, fn) {
        options.fadeSpeed ? $elem.fadeOut(options.fadeSpeed, fn) : $elem.hide();
    }

    /**
     * Selection area mousemove event handler
     *
     * @param event
     *            The event object
     */
    function areaMouseMove(event) {
        var x = selX(evX(event)) - selection.x1,
            y = selY(evY(event)) - selection.y1;

        if (!adjusted) {
            adjust();
            adjusted = true;

            $box.one('mouseout', function () { adjusted = false; });
        }

        /* Clear the resize mode */
        resize = '';

        if (options.resizable) {
            /*
             * Check if the mouse pointer is over the resize margin area and set
             * the resize mode accordingly
             */
            if (y <= options.resizeMargin)
                resize = 'n';
            else if (y >= selection.height - options.resizeMargin)
                resize = 's';
            if (x <= options.resizeMargin)
                resize += 'w';
            else if (x >= selection.width - options.resizeMargin)
                resize += 'e';
        }

        $box.css('cursor', resize ? resize + '-resize' :
            options.movable ? 'move' : '');
        if ($areaOpera)
            $areaOpera.toggle();
    }

    /**
     * Document mouseup event handler
     *
     * @param event
     *            The event object
     */
    function docMouseUp(event) {
        /* Set back the default cursor */
        $('body').css('cursor', '');
        /*
         * If autoHide is enabled, or if the selection has zero width/height,
         * hide the selection and the outer area
         */
        if (options.autoHide || selection.width * selection.height == 0)
            hide($box.add($outer), function () { $(this).hide(); });

        $(document).unbind('mousemove', selectingMouseMove);
        $box.mousemove(areaMouseMove);

        options.onSelectEnd(img, getSelection());
    }

    /**
     * Selection area mousedown event handler
     *
     * @param event
     *            The event object
     * @return false
     */
    function areaMouseDown(event) {
        if (event.which != 1) return false;

        adjust();

        if (resize) {
            /* Resize mode is in effect */
            $('body').css('cursor', resize + '-resize');

            x1 = viewX(selection[/w/.test(resize) ? 'x2' : 'x1']);
            y1 = viewY(selection[/n/.test(resize) ? 'y2' : 'y1']);

            $(document).mousemove(selectingMouseMove)
                .one('mouseup', docMouseUp);
            $box.unbind('mousemove', areaMouseMove);
        }
        else if (options.movable) {
            startX = left + selection.x1 - evX(event);
            startY = top + selection.y1 - evY(event);

            $box.unbind('mousemove', areaMouseMove);

            $(document).mousemove(movingMouseMove)
                .one('mouseup', function () {
                    options.onSelectEnd(img, getSelection());

                    $(document).unbind('mousemove', movingMouseMove);
                    $box.mousemove(areaMouseMove);
                });
        }
        else
            $img.mousedown(event);

        return false;
    }

    /**
     * Adjust the x2/y2 coordinates to maintain aspect ratio (if defined)
     *
     * @param xFirst
     *            If set to <code>true</code>, calculate x2 first. Otherwise,
     *            calculate y2 first.
     */
    function fixAspectRatio(xFirst) {
        if (aspectRatio)
            if (xFirst) {
                x2 = max(left, min(left + imgWidth,
                    x1 + abs(y2 - y1) * aspectRatio * (x2 > x1 || -1)));
                y2 = round(max(top, min(top + imgHeight,
                    y1 + abs(x2 - x1) / aspectRatio * (y2 > y1 || -1))));
                x2 = round(x2);
            }
            else {
                y2 = max(top, min(top + imgHeight,
                    y1 + abs(x2 - x1) / aspectRatio * (y2 > y1 || -1)));
                x2 = round(max(left, min(left + imgWidth,
                    x1 + abs(y2 - y1) * aspectRatio * (x2 > x1 || -1))));
                y2 = round(y2);
            }
    }

    /**
     * Resize the selection area respecting the minimum/maximum dimensions and
     * aspect ratio
     */
    function doResize() {
        /*
         * Make sure the top left corner of the selection area stays within
         * image boundaries (it might not if the image source was dynamically
         * changed).
         */
        x1 = min(x1, left + imgWidth);
        y1 = min(y1, top + imgHeight);

        if (abs(x2 - x1) < minWidth) {
            /* Selection width is smaller than minWidth */
            x2 = x1 - minWidth * (x2 < x1 || -1);

            if (x2 < left)
                x1 = left + minWidth;
            else if (x2 > left + imgWidth)
                x1 = left + imgWidth - minWidth;
        }

        if (abs(y2 - y1) < minHeight) {
            /* Selection height is smaller than minHeight */
            y2 = y1 - minHeight * (y2 < y1 || -1);

            if (y2 < top)
                y1 = top + minHeight;
            else if (y2 > top + imgHeight)
                y1 = top + imgHeight - minHeight;
        }

        x2 = max(left, min(x2, left + imgWidth));
        y2 = max(top, min(y2, top + imgHeight));

        fixAspectRatio(abs(x2 - x1) < abs(y2 - y1) * aspectRatio);

        if (abs(x2 - x1) > maxWidth) {
            /* Selection width is greater than maxWidth */
            x2 = x1 - maxWidth * (x2 < x1 || -1);
            fixAspectRatio();
        }

        if (abs(y2 - y1) > maxHeight) {
            /* Selection height is greater than maxHeight */
            y2 = y1 - maxHeight * (y2 < y1 || -1);
            fixAspectRatio(true);
        }

        selection = { x1: selX(min(x1, x2)), x2: selX(max(x1, x2)),
            y1: selY(min(y1, y2)), y2: selY(max(y1, y2)),
            width: abs(x2 - x1), height: abs(y2 - y1) };

        update();

        options.onSelectChange(img, getSelection());
    }

    /**
     * Mousemove event handler triggered when the user is selecting an area
     *
     * @param event
     *            The event object
     * @return false
     */
    function selectingMouseMove(event) {
        x2 = /w|e|^$/.test(resize) || aspectRatio ? evX(event) : viewX(selection.x2);
        y2 = /n|s|^$/.test(resize) || aspectRatio ? evY(event) : viewY(selection.y2);

        doResize();

        return false;
    }

    /**
     * Move the selection area
     *
     * @param newX1
     *            New viewport X1
     * @param newY1
     *            New viewport Y1
     */
    function doMove(newX1, newY1) {
        x2 = (x1 = newX1) + selection.width;
        y2 = (y1 = newY1) + selection.height;

        $.extend(selection, { x1: selX(x1), y1: selY(y1), x2: selX(x2),
            y2: selY(y2) });

        update();

        options.onSelectChange(img, getSelection());
    }

    /**
     * Mousemove event handler triggered when the selection area is being moved
     *
     * @param event
     *            The event object
     * @return false
     */
    function movingMouseMove(event) {
        x1 = max(left, min(startX + evX(event), left + imgWidth - selection.width));
        y1 = max(top, min(startY + evY(event), top + imgHeight - selection.height));

        doMove(x1, y1);

        event.preventDefault();
        return false;
    }

    /**
     * Start selection
     */
    function startSelection() {
        $(document).unbind('mousemove', startSelection);
        adjust();

        x2 = x1;
        y2 = y1;
        doResize();

        resize = '';

        if (!$outer.is(':visible'))
            /* Show the plugin elements */
            $box.add($outer).hide().fadeIn(options.fadeSpeed||0);

        shown = true;

        $(document).unbind('mouseup', cancelSelection)
            .mousemove(selectingMouseMove).one('mouseup', docMouseUp);
        $box.unbind('mousemove', areaMouseMove);

        options.onSelectStart(img, getSelection());
    }

    /**
     * Cancel selection
     */
    function cancelSelection() {
        $(document).unbind('mousemove', startSelection)
            .unbind('mouseup', cancelSelection);
        hide($box.add($outer));

        setSelection(selX(x1), selY(y1), selX(x1), selY(y1));

        /* If this is an API call, callback functions should not be triggered */
        if (!(this instanceof $.imgAreaSelect)) {
            options.onSelectChange(img, getSelection());
            options.onSelectEnd(img, getSelection());
        }
    }

    /**
     * Image mousedown event handler
     *
     * @param event
     *            The event object
     * @return false
     */
    function imgMouseDown(event) {
        /* Ignore the event if animation is in progress */
        if (event.which != 1 || $outer.is(':animated')) return false;

        adjust();
        startX = x1 = evX(event);
        startY = y1 = evY(event);

        /* Selection will start when the mouse is moved */
        $(document).mousemove(startSelection).mouseup(cancelSelection);

        return false;
    }

    /**
     * Window resize event handler
     */
    function windowResize() {
        doUpdate(false);
    }

    /**
     * Image load event handler. This is the final part of the initialization
     * process.
     */
    function imgLoad() {
        imgLoaded = true;

        /* Set options */
        setOptions(options = $.extend({
            classPrefix: 'imgareaselect',
            movable: true,
            parent: 'body',
            resizable: true,
            resizeMargin: 10,
            onInit: function () {},
            onSelectStart: function () {},
            onSelectChange: function () {},
            onSelectEnd: function () {}
        }, options));

        $box.add($outer).css({ visibility: '' });

        if (options.show) {
            shown = true;
            adjust();
            update();
            $box.add($outer).hide().fadeIn(options.fadeSpeed||0);
        }

        /*
         * Call the onInit callback. The setTimeout() call is used to ensure
         * that the plugin has been fully initialized and the object instance is
         * available (so that it can be obtained in the callback).
         */
        setTimeout(function () { options.onInit(img, getSelection()); }, 0);
    }

    /**
     * Document keypress event handler
     *
     * @param event
     *            The event object
     * @return false
     */
    var docKeyPress = function(event) {
        var k = options.keys, d, t, key = event.keyCode;

        d = !isNaN(k.alt) && (event.altKey || event.originalEvent.altKey) ? k.alt :
            !isNaN(k.ctrl) && event.ctrlKey ? k.ctrl :
            !isNaN(k.shift) && event.shiftKey ? k.shift :
            !isNaN(k.arrows) ? k.arrows : 10;

        if (k.arrows == 'resize' || (k.shift == 'resize' && event.shiftKey) ||
            (k.ctrl == 'resize' && event.ctrlKey) ||
            (k.alt == 'resize' && (event.altKey || event.originalEvent.altKey)))
        {
            /* Resize selection */

            switch (key) {
            case 37:
                /* Left */
                d = -d;
            case 39:
                /* Right */
                t = max(x1, x2);
                x1 = min(x1, x2);
                x2 = max(t + d, x1);
                fixAspectRatio();
                break;
            case 38:
                /* Up */
                d = -d;
            case 40:
                /* Down */
                t = max(y1, y2);
                y1 = min(y1, y2);
                y2 = max(t + d, y1);
                fixAspectRatio(true);
                break;
            default:
                return;
            }

            doResize();
        }
        else {
            /* Move selection */

            x1 = min(x1, x2);
            y1 = min(y1, y2);

            switch (key) {
            case 37:
                /* Left */
                doMove(max(x1 - d, left), y1);
                break;
            case 38:
                /* Up */
                doMove(x1, max(y1 - d, top));
                break;
            case 39:
                /* Right */
                doMove(x1 + min(d, imgWidth - selX(x2)), y1);
                break;
            case 40:
                /* Down */
                doMove(x1, y1 + min(d, imgHeight - selY(y2)));
                break;
            default:
                return;
            }
        }

        return false;
    };

    /**
     * Apply style options to plugin element (or multiple elements)
     *
     * @param $elem
     *            A jQuery object representing the element(s) to style
     * @param props
     *            An object that maps option names to corresponding CSS
     *            properties
     */
    function styleOptions($elem, props) {
        for (var option in props)
            if (options[option] !== undefined)
                $elem.css(props[option], options[option]);
    }

    /**
     * Set plugin options
     *
     * @param newOptions
     *            The new options object
     */
    function setOptions(newOptions) {
        if (newOptions.parent)
            ($parent = $(newOptions.parent)).append($box.add($outer));

        /* Merge the new options with the existing ones */
        $.extend(options, newOptions);

        adjust();

        if (newOptions.handles != null) {
            /* Recreate selection area handles */
            $handles.remove();
            $handles = $([]);

            i = newOptions.handles ? newOptions.handles == 'corners' ? 4 : 8 : 0;

            while (i--)
                $handles = $handles.add(div());

            /* Add a class to handles and set the CSS properties */
            $handles.addClass(options.classPrefix + '-handle').css({
                position: 'absolute',
                /*
                 * The font-size property needs to be set to zero, otherwise
                 * Internet Explorer makes the handles too large
                 */
                fontSize: 0,
                zIndex: zIndex + 1 || 1
            });

            /*
             * If handle width/height has not been set with CSS rules, set the
             * default 5px
             */
            if (!parseInt($handles.css('width')) >= 0)
                $handles.width(5).height(5);

            /*
             * If the borderWidth option is in use, add a solid border to
             * handles
             */
            if (o = options.borderWidth)
                $handles.css({ borderWidth: o, borderStyle: 'solid' });

            /* Apply other style options */
            styleOptions($handles, { borderColor1: 'border-color',
                borderColor2: 'background-color',
                borderOpacity: 'opacity' });
        }

        /* Calculate scale factors */
        scaleX = options.imageWidth / imgWidth || 1;
        scaleY = options.imageHeight / imgHeight || 1;

        /* Set selection */
        if (newOptions.x1 != null) {
            setSelection(newOptions.x1, newOptions.y1, newOptions.x2,
                newOptions.y2);
            newOptions.show = !newOptions.hide;
        }

        if (newOptions.keys)
            /* Enable keyboard support */
            options.keys = $.extend({ shift: 1, ctrl: 'resize' },
                newOptions.keys);

        /* Add classes to plugin elements */
        $outer.addClass(options.classPrefix + '-outer');
        $area.addClass(options.classPrefix + '-selection');
        for (i = 0; i++ < 4;)
            $($border[i-1]).addClass(options.classPrefix + '-border' + i);

        /* Apply style options */
        styleOptions($area, { selectionColor: 'background-color',
            selectionOpacity: 'opacity' });
        styleOptions($border, { borderOpacity: 'opacity',
            borderWidth: 'border-width' });
        styleOptions($outer, { outerColor: 'background-color',
            outerOpacity: 'opacity' });
        if (o = options.borderColor1)
            $($border[0]).css({ borderStyle: 'solid', borderColor: o });
        if (o = options.borderColor2)
            $($border[1]).css({ borderStyle: 'dashed', borderColor: o });

        /* Append all the selection area elements to the container box */
        $box.append($area.add($border).add($areaOpera)).append($handles);

        if (msie) {
            if (o = ($outer.css('filter')||'').match(/opacity=(\d+)/))
                $outer.css('opacity', o[1]/100);
            if (o = ($border.css('filter')||'').match(/opacity=(\d+)/))
                $border.css('opacity', o[1]/100);
        }

        if (newOptions.hide)
            hide($box.add($outer));
        else if (newOptions.show && imgLoaded) {
            shown = true;
            $box.add($outer).fadeIn(options.fadeSpeed||0);
            doUpdate();
        }

        /* Calculate the aspect ratio factor */
        aspectRatio = (d = (options.aspectRatio || '').split(/:/))[0] / d[1];

        $img.add($outer).unbind('mousedown', imgMouseDown);

        if (options.disable || options.enable === false) {
            /* Disable the plugin */
            $box.unbind('mousemove', areaMouseMove).unbind('mousedown', areaMouseDown);
            $(window).unbind('resize', windowResize);
        }
        else {
            if (options.enable || options.disable === false) {
                /* Enable the plugin */
                if (options.resizable || options.movable)
                    $box.mousemove(areaMouseMove).mousedown(areaMouseDown);

                $(window).resize(windowResize);
            }

            if (!options.persistent)
                $img.add($outer).mousedown(imgMouseDown);
        }

        options.enable = options.disable = undefined;
    }

    /**
     * Remove plugin completely
     */
    this.remove = function () {
        /*
         * Call setOptions with { disable: true } to unbind the event handlers
         */
        setOptions({ disable: true });
        $box.add($outer).remove();
    };

    /*
     * Public API
     */

    /**
     * Get current options
     *
     * @return An object containing the set of options currently in use
     */
    this.getOptions = function () { return options; };

    /**
     * Set plugin options
     *
     * @param newOptions
     *            The new options object
     */
    this.setOptions = setOptions;

    /**
     * Get the current selection
     *
     * @param noScale
     *            If set to <code>true</code>, scaling is not applied to the
     *            returned selection
     * @return Selection object
     */
    this.getSelection = getSelection;

    /**
     * Set the current selection
     *
     * @param x1
     *            X coordinate of the upper left corner of the selection area
     * @param y1
     *            Y coordinate of the upper left corner of the selection area
     * @param x2
     *            X coordinate of the lower right corner of the selection area
     * @param y2
     *            Y coordinate of the lower right corner of the selection area
     * @param noScale
     *            If set to <code>true</code>, scaling is not applied to the
     *            new selection
     */
    this.setSelection = setSelection;

    /**
     * Cancel selection
     */
    this.cancelSelection = cancelSelection;

    /**
     * Update plugin elements
     *
     * @param resetKeyPress
     *            If set to <code>false</code>, this instance's keypress
     *            event handler is not activated
     */
    this.update = doUpdate;

    /* Do the dreaded browser detection */
    var msie = (/msie ([\w.]+)/i.exec(ua)||[])[1],
        opera = /opera/i.test(ua),
        safari = /webkit/i.test(ua) && !/chrome/i.test(ua);

    /*
     * Traverse the image's parent elements (up to <body>) and find the
     * highest z-index
     */
    $p = $img;

    while ($p.length) {
        zIndex = max(zIndex,
            !isNaN($p.css('z-index')) ? $p.css('z-index') : zIndex);
        /* Also check if any of the ancestor elements has fixed position */
        if ($p.css('position') == 'fixed')
            position = 'fixed';

        $p = $p.parent(':not(body)');
    }

    /*
     * If z-index is given as an option, it overrides the one found by the
     * above loop
     */
    zIndex = options.zIndex || zIndex;

    if (msie)
        $img.attr('unselectable', 'on');

    /*
     * In MSIE and WebKit, we need to use the keydown event instead of keypress
     */
    $.imgAreaSelect.keyPress = msie || safari ? 'keydown' : 'keypress';

    /*
     * There is a bug affecting the CSS cursor property in Opera (observed in
     * versions up to 10.00) that prevents the cursor from being updated unless
     * the mouse leaves and enters the element again. To trigger the mouseover
     * event, we're adding an additional div to $box and we're going to toggle
     * it when mouse moves inside the selection area.
     */
    if (opera)
        $areaOpera = div().css({ width: '100%', height: '100%',
            position: 'absolute', zIndex: zIndex + 2 || 2 });

    /*
     * We initially set visibility to "hidden" as a workaround for a weird
     * behaviour observed in Google Chrome 1.0.154.53 (on Windows XP). Normally
     * we would just set display to "none", but, for some reason, if we do so
     * then Chrome refuses to later display the element with .show() or
     * .fadeIn().
     */
    $box.add($outer).css({ visibility: 'hidden', position: position,
        overflow: 'hidden', zIndex: zIndex || '0' });
    $box.css({ zIndex: zIndex + 2 || 2 });
    $area.add($border).css({ position: 'absolute', fontSize: 0 });

    /*
     * If the image has been fully loaded, or if it is not really an image (eg.
     * a div), call imgLoad() immediately; otherwise, bind it to be called once
     * on image load event.
     */
    img.complete || img.readyState == 'complete' || !$img.is('img') ?
        imgLoad() : $img.one('load', imgLoad);

    /*
     * MSIE 9.0 doesn't always fire the image load event -- resetting the src
     * attribute seems to trigger it. The check is for version 7 and above to
     * accommodate for MSIE 9 running in compatibility mode.
     */
    if (!imgLoaded && msie && msie >= 7)
        img.src = img.src;
};

/**
 * Invoke imgAreaSelect on a jQuery object containing the image(s)
 *
 * @param options
 *            Options object
 * @return The jQuery object or a reference to imgAreaSelect instance (if the
 *         <code>instance</code> option was specified)
 */
$.fn.imgAreaSelect = function (options) {
    options = options || {};

    this.each(function () {
        /* Is there already an imgAreaSelect instance bound to this element? */
        if ($(this).data('imgAreaSelect')) {
            /* Yes there is -- is it supposed to be removed? */
            if (options.remove) {
                /* Remove the plugin */
                $(this).data('imgAreaSelect').remove();
                $(this).removeData('imgAreaSelect');
            }
            else
                /* Reset options */
                $(this).data('imgAreaSelect').setOptions(options);
        }
        else if (!options.remove) {
            /* No exising instance -- create a new one */

            /*
             * If neither the "enable" nor the "disable" option is present, add
             * "enable" as the default
             */
            if (options.enable === undefined && options.disable === undefined)
                options.enable = true;

            $(this).data('imgAreaSelect', new $.imgAreaSelect(this, options));
        }
    });

    if (options.instance)
        /*
         * Return the imgAreaSelect instance bound to the first element in the
         * set
         */
        return $(this).data('imgAreaSelect');

    return this;
};

})(jQuery);
>>>>>>> 3.5.1
