/*
 * IMPORTANT here should be listed things to easily understand / modify scripts in this file
 * Dependencies => "jQuery"
 *                 "jQuery UI" - version 1.11.4
 *                 "videojs"
 *                 "jsrender"
 *                 "highlight"
 *                 
 * Other dependencies => 
 * Events =>
 * Modules = > "presentation_json_data" ,
 *             "presentation_resize",
 *             "presentation_player",
 *             "presentation_annotations"
 *             "presentation_transcript",
 *             "presentation_author",
 *             "presentation_resources", (references)
 *             "presentation_navigation",
 *             "presentation_editmode",
 *             "presentation_layout_manager",
 *             "presentation_general",
 *             "presentation_track"
 * 
 */

/** Used to overwrite console for production */
if (window.location && window.location.origin && window.location.origin.indexOf("192.168.1.114") === -1) {
    /*console.log = function() {
     };*/
}

/*********************
 * START UTILITIES
 ******************/

var APP_URL_BACK = APP_URL + "adm/";
var APP_UNI = '?&uni=' + UNI;
/* 1.param => Do not show hours (Boolean)
 * 2. param => add zero to minutes and hours
 * if they are less than 10 (Boolean) */
String.prototype.toHHMMSS = function(hou, addZero) {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours < 10) {
        hours = (addZero) ? "0" + hours : hours;
    }

    if (minutes < 10) {

        minutes = (addZero) ? "0" + minutes : minutes;
    }
    if (seconds < 10) {
        seconds = "0" + seconds;
    }

    var time;
    if (!hou) {
        if (parseInt(hours) === 0) {
            time = minutes + ':' + seconds;
        } else {
            time = hours + ':' + minutes + ':' + seconds;
        }

    } else {

        time = minutes + ':' + seconds;
    }
    return time;
};


(function(old) {
    $.fn.attrs = function() {
        if (arguments.length === 0) {
            if (this.length === 0) {
                return null;
            }

            var arr = [];
            $.each(this[0].attributes, function() {
                if (this.specified) {
                    arr.push(this.name + '="' + this.value + '" ');
                }
            });
            return arr.join('');
        }

        return old.apply(this, arguments);
    };
})($.fn.attrs);

/**
 * Transforms formated time to seconds
 * @param {String} hms - hours, minutes, seconds Exp: "12:20"
 * @return {Number} seconds - value of formated time in seconds
 *  */
var hmsToSeconds = function(hms) {
    var a = hms.split(':'); // split it at the colons

    // minutes are worth 60 seconds. Hours are worth 60 minutes.
    var seconds;
    if (a.length === 3) {
        seconds = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
    } else if (a.length === 2) {
        seconds = (+a[0]) * 60 + (+a[1]);
    } else {
        seconds = 0;
    }

    return seconds;
};

/**
 * Get one of 4 predefined colors (red to green) depending on percentage
 * @param {Float} percentage - 0 to 1
 * @returns {String} color - color code in hex
 */
var getColorForPercentage = function(pct) {
    var color;
    if (pct <= 0.25) {
        color = "#DE330A";
    } else if (pct > 0.25 && pct <= 0.5) {
        color = "#DEA10A";
    } else if (pct > 0.5 && pct <= 0.9) {
        color = "#969C06";
    } else {
        color = "#008000";
    }
    return color;
};

/** Create custom spinner */
/*$.widget("ui.durationspinner", $.ui.spinner, {
    options: {
        step: 1,
        stop: function(event, ui) {
            var el = $(event.target);
            if (el.hasClass("annotation-startime")) {
                $(document).trigger("spinnerTimeStart", el);
            } else {
                $(document).trigger("spinnerTimeEnd", el);
            }
        }
    },
    _parse: function(value) {
        if (typeof value === "string") {
            return hmsToSeconds(value);
        }
        return value;
    },
    _format: function(value) {
        var retVal = value + "";
        return retVal.toHHMMSS(true);
    }
});*/

/**
 * Takes json data returns javascript object
 * @param {String | Object} data - json data
 * @return {Object} object - json data parsed
 *  */
JSON.smartParse = function(data) {
    try {
        if (typeof data === "object") {
            return data;
        } else {
            var parsed = JSON.parse(data);
            return parsed;
        }
    } catch (e) {
        console.error(" JSON IS NOT VALID ", e);
    }
};

/*********************
 * END UTILITIES
 ******************/

/* 
 * Listen => "layoutManagerReady"
 * Trigger => "jsonPublished", "slideChange", "slideChangeStatus"
 * 
 * @Note Important : this module needs to wait for "layoutManagerReady" Event
 * before triggering "jsonPublished" event.
 * */
EW["presentation_json_data"] = {
    dependencies: ['videojs', 'videojscss', 'jsrender', 'highlightjs'],
    data: {},
    /* will contain presentation json data if needed */
    activeSlideIndex: 1,
    allSlides: 5,
    layoutManagerReady: false,
    dataPublishedOnce: false,
    init: function(initWhat) {

        var _this = this;
        var rand = new Date().getTime();
        if (!jsonuri) {
            console.error("Please provide an url for presentation data");
            return;
        }

        try {
            /**  Calling setConfig from  EW["presentation_layout_manager"] */
            EW["presentation_layout_manager"].setConfig({fn: function(layoutStates) {
                    _this.getJson(initWhat, layoutStates);
                }});

        } catch (e) {
            /**
             * Still initing application even if
             * layout manager failed
             * @param {String} initWhat
             */
            _this.getJson(initWhat, {});

            console.log("presentation_layout_manager error", e);
        }

        $(document).off("reinit").on("reinit", function(event, slide) {
            if (slide) {
                _this.activeSlideIndex = slide.index;
            }
            _this.init();
        });

        /* listen for reference reinit */
        $(document).off("reinit.reference").on("reinit.reference", function(event, slide) {
            if (slide) {
                _this.activeSlideIndex = slide.index;
            }
            if (event.namespace == "reference") {
                _this.init("reference");
            }

        });
        /* listen for navigation reinit */
        $(document).off("reinit.navigation").on("reinit.navigation", function(event, slide) {
            if (slide) {
                _this.activeSlideIndex = slide.index;
            }

            if (event.namespace == "navigation") {
                _this.init("navigation");
            }
        });

        /* listen for player reinit */
        $(document).off("reinit.player").on("reinit.player", function(event, slide) {
            if (slide) {
                _this.activeSlideIndex = slide.index;
            }
            if (event.namespace === "player") {
                _this.init("player");
            }
        });

        /* listen for player reinit */
        $(document).off("reinit.transcript").on("reinit.transcript", function(event, slide) {
            if (slide) {
                _this.activeSlideIndex = slide.index;
            }
            if (event.namespace == "transcript") {
                _this.init("transcript");
            }
        });

        /* listen for author reinit */
        $(document).off("reinit.author").on("reinit.author", function(event, slide) {
            if (slide) {
                _this.activeSlideIndex = slide.index;
            }
            if (event.namespace == "author") {
                _this.init("author");
            }
        });

        $(document).off("getPresentationJson").on("getPresentationJson", function(event, fn) {
            fn(_this.data);
        });

        $(document).off("postPresentationJson").on("postPresentationJson", function(event, data) {
            _this.data = data;
        });

    },
    /**
     * Makes ajax call to get presentation data and triggers "jsonPublished"
     * to let modules know that datas are available
     * @param {String} initWhat - which of the modules should be inited
     * @param {Object} layoutStates - 
     */
    getJson: function(initWhat, layoutStates) {
        var module = this;
        $.ajax({
            url: jsonuri + "&tmstm=" + '' + Math.floor(1000 * Math.random()),
            success: function(data) {

                if (typeof data === "object") {
                    if (data.slides && data.slides.length === 0) {
                        return;
                    }
                    module.publishJson(data, initWhat, layoutStates);
                } else {
                    try {
                        var obj = JSON.smartParse(data);
                        console.log('data', data)
                        if (typeof obj === "object") {
                            if (obj.slides && obj.slides.length === 0) {
                                return;
                            }
                            module.publishJson(obj, initWhat, layoutStates);
                        } else {
                            console.error(" JSON IS NOT VALID ");
                        }
                    } catch (e) {
                        console.error(" JSON IS NOT VALID ");
                    }
                }

            },
            error: function(error) {
                console.error(error);
            },
            complete: function() {

            }
        });
    },
    /**
     * Gets presentation data and triggers all or specific modules initialization
     * @param {Object} data - presentation player data taken from ajax
     * @param {String} initWhat - namespace of module if only one should be inited
     * @param {Object} layoutStates - active layout states object
     * */
    publishJson: function(data, initWhat, layoutStates) {

        /**
         * @NOTE just for test
         * !important to be removed after test */
        if (window.top && window.top.location.href.indexOf("durationchange") !== -1) {
            data.durationCalculated = 0;
        }
        /*  */

        this.data = data;
        if (initWhat && initWhat === "reference") { /* refresh only reference */
            $(document).trigger("jsonPublished." + initWhat, [this.data, layoutStates]);
        }
        else if (initWhat && initWhat === "navigation") { /* refresh only navigation */
            $(document).trigger("jsonPublished." + initWhat, [this.data, layoutStates]);
        }
        else if (initWhat && initWhat === "player") { /* refresh only player */
            $(document).trigger("jsonPublished." + initWhat, [this.data, layoutStates]);
        }
        else if (initWhat && initWhat === "author") { /* refresh only author */
            $(document).trigger("jsonPublished." + initWhat, [this.data, layoutStates]);
        }
        else if (initWhat && initWhat === "transcript") { /* refresh only author */
            $(document).trigger("jsonPublished." + initWhat, [this.data, layoutStates]);
        }
        else {
            $(document).trigger("jsonPublished", [this.data, layoutStates]);
        }

        /**
         * If Author and reference is editted, slideChange not required
         * */
        if (initWhat !== "author") {
            $(document).trigger("slideChange", this.data.slides["slide_" + this.activeSlideIndex]);
        }

    }
};

/**
 * This module is responsible to make presentation responsive
 * on different screen sizes and in fullscreen mode as well
 *   */
EW["presentation_resize"] = {
    virtualClass: "virtual_slides",
    fullscreenClass: "presentation_fullscreen",
    isFullscreen: false,
    init: function() {
        /** Full screen event to be implemented for flex container */
        var _this = this;

        var p = $("#presentation");
        var pLecture = $("#LectureContainer");
        var pBlock = $(".presentation-outer-container");
        this.bodyDom = $("body");

        $(document).on("jsonPublished", function(event, data, layoutStates) {

            /* POPUP VIRTUAL SLIDE  */
            if ((data.has_virtual_slides === true) && (typeof data.internal_virtual === "undefined")) {
                _this.hasVirtualSlides = true;
            }/* VIRTUAL SLIDE INSIDE SLIDE */
            else if ((data.has_virtual_slides === false) && (typeof data.internal_virtual === "undefined")) {
                _this.hasVirtualSlides = false;
            }/* SLIDE NORMAL */
            else {
                _this.hasVirtualSlidesInternal = true;
            }
        });

        /* Used mostly online */
        $(window).on("resize lectureContentWrapperDimensionsChanged", function() {
            _this.resizeHandler(pLecture, pBlock, p, true);
        });

        // Changed adding of event hander to body instead fo pBlock element because it was not working on FF.
        $(document).on("fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange", function(evt) {
            var state = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen || document.MSFullscreenChange;
            var event = state ? 'on' : 'off';
            var widthOverHeightRatio = 1.5;

            if (event === "on") {
                if (!_this.bodyDom.hasClass(_this.fullscreenClass)) {
                    _this.bodyDom.addClass(_this.fullscreenClass);
                }
                _this.isFullscreen = true;
            } else {
                if (_this.bodyDom.hasClass(_this.fullscreenClass)) {
                    _this.bodyDom.removeClass(_this.fullscreenClass);
                }
                _this.isFullscreen = false;

            }
            _this.resizeHandler(pLecture, pBlock, p);
        });

        $(document).on("navTplAdded layoutManagerReady", function() {
            _this.resizeHandler(pLecture, pBlock, p, true);
        });

    },
    /**
     * @param {Object} pLecture -  jquery dom object "#LectureContainer"
     * @param {Object} pBlock -  jquery dom object ".presentation-outer-container"
     * @param {Object} p -  jquery dom object "#presentation"
     * @param {Boolean} nodelay - if true envoke after fullscreen calculation functions without delay
     */
    resizeHandler: function(pLecture, pBlock, p, nodelay) {
        var _this = this;

        this.isDesktop = !!pLecture.length ? (pLecture.outerWidth(true)) > 767 : (pBlock.outerWidth(true) > 767);

        $(document).off("isDesktop").on("isDesktop", function(event, fn) {
            fn(_this.isDesktop);
        });

        if (!this.hasVirtualSlides) {
            this.hasVirtualSlides = _this.bodyDom.hasClass(_this.virtualClass);
        }

        if (this.isDesktop && this.isFullscreen) {
            /* Reset dimensions */
            clearTimeout(this.fullscreenTime);
            /**
             * If fullscreen event wait a bit until
             * presentation new dimensions are set up
             * */
            this.fullscreenTime = setTimeout(function() {
                p.css({
                    "width": "auto",
                    "height": "auto"
                });

                if (p.height() > window.innerHeight - 60) {
                    var width = p.width() * ((window.innerHeight - 60) / p.height());
                    p.css("width", width);
                }

            }, (nodelay ? 0 : 700)); //if no delay timer is 0

        } else if (this.isDesktop && !this.isFullscreen) {
            p.css({
                "width": "auto",
                "height": "auto"
            });
        } else if (this.isDesktop === false && this.hasVirtualSlides === true) {
            console.log(" not desktop and virtual slides ");
        }
        else {
            /* Mobile */
            var navDom = $("#presentationNavigation");
            var len = navDom.find("li").length;
            var itemwidth = navDom.parents('.presentation_separate_virtualslides').length ? 110 : 74;
            navDom.width(itemwidth * len);

        }
    }
};


/* Video player */
/* Listen =>
 * Trigger => 
 **/
