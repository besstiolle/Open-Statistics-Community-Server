/**
 * Title: jqPlot Charts
 * 
 * Pure JavaScript plotting plugin for jQuery.
 * 
 * About: Version
 * 
 * @VERSION 
 * 
 * About: Copyright & License
 * 
 * Copyright (c) 2009 Chris Leonello
 * jqPlot is currently available for use in all personal or commercial projects 
 * under both the MIT and GPL version 2.0 licenses. This means that you can 
 * choose the license that best suits your project and use it accordingly.
 * 
 * See <GPL Version 2> and <MIT License> contained within this distribution for further information. 
 *
 * The author would appreciate an email letting him know of any substantial
 * use of jqPlot.  You can reach the author at: chris dot leonello at gmail 
 * dot com or see http://www.jqplot.com/info.php.  This is, of course, not required.
 *
 * If you are feeling kind and generous, consider supporting the project by
 * making a donation at: http://www.jqplot.com/donate.php.
 *
 * 
 * About: Introduction
 * 
 * jqPlot requires jQuery (tested with 1.3.2 or better). jQuery 1.3.2 is included in the distribution.  
 * To use jqPlot include jQuery, the jqPlot jQuery plugin, the jqPlot css file and optionally 
 * the excanvas script for IE support in your web page:
 * 
 * > <!--[if IE]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
 * > <script language="javascript" type="text/javascript" src="jquery-1.3.2.min.js"></script>
 * > <script language="javascript" type="text/javascript" src="jquery.jqplot.min.js"></script>
 * > <link rel="stylesheet" type="text/css" href="jquery.jqplot.css" />
 * 
 * jqPlot can be customized by overriding the defaults of any of the objects which make
 * up the plot. The general usage of jqplot is:
 * 
 * > chart = $.jqplot('targetElemId', [dataArray,...], {optionsObject});
 * 
 * The options available to jqplot are detailed in <jqPlot Options> in the jqPlotOptions.txt file.
 * 
 * An actual call to $.jqplot() may look like the 
 * examples below:
 * 
 * > chart = $.jqplot('chartdiv',  [[[1, 2],[3,5.12],[5,13.1],[7,33.6],[9,85.9],[11,219.9]]]);
 * 
 * or
 * 
 * > dataArray = [34,12,43,55,77];
 * > chart = $.jqplot('targetElemId', [dataArray, ...], {title:'My Plot', axes:{yaxis:{min:20, max:100}}});
 * 
 * For more inforrmation, see <jqPlot Usage>.
 * 
 * About: Usage
 * 
 * See <jqPlot Usage>
 * 
 * About: Available Options 
 * 
 * See <jqPlot Options> for a list of options available thorugh the options object (not complete yet!)
 * 
 * About: Options Usage
 * 
 * See <Options Tutorial>
 * 
 * About: Changes
 * 
 * See <Change Log>
 * 
 */

