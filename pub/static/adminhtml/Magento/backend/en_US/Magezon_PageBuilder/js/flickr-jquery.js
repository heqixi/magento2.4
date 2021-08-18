/*
* jQuery Flickr Photoset
* https://github.com/hadalin/jquery-flickr-photoset
*
* Copyright 2014, Primož Hadalin
*
* Licensed under the MIT license:
* http://www.opensource.org/licenses/MIT
*/

;(function ($, window, document, undefined) {

    'use strict';

    var pluginName = "flickr",
        defaults = {
            apiKey: "",
            photosetId: "",
            errorText: "Error generating gallery.",
            loadingSpeed: 38,
            photosLimit: 30,
            colClass: ''
        },
        apiUrl = 'https://api.flickr.com/services/rest/',
        photos = [];

    // The actual plugin constructor
    function Plugin(element, options) {
        var self = this;
        this.element = $(element);
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;

        this._hideSpinner = function() {
            this.element.find('.spinner-wrapper').hide().find('*').hide();
        };

        this._printError = function() {
            this.element.find('.gallery-container').append($("<div></div>", { "class": "col-lg-12 col-lg-offset-1" })
                .append($("<div></div>", { "class": "error-wrapper" })
                    .append($("<span></span>", { "class": "label label-danger error" })
                        .html(this.settings.errorText))));
        };

        this._flickrAnimate = function() {
            this.element.find('.gallery-container img').each($.proxy(function(index, el) {
                var image = el;
                setTimeout(function() {
                    $(image).parent().fadeIn();
                }, this.settings.loadingSpeed * index);
            }, this));
        };

        this._printGallery = function(photos) {
            var _this = this;
            var element = this.element.find('.gallery-container');
            $.each(photos, function(key, photo) {
                var html = '<div class="' + _this.settings.colClass + '">';
                html += '<a href="' + photo.href + '" title="' + photo.title + '" data-gallery="">';
                html += '<img alt="' + photo.title + '" src="' +  photo.thumbnail  + '"/>'
                html += '</a>';
                html += '</div>';
                element.append(html);
            });

            element.imagesLoaded()
                .done($.proxy(this._flickrAnimate, this))
                .always($.proxy(this._hideSpinner, this));
        };

        this._flickrPhotoset = function(photoset) {
            var _this = this;

            photos[photoset.id] = [];
            $.each(photoset.photo, function(key, photo) {
                // Limit number of photos.
                if(key >= _this.settings.photosLimit) {
                    return false;
                }

                photos[photoset.id][key] = {
                    thumbnail: 'http://farm' + photo.farm + '.static.flickr.com/' + photo.server + '/' + photo.id + '_' + photo.secret + '_n.jpg',
                    href: 'http://farm' + photo.farm + '.static.flickr.com/' + photo.server + '/' + photo.id + '_' + photo.secret + '_b.jpg'
                };

                if (_this.settings.showPhotoTitle) {
                    photos[photoset.id][key]['title'] = photo.title;
                }
            });

            this._printGallery(photos[photoset.id]);
        };

        this._onFlickrResponse = function(response) {
            if(response.stat === "ok") {
                 this._flickrPhotoset(response.photoset);
            }
            else {
                this._hideSpinner();
                this._printError();
            }
        };

        this._flickrRequest = function(method, data) {
            var url = apiUrl + "?format=json&jsoncallback=?&method=" + method + "&api_key=" + this.settings.apiKey;

            $.each(data, function(key, value) {
                url += "&" + key + "=" + value;
            });

            $.ajax({
                dataType: "json",
                url: url,
                context: this,
                success: this._onFlickrResponse
            });
        };

        this._flickrInit = function () {
            this._flickrRequest('flickr.photosets.getPhotos', {
                photoset_id: this.settings.photosetId,
                per_page: this.settings.photosLimit
            });
        };

        // Init
        this.init();
    }

    Plugin.prototype = {
        init: function () {
            this._flickrInit();
        }
    };

    // Wrapper
    $.fn[pluginName] = function (options) {
        this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });

        // Chain
        return this;
    };

})(jQuery, window, document);