EW["presentation_player"] = {
    containerSelector: "#presentationPlayer",
    templateSelector: "#presentationPlayerTemplate",
    noChangeModeClass: "slide_no_changemode",
    fullscreenClass: "presentation_fullscreen",
    playerContainerDom: {},
    containerDom: {},
    slideIndex: 1,
    slideId: null,
    dataSlidesCount: 0,
    playerMode: "video",
    videoExtraDelay: 0, //2000 => define in NEM
    audioRtmpEnabled: false,
    hasVirtualSlides: false,
    hasVirtualSlidesInternal: undefined,
    dataSlides: {},
    init: function() {
        this.containerDom = $(this.containerSelector);
        this.playerContainer = $("#presentationPlayerContainer");
        this.handleEvents();
        this.initPlayer();

        if (bowser.mobile || bowser.tablet) {
            //console.log(this.containerDom);
            this.containerDom.addClass('disable-fullscreen');
        }

    },
    /**
     * Checks if presentation slides list has another slide after current one
     * @return {Boolean} - false if we are in the last slide
     **/
    hasNext: function() {
        /* if virtual slides of slide are active "virtualSlide" will contain vitual slides list */
        var slidesByCondition = (!this.hasVirtualSlidesInternal) ? "dataSlides" : "virtualSlides";
        if (typeof this[slidesByCondition]["slide_" + (this.slideIndex + 1)] !== "undefined") {
            return true;
        } else {
            return false;
        }
    },
    /**
     * triggers respective events to go to next slide/virtual slide if there is a next slide
     */
    slideNext: function() {
        if (this.hasNext()) {
            this.slideIndex = this.slideIndex + 1;
            if (this.hasVirtualSlides === false) {
                $(document).trigger("slideChange", this.dataSlides["slide_" + this.slideIndex]);
            } else {
                $(document).trigger("virtualSlideChange", this.virtualSlides["slide_" + this.slideIndex]);
            }
        } else {
            if (this.hasVirtualSlides === false) {
                $(document).trigger("lastSlideActive", this.dataSlides["slide_" + this.slideIndex]);
            } else {
                $(document).trigger("virtualSlideChange", this.virtualSlides["slide_" + this.slideIndex]);
            }
        }
    },
    /**
     * triggers respective events to go to previous slide/virtual slide if there is a previous slide
     */
    slidePrev: function() {
        /* if virtual slides of slide are active "virtualSlide" will contain vitual slides list */
        var slidesByCondition = (!this.hasVirtualSlidesInternal) ? "dataSlides" : "virtualSlides";
        if (typeof this[slidesByCondition]["slide_" + (this.slideIndex - 1)] !== "undefined") {
            this.slideIndex = this.slideIndex - 1;
            if (this.hasVirtualSlides === false) {
                $(document).trigger("slideChange", this.dataSlides["slide_" + this.slideIndex]);
            } else {
                $(document).trigger("virtualSlideChange", this.virtualSlides["slide_" + this.slideIndex]);
            }
        } else {
            if (this.hasVirtualSlides === false) {
                $(document).trigger("firstSlideActive", this.dataSlides["slide_" + this.slideIndex]);
            } else {
                $(document).trigger("virtualSlideChange", this.virtualSlides["slide_" + this.slideIndex]);
            }
        }
    },
    /**
     * Init player after template ready,
     * Also handles some events related to player
     **/
    initPlayer: function() {
        var _this = this;

        $(document).on("videoTplAdded", function() {
            _this.addControlItems();
            var vidId = "slide_video";

            _this.videoPlayer = videojs(vidId, {
                plugins: {
                    //firstbtn: {},
                    //prevbtn: {},
                    nextbtn: {},
                    //lastbtn: {},
                    //fullscreenBtn: {},
                    //settingBtn: {},
                    //audioplayerBtn: {},
                    //videoplayerBtn: {},
                    //volumetoggleBtn: {},
                    //searchslidesBtn: {},
                    //restartBtn: {},
                    //gotoendBtn: {}
                }
            }, function() {
                /* handles progress time tooltip */
                this.progressTips();

            });

            /* set custom controls events after video is ready */
            _this.videoPlayer.ready(function() {
                _this.controlbarEvents();
                _this.sessionEvents();

                var newplayerdom = $('video[id*="slide_video"]');
                enableInlineVideo(newplayerdom[0]);
                newplayerdom.addClass('IIV');
            });

            /* saves volume level as user preference */
            _this.videoPlayer.persistvolume({
                namespace: "Virality-Is-Reality"
            });

            var originalDur;
            var firstTimeTimer;

            _this.videoPlayer.one('play', function() {

                /* check if first slide has video otherwise make a slide change
                 * and the available media will be found */
                if (_this.hasVirtualSlides === false) {
                    var firstSlide = _this.dataSlides["slide_" + _this.slideIndex];
                    if ((!firstSlide.video.mp4) && (!firstSlide.video.webm)) {
                        $(document).trigger("slideChange", firstSlide);
                    }
                }

                onfirstplay(false);

                setTimeout(function() {
                    _this.videoPlayer.on("firstplay", onfirstplay);
                }, 200);

            });
            function onfirstplay(shouldPause) {

                clearTimeout(firstTimeTimer);
                firstTimeTimer = setTimeout(function() {
                    originalDur = _this.videoPlayer.duration();
//                    _this.videoPlayer.duration( durationCalculated + durationExtra);
                }, 500);
                if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                    $(document).trigger("videoPlayerInited");
                }

                /* Pause on edit mode */
                if (_this.playerEditMode && _this.playerEditMode === true) {
                    if (shouldPause !== false) {
                        $(document).trigger("slidePause");
                    }
                }
            }

            var progresManTimer;
            $(document).on("slideChange", function(event, data) {
                clearTimeout(_this.vEndTimer);
                clearInterval(progresManTimer);
            });


            var currentTimeDom = _this.containerDom.find(".vjs-current-time-display");
            var allTimeDom = _this.containerDom.find(".vjs-duration-display");

            /**
             * Handles Duration extra
             * */
            _this.videoDurationExtra();


            var lastplaytime;
            _this.videoPlayer.on("timeupdate", function() {
                var currentTime = _this.videoPlayer.currentTime();
                if (!_this.videoPlayer.paused()) {
                    $(document).trigger("videoTimeUpdate", currentTime);
                } else {
                    $(document).trigger("videoTimeUpdate.annoedit", currentTime);
                    $(document).trigger("videoTimeUpdate.annoprev", currentTime);
                }
            });



            $(document).on("getPlayer", function(event, fn) {
                fn(_this.videoPlayer);
            });

            /* remove poster on loadedmetada in video mode */
            _this.playerContainer.find("video").on('loadedmetadata', function() {

                try {
                    var slide = _this.dataSlides["slide_" + _this.slideIndex];
                    if (_this.playerMode === "video" && ((slide.video && slide.video.mp4 && slide.video.mp4.url && slide.video.mp4.url !== "") ||
                            (slide.video && slide.video.webm && slide.video.webm.url && slide.video.webm.url !== ""))) {
                        _this.containerDom.find("video").attr("poster", "");
                    }
                } catch (e) {
                    console.error(e);
                }
                $(document).trigger("loadedMetadata", _this.videoPlayer);
            });

        });

        $("#editPlayerItemSave").on("click", function(event, data) {
            $(document).trigger("reinit.player", _this.dataSlides["slide_" + _this.slideIndex]);
            $(document).trigger("reinit.navigation", _this.dataSlides["slide_" + _this.slideIndex]);
        });

    },
    /**
     * Gets extra duration of current slide or the predefined one
     * @returns {Number} extra duration in milli seconds
     */
    getDurationExtra: function() {
        /**
         * @Note slide.durationExtra( from json ) is in seconds WHEREAS
         * durationExtra below is in millisecond ( because setTimeout need it that way ),
         * but player expects it in seconds */
        var durationExtra;
        /* if exists or different */
        if (this.dataSlides["slide_" + this.slideIndex].durationExtra) {
            durationExtra = Number(this.dataSlides["slide_" + this.slideIndex].durationExtra) * 1000; /* was parseInt() */
        } else {
            durationExtra = this.videoExtraDelay;
        }
        return durationExtra;
    },
    /**
     * Handles Duration extra
     *   If positive  => adds extra time to video ( next slide with delay)
     *   If negative => changes video duration ( actualDuration + extraduration which in this case is negative )
     */
    videoDurationExtra: function() {

        var _this = this;

        var durationExtra = _this.getDurationExtra();

        /**
         * check whether duration is positive or negative
         * */

        /* Delays slideNext call */
        _this.videoPlayer.on("ended", function() {
            if (typeof durationExtra !== "undefined" && durationExtra >= 0) {

                if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                    return;
                }
                /* @DONT change slide on end if edit mode is active */
                if (typeof _this.playerEditMode === "undefined" || _this.playerEditMode === false) {
                    /* EXTRA DURATION */
                    clearTimeout(_this.vEndTimer);
                    _this.vEndTimer = setTimeout(function() {
                        _this.slideNext();
                    }, durationExtra);

                }
            }
        });

        var videoDurationInSec,
                durationExtraInSec,
                finalDurationInSec,
                finalDurationInSecFloor;

        /** In order to get duration of player we should wait until
         * it starts playing only then we are able to get it */
        _this.videoPlayer.on('loadedmetadata', function() {

            durationExtra = _this.getDurationExtra();

            /* the above event will trigger on every slide,
             * we have to quit code if duration is positive */
            if (typeof durationExtra !== "undefined" && durationExtra >= 0) {
                return;
            }
            /* getting video duration */
            videoDurationInSec = _this.videoPlayer.duration();

            /** In case video.duration() is not provided we can use slide.durationCalculated shown below */
//            videoDurationInSec = Number(_this.dataSlides["slide_" + _this.slideIndex].durationCalculated);

            durationExtraInSec = durationExtra / 1000;

            finalDurationInSec = videoDurationInSec + durationExtraInSec;
            finalDurationInSecFloor = Math.floor(finalDurationInSec);

            if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                return;
            }

            /** @NOTE
             * DONT change slide on end if edit mode is active.
             * We ONLY affect duration in Review and Publish Mode NOT in edit mode */
            /* COMMENTED FOR NOW */
//            if (typeof _this.playerEditMode === "undefined" || _this.playerEditMode === false) {
            _this.videoPlayer.duration(finalDurationInSec);
//            }

        });

        /**
         * @TODO - Comment
         *  */
        _this.videoPlayer.on('timeupdate', function() {

            if (typeof durationExtra !== "undefined" && durationExtra >= 0) {
                return;
            }

            if (!_this.videoPlayer.paused()) {
                var currentTime = _this.videoPlayer.currentTime();
                var currentTimeInteger = Math.floor(currentTime);

                /**
                 * If player's currentTime is equal to slide's finalDuration
                 * go to Next Slide
                 * */
                if (finalDurationInSecFloor === currentTimeInteger) {
                    /** Check if has next */
                    if (_this.hasNext()) {
                        _this.slideNext();
                    } else {
                        _this.videoPlayer.pause();
                        _this.videoPlayer.currentTime(0);

                        _this.videoPlayer.posterImage.show();
                        _this.videoPlayer.bigPlayButton.show();
                    }

                }

            }
        });

    },
    /**
     * Add Buttons to controlbar
     * and also attach its events
     * */
    addControlItems: function() {
        var _this = this;

        var Button = videojs.getComponent('Button');

        console.log(videojs.extend, _this);

        videojs.nextBtn = videojs.extend(Button, {
            constructor: function() {
                Button.apply(this, arguments);
                /* initialize your button */
            },
            createEl: function(){

            },
            handleClick: function() {
                /* do something on click */
            }
        });

        videojs.registerComponent('nextbtn', videojs.nextBtn);

        // videojs.nextBtn = videojs.Button.extend({
            // /** @constructor */
            // init: function(player, options) {
                // videojs.Button.call(this, player, options);
                // this.on('click', this.onClick);
            // }
        // });


        // Note that we're not doing this in prototype.createEl() because
        // it won't be called by Component.init (due to name obfuscation).
        var createButton = function(type, title) {
            var props = {
                className: 'vjs-' + type + '-button vjs-control',
                innerHTML: '<div class="vjs-control-content" title="' + title + '"><span class="vjs-control-text">' + type + 'Btn</span></div>',
                role: 'button',
                'aria-live': 'polite', // let the screen reader user know that the text of the button may change
                tabIndex: 0
            };
            //return videojs.Component.prototype.createEl(null, props);
            return videojs.createEl(null, props);
        };

        var nextBtn;
        videojs.registerPlugin('nextbtn', function() {
            var options = {'el': createButton("next", "Next slide")};
            nextBtn = new videojs.nextBtn(this, options);
            //this.controlBar.el().appendChild(nextBtn.el());
            this.getChild('controlBar').addChild(nextBtn, {});
        });

        /* vjs-next-button */
        videojs.nextBtn.prototype.onClick = function() {
        };

    },
    /**
     * @NOTE not working on 
     * Click events of custom controlbar items
     **/
    controlbarEvents: function() {
        var _this = this;
        var time_out;
        /* vjs-next-button */
        customControlEvent(".vjs-next-button", function() {
            //this.containerDom.append('<div style="position:relative;">Next clicked</div>')
            clearTimeout(time_out);
            time_out = setTimeout(function(){
                if (_this.slideStatus().isLast === false) {
                    _this.slideNext();
                }
            }, 500);
        });

        /* vjs-prev-button */
        customControlEvent(".vjs-prev-button", function() {
            clearTimeout(time_out);
            time_out = setTimeout(function(){
                if (_this.slideStatus().isFirst === false) {
                    _this.slidePrev();
                }
            }, 500);
        });

        /* vjs-first-button  */
        customControlEvent(".vjs-first-button", function() {
            if (_this.slideStatus().isFirst === false) {
                _this.slideIndex = 1;
                if (_this.hasVirtualSlides === false) {
                    $(document).trigger("slideChange", _this.dataSlides["slide_1"]);
                } else {
                    $(document).trigger("virtualSlideChange", _this.virtualSlides["slide_1"]);
                }
            }
        });

        /* vjs-last-button */
        customControlEvent(".vjs-last-button", function() {
            if (_this.slideStatus().isLast === false) {
                _this.slideIndex = _this.dataSlidesCount;
                if (_this.hasVirtualSlides === false) {
                    $(document).trigger("slideChange", _this.dataSlides["slide_" + _this.dataSlidesCount]);
                } else {
                    $(document).trigger("virtualSlideChange", _this.virtualSlides["slide_" + _this.dataSlidesCount]);
                }
            }
        });

        /* vjs-fullscreen-button */
        customControlEvent(".vjs-fullscreen-button", function() {
            $(document).trigger("isDesktop", function(isDesktop) {
                var goFullscreenSel = isDesktop ? ".presentation-outer-container" : ".presentation-outer-container video";
                _this.goFullscreen($(goFullscreenSel)[0]);
            });

        });

        /* vjs-setting-button */
        customControlEvent(".vjs-setting-button", function() {
            _this.settings();
        });

        /* vjs-audioplayer-button */
        customControlEvent(".vjs-audioplayer-button", function() {
            /* Action */
            if (!_this.changeModeAudioBtn) {
                _this.changeModeAudioBtn = $(".vjs-audioplayer-button");
            }
            $(document).trigger("playerModeChange", {canchange: !_this.changeModeAudioBtn.hasClass("disabled")});
        });

        /* vjs-videoplayer-button */
        customControlEvent(".vjs-videoplayer-button", function() {
            /* Action */
            if (!_this.changeModeVideoBtn) {
                _this.changeModeVideoBtn = $(".vjs-videoplayer-button");
            }
            $(document).trigger("playerModeChange", {canchange: !_this.changeModeVideoBtn.hasClass("disabled")});
        });

        /* vjs-volumetoggle-button */
        customControlEvent(".vjs-volumetoggle-button", function() {
            /* on/off on mobile */
            _this.containerDom.toggleClass("volume_bar_mobile");
        });

        /* vjs-searchslides-button */
        customControlEvent(".vjs-searchslides-button", function() {
            $(document).trigger("slidesSearchMobile");
        });

        /* vjs-restart-button */
        customControlEvent(".vjs-restart-button", function() {
            _this.videoPlayer.currentTime(0);
        });

        /* vjs-gotoend-button */
        customControlEvent(".vjs-gotoend-button", function() {
            var durationCalculated = _this.dataSlides["slide_" + _this.slideIndex].durationCalculated;
            durationCalculated = parseFloat(durationCalculated);
            var endtime = _this.videoPlayer.duration();
            var timetogo = endtime || durationCalculated;
            //console.log(timetogo);
            _this.videoPlayer.currentTime(timetogo - 2);
        });


        function customControlEvent(selector, fn) {
            var el = _this.containerDom.find(selector);
            el.on("click.control touchstart.control", function(e) {
                fn();
                e.stopPropagation();
            });
        }

    },
    /**
     * Toggles preferences when clicking in
     * settings button of controlbar
     **/
    settings: function() {
        if (!this.playerSettingsDom) {
            this.playerSettingsDom = $("#presentationPlayerSettings");
        }

        if (this.settingsShown === true) {
            this.playerSettingsDom.hide();
            this.settingsShown = false;
        } else {
            this.playerSettingsDom.show();
            this.settingsShown = true;
        }
    },
    handleEvents: function() {
        var _this = this;
        $(document).on("jsonPublished.player", function(event, data) {

            /* POPUP VIRTUAL SLIDE  */
            if ((data.has_virtual_slides === true) && (typeof data.internal_virtual === "undefined")) {
                _this.hasVirtualSlides = true;
            }/* VIRTUAL SLIDE INSIDE SLIDE */
            else if ((data.has_virtual_slides === false) && (typeof data.internal_virtual === "undefined")) {
                _this.hasVirtualSlides = false;
            }/* SLIDE NORMAL */
            else {
                _this.hasVirtualSlides = undefined;
                _this.hasVirtualSlidesInternal = true;
            }

            if (_this.hasVirtualSlides) {
                _this.virtualJson = data;

                var bodyDom = $("body");

                if (!bodyDom.hasClass("virtual_slides")) {
                    bodyDom.addClass("virtual_slides");
                }
            }

            if (_this.hasVirtualSlidesInternal) {
                _this.virtualSlides = data.virtual_slides;
            } else {
                _this.virtualSlides = data.slides;
            }

            _this.dataSlidesCount = 0;
            /* using virtualSlides Object because it contains data.slides
             * were normal slide mode is active */
            for (var key in _this.virtualSlides) {
                _this.dataSlidesCount = _this.dataSlidesCount + 1;
            }

            _this.dataSlides = data.slides;

        });

        $(document).on("slideChange", function(event, slide) {

            if (_this.hasVirtualSlides !== true) {
                _this.slideIndex = parseInt(slide.index);
                _this.slideId = parseInt(slide.slide_id);

                if (!_this.videoPlayer) {
                    _this.templating(slide);
                } else {

                    /* checks to set either audio or video */
                    _this.setMediaMode(slide);

                    if (slide.video_start_time && slide.video_start_time !== "") {
                        _this.videoPlayer.currentTime(parseInt(slide.video_start_time));
                    }

                    _this.videoPlayer.play();

                }

                _this.slideStatus(true);

                setTimeout(function() {
                    $(document).trigger("videoDuration", _this.videoPlayer.duration());
                }, 1000);
                _this.replaceEditUrl();
                /* disables/enables change audio/video mode based on its items */
                _this.playerModeAction(_this.playerMode);
            } else {
                _this.virtualSlidesHandle();
            }

        });

        $(document).on("virtualSlideChange", function(event, slide) {
            if (slide && slide.start_time) {
                _this.videoPlayer.currentTime(slide.start_time);
            }

            _this.slideIndex = slide.index;
            _this.slideStatus();
        });

        $(document).on("virtualSlideChange.navigation", function(event, slide) {
            _this.slideIndex = slide.index;
            _this.slideStatus(true);
        });

        /**
         * Handles player mode change
         * Going from video mode to audio mode or vice versa
         *   */
        $(document).on("playerModeChange", function(event, data) {
            if (data && (typeof data.canchange !== "undefined") && data.canchange === false) {
                return;
            }
            _this.playerModeAction(_this.playerMode);

            var time = _this.videoPlayer.currentTime();
            if (_this.playerMode === "video") {
                _this.playerMode = "audio";
                _this.containerDom.addClass("audio_mode");

                _this.setMediaMode(_this.dataSlides["slide_" + _this.slideIndex], time, true);
            } else {
                _this.playerMode = "video";
                _this.containerDom.removeClass("audio_mode");

                _this.setMediaMode(_this.dataSlides["slide_" + _this.slideIndex], time, true);
            }

        });

        /** Pauses player if it is playing */
        $(document).on("slidePause", function() {
            if (!_this.videoPlayer.paused()) {
                _this.videoPlayer.pause();
            }
        });

        /** Start player if it is paused */
        $(document).on("slidePlay", function() {
            if (_this.videoPlayer.paused()) {
                _this.videoPlayer.play();
            }
        });

        $(document).on("subtitlesTimeUpdate", function(event, time) {
            if (typeof time !== 'undefined') {
                _this.videoPlayer.currentTime(time);
            }
        });

        $(document).on("annotationsTimeUpdate", function(event, time) {
            if (time) {
                _this.videoPlayer.currentTime(time);
            }
        });

        var constrolBarDom;
        $(document).on("slideChangeStatus", function(event, obj) {
            if (!constrolBarDom) {
                constrolBarDom = _this.containerDom.find(".vjs-control-bar");
            }
            constrolBarDom.find(">*").removeClass("disabled");
            if (obj.isFirst === true) {
                constrolBarDom.find(".vjs-prev-button,.vjs-first-button").addClass("disabled");
            }
            if (obj.isLast === true) {
                constrolBarDom.find(".vjs-next-button,.vjs-last-button").addClass("disabled");
            }
        });

        $(document).on("player_editmode", function(event, mode) {
            if (mode.mode) {
                _this.playerEditMode = true;
                _this.playerContainer.addClass("editmode_player");
            } else {
                _this.playerEditMode = false;
                _this.playerContainer.removeClass("editmode_player");
            }
        });

    },
    virtualSlidesHandle: function() {
        var _this = this;
        _this.templating(_this.virtualJson);
    },
    replaceEditUrl: function() {
        try {
            if (!this.regexEditDataUrl) {
                this.regexEditDataUrl = this.containerDom.find(".editmode_player_edit").attr("data-url");
            }
            if (this.regexEditDataUrl) {
                var dtUrl = this.regexEditDataUrl.replace(/\{slide_id\}/g, this.slideId);
                this.containerDom.find(".editmode_player_edit").attr("data-url", dtUrl);
            }
        } catch (e) {
            console.error(e);
        }
    },
    playerModeAction: function(mode) {
        /* mode => audio, video */

        var slide = this.dataSlides["slide_" + this.slideIndex];

        if (!this.changeModeVideoBtn || this.changeModeVideoBtn.length === 0) {
            this.changeModeVideoBtn = $(".vjs-videoplayer-button");
        }
        if (!this.changeModeAudioBtn || this.changeModeAudioBtn.length === 0) {
            this.changeModeAudioBtn = $(".vjs-audioplayer-button");
        }

        this.changeModeAudioBtn.removeClass("disabled");
        this.changeModeVideoBtn.removeClass("disabled");

        /* if we are in video mode we check for audi */
        if (mode === "video") {
            if (slide && !slide.big_img) {
                /* audio mode not possible  */
                this.changeModeAudioBtn.addClass("disabled");

                $(document).trigger("playerModeChange", {canchange: false});
            }
        } else { /* audio */

            if (slide.video.mp4 && (slide.video.mp4.url || slide.video.webm.url)) {

            } else { /* video doesnt exits */

                this.changeModeVideoBtn.addClass("disabled");

                this.containerDom.addClass("audio_mode");
                $(document).trigger("playerModeChange", {canchange: false});
            }
        }

    },
    /**
     * PARAMS
     * 1. slide (Obj),
     * 2. time to set (optional - integer),
     * 3. show notification (optional - Boolean) */
    setMediaMode: function() {

        var slide = arguments[0];
        var changeAvailable = true;
        /* if current slide has video (mp4 or webm) and also mp3 => change mode will be available 
         * else not available and change mode icon will be hidden */
        if (((slide.video.mp4 && slide.video.mp4.url && slide.video.mp4.url !== "") || (slide.video.webm && slide.video.webm.url && slide.video.webm.url !== ""))
                && (slide.audio.mp3 && slide.audio.mp3.url && slide.audio.mp3.url !== "")) {
            changeAvailable = true;
        } else {
            changeAvailable = false;
        }

        /* add/remove "slide_no_changemode" class */
        changeAvailable ? this.playerContainer.removeClass(this.noChangeModeClass) : this.playerContainer.addClass(this.noChangeModeClass);

        if (this.playerMode === "video") {
            this.setVideo.apply(this, arguments);
        } else {
            this.setAudio.apply(this, arguments);
        }

    },
    setVideo: function(slide, at, notify) {
        var _this = this;

        if (!this.posterDom) {
            this.posterDom = $(".vjs-poster");
        }
        if ((slide.video.mp4 && slide.video.mp4.url && slide.video.mp4.url !== "") && (slide.video.webm && slide.video.webm.url && slide.video.webm.url !== "")) {

            try {
                this.videoPlayer.src([
                    {"type": slide.video.mp4.type, "src": STREAMING_URL + slide.video.mp4.url + APP_UNI + '&stamp=' + new Date().getTime()}, /* mp4 */
                    {"type": slide.video.webm.type, "src": VIDEO_FILE_URL_RC + slide.video.webm.url + APP_UNI + '&stamp=' + new Date().getTime()} /* webm */
                ]);

                if (this.playerEditMode !== true) {
                    this.videoPlayer.play();
                } else {
                    /* pause state pause control shown bug fix */
                    this.videoPlayer.play();
                    this.videoPlayer.pause();
                }

                this.videoPlayer.duration(parseInt(slide.durationCalculated) + parseInt(slide.durationExtra));
            } catch (e) {
                console.error(e);
            }
        }/* mp4 only*/
        else if (slide.video.mp4 && slide.video.mp4.url && slide.video.mp4.url !== "") { /* mp4 */

            try {
                this.videoPlayer.src({"type": slide.video.mp4.type, "src": STREAMING_URL + slide.video.mp4.url + APP_UNI + '&stamp=' + new Date().getTime()});
//                this.posterDom.hide();

                if (this.playerEditMode !== true) {
                    this.videoPlayer.play();
                } else {
                    setTimeout(function() {
                        /* pause state pause control shown bug fix */
                        _this.videoPlayer.play();
                        _this.videoPlayer.pause();
                    }, 400);

                }

                this.videoPlayer.duration(parseInt(slide.durationCalculated) + parseInt(slide.durationExtra));
            } catch (e) {
                console.error(e);
            }


        } /* webm only */
        else if (slide.video.webm && slide.video.webm.url && slide.video.webm.url !== "") { /* webm */

            try {
                this.videoPlayer.src({"type": slide.video.webm.type, "src": VIDEO_FILE_URL_RC + slide.video.webm.url + APP_UNI + '&stamp=' + new Date().getTime()});
//                this.posterDom.hide();

                if (this.playerEditMode !== true) {
                    this.videoPlayer.play();
                } else {
                    /* pause state pause control shown bug fix */
                    this.videoPlayer.play();
                    this.videoPlayer.pause();
                }

                this.videoPlayer.duration(parseInt(slide.durationCalculated) + parseInt(slide.durationExtra));
            } catch (e) {
                console.error(e);
            }


        } else { /* other */
            console.error("NO VIDEO AVAILABLE => REDIRECT TO AUDIO");
            this.setAudio(slide);
        }

        if (at) {
            this.videoPlayer.currentTime(at);
        }
        if (notify) {
            var title_v = 'Video mode activated';
            var content_v = '';
            $(document).trigger("smart-notification", {
                title: title_v, content: content_v, type: "success"
            });
        }

    },
    setAudio: function(slide, at, notify) {
        var _this = this;
        if (!this.posterDom) {
            this.posterDom = $(".vjs-poster");
        }

        if (slide.audio.mp3 && slide.audio.mp3.url && slide.audio.mp3.url !== "") {
            try {
                if (this.audioRtmpEnabled) {
                    this.videoPlayer.src({"type": slide.audio.mp3.type, "src": STREAMING_URL + slide.audio.mp3.url + APP_UNI + '&stamp=' + new Date().getTime()});
                } else {
                    this.videoPlayer.src({"type": "audio/mp3", "src": APP_URL + slide.audio.mp3.url + APP_UNI + '&stamp=' + new Date().getTime()});
                }

                $("video").attr("poster", APP_URL + slide.big_img);
                this.posterDom.hide();
                if (slide.big_img) {
                    this.containerDom.find("video").attr("poster", "").attr("poster", APP_URL + slide.big_img + APP_UNI + '&stamp=' + new Date().getTime());
                } else {
                    this.posterDom.css('background-image', 'url(' + APP_URL + slide.big_img + APP_UNI + '&stamp=' + new Date().getTime() + ')').show();
                }

                if (this.playerEditMode !== true) {
                    this.videoPlayer.play();
                } else {
                    /* pause state pause control shown bug fix */
                    this.videoPlayer.play();
                    this.videoPlayer.pause();
                }

                try {
                    this.videoPlayer.duration(parseInt(slide.durationCalculated) + parseInt(slide.durationExtra));
                } catch (e) {
                    console.error(e);
                }

            } catch (e) {
                console.error("audio error");
            }
        } else { /* ONLY IMG */

            this.videoPlayer.src({"type": "audio/mp3", "src": APP_URL + "include_js/player/point1sec.mp3"});

            this.posterDom.hide();

            if (slide.big_img) {
                this.containerDom.find("video").attr("poster", "").attr("poster", APP_URL + slide.big_img + APP_UNI + '&stamp=' + new Date().getTime());
            }

            if (this.playerEditMode !== true) {
                this.videoPlayer.play();
            } else {
                /* pause state pause control shown bug fix */
                this.videoPlayer.play();
                this.videoPlayer.pause();
            }

            try {
                var newDuration = (parseInt(slide.durationExtra) >= 0) ? parseInt(slide.durationExtra) : 0;
                this.videoPlayer.duration(newDuration);
            } catch (e) {
                console.error(e);
            }
            ;
        }

        if (at) {
            this.videoPlayer.currentTime(at);
        }
        if (notify) {
            var title_v = 'Image mode activated';
            var content_v = ' ';
            $(document).trigger("smart-notification", {
                title: title_v, content: content_v, type: "success"
            });
        }
    },
    goFullscreen: function(vid) {
        var state = document.fullScreen || document.mozFullScreen || document.webkitIsFullScreen || document.MSFullscreenChange;

        if (!this.bodyDom) {
            this.bodyDom = $("body");
        }

        //console.log(screenfull.enabled);

        if (screenfull.enabled !== true) {
            var simulateFullscreenClass = 'simulate-fullscreen';
            $('body').toggleClass(simulateFullscreenClass);
            if (!$('body').hasClass(simulateFullscreenClass)) {
                $(window).scrollTop(0);
            }
        }else{
            if (!state) {
                try { //go full-screen
                    if (vid.requestFullscreen) {
                        vid.requestFullscreen();
                    } else if (vid.webkitRequestFullscreen) {
                        vid.webkitRequestFullscreen();
                    } else if (vid.mozRequestFullScreen) {
                        vid.mozRequestFullScreen();
                    } else if (vid.msRequestFullscreen) {
                        vid.msRequestFullscreen();
                    }

                } catch (e) {
                    console.error(e);
                }
            } else {
                try { // exit full-screen

                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                } catch (e) {
                    console.error(e);
                }
            }
        }

    },
    /**
     * Generating template info from slide object
     * using jsrender.
     * Triggers "videoTplAdded" after finish
     * @param {Object} slide - current slide data object
     **/
    templating: function(slide) {

        var _this = this;
        try {
            var template = $.templates(this.templateSelector);
            var slideReady = _this.slideConfigCheck(slide);
            var htmlOutput = template.render(slideReady);
            this.containerDom.html(htmlOutput);
        } catch (e) {
            console.error(e);
        }
        $(document).trigger("videoTplAdded");
    },
    /**
     * This function is responsible
     * for configuration preferences of player's slide
     * @param {Object} slide
     * @returns {Object} slide ready
     */
    slideConfigCheck: function(slide) {

        if (slide.big_img.indexOf("http") === -1) {
            slide.big_img = APP_URL + slide.big_img;
        }

        /** If big image does not exist and cover img is available,
         * user cover img */
        if (!$.trim(slide.big_img) &&
                presentationConfig.player.extra.coverImage &&
                presentationConfig.player.extra.coverImage.indexOf("http") !== -1) {
            slide.big_img = presentationConfig.player.extra.coverImage;
        }

        return slide;
    },
    /* slide Status 1. param shouldTrigger (Boolean) 
     * true if an event should be triggered (true when called from slideChange and virtualSlideChange) */
    slideStatus: function(shouldTrigger) {
        var slideStatus = {};
        slideStatus.currentNr = this.slideIndex;
        slideStatus.allNr = this.dataSlidesCount;
        slideStatus.isFirst = (slideStatus.currentNr === 1) ? true : false;
        slideStatus.isLast = (slideStatus.currentNr === slideStatus.allNr) ? true : false;
        slideStatus.isBetween = (slideStatus.isFirst === false && slideStatus.isLast === false) ? true : false;
        /**
         * @param 
         * Int currentNr, Int allNr, Boolean isFirst, Boolean isLast, Boolean isBetween
         **/
        if (shouldTrigger) {
            /** timeout is used because "slideChangeStatus" was being triggered before
             * "slideChange" in EW[presentation_navigation] causing currentTime not being reflected correctly  */
            setTimeout(function() {
                $(document).trigger("slideChangeStatus", slideStatus);
            }, 400);
        }
        return slideStatus;
    },
    /* This function is responsble for
     * triggering player based events
     * which are used for keeping alive/killing user session  */
    sessionEvents: function() {

        var _this = this;
        var playingTriggerDelay = 5;

        _this.videoPlayer.one('play', function() {
            /* keep session alive */
            $(document).trigger("presentation.playing");
        });

        var lastplaytime;
        _this.videoPlayer.on('timeupdate', function() {
            if (!_this.videoPlayer.paused()) {

                var currentTime = _this.videoPlayer.currentTime();
                /* keep session alive */
                var sessionPlayTime = Math.ceil(currentTime);
                if (sessionPlayTime && (sessionPlayTime != lastplaytime) && (sessionPlayTime % playingTriggerDelay === 0)) {
                    lastplaytime = sessionPlayTime;
                    $(document).trigger("presentation.playing");
                }

            }
        });
        $(document).on("slideChange", function() {
            $(document).trigger("presentation.slidechange");
        });
    }
};


