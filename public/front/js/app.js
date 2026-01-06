
$(function () {

    
    NProgress.configure({ showSpinner: false });
    
    $.scrollUp({
        scrollName: 'topScroll',
        scrollText: '<i class="fas fa-long-arrow-alt-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade',
        zIndex: 100,
    });

    
    $('#zoom-pro').elevateZoom({
        gallery: 'gallery',
        galleryActiveClass: 'active',
        borderSize: 1,
        zoomWindowWidth: 540,
        zoomWindowHeight: 540,
        zoomWindowOffetx: 10,
        borderColour: '#e9e9e9',
    });

    
    $('#zoom-pro-quick-view').elevateZoom({
        gallery: 'gallery-quick-view',
        galleryActiveClass: 'active',
        zoomEnabled: false, 
    });

    
    $('#select-category', document).ResizeSelect();
    $('.select-hide').removeClass('select-hide');

    
    $('.v-menu', document).MegaMenuDropDowns();

    
    $('.section-timing-wrapper.dynamic', document).CountDown();
});
(function ($, window, document) {
    'use strict';
    
    
    
    let $vMenu = $('.v-menu');
    let mode = '';
    let bigScreenFlag = Number.MAX_VALUE;
    let smallScreenFlag = 1;
    
    let listItembackDropFlag = false;
    let $backDrop;
    let $searchFormWrapper;
    let $searchFormElement;
    let $allListItemsForHover = $('.js-backdrop');
    
    
    let settings = {
        bodyBackDropOnScenes: true,
        zIndexNumber: 999998
    };

        const windowWidth = function () {
        return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
    };

        const showBackDrop = function (element) {
        element.css('display', 'block').on('click', function () {
            $(this).css('display', '');
        });
    };

        const removeBackDrop = function (element) {
        element.css('display', '');
    };

        const attachClickOnResponsiveSearchForm = function () {
        $('#responsive-search').on('click', function () {
            $('.responsive-search-wrapper').stop(true, true).fadeIn();
        });

        $('#responsive-search-close-button').on('click', function () {
            $('.responsive-search-wrapper').stop(true, true).fadeOut();
        });
    };

        const attachClickOnMiniCart = function () {
        
        
        
        $('#mini-cart-trigger').on('click', function () {
            $('.mini-cart-wrapper').addClass('mini-cart-open');
        });

        $('#mini-cart-close').on('click', function () {
            $('.mini-cart-wrapper').removeClass('mini-cart-open');
        });
    };

        const attachClickOnVMenu = function () {
        $('.v-title').on('click', function () {
            $vMenu.toggleClass('v-close');
        });
    };

        const MouseEnterFunctionForMegaMenu = function () {
        
        $vMenu.css({'z-index': settings.zIndexNumber});
        
        showBackDrop($backDrop);
    };
        const MouseLeaveFunctionForMegaMenu = function () {
        
        $vMenu.css({'z-index': ''});
        
        removeBackDrop($backDrop);
    };

        const hoverOnListItems = function () {
        $allListItemsForHover.on('mouseenter', MouseEnterFunctionForMegaMenu);
        $allListItemsForHover.on('mouseleave', MouseLeaveFunctionForMegaMenu);
    };
        const hoverOffListItems = function () {
        $allListItemsForHover.off('mouseenter');
        $allListItemsForHover.off('mouseleave');
    };

        const mainBackDropManipulator = function () {
        if (settings.bodyBackDropOnScenes) {
            if (mode === 'landscape' && !listItembackDropFlag) {
                
                
                
                if ($('#app').find('.body-backdrop').length === 0) {
                    $('#app').append('<div class="body-backdrop"></div>\n');
                    
                    $backDrop = $('div.body-backdrop');
                    
                    $searchFormElement = $('#search-landscape');
                    $searchFormWrapper = $('.form-searchbox');
                    $searchFormElement.focus(function () {
                        
                        $searchFormWrapper.css({'position': 'relative', 'z-index': settings.zIndexNumber});
                        
                        showBackDrop($backDrop);
                    }).blur(function () {
                        
                        $searchFormWrapper.css({'position': '', 'z-index': ''});
                        
                        removeBackDrop($backDrop);
                    });
                    
                    
                    hoverOnListItems();
                    
                    listItembackDropFlag = true;
                }
            }

            if (mode === 'landscape' && listItembackDropFlag) {
                
                hoverOnListItems();
            } else if (mode === 'portrait' && listItembackDropFlag) {
                
                hoverOffListItems();
            }
        }

    };
        const manuallyRestartProgress = function () {
        
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            
            NProgress.start();
            
            NProgress.done();
        });
    };
        const attachClickQuantityButton = function () {
        let $currentTextField,currentVal;
        $('.plus-a').each(function () {
            $(this).on('click', function () {
                let $currentTextField = $(this).prev();
                let currentVal = parseInt($currentTextField.val());
                                if (!currentVal || currentVal === '' || currentVal === 'NaN' || currentVal === 0) {
                    
                    $currentTextField.val(1);
                }
                
                else if (currentVal < $(this).data('max')) {
                    $currentTextField.val(currentVal + 1);
                }
            });
        });
        $('.minus-a').each(function () {
            $(this).on('click', function () {
                $currentTextField = $(this).closest('div').find('input');
                currentVal = parseInt($currentTextField.val());
                                if (!currentVal || currentVal === '' || currentVal === 'NaN' || currentVal === 0) {
                    
                    $currentTextField.val(1);
                }
                
                else if (currentVal > $(this).data('min')) {
                    $currentTextField.val(currentVal - 1);
                }
            });
        });
    };

        const windowResizeBreakpoint = function () {
        if (windowWidth() <= 991 && bigScreenFlag > 991) {
            
            mode = 'portrait';
            
            mainBackDropManipulator();
        }

        if (windowWidth() > 991 && smallScreenFlag <= 991) {
            
            mode = 'landscape';
            
            mainBackDropManipulator();
        }
        bigScreenFlag = windowWidth();
        smallScreenFlag = windowWidth();
    };

        $(window).resize(function () {
        
        windowResizeBreakpoint();
    });


        $(function () {
        
        attachClickOnResponsiveSearchForm();
        
        attachClickOnMiniCart();
        
        attachClickOnVMenu();
       
        manuallyRestartProgress();
        
        attachClickQuantityButton();
        
        windowResizeBreakpoint();
    });
})(jQuery, window, document);