(function($) {
    // make sure undefined is undefined
    var undefined;

    /**
     * Class: $.jqplot
     * jQuery function called by the user to create a plot.
     *  
     * Parameters:
     * target - ID of target element to render the plot into.
     * data - an array of data series.
     * options - user defined options object.  See the individual classes for available options.
     * 
     * Properties:
     * config - object to hold configuration information for jqPlot plot object.
     * 
     * attributes:
     * enablePlugins - False to disable plugins by default.  Plugins must then be explicitly 
     *   enabled in the individual plot options.  Default: true.
     *   This property sets the "show" property of certain plugins to true or false.
     *   Only plugins that can be immediately active upon loading are affected.  This includes
     *   non-renderer plugins like cursor, dragable, highlighter, and trendline.
     * defaultHeight - Default height for plots where no css height specification exists.  This
     *   is a jqplot wide default.
     * defaultWidth - Default height for plots where no css height specification exists.  This
     *   is a jqplot wide default.
     */

    $.jqplot = function(target, data, options) {
        var _data, _options;
        
        // check to see if only 2 arguments were specified, what is what.
        if (data == null) {
            throw "No data specified";
        }
        if (data.constructor == Array && data.length == 0 || data[0].constructor != Array) {
            throw "Improper Data Array";
        }
        if (options == null) {
            if (data instanceof Array) {
                _data = data;
                _options = null;   
            }
            
            else if (data.constructor == Object) {
                _data = null;
                _options = data;
            }
        }
        else {
            _data = data;
            _options = options;
        }
        var plot = new jqPlot();
        plot.init(target, _data, _options);
        plot.draw();
        return plot;
    };
        
    $.jqplot.debug = 1;
    $.jqplot.config = {
        debug:1,
        enablePlugins:true,
        defaultHeight:300,
        defaultWidth:400
    };
    
    $.jqplot.enablePlugins = $.jqplot.config.enablePlugins;
    
    /**
     * 
     * Hooks: jqPlot Pugin Hooks
     * 
     * $.jqplot.preInitHooks - called before initialization.
     * $.jqplot.postInitHooks - called after initialization.
     * $.jqplot.preParseOptionsHooks - called before user options are parsed.
     * $.jqplot.postParseOptionsHooks - called after user options are parsed.
     * $.jqplot.preDrawHooks - called before plot draw.
     * $.jqplot.postDrawHooks - called after plot draw.
     * $.jqplot.preDrawSeriesHooks - called before each series is drawn.
     * $.jqplot.postDrawSeriesHooks - called after each series is drawn.
     * $.jqplot.preDrawLegendHooks - called before the legend is drawn.
     * $.jqplot.addLegendRowHooks - called at the end of legend draw, so plugins
     *     can add rows to the legend table.
     * $.jqplot.preSeriesInitHooks - called before series is initialized.
     * $.jqplot.postSeriesInitHooks - called after series is initialized.
     * $.jqplot.preParseSeriesOptionsHooks - called before series related options
     *     are parsed.
     * $.jqplot.postParseSeriesOptionsHooks - called after series related options
     *     are parsed.
     * $.jqplot.eventListenerHooks - called at the end of plot drawing, binds
     *     listeners to the event canvas which lays on top of the grid area.
     * $.jqplot.preDrawSeriesShadowHooks - called before series shadows are drawn.
     * $.jqplot.postDrawSeriesShadowHooks - called after series shadows are drawn.
     * 
     */
    
    $.jqplot.preInitHooks = [];
    $.jqplot.postInitHooks = [];
    $.jqplot.preParseOptionsHooks = [];
    $.jqplot.postParseOptionsHooks = [];
    $.jqplot.preDrawHooks = [];
    $.jqplot.postDrawHooks = [];
    $.jqplot.preDrawSeriesHooks = [];
    $.jqplot.postDrawSeriesHooks = [];
    $.jqplot.preDrawLegendHooks = [];
    $.jqplot.addLegendRowHooks = [];
    $.jqplot.preSeriesInitHooks = [];
    $.jqplot.postSeriesInitHooks = [];
    $.jqplot.preParseSeriesOptionsHooks = [];
    $.jqplot.postParseSeriesOptionsHooks = [];
    $.jqplot.eventListenerHooks = [];
    $.jqplot.preDrawSeriesShadowHooks = [];
    $.jqplot.postDrawSeriesShadowHooks = [];

    // A superclass holding some common properties and methods.
    $.jqplot.ElemContainer = function() {
        this._elem;
        this._plotWidth;
        this._plotHeight;
        this._plotDimensions = {height:null, width:null};
    };
    
    $.jqplot.ElemContainer.prototype.getWidth = function() {
        if (this._elem) {
            return this._elem.outerWidth(true);
        }
        else {
            return null;
        }
    };
    
    $.jqplot.ElemContainer.prototype.getHeight = function() {
        if (this._elem) {
            return this._elem.outerHeight(true);
        }
        else {
            return null;
        }
    };
    
    $.jqplot.ElemContainer.prototype.getPosition = function() {
        if (this._elem) {
            return this._elem.position();
        }
        else {
            return {top:null, left:null, bottom:null, right:null};
        }
    };
    
    $.jqplot.ElemContainer.prototype.getTop = function() {
        return this.getPosition().top;
    };
    
    $.jqplot.ElemContainer.prototype.getLeft = function() {
        return this.getPosition().left;
    };
    
    $.jqplot.ElemContainer.prototype.getBottom = function() {
        return this._elem.css('bottom');
    };
    
    $.jqplot.ElemContainer.prototype.getRight = function() {
        return this._elem.css('right');
    };
    

    /**
     * Class: Axis
     * An individual axis object.  Cannot be instantiated directly, but created
     * by the Plot oject.  Axis properties can be set or overriden by the 
     * options passed in from the user.
     * 
     */
    function Axis(name) {
        $.jqplot.ElemContainer.call(this);
        // Group: Properties
        //
        // Axes options are specified within an axes object at the top level of the 
        // plot options like so:
        // > {
        // >    axes: {
        // >        xaxis: {min: 5},
        // >        yaxis: {min: 2, max: 8, numberTicks:4},
        // >        x2axis: {pad: 1.5},
        // >        y2axis: {ticks:[22, 44, 66, 88]}
        // >        }
        // > }
        // There are 4 axes, 'xaxis', 'yaxis', 'x2axis', 'y2axis'.  Any or all of 
        // which may be specified.
        this.name = name;
        this._series = [];
        // prop: show
        // Wether to display the axis on the graph.
        this.show = false;
        // prop: tickRenderer
        // A class of a rendering engine for creating the ticks labels displayed on the plot, 
        // See <$.jqplot.AxisTickRenderer>.
        this.tickRenderer = $.jqplot.AxisTickRenderer;
        // prop: tickOptions
        // Options that will be passed to the tickRenderer, see <$.jqplot.AxisTickRenderer> options.
        this.tickOptions = {};
        // prop: labelRenderer
        // A class of a rendering engine for creating an axis label.
        this.labelRenderer = $.jqplot.AxisLabelRenderer;
        // prop: labelOptions
        // Options passed to the label renderer.
        this.labelOptions = {};
        // prop: label
        // Label for the axis
        this.label = null;
        // prop: showLabel
        // true to show the axis label.
        this.showLabel = true;
        // prop: min
        // minimum value of the axis (in data units, not pixels).
        this.min=null;
        // prop: max
        // maximum value of the axis (in data units, not pixels).
        this.max=null;
        // prop: autoscale
        // Autoscale the axis min and max values to provide sensible tick spacing.
        // If axis min or max are set, autoscale will be turned off.
        // The numberTicks, tickInterval and pad options do work with 
        // autoscale, although tickInterval has not been tested yet.
        // padMin and padMax do nothing when autoscale is on.
        this.autoscale = false;
        // prop: pad
        // Padding to extend the range above and below the data bounds.
        // The data range is multiplied by this factor to determine minimum and maximum axis bounds.
        // A value of 0 will be interpreted to mean no padding, and pad will be set to 1.0.
        this.pad = 1.2;
        // prop: padMax
        // Padding to extend the range above data bounds.
        // The top of the data range is multiplied by this factor to determine maximum axis bounds.
        // A value of 0 will be interpreted to mean no padding, and padMax will be set to 1.0.
        this.padMax = null;
        // prop: padMin
        // Padding to extend the range below data bounds.
        // The bottom of the data range is multiplied by this factor to determine minimum axis bounds.
        // A value of 0 will be interpreted to mean no padding, and padMin will be set to 1.0.
        this.padMin = null;
        // prop: ticks
        // 1D [val, val, ...] or 2D [[val, label], [val, label], ...] array of ticks for the axis.
        // If no label is specified, the value is formatted into an appropriate label.
        this.ticks = [];
        // prop: numberTicks
        // Desired number of ticks.  Default is to compute automatically.
        this.numberTicks;
        // prop: tickInterval
        // number of units between ticks.  Mutually exclusive with numberTicks.
        this.tickInterval;
        // prop: renderer
        // A class of a rendering engine that handles tick generation, 
        // scaling input data to pixel grid units and drawing the axis element.
        this.renderer = $.jqplot.LinearAxisRenderer;
        // prop: rendererOptions
        // renderer specific options.  See <$.jqplot.LinearAxisRenderer> for options.
        this.rendererOptions = {};
        // prop: showTicks
        // wether to show the ticks (both marks and labels) or not.
        this.showTicks = true;
        // prop: showTickMarks
        // wether to show the tick marks (line crossing grid) or not.
        this.showTickMarks = true;
        // prop: showMinorTicks
        // Wether or not to show minor ticks.  This is renderer dependent.
        // The default <$.jqplot.LinearAxisRenderer> does not have minor ticks.
        this.showMinorTicks = true;
        // prop: useSeriesColor
        // Use the color of the first series associated with this axis for the
        // tick marks and line bordering this axis.
        this.useSeriesColor = false;
        // prop: borderWidth
        // width of line stroked at the border of the axis.  Defaults
        // to the width of the grid boarder.
        this.borderWidth = null;
        // prop: borderColor
        // color of the border adjacent to the axis.  Defaults to grid border color.
        this.borderColor = null;
        // minimum and maximum values on the axis.
        this._dataBounds = {min:null, max:null};
        // pixel position from the top left of the min value and max value on the axis.
        this._offsets = {min:null, max:null};
        this._ticks=[];
        this._label = null;
        // prop: syncTicks
        // true to try and synchronize tick spacing across multiple axes so that ticks and
        // grid lines line up.  This has an impact on autoscaling algorithm, however.
        // In general, autoscaling an individual axis will work better if it does not
        // have to sync ticks.
        this.syncTicks = null;
        // prop: tickSpacing
        // Approximate pixel spacing between ticks on graph.  Used during autoscaling.
        // This number will be an upper bound, actual spacing will be less.
        this.tickSpacing = 75;
        // Properties to hold the original values for min, max, ticks, tickInterval and numberTicks
        // so they can be restored if altered by plugins.
        this._min = null;
        this._max = null;
        this._tickInterval = null;
        this._numberTicks = null;
        this.__ticks = null;
    }
    
    Axis.prototype = new $.jqplot.ElemContainer();
    Axis.prototype.constructor = Axis;
    
    Axis.prototype.init = function() {
        this.renderer = new this.renderer();
        // set the axis name
        this.tickOptions.axis = this.name;
        if (this.label == null || this.label == '') {
            this.showLabel = false;
        }
        else {
            this.labelOptions.label = this.label;
        }
        if (this.showLabel == false) {
            this.labelOptions.show = false;
        }
        // set the default padMax, padMin if not specified
        // special check, if no padding desired, padding
        // should be set to 1.0
        if (this.pad == 0) {
            this.pad = 1.0;
        }
        if (this.padMax == 0) {
            this.padMax = 1.0;
        }
        if (this.padMin == 0) {
            this.padMin = 1.0;
        }
        if (this.padMax == null) {
            this.padMax = (this.pad-1)/2 + 1;
        }
        if (this.padMin == null) {
            this.padMin = (this.pad-1)/2 + 1;
        }
        // now that padMin and padMax are correctly set, reset pad in case user has supplied 
        // padMin and/or padMax
        this.pad = this.padMax + this.padMin - 1;
        if (this.min != null || this.max != null) {
            this.autoscale = false;
        }
        // if not set, sync ticks for y axes but not x by default.
        if (this.syncTicks == null && this.name.indexOf('y') > -1) {
            this.syncTicks = true;
        }
        else if (this.syncTicks == null){
            this.syncTicks = false;
        }
        this.renderer.init.call(this, this.rendererOptions);
        
    };
    
    Axis.prototype.draw = function(ctx) {
        return this.renderer.draw.call(this, ctx);
        
    };
    
    Axis.prototype.set = function() {
        this.renderer.set.call(this);
    };
    
    Axis.prototype.pack = function(pos, offsets) {
        if (this.show) {
            this.renderer.pack.call(this, pos, offsets);
        }
        // these properties should all be available now.
        if (this._min == null) {
            this._min = this.min;
            this._max = this.max;
            this._tickInterval = this.tickInterval;
            this._numberTicks = this.numberTicks;
            this.__ticks = this._ticks;
        }
    };
    
    // reset the axis back to original values if it has been scaled, zoomed, etc.
    Axis.prototype.reset = function() {
        this.renderer.reset.call(this);
    };
    
    Axis.prototype.resetScale = function() {
        this.min = null;
        this.max = null;
        this.numberTicks = null;
        this.tickInterval = null;
    };

    /**
     * Class: Legend
     * Legend object.  Cannot be instantiated directly, but created
     * by the Plot oject.  Legend properties can be set or overriden by the 
     * options passed in from the user.
     */
    function Legend(options) {
        $.jqplot.ElemContainer.call(this);
        // Group: Properties
        
        // prop: show
        // Wether to display the legend on the graph.
        this.show = false;
        // prop: location
        // Placement of the legend.  one of the compass directions: nw, n, ne, e, se, s, sw, w
        this.location = 'ne';
        // prop: xoffset
        // offset from the inside edge of the plot in the x direction in pixels.
        this.xoffset = 12;
        // prop: yoffset
        // offset from the inside edge of the plot in the y direction in pixels.
        this.yoffset = 12;
        // prop: border
        // css spec for the border around the legend box.
        this.border;
        // prop: background
        // css spec for the background of the legend box.
        this.background;
        // prop: textColor
        // css color spec for the legend text.
        this.textColor;
        // prop: fontFamily
        // css font-family spec for the legend text.
        this.fontFamily; 
        // prop: fontSize
        // css font-size spec for the legend text.
        this.fontSize ;
        // prop: rowSpacing
        // css padding-top spec for the rows in the legend.
        this.rowSpacing = '0.5em';
        // renderer
        // A class that will create a DOM object for the legend,
        // see <$.jqplot.TableLegendRenderer>.
        this.renderer = $.jqplot.TableLegendRenderer;
        // prop: rendererOptions
        // renderer specific options passed to the renderer.
        this.rendererOptions = {};
        // prop: predraw
        // Wether to draw the legend before the series or not.
        this.preDraw = false;
        this.escapeHtml = false;
        this._series = [];
        
        $.extend(true, this, options);
    }
    
    Legend.prototype = new $.jqplot.ElemContainer();
    Legend.prototype.constructor = Legend;
    
    Legend.prototype.init = function() {
        this.renderer = new this.renderer();
        this.renderer.init.call(this, this.rendererOptions);
    };
    
    Legend.prototype.draw = function(offsets) {
        for (var i=0; i<$.jqplot.preDrawLegendHooks.length; i++){
            $.jqplot.preDrawLegendHooks[i].call(this, offsets);
        }
        return this.renderer.draw.call(this, offsets);
    };
    
    Legend.prototype.pack = function(offsets) {
        this.renderer.pack.call(this, offsets);
    };

    /**
     * Class: Title
     * Plot Title object.  Cannot be instantiated directly, but created
     * by the Plot oject.  Title properties can be set or overriden by the 
     * options passed in from the user.
     * 
     * Parameters:
     * text - text of the title.
     */
    function Title(text) {
        $.jqplot.ElemContainer.call(this);
        // Group: Properties
        
        // prop: text
        // text of the title;
        this.text = text;
        // prop: show
        // wether or not to show the title
        this.show = true;
        // prop: fontFamily
        // css font-family spec for the text.
        this.fontFamily;
        // prop: fontSize
        // css font-size spec for the text.
        this.fontSize ;
        // prop: textAlign
        // css text-align spec for the text.
        this.textAlign;
        // prop: textColor
        // css color spec for the text.
        this.textColor;
        // prop: renderer
        // A class for creating a DOM element for the title,
        // see <$.jqplot.DivTitleRenderer>.
        this.renderer = $.jqplot.DivTitleRenderer;
        // prop: rendererOptions
        // renderer specific options passed to the renderer.
        this.rendererOptions = {};   
    }
    
    Title.prototype = new $.jqplot.ElemContainer();
    Title.prototype.constructor = Title;
    
    Title.prototype.init = function() {
        this.renderer = new this.renderer();
        this.renderer.init.call(this, this.rendererOptions);
    };
    
    Title.prototype.draw = function(width) {
        return this.renderer.draw.call(this, width);
    };
    
    Title.prototype.pack = function() {
        this.renderer.pack.call(this);
    };


    /**
     * Class: Series
     * An individual data series object.  Cannot be instantiated directly, but created
     * by the Plot oject.  Series properties can be set or overriden by the 
     * options passed in from the user.
     */
    function Series() {
        $.jqplot.ElemContainer.call(this);
        // Group: Properties
        // Properties will be assigned from a series array at the top level of the
        // options.  If you had two series and wanted to change the color and line
        // width of the first and set the second to use the secondary y axis with
        // no shadow and supply custom labels for each:
        // > {
        // >    series:[
        // >        {color: '#ff4466', lineWidth: 5, label:'good line'},
        // >        {yaxis: 'y2axis', shadow: false, label:'bad line'}
        // >    ]
        // > }
        
        // prop: show
        // wether or not to draw the series.
        this.show = true;
        // prop: xaxis
        // which x axis to use with this series, either 'xaxis' or 'x2axis'.
        this.xaxis = 'xaxis';
        this._xaxis;
        // prop: yaxis
        // which y axis to use with this series, either 'yaxis' or 'y2axis'.
        this.yaxis = 'yaxis';
        this._yaxis;
        this.gridBorderWidth = 2.0;
        // prop: renderer
        // A class of a renderer which will draw the series, 
        // see <$.jqplot.LineRenderer>.
        this.renderer = $.jqplot.LineRenderer;
        // prop: rendererOptions
        // Options to pass on to the renderer.
        this.rendererOptions = {};
        this.data = [];
        this.gridData = [];
        // prop: label
        // Line label to use in the legend.
        this.label = '';
        // prop: showLabel
        // true to show label for this series in the legend.
        this.showLabel = true;
        // prop: color
        // css color spec for the series
        this.color;
        // prop: lineWidth
        // width of the line in pixels.  May have different meanings depending on renderer.
        this.lineWidth = 2.5;
        // prop: shadow
        // wether or not to draw a shadow on the line
        this.shadow = true;
        // prop: shadowAngle
        // Shadow angle in degrees
        this.shadowAngle = 45;
        // prop: shadowOffset
        // Shadow offset from line in pixels
        this.shadowOffset = 1.25;
        // prop: shadowDepth
        // Number of times shadow is stroked, each stroke offset shadowOffset from the last.
        this.shadowDepth = 3;
        // prop: shadowAlpha
        // Alpha channel transparency of shadow.  0 = transparent.
        this.shadowAlpha = '0.1';
        // prop: breakOnNull
        // Not implemented. wether line segments should be be broken at null value.
        // False will join point on either side of line.
        this.breakOnNull = false;
        // prop: markerRenderer
        // A class of a renderer which will draw marker (e.g. circle, square, ...) at the data points,
        // see <$.jqplot.MarkerRenderer>.
        this.markerRenderer = $.jqplot.MarkerRenderer;
        // prop: markerOptions
        // renderer specific options to pass to the markerRenderer,
        // see <$.jqplot.MarkerRenderer>.
        this.markerOptions = {};
        // prop: showLine
        // wether to actually draw the line or not.  Series will still be renderered, even if no line is drawn.
        this.showLine = true;
        // prop: showMarker
        // wether or not to show the markers at the data points.
        this.showMarker = true;
        // prop: index
        // 0 based index of this series in the plot series array.
        this.index;
        // prop: fill
        // true or false, wether to fill under lines or in bars.
        // May not be implemented in all renderers.
        this.fill = false;
        // prop: fillColor
        // CSS color spec to use for fill under line.  Defaults to line color.
        this.fillColor;
        // prop: fillAlpha
        // Alpha transparency to apply to the fill under the line.
        // Use this to adjust alpha separate from fill color.
        this.fillAlpha;
        // prop: fillAndStroke
        // If true will stroke the line (with color this.color) as well as fill under it.
        // Applies only when fill is true.
        this.fillAndStroke = false;
        // prop: disableStack
        // true to not stack this series with other series in the plot.
        // To render properly, non-stacked series must come after any stacked series
        // in the plot's data series array.  So, the plot's data series array would look like:
        // > [stackedSeries1, stackedSeries2, ..., nonStackedSeries1, nonStackedSeries2, ...]
        // disableStack will put a gap in the stacking order of series, and subsequent
        // stacked series will not fill down through the non-stacked series and will
        // most likely not stack properly on top of the non-stacked series.
        this.disableStack = false;
        // _stack is set by the Plot if the plot is a stacked chart.
        // will stack lines or bars on top of one another to build a "mountain" style chart.
        // May not be implemented in all renderers.
        this._stack = false;
        // prop: neighborThreshold
        // how close or far (in pixels) the cursor must be from a point marker to detect the point.
        this.neighborThreshold = 4;
        // prop: fillToZero
        // true will force bar and filled series to fill toward zero on the fill Axis.
        this.fillToZero = false;
        // prop: fillAxis
        // Either 'x' or 'y'.  Which axis to fill the line toward if fillToZero is true.
        // 'y' means fill up/down to 0 on the y axis for this series.
        this.fillAxis = 'y';
        // prop: useNegativeColors
        // true to color negative values differently in filled and bar charts.
        this.useNegativeColors = true;
        this._stackData = [];
        // _plotData accounts for stacking.  If plots not stacked, _plotData and data are same.  If
        // stacked, _plotData is accumulation of stacking data.
        this._plotData = [];
        // _plotValues hold the individual x and y values that will be plotted for this series.
        this._plotValues = {x:[], y:[]};
        // statistics about the intervals between data points.  Used for auto scaling.
        this._intervals = {x:{}, y:{}};
        // data from the previous series, for stacked charts.
        this._prevPlotData = [];
        this._prevGridData = [];
        this._stackAxis = 'y';
        this._primaryAxis = '_xaxis';
        // give each series a canvas to draw on.  This should allow for redrawing speedups.
        this.canvas = new $.jqplot.GenericCanvas();
        this.shadowCanvas = new $.jqplot.GenericCanvas();
        this.plugins = {};
        // sum of y values in this series.
        this._sumy = 0;
        this._sumx = 0;
    }
    
    Series.prototype = new $.jqplot.ElemContainer();
    Series.prototype.constructor = Series;
    
    Series.prototype.init = function(index, gridbw, plot) {
        // weed out any null values in the data.
        this.index = index;
        this.gridBorderWidth = gridbw;
        var d = this.data;
        for (var i=0; i<d.length; i++) {
            if (! this.breakOnNull) {
                if (d[i] == null || d[i][0] == null || d[i][1] == null) {
                    d.splice(i,1);
                    continue;
                }
            }
            else {
                if (d[i] == null || d[i][0] == null || d[i][1] == null) {
                    // TODO: figure out what to do with null values
                    var undefined;
                }
            }
        }
        if (!this.fillColor) {
            this.fillColor = this.color;
        }
        if (this.fillAlpha) {
            var comp = $.jqplot.normalize2rgb(this.fillColor);
            var comp = $.jqplot.getColorComponents(comp);
            this.fillColor = 'rgba('+comp[0]+','+comp[1]+','+comp[2]+','+this.fillAlpha+')';
        }
        this.renderer = new this.renderer();
        this.renderer.init.call(this, this.rendererOptions, plot);
        this.markerRenderer = new this.markerRenderer();
        if (!this.markerOptions.color) {
            this.markerOptions.color = this.color;
        }
        if (this.markerOptions.show == null) {
            this.markerOptions.show = this.showMarker;
        }
        // the markerRenderer is called within it's own scaope, don't want to overwrite series options!!
        this.markerRenderer.init(this.markerOptions);
    };
    
    // data - optional data point array to draw using this series renderer
    // gridData - optional grid data point array to draw using this series renderer
    // stackData - array of cumulative data for stacked plots.
    Series.prototype.draw = function(sctx, opts, plot) {
        var options = (opts == undefined) ? {} : opts;
        sctx = (sctx == undefined) ? this.canvas._ctx : sctx;
        // hooks get called even if series not shown
        // we don't clear canvas here, it would wipe out all other series as well.
        for (var j=0; j<$.jqplot.preDrawSeriesHooks.length; j++) {
            $.jqplot.preDrawSeriesHooks[j].call(this, sctx, options);
        }
        if (this.show) {
            this.renderer.setGridData.call(this, plot);
            if (!options.preventJqPlotSeriesDrawTrigger) {
                $(sctx.canvas).trigger('jqplotSeriesDraw', [this.data, this.gridData]);
            }
            var data = [];
            if (options.data) {
                data = options.data;
            }
            else if (!this._stack) {
                data = this.data;
            }
            else {
                data = this._plotData;
            }
            var gridData = options.gridData || this.renderer.makeGridData.call(this, data, plot);
            this.renderer.draw.call(this, sctx, gridData, options);
        }
        
        for (var j=0; j<$.jqplot.postDrawSeriesHooks.length; j++) {
            $.jqplot.postDrawSeriesHooks[j].call(this, sctx, options);
        }
    };
    
    Series.prototype.drawShadow = function(sctx, opts, plot) {
        var options = (opts == undefined) ? {} : opts;
        sctx = (sctx == undefined) ? this.shadowCanvas._ctx : sctx;
        // hooks get called even if series not shown
        // we don't clear canvas here, it would wipe out all other series as well.
        for (var j=0; j<$.jqplot.preDrawSeriesShadowHooks.length; j++) {
            $.jqplot.preDrawSeriesShadowHooks[j].call(this, sctx, options);
        }
        if (this.shadow) {
            this.renderer.setGridData.call(this, plot);

            var data = [];
            if (options.data) {
                data = options.data;
            }
            else if (!this._stack) {
                data = this.data;
            }
            else {
                data = this._plotData;
            }
            var gridData = options.gridData || this.renderer.makeGridData.call(this, data, plot);
        
            this.renderer.drawShadow.call(this, sctx, gridData, options);
        }
        
        for (var j=0; j<$.jqplot.postDrawSeriesShadowHooks.length; j++) {
            $.jqplot.postDrawSeriesShadowHooks[j].call(this, sctx, options);
        }
        
    };
    


    /**
     * Class: Grid
     * 
     * Object representing the grid on which the plot is drawn.  The grid in this
     * context is the area bounded by the axes, the area which will contain the series.
     * Note, the series are drawn on their own canvas.
     * The Grid object cannot be instantiated directly, but is created by the Plot oject.  
     * Grid properties can be set or overriden by the options passed in from the user.
     */
    function Grid() {
        $.jqplot.ElemContainer.call(this);
        // Group: Properties
        
        // prop: drawGridlines
        // wether to draw the gridlines on the plot.
        this.drawGridlines = true;
        // prop: gridLineColor
        // color of the grid lines.
        this.gridLineColor = '#cccccc';
        // prop: gridLineWidth
        // width of the grid lines.
        this.gridLineWidth = 1.0;
        // prop: background
        // css spec for the background color.
        this.background = '#fffdf6';
        // prop: borderColor
        // css spec for the color of the grid border.
        this.borderColor = '#999999';
        // prop: borderWidth
        // width of the border in pixels.
        this.borderWidth = 2.0;
        // prop: shadow
        // wether to show a shadow behind the grid.
        this.shadow = true;
        // prop: shadowAngle
        // shadow angle in degrees
        this.shadowAngle = 45;
        // prop: shadowOffset
        // Offset of each shadow stroke from the border in pixels
        this.shadowOffset = 1.5;
        // prop: shadowWidth
        // width of the stoke for the shadow
        this.shadowWidth = 3;
        // prop: shadowDepth
        // Number of times shadow is stroked, each stroke offset shadowOffset from the last.
        this.shadowDepth = 3;
        // prop: shadowAlpha
        // Alpha channel transparency of shadow.  0 = transparent.
        this.shadowAlpha = '0.07';
        this._left;
        this._top;
        this._right;
        this._bottom;
        this._width;
        this._height;
        this._axes = [];
        // prop: renderer
        // Instance of a renderer which will actually render the grid,
        // see <$.jqplot.CanvasGridRenderer>.
        this.renderer = $.jqplot.CanvasGridRenderer;
        // prop: rendererOptions
        // Options to pass on to the renderer,
        // see <$.jqplot.CanvasGridRenderer>.
        this.rendererOptions = {};
        this._offsets = {top:null, bottom:null, left:null, right:null};
    }
    
    Grid.prototype = new $.jqplot.ElemContainer();
    Grid.prototype.constructor = Grid;
    
    Grid.prototype.init = function() {
        this.renderer = new this.renderer();
        this.renderer.init.call(this, this.rendererOptions);
    };
    
    Grid.prototype.createElement = function(offsets) {
        this._offsets = offsets;
        return this.renderer.createElement.call(this);
    };
    
    Grid.prototype.draw = function() {
        this.renderer.draw.call(this);
    };
    
    $.jqplot.GenericCanvas = function() {
        $.jqplot.ElemContainer.call(this);
        this._ctx;  
    };
    
    $.jqplot.GenericCanvas.prototype = new $.jqplot.ElemContainer();
    $.jqplot.GenericCanvas.prototype.constructor = $.jqplot.GenericCanvas;
    
    $.jqplot.GenericCanvas.prototype.createElement = function(offsets, clss, plotDimensions) {
        this._offsets = offsets;
        var klass = 'jqplot';
        if (clss != undefined) {
            klass = clss;
        }
        var elem = document.createElement('canvas');
        // if new plotDimensions supplied, use them.
        if (plotDimensions != undefined) {
            this._plotDimensions = plotDimensions;
        }
        elem.width = this._plotDimensions.width - this._offsets.left - this._offsets.right;
        elem.height = this._plotDimensions.height - this._offsets.top - this._offsets.bottom;
        this._elem = $(elem);
        this._elem.addClass(klass);
        this._elem.css({ position: 'absolute', left: this._offsets.left, top: this._offsets.top });
        // borrowed from flot by Ole Laursen
        if ($.browser.msie) {
            window.G_vmlCanvasManager.init_(document);
        }
        if ($.browser.msie) {
            elem = window.G_vmlCanvasManager.initElement(elem);
        }
        return this._elem;
    };
    
    $.jqplot.GenericCanvas.prototype.setContext = function() {
        this._ctx = this._elem.get(0).getContext("2d");
        return this._ctx;
    };

    /**
     * Class: jqPlot
     * Plot object returned by call to $.jqplot.  Handles parsing user options,
     * creating sub objects (Axes, legend, title, series) and rendering the plot.
     */
    function jqPlot() {
        // Group: Properties
        // These properties are specified at the top of the options object
        // like so:
        // > {
        // >     axesDefaults:{min:0},
        // >     series:[{color:'#6633dd'}],
        // >     title: 'A Plot'
        // > }
        //
        // prop: data
        // user's data.  Data should *NOT* be specified in the options object,
        // but be passed in as the second argument to the $.jqplot() function.
        // The data property is described here soley for reference. 
        // The data should be in the form of an array of 2D or 1D arrays like
        // > [ [[x1, y1], [x2, y2],...], [y1, y2, ...] ].
        this.data = [];
        // The id of the dom element to render the plot into
        this.targetId = null;
        // the jquery object for the dom target.
        this.target = null; 
        this.defaults = {
            // prop: axesDefaults
            // default options that will be applied to all axes.
            // see <Axis> for axes options.
            axesDefaults: {},
            axes: {xaxis:{}, yaxis:{}, x2axis:{}, y2axis:{}, y3axis:{}, y4axis:{}, y5axis:{}, y6axis:{}, y7axis:{}, y8axis:{}, y9axis:{}},
            // prop: seriesDefaults
            // default options that will be applied to all series.
            // see <Series> for series options.
            seriesDefaults: {},
            gridPadding: {top:10, right:10, bottom:23, left:10},
            series:[]
        };
        // prop: series
        // Array of series object options.
        // see <Series> for series specific options.
        this.series = [];
        // prop: axes
        // up to 4 axes are supported, each with it's own options, 
        // See <Axis> for axis specific options.
        this.axes = {xaxis: new Axis('xaxis'), yaxis: new Axis('yaxis'), x2axis: new Axis('x2axis'), y2axis: new Axis('y2axis'), y3axis: new Axis('y3axis'), y4axis: new Axis('y4axis'), y5axis: new Axis('y5axis'), y6axis: new Axis('y6axis'), y7axis: new Axis('y7axis'), y8axis: new Axis('y8axis'), y9axis: new Axis('y9axis')};
        // prop: grid
        // See <Grid> for grid specific options.
        this.grid = new Grid();
        // prop: legend
        // see <$.jqplot.TableLegendRenderer>
        this.legend = new Legend();
        this.baseCanvas = new $.jqplot.GenericCanvas();
        // this.seriesCanvas = new $.jqplot.GenericCanvas();
        this.eventCanvas = new $.jqplot.GenericCanvas();
        this._width = null;
        this._height = null; 
        this._plotDimensions = {height:null, width:null};
        this._gridPadding = {top:10, right:10, bottom:10, left:10};
        // a shortcut for axis syncTicks options.  Not implemented yet.
        this.syncXTicks = true;
        // a shortcut for axis syncTicks options.  Not implemented yet.
        this.syncYTicks = true;
        // prop: seriesColors
        // Ann array of CSS color specifications that will be applied, in order,
        // to the series in the plot.  Colors will wrap around so, if their
        // are more series than colors, colors will be reused starting at the
        // beginning.  For pie charts, this specifies the colors of the slices.
        this.seriesColors = [ "#4bb2c5", "#EAA228", "#c5b47f", "#579575", "#839557", "#958c12", "#953579", "#4b5de4", "#d8b83f", "#ff5800", "#0085cc", "#c747a3", "#cddf54", "#FBD178", "#26B4E3", "#bd70c7"];
        this.negativeSeriesColors = [ "#498991", "#C08840", "#9F9274", "#546D61", "#646C4A", "#6F6621", "#6E3F5F", "#4F64B0", "#A89050", "#C45923", "#187399", "#945381", "#959E5C", "#C7AF7B", "#478396", "#907294"];
        // prop: sortData
        // false to not sort the data passed in by the user.
        // Many bar, stakced and other graphs as well as many plugins depend on
        // having sorted data.
        this.sortData = true;
        var seriesColorsIndex = 0;
        // prop textColor
        // css spec for the css color attribute.  Default for the entire plot.
        this.textColor;
        // prop; fontFamily
        // css spec for the font-family attribute.  Default for the entire plot.
        this.fontFamily;
        // prop: fontSize
        // css spec for the font-size attribute.  Default for the entire plot.
        this.fontSize;
        // prop: title
        // Title object.  See <Title> for specific options.  As a shortcut, you
        // can specify the title option as just a string like: title: 'My Plot'
        // and this will create a new title object with the specified text.
        this.title = new Title();
        // container to hold all of the merged options.  Convienence for plugins.
        this.options = {};
        // prop: stackSeries
        // true or false, creates a stack or "mountain" plot.
        // Not all series renderers may implement this option.
        this.stackSeries = false;
        // array to hold the cumulative stacked series data.
        // used to ajust the individual series data, which won't have access to other
        // series data.
        this._stackData = [];
        // array that holds the data to be plotted. This will be the series data
        // merged with the the appropriate data from _stackData according to the stackAxis.
        this._plotData = [];
        // Namespece to hold plugins.  Generally non-renderer plugins add themselves to here.
        this.plugins = {};
        // Count how many times the draw method has been called while the plot is visible.
        // Mostly used to test if plot has never been dran (=0), has been successfully drawn
        // into a visible container once (=1) or draw more than once into a visible container.
        // Can use this in tests to see if plot has been visibly drawn at least one time.
        // After plot has been visibly drawn once, it generally doesn't need redrawn if its
        // container is hidden and shown.
        this._drawCount = 0;
        // prop: drawIfHidden
        // True to execute the draw method even if the plot target is hidden.
        // Generally, this should be false.  Most plot elements will not be sized/
        // positioned correclty if renderered into a hidden container.  To render into
        // a hidden container, call the replot method when the container is shown.
        this.drawIfHidden = false;
        // sum of y values for all series in plot.
        // used in mekko chart.
        this._sumy = 0;
        this._sumx = 0;
        
        this.colorGenerator = $.jqplot.ColorGenerator;
        
        // Group: methods
        //
        // method: init
        // sets the plot target, checks data and applies user
        // options to plot.
        this.init = function(target, data, options) {
            for (var i=0; i<$.jqplot.preInitHooks.length; i++) {
                $.jqplot.preInitHooks[i].call(this, target, data, options);
            }
            this.targetId = '#'+target;
            this.target = $('#'+target);
            if (!this.target.get(0)) {
                throw "No plot target specified";
            }
            
            // make sure the target is positioned by some means and set css
            if (this.target.css('position') == 'static') {
                this.target.css('position', 'relative');
            }
            if (!this.target.hasClass('jqplot-target')) {
                this.target.addClass('jqplot-target');
            }
            
            // if no height or width specified, use a default.
            if (!this.target.height()) {
                var h;
                if (options && options.height) {
                    h = parseInt(options.height, 10);
                }
                else if (this.target.attr('data-height')) {
                    h = parseInt(this.target.attr('data-height'), 10);
                }
                else {
                    h = parseInt($.jqplot.config.defaultHeight, 10);
                }
                this._height = h;
                this.target.css('height', h+'px');
            }
            else {
                this._height = this.target.height();
            }
            if (!this.target.width()) {
                var w;
                if (options && options.width) {
                    w = parseInt(options.width, 10);
                }
                else if (this.target.attr('data-width')) {
                    w = parseInt(this.target.attr('data-width'), 10);
                }
                else {
                    w = parseInt($.jqplot.config.defaultWidth, 10);
                }
                this._width = w;
                this.target.css('width', w+'px');
            }
            else {
                this._width = this.target.width();
            }
            
            this._plotDimensions.height = this._height;
            this._plotDimensions.width = this._width;
            this.grid._plotDimensions = this._plotDimensions;
            this.title._plotDimensions = this._plotDimensions;
            this.baseCanvas._plotDimensions = this._plotDimensions;
            this.eventCanvas._plotDimensions = this._plotDimensions;
            this.legend._plotDimensions = this._plotDimensions;
            if (this._height <=0 || this._width <=0 || !this._height || !this._width) {
                throw "Canvas dimension not set";
            }
            
            this.data = data;
            
            this.parseOptions(options);
            
            if (this.textColor) {
                this.target.css('color', this.textColor);
            }
            if (this.fontFamily) {
                this.target.css('font-family', this.fontFamily);
            }
            if (this.fontSize) {
                this.target.css('font-size', this.fontSize);
            }
            
            this.title.init();
            this.legend.init();
            this._sumy = 0;
            this._sumx = 0;
            for (var i=0; i<this.series.length; i++) {
                this.series[i].shadowCanvas._plotDimensions = this._plotDimensions;
                this.series[i].canvas._plotDimensions = this._plotDimensions;
                for (var j=0; j<$.jqplot.preSeriesInitHooks.length; j++) {
                    $.jqplot.preSeriesInitHooks[j].call(this.series[i], target, data, this.options.seriesDefaults, this.options.series[i]);
                }
                this.populatePlotData(this.series[i], i);
                this.series[i]._plotDimensions = this._plotDimensions;
                this.series[i].init(i, this.grid.borderWidth, this);
                for (var j=0; j<$.jqplot.postSeriesInitHooks.length; j++) {
                    $.jqplot.postSeriesInitHooks[j].call(this.series[i], target, data, this.options.seriesDefaults, this.options.series[i]);
                }
                this._sumy += this.series[i]._sumy;
                this._sumx += this.series[i]._sumx;
            }

            for (var name in this.axes) {
                this.axes[name]._plotDimensions = this._plotDimensions;
                this.axes[name].init();
            }
            
            if (this.sortData) {
                sortData(this.series);
            }
            this.grid.init();
            this.grid._axes = this.axes;
            
            this.legend._series = this.series;

            for (var i=0; i<$.jqplot.postInitHooks.length; i++) {
                $.jqplot.postInitHooks[i].call(this, target, data, options);
            }
        };  
        
        // method: resetAxesScale
        // Reset the specified axes min, max, numberTicks and tickInterval properties to null
        // or reset these properties on all axes if no list of axes is provided.
        //
        // Parameters:
        // axes - Boolean to reset or not reset all axes or an array or object of axis names to reset.
        this.resetAxesScale = function(axes) {
            var ax = (axes != undefined) ? axes : this.axes;
            if (ax === true) {
                ax = this.axes;
            }
            if (ax.constructor === Array) {
                for (var i = 0; i < ax.length; i++) {
                    this.axes[ax[i]].resetScale();
                }
            }
            else if (ax.constructor === Object) {
                for (var name in ax) {
                    this.axes[name].resetScale();
                }
            }
        };
        // method: reInitialize
        // reinitialize plot for replotting.
        // not called directly.
        this.reInitialize = function () {
            // Plot should be visible and have a height and width.
            // If plot doesn't have height and width for some
            // reason, set it by other means.  Plot must not have
            // a display:none attribute, however.
            if (!this.target.height()) {
                var h;
                if (options && options.height) {
                    h = parseInt(options.height, 10);
                }
                else if (this.target.attr('data-height')) {
                    h = parseInt(this.target.attr('data-height'), 10);
                }
                else {
                    h = parseInt($.jqplot.config.defaultHeight, 10);
                }
                this._height = h;
                this.target.css('height', h+'px');
            }
            else {
                this._height = this.target.height();
            }
            if (!this.target.width()) {
                var w;
                if (options && options.width) {
                    w = parseInt(options.width, 10);
                }
                else if (this.target.attr('data-width')) {
                    w = parseInt(this.target.attr('data-width'), 10);
                }
                else {
                    w = parseInt($.jqplot.config.defaultWidth, 10);
                }
                this._width = w;
                this.target.css('width', w+'px');
            }
            else {
                this._width = this.target.width();
            }
            
            if (this._height <=0 || this._width <=0 || !this._height || !this._width) {
                throw "Target dimension not set";
            }
            
            this._plotDimensions.height = this._height;
            this._plotDimensions.width = this._width;
            this.grid._plotDimensions = this._plotDimensions;
            this.title._plotDimensions = this._plotDimensions;
            this.baseCanvas._plotDimensions = this._plotDimensions;
            // this.seriesCanvas._plotDimensions = this._plotDimensions;
            this.eventCanvas._plotDimensions = this._plotDimensions;
            this.legend._plotDimensions = this._plotDimensions;
            
            for (var n in this.axes) {
                var axis = this.axes[n];
                axis._plotWidth = this._width;
                axis._plotHeight = this._height;
            }
            
            this.title._plotWidth = this._width;
            
            if (this.textColor) {
                this.target.css('color', this.textColor);
            }
            if (this.fontFamily) {
                this.target.css('font-family', this.fontFamily);
            }
            if (this.fontSize) {
                this.target.css('font-size', this.fontSize);
            }
            
            this._sumy = 0;
            this._sumx = 0;
            for (var i=0; i<this.series.length; i++) {
                this.populatePlotData(this.series[i], i);
                this.series[i]._plotDimensions = this._plotDimensions;
                this.series[i].canvas._plotDimensions = this._plotDimensions;
                //this.series[i].init(i, this.grid.borderWidth);
                this._sumy += this.series[i]._sumy;
                this._sumx += this.series[i]._sumx;
            }
            
            for (var name in this.axes) {
                this.axes[name]._plotDimensions = this._plotDimensions;
                this.axes[name]._ticks = [];
                this.axes[name].renderer.init.call(this.axes[name], {});
            }
            
            if (this.sortData) {
                sortData(this.series);
            }
            
            this.grid._axes = this.axes;
            
            this.legend._series = this.series;
        };
        
        // sort the series data in increasing order.
        function sortData(series) {
            var d, ret;
            for (var i=0; i<series.length; i++) {
                d = series[i].data;
                var check = true;
                if (series[i]._stackAxis == 'x') {
                    for (var j = 0; j < d.length; j++) {
                        if (typeof(d[j][1]) != "number") {
                            check = false;
                            break;
                        }
                    }
                    if (check) {
                        d.sort(function(a,b) { return a[1] - b[1]; });
                    }
                }
                else {
                    for (var j = 0; j < d.length; j++) {
                        if (typeof(d[j][0]) != "number") {
                            check = false;
                            break;
                        }
                    }
                    if (check) {
                        d.sort(function(a,b) { return a[0] - b[0]; });
                    }
                }
            }
        }
        
        // populate the _stackData and _plotData arrays for the plot and the series.
        this.populatePlotData = function(series, index) {
            // if a stacked chart, compute the stacked data
            this._plotData = [];
            this._stackData = [];
            series._stackData = [];
            series._plotData = [];
            var plotValues = {x:[], y:[]};
            if (this.stackSeries && !series.disableStack) {
                series._stack = true;
                var sidx = series._stackAxis == 'x' ? 0 : 1;
                var idx = sidx ? 0 : 1;
                // push the current data into stackData
                //this._stackData.push(this.series[i].data);
                var temp = $.extend(true, [], series.data);
                // create the data that will be plotted for this series
                var plotdata = $.extend(true, [], series.data);
                // for first series, nothing to add to stackData.
                for (var j=0; j<index; j++) {
                    var cd = this.series[j].data;
                    for (var k=0; k<cd.length; k++) {
                        temp[k][0] += cd[k][0];
                        temp[k][1] += cd[k][1];
                        // only need to sum up the stack axis column of data
                        plotdata[k][sidx] += cd[k][sidx];
                    }
                }
                for (var i=0; i<plotdata.length; i++) {
                    plotValues.x.push(plotdata[i][0]);
                    plotValues.y.push(plotdata[i][1]);
                }
                this._plotData.push(plotdata);
                this._stackData.push(temp);
                series._stackData = temp;
                series._plotData = plotdata;
                series._plotValues = plotValues;
            }
            else {
                for (var i=0; i<series.data.length; i++) {
                    plotValues.x.push(series.data[i][0]);
                    plotValues.y.push(series.data[i][1]);
                }
                this._stackData.push(series.data);
                this.series[index]._stackData = series.data;
                this._plotData.push(series.data);
                series._plotData = series.data;
                series._plotValues = plotValues;
            }
            if (index>0) {
                series._prevPlotData = this.series[index-1]._plotData;
            }
            series._sumy = 0;
            series._sumx = 0;
            for (i=series.data.length-1; i>-1; i--) {
                series._sumy += series.data[i][1];
                series._sumx += series.data[i][0];
            }
        };
        
        // function to safely return colors from the color array and wrap around at the end.
        this.getNextSeriesColor = (function(t) {
            var idx = 0;
            var sc = t.seriesColors;
            
            return function () { 
                if (idx < sc.length) {
                    return sc[idx++];
                }
                else {
                    idx = 0;
                    return sc[idx++];
                }
            };
        })(this);
    
        this.parseOptions = function(options){
            for (var i=0; i<$.jqplot.preParseOptionsHooks.length; i++) {
                $.jqplot.preParseOptionsHooks[i].call(this, options);
            }
            this.options = $.extend(true, {}, this.defaults, options);
            this.stackSeries = this.options.stackSeries;
            if (this.options.seriesColors) {
                this.seriesColors = this.options.seriesColors;
            }
            var cg = new this.colorGenerator(this.seriesColors);
            // this._gridPadding = this.options.gridPadding;
            $.extend(true, this._gridPadding, this.options.gridPadding);
            this.sortData = (this.options.sortData != null) ? this.options.sortData : this.sortData;
            for (var n in this.axes) {
                var axis = this.axes[n];
                $.extend(true, axis, this.options.axesDefaults, this.options.axes[n]);
                axis._plotWidth = this._width;
                axis._plotHeight = this._height;
            }
            if (this.data.length == 0) {
                this.data = [];
                for (var i=0; i<this.options.series.length; i++) {
                    this.data.push(this.options.series.data);
                }    
            }
                
            var normalizeData = function(data, dir) {
                // return data as an array of point arrays,
                // in form [[x1,y1...], [x2,y2...], ...]
                var temp = [];
                var i;
                dir = dir || 'vertical';
                if (!(data[0] instanceof Array)) {
                    // we have a series of scalars.  One line with just y values.
                    // turn the scalar list of data into a data array of form:
                    // [[1, data[0]], [2, data[1]], ...]
                    for (var i=0; i<data.length; i++) {
                        if (dir == 'vertical') {
                            temp.push([i+1, data[i]]);   
                        }
                        else {
                            temp.push([data[i], i+1]);
                        }
                    }
                }            
                else {
                    // we have a properly formatted data series, copy it.
                    $.extend(true, temp, data);
                }
                return temp;
            };

            for (var i=0; i<this.data.length; i++) { 
                var temp = new Series();
                for (var j=0; j<$.jqplot.preParseSeriesOptionsHooks.length; j++) {
                    $.jqplot.preParseSeriesOptionsHooks[j].call(temp, this.options.seriesDefaults, this.options.series[i]);
                }
                $.extend(true, temp, {seriesColors:this.seriesColors, negativeSeriesColors:this.negativeSeriesColors}, this.options.seriesDefaults, this.options.series[i]);
                var dir = 'vertical';
                if (temp.renderer.constructor == $.jqplot.barRenderer && temp.rendererOptions && temp.rendererOptions.barDirection == 'horizontal') {
                    dir = 'horizontal';
                }
                temp.data = normalizeData(this.data[i], dir);
                switch (temp.xaxis) {
                    case 'xaxis':
                        temp._xaxis = this.axes.xaxis;
                        break;
                    case 'x2axis':
                        temp._xaxis = this.axes.x2axis;
                        break;
                    default:
                        break;
                }
                temp._yaxis = this.axes[temp.yaxis];
                temp._xaxis._series.push(temp);
                temp._yaxis._series.push(temp);
                if (temp.show) {
                    temp._xaxis.show = true;
                    temp._yaxis.show = true;
                }

                // parse the renderer options and apply default colors if not provided
                if (!temp.color && temp.show != false) {
                    temp.color = cg.next();
                }
                if (!temp.label) {
                    temp.label = 'Series '+ (i+1).toString();
                }
                // temp.rendererOptions.show = temp.show;
                // $.extend(true, temp.renderer, {color:this.seriesColors[i]}, this.rendererOptions);
                this.series.push(temp);  
                for (var j=0; j<$.jqplot.postParseSeriesOptionsHooks.length; j++) {
                    $.jqplot.postParseSeriesOptionsHooks[j].call(this.series[i], this.options.seriesDefaults, this.options.series[i]);
                }
            }
            
            // copy the grid and title options into this object.
            $.extend(true, this.grid, this.options.grid);
            // if axis border properties aren't set, set default.
            for (var n in this.axes) {
                var axis = this.axes[n];
                if (axis.borderWidth == null) {
                    axis.borderWidth =this.grid.borderWidth;
                }
                if (axis.borderColor == null) {
                    if (n != 'xaxis' && n != 'x2axis' && axis.useSeriesColor === true && axis.show) {
                        axis.borderColor = axis._series[0].color;
                    }
                    else {
                        axis.borderColor = this.grid.borderColor;
                    }
                }
            }
            
            if (typeof this.options.title == 'string') {
                this.title.text = this.options.title;
            }
            else if (typeof this.options.title == 'object') {
                $.extend(true, this.title, this.options.title);
            }
            this.title._plotWidth = this._width;
            $.extend(true, this.legend, this.options.legend);
            
            for (var i=0; i<$.jqplot.postParseOptionsHooks.length; i++) {
                $.jqplot.postParseOptionsHooks[i].call(this, options);
            }
        };
        
        // method: replot
        // Does a reinitialization of the plot followed by
        // a redraw.  Method could be used to interactively
        // change plot characteristics and then replot.
        //
        // Parameters:
        // options - Options used for replotting.
        //
        // Properties:
        // clear - false to not clear (empty) the plot container before replotting (default: true).
        // resetAxes - true to reset all axes min, max, numberTicks and tickInterval setting so axes will rescale themselves.
        //             optionally pass in list of axes to reset (e.g. ['xaxis', 'y2axis']) (default: false).
        this.replot = function(options) {
            var opts = (options != undefined) ? options : {};
            var clear = (opts.clear != undefined) ? opts.clear : true;
            var resetAxes = (opts.resetAxes != undefined) ? opts.resetAxes : false;
            this.target.trigger('jqplotPreReplot');
            if (clear) {
                this.target.empty();
            }
            if (resetAxes) {
                this.resetAxesScale(resetAxes);
            }
            this.reInitialize();
            this.draw();
            this.target.trigger('jqplotPostReplot');
        };
        
        // method: redraw
        // Empties the plot target div and redraws the plot.
        // This enables plot data and properties to be changed
        // and then to comletely clear the plot and redraw.
        // redraw *will not* reinitialize any plot elements.
        // That is, axes will not be autoscaled and defaults
        // will not be reapplied to any plot elements.  redraw
        // is used primarily with zooming. 
        //
        // Parameters:
        // clear - false to not clear (empty) the plot container before redrawing (default: true).
        this.redraw = function(clear) {
            clear = (clear != null) ? clear : true;
            this.target.trigger('jqplotPreRedraw');
            if (clear) {
                this.target.empty();
            }
             for (var ax in this.axes) {
                this.axes[ax]._ticks = [];
            }
            for (var i=0; i<this.series.length; i++) {
                this.populatePlotData(this.series[i], i);
            }
            this._sumy = 0;
            this._sumx = 0;
            for (i=0; i<this.series.length; i++) {
                this._sumy += this.series[i]._sumy;
                this._sumx += this.series[i]._sumx;
            }
            this.draw();
            this.target.trigger('jqplotPostRedraw');
        };
        
        // method: draw
        // Draws all elements of the plot into the container.
        // Does not clear the container before drawing.
        this.draw = function(){
            if (this.drawIfHidden || this.target.is(':visible')) {
                this.target.trigger('jqplotPreDraw');
                var i;
                for (i=0; i<$.jqplot.preDrawHooks.length; i++) {
                    $.jqplot.preDrawHooks[i].call(this);
                }
                // create an underlying canvas to be used for special features.
                this.target.append(this.baseCanvas.createElement({left:0, right:0, top:0, bottom:0}, 'jqplot-base-canvas'));
                var bctx = this.baseCanvas.setContext();
                this.target.append(this.title.draw());
                this.title.pack({top:0, left:0});
                for (var name in this.axes) {
                    this.target.append(this.axes[name].draw(bctx));
                    this.axes[name].set();
                }
                if (this.axes.yaxis.show) {
                    this._gridPadding.left = this.axes.yaxis.getWidth();
                }
                var ra = ['y2axis', 'y3axis', 'y4axis', 'y5axis', 'y6axis', 'y7axis', 'y8axis', 'y9axis'];
                var rapad = [0, 0, 0, 0];
                var gpr = 0;
                var n, ax;
                for (n=8; n>0; n--) {
                    ax = this.axes[ra[n-1]];
                    if (ax.show) {
                        rapad[n-1] = gpr;
                        gpr += ax.getWidth();
                    }
                }
                if (gpr > this._gridPadding.right) {
                    this._gridPadding.right = gpr;
                }
                if (this.title.show && this.axes.x2axis.show) {
                    this._gridPadding.top = this.title.getHeight() + this.axes.x2axis.getHeight();
                }
                else if (this.title.show) {
                    this._gridPadding.top = this.title.getHeight();
                }
                else if (this.axes.x2axis.show) {
                    this._gridPadding.top = this.axes.x2axis.getHeight();
                }
                if (this.axes.xaxis.show) {
                    this._gridPadding.bottom = this.axes.xaxis.getHeight();
                }
            
                this.axes.xaxis.pack({position:'absolute', bottom:0, left:0, width:this._width}, {min:this._gridPadding.left, max:this._width - this._gridPadding.right});
                this.axes.yaxis.pack({position:'absolute', top:0, left:0, height:this._height}, {min:this._height - this._gridPadding.bottom, max: this._gridPadding.top});
                this.axes.x2axis.pack({position:'absolute', top:this.title.getHeight(), left:0, width:this._width}, {min:this._gridPadding.left, max:this._width - this._gridPadding.right});
                for (i=8; i>0; i--) {
                    this.axes[ra[i-1]].pack({position:'absolute', top:0, right:rapad[i-1]}, {min:this._height - this._gridPadding.bottom, max: this._gridPadding.top});
                }
                // this.axes.y2axis.pack({position:'absolute', top:0, right:0}, {min:this._height - this._gridPadding.bottom, max: this._gridPadding.top});
            
                this.target.append(this.grid.createElement(this._gridPadding));
                this.grid.draw();
                
                // put the shadow canvases behind the series canvases so shadows don't overlap on stacked bars.
                for (i=0; i<this.series.length; i++) {
                    this.target.append(this.series[i].shadowCanvas.createElement(this._gridPadding, 'jqplot-series-canvas jqplot-shadow'));
                    this.series[i].shadowCanvas.setContext();
                }
                
                for (i=0; i<this.series.length; i++) {
                    this.target.append(this.series[i].canvas.createElement(this._gridPadding, 'jqplot-series-canvas'));
                    this.series[i].canvas.setContext();
                }
                
                // var sctx = this.seriesCanvas.setContext();
                this.target.append(this.eventCanvas.createElement(this._gridPadding, 'jqplot-event-canvas'));
                var ectx = this.eventCanvas.setContext();
                ectx.fillStyle = 'rgba(0,0,0,0)';
                ectx.fillRect(0,0,ectx.canvas.width, ectx.canvas.height);
            
                // bind custom event handlers to regular events.
                this.bindCustomEvents();
            
                // draw legend before series if the series needs to know the legend dimensions.
                if (this.legend.preDraw) {  
                    this.target.append(this.legend.draw());
                    this.legend.pack(this._gridPadding);
                    if (this.legend._elem) {
                        this.drawSeries({legendInfo:{location:this.legend.location, width:this.legend.getWidth(), height:this.legend.getHeight(), xoffset:this.legend.xoffset, yoffset:this.legend.yoffset}});
                    }
                    else {
                        this.drawSeries();
                    }
                }
                else {  // draw series before legend
                    this.drawSeries();
                    $(this.series[this.series.length-1].canvas._elem).after(this.legend.draw());
                    // this.target.append(this.legend.draw());
                    this.legend.pack(this._gridPadding);                
                }
            
                // register event listeners on the overlay canvas
                for (var i=0; i<$.jqplot.eventListenerHooks.length; i++) {
                    var h = $.jqplot.eventListenerHooks[i];
                    // in the handler, this will refer to the eventCanvas dom element.
                    // make sure there are references back into plot objects.
                    this.eventCanvas._elem.bind(h[0], {plot:this}, h[1]);
                }

                for (var i=0; i<$.jqplot.postDrawHooks.length; i++) {
                    $.jqplot.postDrawHooks[i].call(this);
                }
            
                if (this.target.is(':visible')) {
                    this._drawCount += 1;
                }
            
                this.target.trigger('jqplotPostDraw', [this]);
            }
        };
        
        this.bindCustomEvents = function() {
            this.eventCanvas._elem.bind('click', {plot:this}, this.onClick);
            this.eventCanvas._elem.bind('dblclick', {plot:this}, this.onDblClick);
            this.eventCanvas._elem.bind('mousedown', {plot:this}, this.onMouseDown);
            this.eventCanvas._elem.bind('mouseup', {plot:this}, this.onMouseUp);
            this.eventCanvas._elem.bind('mousemove', {plot:this}, this.onMouseMove);
            this.eventCanvas._elem.bind('mouseenter', {plot:this}, this.onMouseEnter);
            this.eventCanvas._elem.bind('mouseleave', {plot:this}, this.onMouseLeave);
        };
        
        function getEventPosition(ev) {
            var plot = ev.data.plot;
            // var xaxis = plot.axes.xaxis;
            // var x2axis = plot.axes.x2axis;
            // var yaxis = plot.axes.yaxis;
            // var y2axis = plot.axes.y2axis;
            var offsets = plot.eventCanvas._elem.offset();
            var gridPos = {x:ev.pageX - offsets.left, y:ev.pageY - offsets.top};
            // var dataPos = {x1y1:{x:null, y:null}, x1y2:{x:null, y:null}, x2y1:{x:null, y:null}, x2y2:{x:null, y:null}};
            var dataPos = {xaxis:null, yaxis:null, x2axis:null, y2axis:null, y3axis:null, y4axis:null, y5axis:null, y6axis:null, y7axis:null, y8axis:null, y9axis:null};
            
            var an = ['xaxis', 'yaxis', 'x2axis', 'y2axis', 'y3axis', 'y4axis', 'y5axis', 'y6axis', 'y7axis', 'y8axis', 'y9axis'];
            var ax = plot.axes;
            for (var n=11; n>0; n--) {
                var axis = an[n-1];
                if (ax[axis].show) {
                    dataPos[axis] = ax[axis].series_p2u(gridPos[axis.charAt(0)]);
                }
            }

            return ({offsets:offsets, gridPos:gridPos, dataPos:dataPos});
        }
        
        function getNeighborPoint(plot, x, y) {
            var ret = null;
            var s, i, d0, d, j, r;
            var threshold;
            for (var i=0; i<plot.series.length; i++) {
                s = plot.series[i];
                r = s.renderer;
                if (s.show) {
                    threshold = Math.abs(s.markerRenderer.size/2+s.neighborThreshold);
                    for (var j=0; j<s.gridData.length; j++) {
                        p = s.gridData[j];
                        // neighbor looks different to OHLC chart.
                        if (r.constructor == $.jqplot.OHLCRenderer) {
                            if (r.candleStick) {
                                var yp = s._yaxis.series_u2p;
                                if (x >= p[0]-r._bodyWidth/2 && x <= p[0]+r._bodyWidth/2 && y >= yp(s.data[j][2]) && y <= yp(s.data[j][3])) {
                                    ret = {seriesIndex: i, pointIndex:j, gridData:p, data:s.data[j]};
                                }
                            }
                            // if an open hi low close chart
                            else if (!r.hlc){
                                var yp = s._yaxis.series_u2p;
                                if (x >= p[0]-r._tickLength && x <= p[0]+r._tickLength && y >= yp(s.data[j][2]) && y <= yp(s.data[j][3])) {
                                    ret = {seriesIndex: i, pointIndex:j, gridData:p, data:s.data[j]};
                                }
                            }
                            // a hi low close chart
                            else {
                                var yp = s._yaxis.series_u2p;
                                if (x >= p[0]-r._tickLength && x <= p[0]+r._tickLength && y >= yp(s.data[j][1]) && y <= yp(s.data[j][2])) {
                                    ret = {seriesIndex: i, pointIndex:j, gridData:p, data:s.data[j]};
                                }
                            }
                            
                        }
                        else {
                            d = Math.sqrt( (x-p[0]) * (x-p[0]) + (y-p[1]) * (y-p[1]) );
                            if (d <= threshold && (d <= d0 || d0 == null)) {
                               d0 = d;
                               ret = {seriesIndex: i, pointIndex:j, gridData:p, data:s.data[j]};
                            }
                        }
                    } 
                }
            }
            return ret;
        }
        
        this.onClick = function(ev) {
            // Event passed in is unnormalized and will have data attribute.
            // Event passed out in normalized and won't have data attribute.
            var positions = getEventPosition(ev);
            var p = ev.data.plot;
            var neighbor = getNeighborPoint(p, positions.gridPos.x, positions.gridPos.y);
            ev.data.plot.eventCanvas._elem.trigger('jqplotClick', [positions.gridPos, positions.dataPos, neighbor, p]);
        };
        
        this.onDblClick = function(ev) {
            // Event passed in is unnormalized and will have data attribute.
            // Event passed out in normalized and won't have data attribute.
            var positions = getEventPosition(ev);
            var p = ev.data.plot;
            var neighbor = getNeighborPoint(p, positions.gridPos.x, positions.gridPos.y);
            ev.data.plot.eventCanvas._elem.trigger('jqplotDblClick', [positions.gridPos, positions.dataPos, neighbor, p]);
        };
        
        this.onMouseDown = function(ev) {
            var positions = getEventPosition(ev);
            var p = ev.data.plot;
            var neighbor = getNeighborPoint(p, positions.gridPos.x, positions.gridPos.y);
            ev.data.plot.eventCanvas._elem.trigger('jqplotMouseDown', [positions.gridPos, positions.dataPos, neighbor, p]);
        };
        
        this.onMouseUp = function(ev) {
            var positions = getEventPosition(ev);
            ev.data.plot.eventCanvas._elem.trigger('jqplotMouseUp', [positions.gridPos, positions.dataPos, null, ev.data.plot]);
        };
        
        this.onMouseMove = function(ev) {
            var positions = getEventPosition(ev);
            var p = ev.data.plot;
            var neighbor = getNeighborPoint(p, positions.gridPos.x, positions.gridPos.y);
            ev.data.plot.eventCanvas._elem.trigger('jqplotMouseMove', [positions.gridPos, positions.dataPos, neighbor, p]);
        };
        
        this.onMouseEnter = function(ev) {
            var positions = getEventPosition(ev);
            var p = ev.data.plot;
            ev.data.plot.eventCanvas._elem.trigger('jqplotMouseEnter', [positions.gridPos, positions.dataPos, null, p]);
        };
        
        this.onMouseLeave = function(ev) {
            var positions = getEventPosition(ev);
            var p = ev.data.plot;
            ev.data.plot.eventCanvas._elem.trigger('jqplotMouseLeave', [positions.gridPos, positions.dataPos, null, p]);
        };
        
        // convienece function to draw series shadows and series.
        this.drawSeries = function(options, idx){
            var i, series, ctx;
            // draw specified series
            if (idx != undefined) {
                series = this.series[idx];
                ctx = series.shadowCanvas._ctx;
                ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                series.drawShadow(ctx, options, this);
                ctx = series.canvas._ctx;
                ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                series.draw(ctx, options, this);
            }
            
            else {
                // if call series drawShadow method first, in case all series shadows
                // should be drawn before any series.  This will ensure, like for 
                // stacked bar plots, that shadows don't overlap series.
                for (i=0; i<this.series.length; i++) {
                    // first clear the canvas
                    series = this.series[i];
                    ctx = series.shadowCanvas._ctx;
                    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                    series.drawShadow(ctx, options, this);
                    ctx = series.canvas._ctx;
                    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                    series.draw(ctx, options, this);
                }
            }
        };
    }
    
        
   $.jqplot.ColorGenerator = function(colors) {
        var idx = 0;
        
        this.next = function () { 
            if (idx < colors.length) {
                return colors[idx++];
            }
            else {
                idx = 0;
                return colors[idx++];
            }
        };
        
        this.previous = function () { 
            if (idx > 0) {
                return colors[idx--];
            }
            else {
                idx = colors.length-1;
                return colors[idx];
            }
        };
        
        // get a color by index without advancing pointer.
        this.get = function(i) {
            return colors[i];
        };
        
        this.setColors = function(c) {
            colors = c;
        };
        
        this.reset = function() {
            idx = 0;
        };
    };

    // convert a hex color string to rgb string.
    // h - 3 or 6 character hex string, with or without leading #
    // a - optional alpha
    $.jqplot.hex2rgb = function(h, a) {
        h = h.replace('#', '');
        if (h.length == 3) {
            h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
        }
        var rgb;
        rgb = 'rgba('+parseInt(h.slice(0,2), 16)+', '+parseInt(h.slice(2,4), 16)+', '+parseInt(h.slice(4,6), 16);
        if (a) {
            rgb += ', '+a;
        }
        rgb += ')';
        return rgb;
    };
    
    // convert an rgb color spec to a hex spec.  ignore any alpha specification.
    $.jqplot.rgb2hex = function(s) {
        var pat = /rgba?\( *([0-9]{1,3}\.?[0-9]*%?) *, *([0-9]{1,3}\.?[0-9]*%?) *, *([0-9]{1,3}\.?[0-9]*%?) *(?:, *[0-9.]*)?\)/;
        var m = s.match(pat);
        var h = '#';
        for (i=1; i<4; i++) {
            var temp;
            if (m[i].search(/%/) != -1) {
                temp = parseInt(255*m[i]/100, 10).toString(16);
                if (temp.length == 1) {
                    temp = '0'+temp;
                }
            }
            else {
                temp = parseInt(m[i], 10).toString(16);
                if (temp.length == 1) {
                    temp = '0'+temp;
                }
            }
            h += temp;
        }
        return h;
    };
    
    // given a css color spec, return an rgb css color spec
    $.jqplot.normalize2rgb = function(s, a) {
        if (s.search(/^ *rgba?\(/) != -1) {
            return s; 
        }
        else if (s.search(/^ *#?[0-9a-fA-F]?[0-9a-fA-F]/) != -1) {
            return $.jqplot.hex2rgb(s, a);
        }
        else {
            throw 'invalid color spec';
        }
    };
    
    // extract the r, g, b, a color components out of a css color spec.
    $.jqplot.getColorComponents = function(s) {
        var rgb = $.jqplot.normalize2rgb(s);
        var pat = /rgba?\( *([0-9]{1,3}\.?[0-9]*%?) *, *([0-9]{1,3}\.?[0-9]*%?) *, *([0-9]{1,3}\.?[0-9]*%?) *,? *([0-9.]* *)?\)/;
        var m = rgb.match(pat);
        var ret = [];
        for (i=1; i<4; i++) {
            if (m[i].search(/%/) != -1) {
                ret[i-1] = parseInt(255*m[i]/100, 10);
            }
            else {
                ret[i-1] = parseInt(m[i], 10);
            }
        }
        ret[3] = parseFloat(m[4]) ? parseFloat(m[4]) : 1.0;
        return ret;
    };
        
    // Convienence function that won't hang IE.
    $.jqplot.log = function() {
        if (window.console && $.jqplot.debug) {
           if (arguments.length == 1) {
               console.log (arguments[0]);
            }
           else {
               console.log(arguments);
            }
        }
    };
    var log = $.jqplot.log;
    
})(jQuery);