EW["presentation_annotations"] = {
    annotationSelector: ".annotation",
    annotationCloseSelector: ".annotation-close",
    playerContainerDom: {},
    annotationFlag: true,
    userBasketPrefs: [],
    layoutStates: undefined,
    blockIfEmpty: false,
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {
        var _this = this;
        this.playerContainerDom = $("#presentationPlayer");

        EW["presentation_layout_manager"].setConfig({fn: function(layoutStates) {
                _this.layoutStates = layoutStates;
            }});

        if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("annotationInit") === -1) {
            console.log("Not initing annotation (based on config)");
            return;
        }

        $(document).on("jsonPublished", function(event, data) {
            if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("annotationNotEmpty") !== -1
                    && data.annotationsEmpty === true) { /* replace false with annotation flag */
                _this.blockIfEmpty = true;
                EW["presentation_layout_manager"].setConfig({
                    config: {
                        annotations: {
                            show: "false"
                        }
                    }
                });
                return;
            }
        });

        $(document).on("slideChange.annotation", function(event, slide) {
            _this.activeSlide = slide;
            _this.getUserPrefs();

            if (_this.blockIfEmpty) {
                return;
            }

            if (_this.annotationFlag === true) {

                _this.activeAnnotations = [];

                _this.playerContainerDom.find(".annotation").each(function() {
                    $(this).remove();
                });

                /* clear slideAnnotations before async request of getting newer ones*/
                _this.slideAnnotations = [];
                _this.getAnnotations(slide.annotation);
            }
        });

        $(document).on('playerSettingsChange', function(event, data) {
            /** Annotation Enable/Disable */
            if (typeof data.annotation === "boolean") {
                _this.annotationFlag = data.annotation;
                if (_this.annotationFlag) {
                    _this.enableAnnotations();
                } else {
                    _this.disableAnnotations();
                }
            }

            if (data.editmodeplayer === true) {
                _this.disableAnnotations();
            }/* preview mode, no edit mode */
            else {
                /*  TODO ANNOTATION PREVIEW START */
                if (_this.annotationFlag) {
                    _this.getAnnotations(_this.activeSlide.annotation, function() {
                        _this.enableAnnotations();

                        if (!_this.videoPlayer) {
                            $(document).trigger("getPlayer", function(player) {
                                _this.videoPlayer = player;
                                var time = _this.videoPlayer.currentTime() || 0;
                                $(document).trigger("videoTimeUpdate.annoprev", time);
                            });
                        } else {
                            var time = _this.videoPlayer.currentTime() || 0;
                            $(document).trigger("videoTimeUpdate.annoprev", time);
                        }

                    });
                }
            }

        });

        $(document).on("click.annobasket", ".annotation .reference-basket", function() {

            var id = $(this).attr("date-ned");
            if (!!id) {
                /**/
                $(document).trigger("changedAnnotationBasket", {id: id, status: true});
            }
        });

        /* Listen for reference add to basket */
        $(document).on("changedReferenceBasket changedAnnotationBasket", function(event, data) {
            var title = 'This item is alredy in your reading basket';
            if (data.title) {
                title = data.title;
            }

            var notToBasketHtml = '<a href="javascript:void(0);" class="reference-basket disabled" title="' + title + '"><i class="fa fa-bookmark disabled"></i></a>';

            $(".annotation .reference-basket").each(function() {
                if ($(this).attr("date-ned") === data.id) {
                    $(this).replaceWith(notToBasketHtml);
                }
            });

        });

        this.enableAnnotations();

    },
    getUserPrefs: function() {
        var _this = this;
        $(document).trigger("getUserPref", function(data) {
            _this.userBasketPrefs = data;
        });
    },
    enableAnnotations: function() {
        var _this = this;
        _this.activeAnnotations = [];
        this.getUserPrefs();

        $(document).off("videoTimeUpdate.annoprev").on("videoTimeUpdate.annoprev", function(event, time) {

            if (_this.slideAnnotations && _this.slideAnnotations.length && (_this.annotationFlag !== false)) {
                _this.addAnnotation(time);
            }
        });

        this.playerContainerDom.off("click.annotation").on("click.annotation", this.annotationCloseSelector, function() {
            _this.closeAnnotation($(this));
        });
        this.playerContainerDom.off("click.annoopen").on("click.annoopen", ".annotation", function() {

            $(document).trigger("slidePause");
        });
    },
    disableAnnotations: function() {
        $(document).off("videoTimeUpdate.annotation");
        $(document).off("videoTimeUpdate.annoprev");

        this.playerContainerDom.off("click.annotation");
        this.activeAnnotations = [];
        this.playerContainerDom.find(".annotation").each(function() {
            $(this).remove();
        });
    },
    closeAnnotation: function(el, parent) {

        if (parent) {
            parent.remove();
        } else {
            el.closest(this.annotationSelector).remove();
        }
    },
    activeAnnotations: [],
    /** displays current annotation on REVIEW mode */
    addAnnotation: function(time) {
        var _this = this;

        var i = 0;
        var annotationCloseTemplate;
        for (i; i < this.slideAnnotations.length; i++) {

            if ((this.activeAnnotations.indexOf(i) === -1) &&
                    this.slideAnnotations[i].startTime <= time && time <= this.slideAnnotations[i].endTime) {

                annotationCloseTemplate = '<span data-index="{index}" class="annotation-close" title="Close annotation" data-start-time="{start}"  data-end-time="{end}" class="txt-color-red pull-right"><i class="fa fa-lg fa-times"></i></span>';

                var annotation = $.parseHTML(this.slideAnnotations[i].text);
                annotationCloseTemplate = annotationCloseTemplate.replace(/\{index\}/g, i);
                annotationCloseTemplate = annotationCloseTemplate.replace(/\{start\}/g, this.slideAnnotations[i].startTime);
                annotationCloseTemplate = annotationCloseTemplate.replace(/\{end\}/g, this.slideAnnotations[i].endTime);
                this.activeAnnotations.push(i);

                $(annotation).append(annotationCloseTemplate);

                /* annotation basket */
                var anno;

                anno = handleElement($(annotation), annotation);

                this.playerContainerDom.append(anno);
            }
        }

        function handleElement(el, annotation) {
            try {
                var id = el.attr("data-reference-id");
                var isAvailable;
                if (_this.userBasketPrefs["reference_" + id] && typeof _this.userBasketPrefs["reference_" + id].toBasket !== "undefined") {
                    isAvailable = !_this.userBasketPrefs["reference_" + id].toBasket;
                } else {
                    isAvailable = true;
                }

                if (isAvailable) {
                    var basketActive = '<a href="javascript:void(0);" class="reference-basket text-success save-fav" date-fav="in" date-ned="' + id + '" title="Add to the reading basket" data-title="The surgical approach to subaxial cervical spine injuries: an evidence-based algorithm based on the SLIC classification system."><i class="fa fa-bookmark"></i></a>';
                    el.find('.reference-basket').replaceWith(basketActive);
                } else {
                    var notBasketActive = '<a href="javascript:void(0);" class="reference-basket disabled" title="Already in the reading basket" data-title="Spinal Disorders; Section Fractures; Chapter about subaxial CS Injuries."><i class="fa fa-bookmark disabled"></i></a>';
                    el.find('.reference-basket').replaceWith(notBasketActive);
                }
                return el.prop("outerHTML");
            } catch (e) {
                return annotation;
            }
        }

        var annos = this.playerContainerDom.find(this.annotationSelector);
        annos.each(function() {
            var closeBtn = $(this).find(".annotation-close");

            if (time > parseFloat(closeBtn.attr("data-end-time")) || time < parseFloat(closeBtn.attr("data-start-time"))) {
                var index = _this.activeAnnotations.indexOf(parseInt(closeBtn.attr("data-index")));
                _this.activeAnnotations.splice(index, 1);
                _this.closeAnnotation(closeBtn, $(this));
            }
        });
    },
    getAnnotations: function(annotation, callback) {
        var _this = this;
        if (annotation.json) {
            $.ajax({
                url: APP_URL + annotation.json + "&tmstma=" + '' + Math.floor(1000 * Math.random()),
                cache: false,
                success: function(data) {

                    try {
                        _this.slideAnnotations = (data ? JSON.smartParse(data) : []); /* should parse?! */
                        $(document).trigger("annotationReturned", {annotations: _this.slideAnnotations});

                        if (callback) {
                            callback();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            });
        } else if (annotation.vtt && annotation.vtt !== "") {
            $.ajax({
                url: APP_URL + annotation.vtt + "?tmstm=" + '' + Math.floor(1000 * Math.random()),
                success: function(data) {

                    if (callback) {
                        _this.parseAnnotations(data, callback);
                    } else {
                        _this.parseAnnotations(data);
                    }
                },
                error: function(error) {
                    console.error(error);
                }
            });
        } else {
            _this.slideAnnotations = [];
            $(document).trigger("annotationReturned", {annotations: _this.slideAnnotations});
            if (callback) {
                callback();
            }
        }
    },
    parseAnnotations: function(data, callback) {

        var _this = this;
        var subtIndex = 1;
        var annotations = [];
        var parser = new WebVTT.Parser(window, WebVTT.StringDecoder());

        parser.oncue = function(cue) {

            var obj = {};
            obj.text = cue.text;
            if (cue.id) {
                obj.id = cue.id;
            }
            obj.index = subtIndex;
            obj.startTime = cue.startTime;
            obj.endTime = cue.endTime;
            subtIndex = subtIndex + 1;
            /* we don't add annotations if they don have class
             * annotation in it */

            if (cue.text.indexOf("annotation") !== -1) {
                annotations.push(cue);
            }
        };

        parser.onflush = function() {
            /* template of messages */
            _this.slideAnnotations = annotations;
            /* Called when new annotation returned */
            $(document).trigger("annotationReturned", {annotations: _this.slideAnnotations});
            if (callback) {
                callback();
            }

        };
        parser.parse(data);
        parser.flush();
    }
};

/* Subtitles of active slide */
/* 
 * Listen => "slideChange", "videoTimeUpdate"
 * Trigger => "subtitlesTimeUpdate"
 * */
EW["presentation_transcript"] = {
    containerSelector: "#presentationSubtitles",
    templateSelector: "#presentationSubtitlesTemplate",
    subtitleSelector: ".subtitle",
    bodyDom: undefined,
    containerDom: {},
    dataSlide: {},
    subtitleFlag: true,
    containerHeight: 95, /* height + top padding*/
    transcriptionEditMode: false,
    highlightedIndex: 0,
    layoutStates: undefined,
    blockIfEmpty: false,
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {
        var _this = this;
        this.containerDom = $(this.containerSelector);
        this.subtContainer = $("#presentationSubtitlesContainer");

        /** 
         * Calling setConfig from  EW["presentation_layout_manager"]
         * @param {function} a callback function
         * @returns {object} activeStates object
         */
        EW["presentation_layout_manager"].setConfig({fn: function(layoutStates) {
                _this.layoutStates = layoutStates;
            }});

        /* return and do not attach any event */
        if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("transcriptionInit") === -1) {
            console.log("Not initing transcript (based on config)");
            return;
        }

        $(document).on("jsonPublished", function(event, data) {

            if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("transcriptionNotEmpty") !== -1
                    && data.transcriptionEmpty === true) {
                _this.blockIfEmpty = true;
                EW["presentation_layout_manager"].setConfig({
                    config: {
                        transcription: {
                            show: "false"
                        }
                    }
                });
                return;
            }

        });

        $(document).on("slideChange.subtitle", function(event, slide) {

            _this.scrolledByScrollTop = true;
            _this.lastSubIndex = "undefined";
            _this.dataSlide = slide;
            _this.getSubtitles(slide.subtitles);
            _this.containerDom.scrollTop(0);
            if (_this.subtitleFindDom) {
                _this.subtitleFindDom.val("");
            }

        });

        $(document).on('playerSettingsChange', function(event, data) {

            if (typeof data.subtitle === "boolean") {
                _this.subtitleFlag = !_this.subtitleFlag;
                if (_this.subtitleFlag) {
                    _this.enableSubtitles();
                } else {
                    _this.disableSubtitles();
                    if (searchbar.is(":visible")) {
                        searchbaricon.trigger("click");/*  removes transcript search on transcript disable */
                    }
                }
            }

        });

        $(document).on("transcript_editmode", function(event, mode) {

            if (mode.mode) {
                setTimeout(function() {
                    EW["ckeditor"].init();
                }, 300);
                _this.transcriptionEditMode = true;
                _this.editModeOn();
            } else {

                _this.transcriptionEditMode = false;
                _this.editModeOff();
            }
        });

        var searchbar = $(".presentation_subtitles_search");
        var searchbaricon = $(".presentation_subtitles_searchicon");

        searchbaricon.on("click", function() {
            if ($(this).hasClass("remove_icon")) {
                $(this).removeClass("remove_icon");
                searchbar.hide(250);
                _this.containerDom.unhighlight();
            } else {
                $(this).addClass("remove_icon");
                searchbar.show(250);
            }
        });

        $("#editTranscript").on("click", function() {

        });

        $(document).on("transcriptEditDialog", function(event, data) {

            $(document).trigger('slidePause');

            $(document).trigger("getPlayer", function(player) {
                $(document).trigger("getPresentationJson", function(presentationJson) {
                    var obj = {};
                    obj.presentation_id = presentationJson.presentation_id;
                    obj.slide_id = _this.dataSlide.slide_id;
                    if (!_this.bodyDom) {
                        _this.bodyDom = $("body");
                    }

                    /* data, player, trigger, remotereference, callback */
                    var argObj = {};
                    argObj.data = _this.slideMessages;
                    argObj.player = player;
                    argObj.trigger = $(data.context);
                    argObj.remotereference = obj;
                    argObj.callback = $.proxy(_this.transcriptRefresh, _this);
                    argObj.slideTitle = _this.dataSlide.title;
                    argObj.slideIndex = _this.dataSlide.index;

                    EW['transcription-editor'].openEditor(argObj);

                    if (argObj.data) {
                        $("body").addClass("transcription_editor_enabled");
                    }
                });

            });

        });

        /** Triggered from EW.transcript-editor.js */
        $(document).on("transcriptEditorClose", function() {
            if (!_this.bodyDom) {
                _this.bodyDom = $("body");
            }
            _this.bodyDom.removeClass("transcription_editor_enabled");
        });

        this.enableSubtitles();
    },
    transcriptRefresh: function(index) {
        var _this = this;

        $(document).trigger("slideChange.subtitle", _this.dataSlide);
    },
    /** Activates Transcript, attachs all required events */
    enableSubtitles: function() {
        var _this = this;
        $("body").removeClass("transcript_disabled");
        this.containerDom.on("click.subtitle", this.subtitleSelector, function() {
//            if (!_this.transcriptionEditMode) {
            var time = $(this).attr("data-subtitle-start");
            $(document).trigger("subtitlesTimeUpdate", parseFloat(time));
//            } else {
            /* Edit mode click (openes mini editor) */
//                _this.itemEdit($(this));
//            }
        });

        $(document).on("videoTimeUpdate.subtitle", function(event, time) {
            if (_this.subtitleFlag === true) {
                _this.scrollOnVideoTimeChange(time);
            }
        });

        /** Filter through subtitles with keyup and pause video */
        if (!this.subtitleFindDom) {
            this.subtitleFindDom = $('#subtitleFind');
        }
        this.subtitleFindDom.on('keyup.subtitle', function() {
            _this.highlightedIndex = 0;

            $(document).trigger("slidePause");
            _this.subtitleFind($(this));
        });

        $(document).on("scrollFoundSubtitles", function(event, data) {
            var sTop = _this.containerDom.scrollTop();
            if (_this.highlightedItems) {
                if (data.down) {
                    _this.highlightedIndex = (_this.highlightedIndex === _this.highlightedItems.length - 1) ? _this.highlightedIndex : ++_this.highlightedIndex;
                }
                if (data.top) {
                    _this.highlightedIndex = (_this.highlightedIndex === 0) ? _this.highlightedIndex : --_this.highlightedIndex;
                }
                _this.highlightedItems.removeClass("highlightActive");
                _this.highlightedItems.eq(_this.highlightedIndex).addClass("highlightActive");

                /* scroll */
                if (_this.highlightedItems.eq(_this.highlightedIndex) && _this.highlightedItems.eq(_this.highlightedIndex)[0]) {
                    _this.containerDom.scrollTop(_this.highlightedItems.eq(_this.highlightedIndex)[0].offsetTop - 2);
                }
                $(document).trigger("subtitlesTimeUpdate", parseFloat(_this.highlightedItems.eq(_this.highlightedIndex).attr("data-subtitle-start")));
            }

        });


        /**
         * @NOTE
         * Below function is NOT used,
         * It can be easily removed
         */
        $(document).on("subTplAdded", function(event, fn) {

            fn(function() {
                return; /* change video on scroll not needed */
                _this.vidTrigOnScrollFinished = true; /* on scroll functions finished */
                _this.containerDom.off("scroll").on("scroll", function(e) {

                    // sscrolledByScrollTop === true when scroll top was triggered
                    if (_this.transcriptionEditMode !== true && _this.scrolledByScrollTop === false) {
                        /* vidTrigOnScrollFinished => make sure scroll function finished */
                        if (_this.vidTrigOnScrollFinished === true) {

                            _this.vidTrigOnScrollFinished = false;
                            _this.triggerVideoOnScroll($(this), function() {
                                _this.vidTrigOnScrollFinished = true;
                            });
                        }
                    }
                });
            });
        });

        $(document).on("getSubtitles", function(event, fn) {
            var sub = _this.slideMessages;
            fn(sub);
        });

    },
    /** Deactivates Transcript, detachs all required events */
    disableSubtitles: function() {
        $("body").addClass("transcript_disabled");
        $(document).off("videoTimeUpdate.subtitle scrollsubtitles");
        this.containerDom.off("click.subtitle");
        $('#subtitleFind').off('keyup.subtitle');
        this.containerDom.off("scroll");
    },
    /** 
     * Scrolls transcript container when player time changes(is playing)
     * In order to keep on center and highlight current phrase.
     * It makes required calculation to find top offset and then calls
     * scrollTipSimulate
     * @param {Number} time
     */
    scrollOnVideoTimeChange: function(time) {
        var _this = this;
        if (this.slideMessages && (typeof this.slideMessages === "object")) {
            var i = 0;
            for (i; i < this.slideMessages.length; i++) {
                if (this.slideMessages[i].startTime <= time && time <= this.slideMessages[i].endTime) {

                    var el = this.containerDom.find('[data-subtitle-index="' + this.slideMessages[i].index + '"]');
                    el.addClass("txt-color-red bold").siblings().removeClass("txt-color-red bold");

                    if (this.slideMessages[i].index !== this.lastSubIndex) {
                        var top = (el[0].offsetTop) ? el[0].offsetTop : el.position().top;
                        this.scrolledByScrollTop = true;
                        this.scrollTopSimulate(el[0].offsetTop - 2);
                        this.lastSubIndex = this.slideMessages[i].index;
                    }
                    break;
                }
            }
        }
    },
    /**
     * @param {Number} top - top offset to scroll
     *  */
    scrollTopSimulate: function(top) {
        var _this = this;
        /* scrollTop */
        this.containerDom.animate({
            scrollTop: top
        }, 200).delay(220).promise().done(function() {
            _this.scrolledByScrollTop = false;
        });
    },
    /**
     * @NOTE
     * Below function is NOT used,
     * It can be easily removed
     */
    triggerVideoOnScroll: function(el, fn) {
        var scrollT = el.scrollTop();

        $(document).trigger("slidePause");
        /** Subtitle index */
        var itemIndex = parseInt(parseFloat(scrollT / el[0].scrollHeight.toPrecision(2)) * this.slideMessages.length);
        itemIndex = itemIndex + 3;
        /* should not be 0 */
        itemIndex = (itemIndex === 0) ? 1 : itemIndex;
        /* should not be bigger than slideMessages number */
        itemIndex = (itemIndex > this.slideMessages.length) ? this.slideMessages.length : itemIndex;

        /** highlight class change */
        var el = this.containerDom.find('[data-subtitle-index="' + itemIndex + '"]');
        el.addClass("txt-color-red bold").siblings().removeClass("txt-color-red bold");

        /** time change*/
        var time = Math.ceil(parseFloat(el.attr("data-subtitle-start")));
        $(document).trigger("subtitlesTimeUpdate", time);

        /** press play with delay */
        clearTimeout(this.playDelayTimer);
        this.playDelayTimer = setTimeout(function() {
            $(document).trigger("slidePlay");
        }, 1200);

        setTimeout(function() {
            fn();
        }, 5);

    },
    /** 
     * Used when searching transcript
     * @param {Object} jthis - jquery object serch input element
     */
    subtitleFind: function(jthis) {
        this.containerDom.unhighlight();
        this.containerDom.highlight(jthis.val());
        this.highlightedItems = this.containerDom.find(".highlight");
    },
    getSubtitles: function(subtitles) {

        var _this = this;
        /* VTT SHOULD BE SECOND */
        if (subtitles.json && subtitles.json.indexOf(".json") !== -1) {

            $.ajax({
                url: APP_URL + subtitles.json + "?tmstm=" + '' + Math.floor(1000 * Math.random()),
                success: function(data) {

                    try {
                        _this.slideMessages = JSON.smartParse(data);
                        var k = 0;
                        for (k; k < _this.slideMessages.length; k++) {
                            _this.slideMessages[k].index = k + 1;
                        }
                        _this.templating({"messages": _this.slideMessages});
                    } catch (e) {
                        console.error(e);
                    }
                }, error: function() {
                    _this.slideMessages = [];
                }
            });
        } else if (subtitles.vtt && subtitles.vtt.indexOf(".vtt") !== -1) {
            $.ajax({
                url: APP_URL + subtitles.vtt + "?tmstm=" + '' + Math.floor(1000 * Math.random()),
                success: function(data) {
                    _this.parseSubtitles(data);
                }
            });
        } else {
            _this.slideMessages = "";
            this.templating({"messages": []});
        }
    },
    parseSubtitles: function(data) {

        var _this = this;
        var subtIndex = 1;
        var messages = [];
        var parser = new WebVTT.Parser(window, WebVTT.StringDecoder());

        parser.oncue = function(cue) {
            var obj = {};
            obj.text = cue.text;
            if (cue.id) {
                obj.id = cue.id;
            }
            obj.index = subtIndex;
            obj.startTime = cue.startTime;
            obj.endTime = cue.endTime;

            subtIndex = subtIndex + 1;
            messages.push(obj);
        };

        parser.onflush = function() {
            /* template of messages */
            _this.slideMessages = messages;
            _this.templating({"messages": messages});
        };

        parser.onparsererror = function() {
        };

        parser.parse(data);

        parser.flush();
    },
    /**
     * Generating template info from slide object
     * using jsrender.
     * Triggers "subTplAdded" after finish
     * @param {Object} slide - current slide data object
     **/
    templating: function(data) {
        try {
            var _this = this;
            if (!this.template) {
                this.template = $.templates(this.templateSelector);
            }
            var htmlOutput = this.template.render(data);
            this.containerDom.off("scroll");

            $(document).trigger("subTplAdded", function(innerCallback) {
                _this.containerDom.html(htmlOutput);
                innerCallback();
            });
        } catch (e) {
            console.error(e);
        }
    },
    editModeOn: function() {
        var _this = this;
        if (!this.oldMessages) {

        }
        this.subtContainer.addClass("editmode_transcript");
        $(document).on("transcript_editmode_save", function() {
            _this.saveEdit();
        });
    },
    editModeOff: function() {
        this.subtContainer.removeClass("editmode_transcript");
        $(document).off("transcript_editmode_save");

    },
    saveEdit: function(transcriptList) {
        this.generatePostObj(transcriptList, function(obj) {

            try {
                var cis = session.GetValue("contentId");
                $.ajax({
                    url: APP_URL_BACK + "ajxrsp.php?uni=" + uniqueid + "&apprcss=editJsonTranscript" + "&cis=" + cis + "&tmstm=" + '' + Math.floor(1000 * Math.random()),
                    type: 'POST',
                    dataType: 'json',
                    data: obj,
                    success: function() {
                        // TRANSCRIPT SAVED SUCCESSFULLY
                    }
                });
            }
            catch (e) {
                console.error(e);
            }
        });
    },
    generatePostObj: function(transcriptList, fn) {
        var _this = this;
        $(document).trigger("getPresentationJson", function(presentationJson) {
            var obj = {};
            obj.presentation_id = presentationJson.presentation_id;
            obj.slide_id = _this.dataSlide.slide_id;
            if (transcriptList) {
                obj.subtitles = transcriptList;
            } else {
                console.error("Transcript list is not passed");
                obj.subtitles = _this.slideMessages;
            }

            fn(obj);
        });
    },
    itemEdit: function(item) {
        var _this = this;

        $(document).trigger("slidePause");
        $(document).trigger("subtitlesTimeUpdate", parseFloat(item.attr('data-subtitle-start')));

        /* call */
        if (item.attr("data-subtitle-index") !== "") {
            item.attr({
                "contenteditable": "true",
                "data-element": "object"
            });
            EW["ckeditor"].startInlineEditor(item);
            item.focus();
        }

        item.off("focusout.editorr").on("focusout.editorr", function() {
            EW["ckeditor"].destroyInlineEditor($(this));
            editorCallbackItemSave($(this));
        });

        var i = 0;
        function editorCallbackItemSave(item) {
            for (i; i < _this.slideMessages.length; i++) {
                var elIndex = parseInt(item.attr("data-subtitle-index"));
                if (i === elIndex - 1) {
                    _this.slideMessages[elIndex - 1].text = item.text();
                }
            }
        }
    }
};