(function ($, window, document) {
    'use strict';
        const showNewsletterModal = function () {
            setTimeout(function () {
                
                $('#newsletter-modal').modal('show');
            }, 5000);
    };
        const sliderMain = function () {
        let $owl = $('.slider-main');
        $owl.owlCarousel({
            items: 1,
            autoplay: true,
			autoplayTimeout: 8000,
            loop: false,
            dots: false,
            rewind: true, 
            nav: true,
            
            navElement: 'div',
            navClass: ['main-slider-previous', 'main-slider-next'],
            navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'], 
        });

    };
        const productSlider = function () {
        
        let $productsSlider = $('.products-slider');
        $productsSlider.on('initialize.owl.carousel',function () {
           $(this).closest('.slider-fouc').removeAttr('class');
        }).each(function () {
            let thisInstance = $(this);
            let itemPerLine = thisInstance.data('item');
            thisInstance.owlCarousel({
                autoplay: false,
                loop: false,
                dots: false,
                rewind: true,
                nav: true,
                navElement: 'div',
                navClass: ['product-slider-previous', 'product-slider-next'],
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'],
                responsive: {
                    0: {
                        items: 1,
                    },
                    768: {
                        items: itemPerLine - 2,
                    },
                    991: {
                        items: itemPerLine - 1,
                    },
                    1200: {
                        items: itemPerLine,
                    },
                }
            });
        });
    };
        const SpecificCategorySlider = function () {
        
        let $specificCategorySlider = $('.specific-category-slider');
        $specificCategorySlider.on('initialize.owl.carousel',function () {
            $(this).closest('.slider-fouc').removeAttr('class');
        }).each(function () {
            let thisInstance = $(this);
            let itemPerLine = thisInstance.data('item');
            thisInstance.owlCarousel({
                autoplay: false,
                loop: false,
                dots: false,
                rewind: true,
                nav: true,
                navElement: 'div',
                navClass: ['specific-category-slider-previous', 'specific-category-slider-next'],
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'],
                responsive: {
                    0: {
                        items: 1,
                    },
                    768: {
                        items: 2,
                    },
                    991: {
                        items: itemPerLine -1,
                    },
                    1200: {
                        items: itemPerLine,
                    },
                }
            });
        });
    };
        const onTabChangeRefreshPositionOfCarousel = function () {
        
        
        $('.section-maker [data-toggle="tab"]').on('shown.bs.tab', function (e) {
            
            let $currentID = $(e.target).attr('href');
            
            $($currentID + '.active').children().trigger('refresh.owl.carousel');
        });

    };
        const brandSlider = function () {
        let thisInstance = $('.brand-slider-content');
        let itemPerLine = thisInstance.data('item');
        thisInstance.owlCarousel({
            autoplay: true,
			autoplayTimeout: 8000,
            loop: false,
            dots: false,
            rewind: true,
            nav: true,
            navElement: 'div',
            navClass: ['brand-slider-previous', 'brand-slider-next'],
            navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'],
            responsive: {
                0: {
                    items: 1,
                },
                768: {
                    items: 3,
                },
                991: {
                    items: itemPerLine,
                },
                1200: {
                    items: itemPerLine,
                },
            }
        });
    };



    $(function () {
        sliderMain();
        productSlider();
        SpecificCategorySlider();
        onTabChangeRefreshPositionOfCarousel();
        brandSlider();
    });

        $(window).on('load',function () {
        showNewsletterModal();
       $('.ph-item').removeClass('ph-item');
    });

})(jQuery, window, document);

