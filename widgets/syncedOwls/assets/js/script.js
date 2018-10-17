function syncedOwls() {
    if ($('.synced-carousel-main') && $('.synced-carousel-thumbs')) {

        var $sync1 = $('.synced-carousel-main'),
            $sync2 = $('.synced-carousel-thumbs'),
            flag = false,
            duration = 300;


        $sync1
            .owlCarousel({
                items: 1,
                margin: 0,
                nav: true,
                navText: ['<svg xmlns="http://www.w3.org/2000/svg" width="8" height="12.969" viewBox="0 0 8 12.969"><path d="M127.255,252.782q3.093-2.772,6.185-5.544c0.848-.76,2.114.475,1.262,1.239q-2.752,2.466-5.5,4.933,2.766,2.552,5.531,5.107a0.884,0.884,0,0,1-1.262,1.238l-6.212-5.734A0.867,0.867,0,0,1,127.255,252.782Z" transform="translate(-127 -247)"/></svg>',
                    '<svg xmlns="http://www.w3.org/2000/svg" width="8.031" height="12.969" viewBox="0 0 8.031 12.969"><path d="M177.745,252.782q-3.093-2.772-6.185-5.544c-0.848-.76-2.114.475-1.262,1.239q2.752,2.466,5.5,4.933-2.766,2.552-5.531,5.107a0.884,0.884,0,0,0,1.262,1.238l6.212-5.734A0.867,0.867,0,0,0,177.745,252.782Z" transform="translate(-170 -247)"/></svg>'
                ],
                dots: false,
            })
            .on('changed.owl.carousel', function (el) {
                var index = el.item.index;
                $('.synced-carousel-thumbs .owl-item').eq(index).addClass('current').siblings().removeClass('current')
            })
            .on('changed.owl.carousel', function (e) {
                if (!flag) {
                    flag = true;
                    $sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
                    flag = false;
                }
            });

        $sync2
            .owlCarousel({
                margin: 20,
                items: 4,
                nav: false,
                center: false,
                dots: false,
                onInitialized: function () {
                    $('.synced-carousel-thumbs .owl-item').eq(0).addClass('current');
                }
            })
            .on('click', '.owl-item', function () {
                $sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);

            })
            .on('changed.owl.carousel', function (e) {
                if (!flag) {
                    flag = true;
                    $sync1.trigger('to.owl.carousel', [e.item.index, duration, true]);
                    flag = false;
                }
            });
    }
}

syncedOwls();