/* Author */
/* 
 * Listen => "jsonPublished"
 * Trigger =>
 * */
EW["presentation_author"] = {
    containerSelector: "#presentationAuthor",
    templateSelector: "#presentationAuthorTemplate",
    containerDom: {},
    dataAuthor: {},
    authorEditMode: false,
    layoutStates: undefined,
    blockIfEmpty: false,
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {
        var _this = this;
        this.containerDom = $(this.containerSelector);

        EW["presentation_layout_manager"].setConfig({fn: function(layoutStates) {
                _this.layoutStates = layoutStates;
            }});
        /* Return if "authorInit" is not in logical states
         * Meaning it should not be shown  */
        if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("authorInit") === -1) {
            console.log("Not initing author (based on config)");
            return;
        }

        $(document).off("jsonPublished.author").on("jsonPublished.author", function(event, data, layoutStates) {

            if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("authorNotEmpty") !== -1
                    && !$.trim(data.author.firstname) && !$.trim(data.author.secondname)) {
                _this.blockIfEmpty = true;
                EW["presentation_layout_manager"].setConfig({
                    config: {
                        author: {
                            show: "false"
                        }
                    }
                });
                return;
            }

            _this.dataAuthor = data.author;

            _this.templating();

        });

        $(document).off("author_editmode").on("author_editmode", function(event, mode) {
            if (mode.mode) {
                _this.authorEditMode = true;
                _this.editModeOn();
            } else {
                _this.authorEditMode = false;
                _this.editModeOff();
            }
        });

        $("#editAuthorSave").off("click").on("click", function() {
            $(document).trigger("reinit.author");
        });

        $(document).on("toggleAuthor", function(event, data) {
            _this.containerDom.toggle().promise().then(function() {
                $(document).trigger("resize");
            });
        });

    },
    editModeOn: function() {
        this.containerDom.addClass("editmode_author");
    },
    editModeOff: function() {
        this.containerDom.removeClass("editmode_author");
    },
    /**
     * Generating template info from slide object
     * using jsrender.
     * Triggers "authorTplAdded" after finish
     * @param {Object} slide - current slide data object
     **/
    templating: function() {
        try {
            if (!this.template) {
                this.template = $.templates(this.templateSelector);
            }
            var htmlOutput = this.template.render(this.dataAuthor);

            this.containerDom.html(htmlOutput);
        } catch (e) {
            console.error(e);
        }
        $(document).trigger("authorTplAdded");
    }
};