(function ($, window, document) {
    'use strict';
        const googleinitMap = function () {
        
        
        let mapOptions = {
            
            zoom: 11,
            scrollwheel: false,
            
            center: new google.maps.LatLng(37.393322, -122.023780),
        };
        
        
        let mapElement = document.getElementById('map');
        
        let map = new google.maps.Map(mapElement, mapOptions);
        
        let marker = new google.maps.Marker({
            position: new google.maps.LatLng(37.393322, -122.023780),
            map: map,
        });
    };


    $(function () {
        
        if ($('#map').length !== 0 ) {
            try {
                google.maps.event.addDomListener(window, 'load', googleinitMap);
            } catch (e) {
                console.log('"Google Maps" refused to connect!');
            }
        }
    });

})(jQuery, window, document);


(function ($, window, document) {
    'use strict';
    
    let $ratingField = $('#your-rating-value');
    let $starWidth = $('#your-stars');
    let $starComment = $('#star-comment');



        const ratingStarsControl = function () {
        let oneStarWidth = 15; 
        let newStarWidth;
        let ratingthresholdNumber = 5;
        let comment;
        let currentVal;
        
        $ratingField.on('keyup',function () {
            
            $starWidth.css('width',0);
            
            $starComment.text('');
            
            
            
            if ($.isNumeric($ratingField.val())) {
                currentVal = parseFloat($ratingField.val());
            } else {
                currentVal = NaN;
            }
                        if ( !currentVal || currentVal === '' || currentVal === 'NaN' || currentVal === 0) {
                
                currentVal = 0;
                $starWidth.css('width',0);
                $starComment.text('');
            } else {
                if ( (currentVal >=1) && (currentVal <= ratingthresholdNumber)) {

                    if (currentVal === 1 ) {
                        comment = 'I hate it.';
                    }
                    else if(currentVal === 2 ) {
                        comment = "I don't like it.";
                    }
                    else if(currentVal === 3 ) {
                        comment = "It's OK.";
                    }
                    else if(currentVal === 4 ) {
                        comment = "I like it.";
                    }
                    else if(currentVal === 5 ) {
                        comment = "It's Perfect.";
                    }
                    
                    currentVal = currentVal.toFixed(1);
                    
                    newStarWidth = oneStarWidth * currentVal;
                    
                    newStarWidth = Math.floor(newStarWidth);
                    
                    $starWidth.css('width',newStarWidth);
                    
                    $starComment.text(comment);
                }
            }
        });

    };



    $(function () {
        
        ratingStarsControl();
    });

})(jQuery, window, document);

(function ($, window, document, undefined) {
    'use strict';
    
    let $shopProductContainer = $('.product-container');
    let $searchFetchAllbtn = $('.fetch-categories ul > li > button');


        const priceRangeSlider = function () {
        $('.price-slider-range').each(function () {
            
            let queryMin = parseFloat($(this).data('min'));
            
            let queryMax = parseFloat($(this).data('max'));
            
            let currecyUnit = $(this).data('currency');
            
            let defaultLow = parseFloat($(this).data('default-low'));
            
            let defaultHigh = parseFloat($(this).data('default-high'));
            
            let $instance = $(this);
            
            $('.price-filter').slider({
                range: true,
                min: queryMin,
                max: queryMax,
                values: [ defaultLow, defaultHigh ],
                slide: function (event, ui) {
                    let result = '<div class="price-from">'+ currecyUnit + ui.values[ 0 ] + '</div>\n<div class="price-to">' + currecyUnit + ui.values[ 1 ] + '</div>\n';
                    $instance.parent().find('.amount-result').html(result);
                }
            });


        });
    };
    
     $shopProductContainer.addClass('grid-style'); 
     $shopProductContainer.removeClass('list-style');

     const attachClickGridAndList = function () {
        $('#list-anchor').on('click',function () {
            $(this).addClass('active');
            $(this).next().removeClass('active');
            $shopProductContainer.removeClass('grid-style');
            $shopProductContainer.addClass('list-style');
        });
        $('#grid-anchor').on('click',function () {
            $(this).addClass('active');
            $(this).prev().removeClass('active');
            $shopProductContainer.removeClass('list-style');
            $shopProductContainer.addClass('grid-style');
        });
    };
        const searchFetchAllCategoriesFunctionality = function () {
        $searchFetchAllbtn.on('click',function () {
            $(this).toggleClass('js-open');
             $(this).next('ul').stop(true,true).slideToggle();
        });
    };
        const bindScrollWithAssociateFilters = function () {
        $('.associate-wrapper').each(function () {
            $(this).slimScroll({
                height: 'auto',
                railClass : 'ScrollRail',
                barClass : 'ScrollBar',
                wrapperClass : 'ScrollDiv',
            });
        });
    };

    $(function () {
        
        priceRangeSlider();
        
        attachClickGridAndList();
        
        bindScrollWithAssociateFilters();
        
        searchFetchAllCategoriesFunctionality();
    });

})(jQuery, window, document);
