;const pluginPhotoswipe = function () {
    const self = this;
    let instance = null;

    self.parseSlides = function (items) {
        let i, slide, src, size, title, thumb;
        const items_length = items.length;
        const slides = [];

        const getTitle = function (item) {
            title = item.getAttribute('data-caption');

            if (!title) {
                title = item.querySelector('figcaption');

                if (title) {
                    title = title.innerHTML;
                }
            }

            return title;
        };

        for (i = 0; i < items_length; i++) {
            const item = items[i];

            src = item.getAttribute('href');
            size = item.getAttribute('data-size');

            if (!src || !size) {
                continue;
            }

            size = size.split('x');
            slide = {
                el: item,
                src,
                w: parseInt(size[0] || 0, 10),
                h: parseInt(size[1] || 0, 10)
            };

            title = getTitle(item);

            if (title) {
                slide.title = title;
            }

            thumb = item.querySelector('img');

            if (thumb) {
                slide.msrc = thumb.getAttribute('src');
            }

            slides.push(slide);
        }

        return slides;
    };

    self.onGalleryClick = function (event, items, item_selector, options) {
        event.preventDefault();

        if (event.target === event.currentTarget) {
            return;
        }

        let i, index, item;
        const items_length = items.length;
        const clicked_el = event.target;

        for (i = 0; i < items_length; i++) {
            item = items[i];

            if (item !== clicked_el && item !== clicked_el.closest(item_selector)) {
                continue;
            }

            index = i;

            break;
        }

        if (index >= 0) {
            self.initPhotoswipe(self.parseSlides(items), options);
            self.openPhotoSwipe(index);
        }
    };

    self.openPhotoSwipe = function (index, disableAnimation) {
        index = parseInt(index, 10);

        if (isNaN(index)) {
            return;
        }

        instance.options.index = index;

        const animationDuration = instance.options.showAnimationDuration;

        if (disableAnimation) {
            instance.options.showAnimationDuration = 0;
        }

        instance.init();
        instance.options.showAnimationDuration = animationDuration;
    };

    self.initPhotoswipe = function (slides, options) {
        instance = new PhotoSwipe(
            document.querySelector('.pswp'),
            PhotoSwipeUI_Default,
            slides,
            options
        );
    };

    self.parseURLHash = function () {
        let i, pair;
        const
            hash = window.location.hash.substring(1),
            params = {};

        if (hash.length < 5) {
            return params;
        }

        const vars = hash.split('&');

        for (i = 0; i < vars.length; i++) {
            if (!vars[i]) {
                continue;
            }

            pair = vars[i].split('=');

            if (pair.length < 2) {
                continue;
            }

            params[pair[0]] = decodeURIComponent(pair[1]);
        }

        return params;
    };

    const init = function (id, item_selector = 'figure', options = {}) {
        if (!id) {
            return;
        }

        const gallery_selector = '#' + id;
        const gallery = document.querySelector(gallery_selector);

        if (!gallery) {
            return;
        }

        const gallery_items = gallery.querySelectorAll(item_selector);

        if (!gallery_items.length) {
            return;
        }

        const centerThumbPosition = options.centerThumbPosition || false;
        delete options.centerThumbPosition;

        function getThumbnailBounds(index) {
            const
                thumbnail = gallery_items[index],
                pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                rect = thumbnail.getBoundingClientRect()
            ;

            let y = rect.top + pageYScroll;

            if (centerThumbPosition) {
                const thumbnail_image = thumbnail.querySelector('img');

                y += (rect.height / 2) - (
                    (
                        (thumbnail_image.width * thumbnail_image.naturalHeight) / thumbnail_image.naturalWidth
                    ) / 2
                );
            }

            return {x: rect.left, y, w: rect.width};
        }

        options = {
            ...{
                galleryUID: id,
                getNumItemsFn: () => gallery_items.length,
                getThumbBoundsFn: getThumbnailBounds,
                showHideOpacity: true
            },
            ...options
        };

        gallery.addEventListener('click', function (event) {
            self.onGalleryClick(event, gallery_items, item_selector, options);
        });

        const hashData = self.parseURLHash();

        if (hashData.pid && hashData.gid && hashData.gid === id) {
            self.initPhotoswipe(self.parseSlides(gallery_items), options);
            self.openPhotoSwipe(parseInt(hashData.pid, 10) - 1, true);
        }
    };

    return {
        init,
        get: () => instance
    };
};