/* References & resources */
/* 
 * Listen => "slideChange","jsonPublished"
 * Trigger => 
 * 
 * */
EW["presentation_resources"] = {
    allReferencesSelector: "#presentationAllReferences",
    allReferencesTemplateSelector: "#presentationAllReferencesTemplate",
    slideReferencesSelector: "#presentationSlideReferences",
    slideReferencesTemplateSelector: "#presentationSlideReferencesTemplate",
    referenceSelector: ".reference",
    dataResources: {},
    activeSlide: {},
    layoutStates: undefined,
    blockIfEmpty: false,
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {

        var _this = this;

        EW["presentation_layout_manager"].setConfig({fn: function(layoutStates) {
                _this.layoutStates = layoutStates;
            }});

        /* Return if "referenceInit" is not in logical states
         * Meaning it should not be shown  */
        if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("referenceInit") === -1) {
            console.log("Not initing reference (based on config)");
            return;
        }

        this.refContainer = $(_this.allReferencesSelector).closest(".jarviswidget");

        $(document).on("slideChange", function(event, slide) {

            _this.activeSlide = (_this.dataResources && _this.dataResources.slides && _this.dataResources.slides["slide_" + slide.index]) ? _this.dataResources.slides["slide_" + slide.index] : slide;

            var dataUrl = "&apprcss=addAssignRa&mode=player&cis=" + slide.slide_id + "#addAssignRa" + slide.slide_id;
            if (!_this.addReferenceDom) {
                _this.addReferenceDom = $("#addReference");
            }
            _this.addReferenceDom.attr("data-url", dataUrl);
            _this.getUserPref(function() {
                _this.templating();
            });
        });

        $(document).on("jsonPublished.reference", function(event, data, layoutStates) {

            /* BLOCKED => blockIfEmpty */
            if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("referenceNotEmpty") !== -1
                    && !Object.keys(data.references).length) {
                _this.blockIfEmpty = true;
                EW["presentation_layout_manager"].setConfig({
                    config: {
                        reference: {
                            show: "false"
                        }
                    }
                });

                return;
            }
            /* if reference slide should not be inited */
//            if( _this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("referenceSlideInit") === -1 ){
//                
//            }
            /** Removing reference */
            delete _this.dataResources;
            _this.dataResources = data;

            /* TO CHECK uncommented 08-07-2016 */
            _this.getUserPref(function() {
                _this.templating();
            });

        });

        $(document).on("getUserPref", function(event, fn) {
            _this.getUserPref(function(data) {
                fn(data);
            });
        });

        $(document).on("references_editmode", function(event, mode) {

            if (mode.mode) {
                _this.referencesEditMode = true;
                _this.editModeOn();
            }
            else {
                _this.referencesEditMode = false;
                _this.editModeOff();
            }
        });

        this.refContainer.on("click", ".editmode_reference_delete", function(e) {
            _this.itemEditDelete($(this));
        });


        this.refContainer.on("click", ".reference-basket", function() {
            var id = $(this).attr("date-ned");
            if (!!id) {
                /**/
                $(document).trigger("changedReferenceBasket", {id: id, status: true});
            }
        });

        /*  */
        $(document).on("changedAnnotationBasket", function(event, data) {
            var notToBasketHtml = '<a href="javascript:void(0);" class="reference-basket disabled" title="Already in the reading basket"><i class="fa fa-bookmark disabled"></i></a>';

            _this.refContainer.find(".reference-basket").each(function() {
                if ($(this).attr("date-ned") === data.id) {
                    $(this).replaceWith(notToBasketHtml);
                }
            });
        });

        /* add new reference */
        $("#editReferenceSave").on("click", function() {
            $(document).trigger("reinit.reference", _this.activeSlide);
        });

        /* edit reference item */
        $("#editReferenceEditItem").on("click", function() {
            $(document).trigger("reinit.reference", _this.activeSlide);
        });

    },
    /* WORKING */
    getUserPref: function(callback) {
        var _this = this;
        var data = {idList: this.referencesIdList()};
        $.ajax({
            url: jsonbasket + "&tmstm=" + '' + Math.floor(1000 * Math.random()),
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                try {
                    var key;
                    for (key in data.references) {
                        if (_this.dataResources.references[key]) {
                            if (typeof data.references[key] !== "undefined" && typeof data.references[key].toBasket !== "undefined") {
                                _this.dataResources.references[key].toBasket = data.references[key].toBasket;
                            }
                        }
                    }
                    if (callback) {
                        callback(data.references);
                    }
                } catch (e) {
                    console.error(e);
                }
            },
            error: function() {

            }
        });
    },
    referencesIdList: function() {
        var ids = [];
        var key;
        for (key in this.dataResources.references) {
            ids.push(this.dataResources.references[key].id);
        }
        return ids;
    },
    /**
     * Generating template info from slide object
     * using jsrender.
     * Triggers "resTplAdded" after finish
     * @param {Boolean} onlyAll if slide references should not be shown
     **/
    templating: function(onlyAll) {

        try {
            if (!onlyAll) {
                if (!this.slideSubtitlestemplate) {
                    this.slideSubtitlestemplate = $.templates(this.slideReferencesTemplateSelector);
                }
                var slideHtmlOutput = this.slideSubtitlestemplate.render(this.slideReferences());
                $(this.slideReferencesSelector).html(slideHtmlOutput);
            }
            if (!this.allSubtitlestemplate) {
                this.allSubtitlestemplate = $.templates(this.allReferencesTemplateSelector);
            }
            var allHtmlOutput = this.allSubtitlestemplate.render(this.allRefences());
            $(this.allReferencesSelector).html(allHtmlOutput);

        } catch (e) {
            console.error(e);
        }
        $(document).trigger("resTplAdded");

    },
    /**
     * Generates the list of all references as needed by template
     * @return {Object} allResource - Object with the list as property
     *   */
    allRefences: function() {
        this.allResource = {"allreferences": []};

        for (var key in this.dataResources.references) {
            this.allResource.allreferences.push(this.dataResources.references[key]);
        }

        return this.allResource;
    },
    /**
     * Generates the list of current slide references as needed by template
     * @return {Object} slidereferences - Object with the list as property
     *   */
    slideReferences: function() {
        var slidereferences = {"slidereferences": []};
        for (var i = 0; i < this.activeSlide.references.length; i++) {
            slidereferences.slidereferences.push(this.dataResources.references["reference_" + (this.activeSlide.references[i])]);
        }
        return slidereferences;
    },
    editModeOn: function() {
        var _this = this;

        this.refContainer.addClass("editmode_references");
        $(document).on("references_editmode_save", function() {

        });
    },
    editModeOff: function() {
        this.refContainer.removeClass("editmode_references");
        $(document).off("references_editmode_save");
    },
    generatePostObj: function(fn) {
        var _this = this;
        $(document).trigger("getPresentationJson", function(presentationJson) {
            var obj = {};
            obj.presentation_id = presentationJson.presentation_id;
            obj.slide_id = _this.activeSlide.slide_id;
            obj.references = _this.dataResources;
            fn(obj);
        });
    },
    itemEditDelete: function(item) {
        var _this = this;
        var slid = _this.activeSlide.slide_id;
        var refid = item.attr("data-reference-id");

        var thisUrl = APP_URL_BACK + "ajxrsp.php?uni=" + uniqueid + "&apprcss=DeleteDocModuleRel&cis=" + slid + "&CID=" + refid + "&tmstm=" + '' + Math.floor(1000 * Math.random());
        (new PNotify({
            title: 'Are you sure you want to remove this assignment?',
            icon: 'fa fa-fw fa-2x fa-question-circle',
            hide: false,
            confirm: {
                confirm: true
            },
            buttons: {
                closer: false,
                sticker: false
            }
        })).get().on('pnotify.confirm', function() {
            $.ajax({
                url: thisUrl,
                success: function(data) {
                    $(document).trigger("reinit.reference", _this.activeSlide);
                    var title_v = _success;
                    var content_v = 'Successfully removed';
                    $(document).trigger("smart-notification", {
                        title: title_v, content: content_v, type: "success"
                    });
                }
            });
        }).on('pnotify.cancel', function() {
            return;
        });

    }
};

/* Slide List */
/* 
 * Listen => "jsonPublished", "slideChange"
 * Trigger => "slideChange"
 * */
EW["presentation_navigation"] = {
    containerSelector: "#presentationNavigation",
    containerSelectorDom: {},
    templateSelector: "#presentationNavigationTemplate",
    selectedClass: "navigation_slide_active",
    modeSelectedBtnClass: "active_nav_mode",
    listViewModeAttr: "data-view-mode",
    modeActive: "",
    imageSelector: ".media-object",
    filterInputSelector: "#filterSlides",
    filterItemsSelector: ".media",
    presentationDuration: "",
    dataSlides: {},
    slideActive: {},
    videoPlayer: undefined,
    layoutStates: undefined,
    blockIfEmpty: false,
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {
        var _this = this;

        EW["presentation_layout_manager"].setConfig({fn: function(layoutStates) {
                _this.layoutStates = layoutStates;
            }});

        if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("navigationInit") === -1) {
            console.log("Not initing navigation (based on config)");
            return;
        }

        $(document).on("jsonPublished.navigation", function(event, data, layoutStates) {

            if (_this.layoutStates.logicalStates && _this.layoutStates.logicalStates.indexOf("navigationNotEmpty") !== -1
                    && Object.keys(data.slides).length === 0 && data.slides.constructor === Object) {
                _this.blockIfEmpty = true;
                EW["presentation_layout_manager"].setConfig({
                    config: {
                        navigation: {
                            show: "false"
                        }
                    }
                });
                return;
            }

            /* POPUP VIRTUAL SLIDE  */
            if ((data.has_virtual_slides === true) && (typeof data.internal_virtual === "undefined")) {
                _this.hasVirtualSlides = true;
            }/* VIRTUAL SLIDE INSIDE SLIDE */
            else if ((data.has_virtual_slides === false) && (typeof data.internal_virtual === "undefined")) {
                _this.hasVirtualSlides = false;
            }/* SLIDE NORMAL */
            else {

                _this.hasVirtualSlidesInternal = true;
            }

            /* if we have virtual slides inside slides
             * dataSlide is filled with virtual_slides list */
            if (_this.hasVirtualSlidesInternal) {
                /* NOTE: this use case support only one parent slide */
                _this.parentSlide = data.slides["slide_1"];
                /** assuming we have only one parent slide with multi virtual slides */
                _this.slideActive = _this.parentSlide;

                _this.dataSlides = data.virtual_slides;
                _this.virtualModeParentSlideEdit();
            } else {
                _this.dataSlides = data.slides;
            }


            /**
             * Slide duration is taken from json data,
             * if not available should be read from video duration metadata
             * */
            if (data.durationCalculated && data.durationCalculated != 0) { /* json data returns "0" string zero sometimes we need to check */
                _this.presentationDuration = data.durationCalculated.toHHMMSS(false, true);
            } else {
                /*
                 * If not available check if we are in VirtualSlidesInternal mode
                 * than we know total duration is that of the slide
                 */
                var duration = "0";
                _this.presentationDuration = duration.toHHMMSS(false, true);
                if (_this.hasVirtualSlidesInternal || _this.hasVirtualSlides) {

                    $(document).on("loadedMetadata", function(event, player) {

                        if (!_this.videoPlayer) {
                            _this.videoPlayer = player;
                        }
                        duration = _this.videoPlayer.duration() + "" || "0";
                        _this.presentationDuration = duration.toHHMMSS(false, true);
                        /*
                         * Not good practice to access module directly but used to re-set slide total time,
                         * when in virtual slides and durationCalculated is not provided
                         */
                        EW["presentation_player"].slideStatus(true);

                    });
                } else {
                    console.error("Caught => durationCalculated is required in presentation with slides");
                }
            }

            _this.filterSlides();
            _this.templating();
        });

        $(document).on("videoPlayerInited", function(event, data) {
            if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                $(document).trigger("virtualSlideChange.navigation", _this.dataSlides["slide_1"]);
            }
        });

        $(document).on("slideChange", function(event, slide) {

            if (_this.hasVirtualSlides === false) {
                _this.slideActive = slide;
                _this.changeSelectedClass(slide);
                _this.changeViewMode();
            }
        });

        var activeKey;
        $(document).on('videoTimeUpdate', function(event, time) {

            if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                var key;
                for (key in _this.dataSlides) {
                    if (time >= _this.dataSlides[key].start_time && time <= _this.dataSlides[key].end_time) {
                        if (key !== activeKey) {
                            $(document).trigger("virtualSlideChange.navigation", _this.dataSlides[key]);
                            activeKey = key;
                        }
                    }
                }
            }
        });

        /** handles slide active scroll& highlight
         * & also virtual slide progress */
        $(document).on("virtualSlideChange.navigation", function(event, slide) {
            if (Object.keys(_this.dataSlides).length === 0 && _this.dataSlides.constructor === Object) {
                return;
            }
            /* is used to handle slide active  */
            _this.changeSelectedClass(slide);
            _this.slidePosition(navList, slide);
            /* virtual time */
            if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                currentTime = slide.start_time;
            }
        });


        this.containerSelectorDom = $(this.containerSelector);
        this.containerSelectorDom.on("click", ".media", function() {

            _this.dontScrollOnClick = true;
            var index = $(this).attr("data-slide-index");
            if (_this.dataSlides["slide_" + index]) {
                if (_this.hasVirtualSlides === false) {
                    $(document).trigger("slideChange", _this.dataSlides["slide_" + index]);
                } else {
                    $(document).trigger("virtualSlideChange", _this.dataSlides["slide_" + index]);
                }

                setTimeout(function() {
                    _this.dontScrollOnClick = false;
                }, 100);
            }
        });

        this.containerSelectorDom.on("click", ".editmode_navigation_item", function(e) {
            if (_this.navigationEditMode) {
                _this.itemEdit($(this).closest("li").find('.media'));
            }
        });

        var navigationSlideStatus = $("#navigationSlideStatus");
        var progressNr = navigationSlideStatus.find(".progress-nr");

        /* when durationCalculated calculated is 0,
         * progressHtml should be reupdated with player time ( only on virtual sli ) */
        var progressHtml = "";
        var baseHtml = "";
        var currentTime = 0;
        var color = "";

        if (!this.elColl) {
            this.elColl = $(".presentation_navigation_container .el-coll");
        }

        /* Calculates the time progress ONLY when switching slides */
        $(document).on("slideChangeStatus", function(event, data) {

            clearInterval(manuallyInterval);
            if (data.currentNr && data.allNr) {
                navigationSlideStatus.find(".progress-bar").css('width', (data.currentNr / data.allNr * 100) + "%");
                if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                    if (!_this.videoPlayer) {
                        $(document).trigger("getPlayer", function(player) {
                            _this.videoPlayer = player;
                        });
                    }
                    currentTime = _this.videoPlayer.currentTime();

                } else {
                    currentTime = _this.generateTime(currentTime);
                }
                var calc = (data.currentNr / data.allNr);

                color = getColorForPercentage(calc);

                baseHtml = progressHtml = "<span class='progress-nr-left' title='Passed duration'>" + (currentTime + "").toHHMMSS(false, true) + "</span>";

                progressHtml = "<span class='progress-nr-center' title='Slides progress'>" + data.currentNr + " / " + data.allNr + "</span>";
                progressHtml += "<span class='progress-nr-right' style='color:" + color + "' title='Total duration'>" + _this.presentationDuration + "</span>";

                progressNr.html(baseHtml + progressHtml);

                if (data.isFirst === true) {
                    _this.elColl.attr("el-state", "initCL");

                    EW["handle-preview-layout"].init();
                } else if (data.isLast === true) {
                    _this.elColl.attr("el-state", "lastCL");

                    EW["handle-preview-layout"].init();
                } else if (data.isBetween === true) {
                    _this.elColl.attr("el-state", "runnCL");

                    EW["handle-preview-layout"].init();
                }
            }
        });

        var thisDelay = new Date();
        $(document).on("videoTimeUpdate", function(event, time) {

            if ((new Date() - thisDelay) > 100) {
                var actualTime = parseInt(currentTime + time) + "";

                /* virtual time */
                if (_this.hasVirtualSlides || _this.hasVirtualSlidesInternal) {
                    currentTime = time;
                    actualTime = currentTime + "";
                }

                if (time === 0) {
                    actualTime = (_this.generateTime(time) + "" || 0);
                }

                actualTime = actualTime.toHHMMSS(false, true);
                baseHtml = "<span class='progress-nr-left' title='Passed duration' >" + actualTime + "</span>";

                progressNr.html(baseHtml + progressHtml);
                thisDelay = new Date();
            }
        });


        var manuallyInterval;
        /* Probably not being used  ( "timechangemanually" trigger is commented )*/
        $(document).on("timechangemanually", function(event, data) {

            /* pasttime, lefttime */
            var leftTime = data.lefttime;
            var progressTime = data.pasttime + 1;
            clearInterval(manuallyInterval);
            manuallyInterval = setInterval(function() {

                $(document).trigger("videoTimeUpdate.subtitle", parseInt(progressTime));
                var actualTime = parseInt(currentTime + progressTime) + "";
                actualTime = actualTime.toHHMMSS();
                baseHtml = "<span class='progress-nr-left' title='Passed duration' >" + actualTime + "</span>";
                progressNr.html(baseHtml + progressHtml);
                thisDelay = new Date();

                progressTime = progressTime + 1;
                if (data.lefttime < progressTime) {
                    clearInterval(manuallyInterval);
                }
            }, 1000);

        });

        $('[' + this.listViewModeAttr + ']').on("click", function(e) {
            _this.modeActive = $(this).attr(_this.listViewModeAttr);
            _this.changeViewMode($(this));
            e.preventDefault();
        });

        var showFilter = false;
        var filterSlidesContainer;
        $(document).on("showFilter", function(event, data) {
            showFilter = !showFilter;
            if (!filterSlidesContainer) {
                filterSlidesContainer = $("#filterSlidesContainer");
            }
            (!showFilter) ? filterSlidesContainer.addClass("filter_slides_hide") : filterSlidesContainer.removeClass("filter_slides_hide");
            if (!showFilter) {
                filterSlidesContainer.find("#filterSlides").val('').keyup();
            }
        });

        var navList = $(".navigation_list .widget-body");
        $(document).on("slideChange", function(event, slide) {
            if (!_this.dontScrollOnClick) {
                _this.slidePosition(navList, slide);
            }
        });

        $(document).on('navigation_editmode', function(event, mode) {
            if (mode.mode) {
                _this.navigationEditMode = true;
                _this.editModeOn();
            } else {
                this.navigationEditMode = false;
                _this.editModeOff();
            }
        });

        $("#editNavigationSave").on("click", function(event) {
            $(document).trigger("reinit.navigation", _this.slideActive);
        });

        $(document).on("navTplAdded", function() {
            _this.containerSelectorDom.find("img").each(function(index) {
                var s = this;
                setTimeout(function() {
                    $(s).attr("src", $(s).attr("data-src") + APP_UNI + '&stamp=' + new Date().getTime() );
                }, (index * 120));
            });
        });

        var itemwidth = $("#navigationList").parents('.presentation_separate_virtualslides').length ? 110 : 74;
        var navScrollDom, scrollLeft = 0, scrollStep = (itemwidth * 3);
        $(document).on("navigationHorizontal", function(event, data) {

            if (!navScrollDom) {
                navScrollDom = $("#navigationList").find(".widget-body").eq(0);

            }
            if (data.left) {
                scrollLeft = scrollLeft >= scrollStep ? scrollLeft - scrollStep : 0;
            }
            if (data.right) {

                scrollLeft = scrollLeft < (_this.containerSelectorDom.width() - navScrollDom.width() - scrollStep) ? (scrollLeft + scrollStep) : _this.containerSelectorDom.width() - navScrollDom.width() + 60;
            }
            navScrollDom.animate({
                scrollLeft: scrollLeft
            }, 200);
        });

    },
    /**
     * keeps current slide in center by scrolling on slide change
     * @param {Object} navList - jquery object
     * @param {Object} slide - current slide
     */
    slidePosition: function(navList, slide) {

        $(document).trigger("isDesktop", function(isDesktop) {

            var el = $("li[data-slide-index='" + slide.index + "']");
            var elPosTop = el.position().top;
            var elPosLeft = el.position().left;

            var elHeight = el.outerHeight();
            var elWidth = el.outerWidth();

            var containerHeight = navList.outerHeight();
            var containerWidth = navList.outerWidth();

            if (isDesktop) {

                navList.scrollTop(elPosTop - ((containerHeight / 2 - elHeight / 2)));
            } else {
                navList.scrollLeft(elPosLeft - ((containerWidth / 2 - elWidth / 2)));
            }

        });

    },
    /** Handles thumbnail toggle
     * @param {Object} - jquery dom element
     *  */
    changeViewMode: function(el) {
        if (el) {
            el.addClass(this.modeSelectedBtnClass).siblings().removeClass(this.modeSelectedBtnClass);
        }
        if (!this.hasVirtualSlides) {
            if (this.modeActive === "nothumb") {
                this.containerSelectorDom.addClass("nothumb_mode");
            } else {
                this.containerSelectorDom.removeClass("nothumb_mode");
            }
        }
    },
    /**
     * Generating template info from slide object
     * using jsrender.
     * Triggers "navTplAdded" after finish
     * @param {Object} slide - current slide data object
     **/
    templating: function() {

        try {
            var _this = this;
            if (!this.template) {
                this.template = $.templates(this.templateSelector);
            }
            var allHtmlOutput = this.template.render(this.allSlides());
            this.containerSelectorDom.html(allHtmlOutput);
        } catch (e) {
            console.error(e);
        }
        $(document).trigger("navTplAdded");

    },
    changeSelectedClass: function(slide) {
        $('li[data-slide-index="' + slide.index + '"]').addClass(this.selectedClass).siblings().removeClass(this.selectedClass);
    },
    filterSlides: function() {
        var _this = this;

        jQuery.expr[':'].icontains = function(a, i, m) {
            return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };

        $(this.filterInputSelector).on("keyup", function() {
            //split the current value of searchInput
            var data = this.value;
            //create a jquery object of the rows
            var jo = _this.containerSelectorDom.find('li');
            if (this.value === "") {
                jo.show();
                return;
            }
            //hide all the rows
            jo.hide();

            //Recusively filter the jquery object to get results.
            jo.filter(function(i, v) {
                var $t = $(this);
                if ($t.is(":icontains('" + data + "')")) {
                    return true;
                }
                return false;
            }).show();
        }).focus(function() {
            this.value = "";
            $(this).unbind('focus');
        });
    },
    allSlides: function() {
        var slides = {"slides": []};
        for (var key in this.dataSlides) {
            this.dataSlides[key].rand = new Date().getTime();
            var slideReady = this.slideConfigCheck(this.dataSlides[key]);
            slides.slides.push(slideReady);
        }
        return slides;
    },
    /**
     * This function is responsible
     * for configuration preferences of navigation's slide
     * @param {Object} slide
     * @returns {Object} slide ready
     */
    slideConfigCheck: function(slide) {
        if (slide.small_img && slide.small_img.indexOf("http") === -1) {
            slide.small_img = APP_URL + slide.small_img;
        }
        if (!$.trim(slide.small_img) &&
                presentationConfig.navigation.extra.slideThumbnail &&
                presentationConfig.navigation.extra.slideThumbnail.indexOf("http") !== -1) {
            slide.small_img = presentationConfig.navigation.extra.slideThumbnail;
        }

        return slide;
    },
    editModeOn: function() {
        this.containerSelectorDom.closest(".jarviswidget").addClass("editmode_navigation");
    },
    editModeOff: function() {
        this.containerSelectorDom.closest(".jarviswidget").removeClass("editmode_navigation");
    },
    itemEdit: function(item) {
        var _this = this;
        var index = item.attr("data-slide-index");
        var slide = this.dataSlides["slide_" + index];
        this.slideActive = slide;
    },
    virtualModeParentSlideEdit: function() {
        var editNavItem = $('.editmode_navigation_item');
        if (editNavItem.length) {
            var dataUrl = editNavItem.attr("data-url");
            var newDataUrl = dataUrl.replace(/\{slide_id\}/g, this.parentSlide.slide_id);
            editNavItem.attr("data-url", newDataUrl);
        }
    },
    slideEditSave: function() {

    },
    /* generates time of previous slides */
    generateTime: function(currentTime) {
        var time = 0;
        if (!this.hasVirtualSlides) {
            var i = 0;
            for (var key in this.dataSlides) {
                if (this.dataSlides[key]) {
                    if (this.dataSlides[key].index < this.slideActive.index) {
                        if (this.dataSlides[key].durationCalculated) {
                            time += parseFloat(this.dataSlides[key].durationCalculated);
                        }

                        if (this.dataSlides[key].durationExtra) {
                            time += parseFloat(this.dataSlides[key].durationExtra);
                        }
                    }
                }
            }
        } else {
            time = currentTime;
        }

        return time;
    }
};



EW["presentation_editmode"] = {
    activeSlide: {},
    slideId: 0,
    editMode: false,
    timelineDuration: 0,
    tabs: {
        subtitles: true,
        annotations: false
    },
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {

        var _this = this;
        var bodyDom = $("body");

        $(document).on("playerSettingsChange", function(event, obj) {

            if (typeof obj.editmodeplayer === "boolean") {
                _this.editMode = obj.editmodeplayer;

                $(document).trigger("slidePause");

                $(document).trigger("player_editmode", {mode: _this.editMode});
                $(document).trigger("transcript_editmode", {mode: _this.editMode});
                $(document).trigger("author_editmode", {mode: _this.editMode});
                $(document).trigger("references_editmode", {mode: _this.editMode});
                $(document).trigger("navigation_editmode", {mode: _this.editMode});
                $(document).trigger("annotationsEdit", {mode: _this.editMode});

            }
        });

        /** 
         * @NOTE
         * Not being used any longer
         *  */
        function onSlideChange() {
            $(document).on("slideChange.editmode", function(event, slide) {
                _this.activeSlide = slide;
                _this.slideId = slide.slide_id;
                if (_this.editMode) {
                    _this.timelineChange();
                }
            });
        }

        $(document).on("videoDuration", function(event, duration) {
            _this.timelineDuration = parseInt(duration);
        });

        $(document).on("editmode_timeline", function(event, obj) {
            if (obj && obj.subtitles) {
                _this.tabs.subtitles = true;
                _this.tabs.annotations = false;
            } else {
                _this.tabs.annotations = true;
                _this.tabs.subtitles = false;
            }
            _this.tabChange();
        });

        bodyDom.on("click.zoom", ".timelinezoom", function() {
            var zoom = $(this).attr("data-zoom");
            if (_this.tabs.subtitles) {

            } else if (_this.tabs.annotations) {

            }
        });

    },
    getTimelineDependensies: function(initVis) {
        if (typeof vis === "undefined") {
            $.getScript(APP_URL + "include_js/vis_js/vis.min.js", function() {
                initVis();
            });
        } else {
            initVis();
        }
    },
    /** 
     * @NOTE
     * Not being used any longer
     *  */
    timelineChange: function() {
        /* get edit tools template */
        if (this.tabs.subtitles === true) {
            this.subtitlesVideoTimeline();
        } else {
            this.annotationVideoTimeline();
        }
    },
    tabChange: function() {

        if (this.tabs.subtitles === true) {
            this.subtitlesVideoTimeline();
        } else {
            this.annotationVideoTimeline();
        }
    },
    subtitlesVideoTimeline: function(update) {
        var _this = this;
        var items = new vis.DataSet(this.genSubtitleData());
        if (!this.subtitleTimelineContainer) {
            this.subtitleTimelineContainer = document.getElementById('subtitlesTimeline');
            var options = {
                editable: true,
                min: 0,
                max: (this.timelineDuration + 10)
            };
            this.subtimeline = new vis.Timeline(this.subtitleTimelineContainer, items, options);

            this.subtimeline.on('rangechange', function(properties) {

            });
            this.subtimeline.on('rangechanged', function(properties) {

            });
            this.subtimeline.on('select', function() {

            });
            this.subtimeline.on('click', function(event, properties) {
                //   console.log(event, properties);
            });
        } else {
            this.subtimeline.setItems(items);
        }
    },
    annotationVideoTimeline: function(update) {

        var items = new vis.DataSet(this.genAnnottationData());

        if (!this.annotationTimelineContainer) {
            this.annotationTimelineContainer = document.getElementById('annotationsTimeline');
            var options = {
                editable: true,
                min: 0,
                max: (this.timelineDuration + 10)
            };
            this.annotimeline = new vis.Timeline(this.annotationTimelineContainer, items, options);

            this.annotimeline.on('rangechange', function(properties) {

            });
            this.annotimeline.on('rangechanged', function(properties) {

            });
            this.annotimeline.on('select', function(properties) {

            });
            this.annotimeline.on('onRemove', function() {

            });
            this.annotimeline.on('click', function(event, properties) {

            });

        } else {
            this.annotimeline.setItems(items);
        }

    },
    genSubtitleData: function() {
        var test = EW["presentation_subtitles"].slideMessages;
        var testArray = [];
        for (var i = 0; i < test.length; i++) {
            var obj = {};
            if (test[i].id) {
                obj.id = test[i].id;
            }
            if (test[i].text) {
                obj.content = test[i].text;
            }
            if (test[i].startTime) {
                obj.start = test[i].startTime;
            }
            if (test[i].endTime) {
                obj.end = test[i].endTime;
            }
            testArray.push(obj);
        }
        return testArray;
    },
    genAnnottationData: function() {
        var test = EW["presentation_annotations"].slideAnnotations;
        var testArray = [];
        for (var i = 0; i < test.length; i++) {
            var obj = {};
            if (test[i].id) {
                obj.id = test[i].id;
            }
            if (test[i].text) {
                obj.content = test[i].text;
            }
            if (test[i].startTime) {
                obj.start = test[i].startTime;
            }
            if (test[i].endTime) {
                obj.end = test[i].endTime;
            }
            testArray.push(obj);
        }
        return testArray;
    },
    annotationListByTime: function(data) {

    }
};

/*
 * Layout manger reads nem configuration provided by javascript config in template.
 * Sets its local config object.
 * After setting config properly we see if this config matches any state's logic
 * If so we insert the matched states inside "statesActive" object.
 * Then each module checks EW["presentation_layout_manager"].statesActive before initializing itself
 */
EW["presentation_layout_manager"] = {
    removeRightClass: "presentation_noright_column",
    bodyDom: undefined,
    presentationDom: undefined,
    preferencesDom: undefined,
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {
        var _this = this;

        this.bodyDom = $("body");
        this.presentationDom = $("#presentation");
        this.preferencesDom = $("#presentationPlayerSettings");

        $(document).on("layoutChange", function(event, data) {
            if (data.rightpanel === false) {
                _this.bodyDom.addClass(_this.removeRightClass);
                _this.toggleSearch(false);
            } else {
                _this.bodyDom.removeClass(_this.removeRightClass);
                $(document).trigger("resize");
                _this.toggleSearch(true);
            }
        });

        /**
         * @NOTE this event handles preferences changed by settings button inside player
         */
        $(document).on("playerPreferencesChange", function() {

        });

        /**
         * @TODO
         * If any block from config object is shown false we should
         * deactivate respective settings toggle of that block
         * Also we should handle settings events here
         * */
        this.preferencesDom.on("change", "#preferenceTranscript", function() {

            if (this.checked) {
                _this.config.transcription.visibility = "visible";
            } else {
                _this.config.transcription.visibility = "invisible";
            }
            _this.setStates();
        });

        this.preferencesDom.on("change", "#preferenceAnnotations", function() {

            if (this.checked) {
                _this.config.annotations.visibility = "visible";
            } else {
                _this.config.annotations.visibility = "invisible";
            }
            _this.setStates();
        });

        this.preferencesDom.on("change", "#preferenceRelatedPanel", function() {
            if (this.checked) {
                _this.config.rightcolumn.visibility = "visible";
            } else {
                _this.config.rightcolumn.visibility = "invisible";
            }

            _this.setStates();
        });

        this.preferencesDom.on("change", "#preferenceAuthor", function() {

            if (this.checked) {
                _this.config.author.visibility = "visible";
            } else {
                _this.config.author.visibility = "invisible";
            }

            _this.setStates();
        });

        /* Reference */
        this.preferencesDom.on("change", "#preferenceReference", function() {

            if (this.checked) {
                _this.config.reference.visibility = "visible";
            } else {
                _this.config.reference.visibility = "invisible";
            }
            _this.setStates();
        });

        this.preferencesDom.on("change", "#preferenceNavigation", function() {

            if (this.checked) {
                _this.config.navigation.visibility = "visible";
            } else {
                _this.config.navigation.visibility = "invisible";
            }
            _this.setStates();
        });

    },
    /** This is used in case of ifnotempty when block is empty and
     * has been hidden in runtime  */
    disablePreferenceCheckbox: function(block) {
        var selectors = {
            "author": "#preferenceAuthor",
            "reference": "#preferenceReference",
            "navigation": "#preferenceNavigation",
            "transcription": "#preferenceTranscript",
            "annotations": "#preferenceAnnotations"
        };

        $(selectors[block]).attr("disabled", "disabled").closest(".toggle").addClass("state-disabled");

    },
    toggleSearch: function(state) {
        var _this = this;
        $(document).trigger("isDesktop", function(isDesktop) {
            if (!isDesktop) {
                if (!_this.controlbarSearchDom) {
                    _this.controlbarSearchDom = $(".vjs-searchslides-button");
                }
                if (!state) {
                    _this.controlbarSearchDom.addClass("disabled");
                } else {
                    _this.controlbarSearchDom.removeClass("disabled");
                }
            }
        });
    },
    config: undefined,
    /**
     * @param {Object} obj - object with arguments
     * @param {Function} obj.fn - callback function
     * @returns {object} activeStates object
     */
    setConfig: function(obj) {

        if (!this.config) {
            this.config = {
                author: {
                    show: "",
                    visibility: "",
                    extra: {
                        photo: "",
                        affiliation: "",
                        country: ""
                    }
                },
                reference: {
                    show: "",
                    visibility: "",
                    extra: {
                        showSlideReferences: ""
                    }
                },
                navigation: {
                    show: "",
                    visibility: "",
                    extra: {
                        showTitle: "",
                        showThumbnail: "",
                        showDescription: "",
                        showIndex: "",
                        slideThumbnail: ""
                    }
                },
                player: {
                    view: "",
                    extra: {
                        coverImage: ""
                    }
                },
                transcription: {
                    show: "",
                    visibility: ""
                },
                annotations: {
                    show: ""
                },
                rightcolumn: {
                    visibility: ""
                }
            };
        }

        /* Deep extend required */
        $.extend(true, this.config, (obj.config || presentationConfig));
        if (obj.config) {
            for (var key in obj.config) {
                this.disablePreferenceCheckbox(key);
            }
        }
        /* Whenever config is changed states should reflect these changes */
        this.setStates(obj.fn);

    },
    /* used only by other modules, config is accessed directly inside this module */
    getConfig: function() {
        return this.config;
    },
    /* Here we can declare any type of state we want
     * @NOTE review mode or edit mode should be checked,
     * Edit mode , Show if exist behet show always */
    states: {
        /* init or not  */
        logicalStates: {
            authorInit: function() {
                var truthy = false;

                if (this.config.author.show == "true" || this.config.author.show == "ifnotempty") {
                    truthy = true;
                }

                return truthy;
            },
            referenceInit: function() {
                var truthy = false;
                if (this.config.reference.show == "true" || this.config.reference.show == "ifnotempty") {
                    truthy = true;
                }

                return truthy;
            },
            referenceSlideInit: function() {
                var truthy = false;
                if (this.config.reference.extra.showSlideReferences == "true") {
                    truthy = true;
                }

                return truthy;
            },
            navigationInit: function() {
                var truthy = false;
                if (this.config.navigation.show == "true" || this.config.navigation.show == "ifnotempty") {
                    truthy = true;
                }

                return truthy;
            },
            transcriptionInit: function() {
                var truthy = false;
                if (this.config.transcription.show == "true" || this.config.transcription.show == "ifnotempty") {
                    truthy = true;
                }

                return truthy;
            },
            annotationInit: function() {
                var truthy = false;
                if (this.config.transcription.show == "true" || this.config.transcription.show == "ifnotempty") {
                    truthy = true;
                }

                return truthy;
            },
            coverImg: function() {
                var truthy = false;

                return truthy;
            },
            slideImg: function() {
                var truthy = false;

                return truthy;
            },
            /* IF EMPTY DOUBLE CHECK */
            authorNotEmpty: function() {
                var truthy = false;
                if (this.config.author.show == "ifnotempty") {
                    truthy = true;
                }
                return truthy;
            },
            referenceNotEmpty: function() {
                var truthy = false;
                if (this.config.reference.show == "ifnotempty") {
                    truthy = true;
                }
                return truthy;
            },
            navigationNotEmpty: function() {
                var truthy = false;
                if (this.config.navigation.show == "ifnotempty") {
                    truthy = true;
                }
                return truthy;
            },
            transcriptionNotEmpty: function() {
                var truthy = false;
                if (this.config.transcription.show == "ifnotempty") {
                    truthy = true;
                }
                return truthy;
            },
            annotationNotEmpty: function() {
                var truthy = false;
                if (this.config.annotations.show == "ifnotempty") {
                    truthy = true;
                }
                return truthy;
            }
        },
        visualStates: {
            /** States that reflect presentation view mostly show/hide blocks, blocks features,
             * @Note new functions can be added above 
             * key is the class that is going to be added
             * to the container (body) ONLY if its envoked function returns true
             **/
            "presentation-fullwidth": function() {
                var truthy = false;
                if (this.config.player.view == "fullwidth") {
                    truthy = true;
                }
                return truthy;
            },
            "no-right-column": function() {
                /* no right panel OR no author, no reference, no navigation */
                var truthy = false;
                if (this.config.rightcolumn.visibility == "invisible" ||
                        (this.config.author.show == "false" || this.config.author.visibility == "invisible")
                        && (this.config.reference.show == "false" || this.config.reference.visibility == "invisible")
                        && (this.config.navigation.show == "false" || this.config.navigation.visibility == "invisible")) {
                    truthy = true;
                }

                return truthy;
            },
            "no-author-block": function() {
                /* no author */
                var truthy = false;
                if (this.config.author.show == "false" || this.config.author.visibility == "invisible") {
                    truthy = true;
                }

                return truthy;
            },
            "no-annotation": function() {
                /* no annotations */
                var truthy = false;
                if (this.config.annotations.show == "false" || this.config.annotations.visibility == "invisible") {
                    truthy = true;
                }
                return truthy;
            },
            "no-author-photo": function() {
                var truthy = false;
                if (this.config.author.extra.photo == "false") {
                    truthy = true;
                }
                return truthy;
            },
            "no-author-affiliation": function() {
                var truthy = false;
                if (this.config.author.extra.affiliation == "false") {
                    truthy = true;
                }
                return truthy;
            },
            "no-author-country": function() {
                var truthy = false;
                if (this.config.author.extra.country == "false") {
                    truthy = true;
                }
                return truthy;
            },
            "no-reference-block": function() {
                var truthy = false;
                if (this.config.reference.show == "false" || this.config.reference.visibility == "invisible") {
                    truthy = true;
                }

                return truthy;
            },
            "no-reference-slide": function() {
                var truthy = false;
                if (this.config.reference.extra.showSlideReferences != "true") {
                    truthy = true;
                }

                return truthy;
            },
            "no-navigation-block": function() {
                var truthy = false;
                if (this.config.navigation.show == "false" || this.config.navigation.visibility == "invisible") {
                    truthy = true;
                }

                return truthy;
            },
            "no-navigation-title": function() {
                var truthy = false;
                if (this.config.navigation.extra.showTitle != "true") {
                    var truthy = true;
                }

                return truthy;
            },
            "no-navigation-thumbnail": function() {
                var truthy = false;
                if (this.config.navigation.extra.showThumbnail != "true") {
                    var truthy = true;
                }

                return truthy;
            },
            "no-navigation-description": function() {
                var truthy = false;

                if (this.config.navigation.extra.showDescription != "true") {
                    var truthy = true;
                }

                return truthy;
            },
            "no-navigation-index": function() {
                var truthy = false;
                if (this.config.navigation.extra.showIndex != "true") {
                    var truthy = true;
                }

                return truthy;
            },
            "no-transcription-block": function() {
                var truthy = false;
                if (this.config.transcription.show == "false" || this.config.transcription.visibility == "invisible") {
                    truthy = true;
                }

                return truthy;
            }
        }
    },
    statesActive: {
        logicalStates: [],
        visualStates: []
    },
    /**
     * Sets active states based on provided config
     * 1. Loops through all visual states functions
     *  and calls each of them.
     *  If function returns true it pushes function name in statesActive.visualStates array
     *  
     * 2. Loops through all logical states functions
     *  and calls each of them.
     *  If function returns true it pushes function name in statesActive.logicalStates array
     * 
     * 3. Makes sure to add respective statesActive.visualStates as a classes in "#presentation"
     * (this way we show only what should be shown)
     *   
     *   @param {Function} fn - callback function that is called after everything is set up
     *   with statesActive object as argument
     *   */
    setStates: function(fn) {

        if (!this.presentationDom) {
            this.presentationDom = $("#presentation");
        }

        this.statesActive.logicalStates = [];
        this.statesActive.visualStates = [];

        /* visual */
        $.each(this.states.visualStates, function(key, val) {
            /* @NOTE !important passing context to the function
             * so 'this' will refer to EW module itself */
            if (val.call(this)) {
                this.statesActive.visualStates.push(key);
            }
        }.bind(this));

        /* logical */
        $.each(this.states.logicalStates, function(key, val) {
            /* @NOTE !important passing context to the function
             * so 'this' will refer to EW module itself */
            if (val.call(this)) {
                this.statesActive.logicalStates.push(key);
            }
        }.bind(this));

        /* Visual states are inserted here as classes into presentation container,
         * first we remove all existing state classes then
         * we re-add active ones, css should do the rest */
        $.each(this.states.visualStates, function(key, val) {
            this.presentationDom.removeClass(key);
        }.bind(this));

        /* visual states is array */
        $.each(this.statesActive.visualStates, function(key, val) {
            this.presentationDom.addClass(val);
        }.bind(this));

        if (fn) {
            fn(this.statesActive);
        }

        /** when layout manager finishes work,
         * below event is triggered */
        $(document).trigger('layoutManagerReady', this.statesActive);

    }
};

EW["presentation_general"] = {
    init: function() {
        this.handleEvents();
    },
    handleEvents: function() {

        var presentationDom = $("#presentation");
        var stackObj = {};
        stackObj.context = presentationDom;
        stackObj.dir1 = "down";
        stackObj.dir2 = "left";

        var title_v = 'Success';
        var content_v = 'Success';
        var icon_v = "fa fa-check";
        var color_v = "#739E73";

        var type_v = "success";
        $(document).on("smart-notification", function(event, data) {
            type_v = "success";
            if (typeof data.title !== "undefined") {
                title_v = data.title;
            }
            if (typeof data.content !== "undefined") {
                content_v = data.content;
            }
            if (data.icon) {
                icon_v = data.icon;
            }
            if (data.color) {
                color_v = data.color;
            }
            if (data.type) {
                type_v = data.type;
            }

            new PNotify({
                title: title_v,
                text: content_v,
                type: type_v,
                stack: stackObj,
                delay: 1000,
                buttons: {
                    sticker: false
                }
            });

        });


        $(document).on("slidesSearchMobile", function() {
            presentationDom.toggleClass("filter_slides_show_mob");
        });

        $(document).on("disableSearch", function(event, data) {
            if (presentationDom.hasClass("search_disabled")) {
                presentationDom.removeClass("search_disabled");
                presentationDom.removeClass("filter_slides_show_mob");
            } else {
                presentationDom.addClass("search_disabled");
            }
        });

        var toogleFlag = false;
        var editModeToggleDom = $("#toggle-edit-mode");
        $(document).on("videoTplAdded", function() {
            var isChecked = editModeToggleDom.is(":checked");
            if (editModeToggleDom.length && isChecked) {
                toogleFlag = true;
                $(document).trigger('playerSettingsChange', {editmodeplayer: isChecked});

            } else if (typeof authorEditModeFlag !== "undefined" && authorEditModeFlag === true) {
                toogleFlag = true;
                $(document).trigger('playerSettingsChange', {editmodeplayer: toogleFlag});
            } else {
                toogleFlag = false;
            }
        });

        $(document).on("videoTplAdded subTplAdded authorTplAdded resTplAdded navTplAdded", function() {
            var isChecked = editModeToggleDom.is(":checked");
            if (isChecked) {
                if (EW["toggle-edit-mode"]) {
                    try {
                        EW["toggle-edit-mode"].showEdit(presentationDom);
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        });

        editModeToggleDom.on("change", function() {
            if (presentationDom.is(":visible")) {
                $(document).trigger('playerSettingsChange', {editmodeplayer: this.checked});
                var isChecked = editModeToggleDom.is(":checked");
                if (isChecked) {
                    EW["toggle-edit-mode"].showEdit(presentationDom);
                }
            }
        });

    }
};

/**
 * Inject script in each slide to track user
 * */
EW["presentation_track"] = {
    template: undefined,
    templateContainerDom: undefined,
    templateContainerSelector: "#trackableContainer",
    templateSelector: "#trackableTemplate",
    activeSlideId: undefined,
    init: function() {
        var _this = this;

        $(document).on("jsonPublished", function(event, data) {

            if (data.slides["slide_1"].trackable_code) {

                _this.templating(data.slides["slide_1"].trackable_code);
                _this.activeSlideId = data.slides["slide_1"].slide_id;

            }

        });

        $(document).on("slideChange", function(event, slide) {
            if (_this.activeSlideId) {
                if (EW.analytics)
                    EW.analytics.trigger('close', _this.activeSlideId);
            }
            if (slide.trackable_code) {
                _this.templating(slide.trackable_code);
                _this.activeSlideId = slide.slide_id;
            }
        });
    },
    /**
     * Generating template info from slide object
     * using jsrender.
     * @param {Object} slide - current slide data object
     **/
    templating: function(trackable_code) {
        if (!this.template) {
            this.template = $.templates(this.templateSelector);
        }
        if (!this.templateContainerDom) {
            this.templateContainerDom = $(this.templateContainerSelector);
        }
        var htmlOutput = this.template.render({trackable_code: trackable_code});
        this.templateContainerDom.html(htmlOutput);
    }
};
