// Register the service worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const reg = await navigator.serviceWorker.register('sw.js');
            console.log('Service worker registered! 😎', reg);
        } catch (err) {
            console.log('😥 Service worker registration failed: ', err);
        }
    });
}

// Load NAV ( experimental )
//    $('nav').load('nav.php');

// Load MAIN or page
if (window.location.hash != '') {
    var hash_string = window.location.hash.split("#");
    $('main').load( 'search.php', {id: hash_string[1]});
} else if (window.location.pathname == '/') {
    $('main').load('start_page.php');
} else if (window.location.pathname == '/%D0%9A%D0%9E%D0%9D%D0%A2%D0%90%D0%9A%D0%A2%D0%AB') {
    contactsPage();
} else {
    var path = window.location.pathname.split(/[_/]+/);
    path_stings = {};
    path_stings['cat'] = decodeURI(path[1]);
    if (path[2] != undefined) {
        path_stings['subcat'] = decodeURI(path[2]);
    }
    $('main').load( 'search.php', path_stings);
}

// Header on top
$(window).scroll( function() {
    if ($('#mobile-menu').css('display') == 'none') {
        if ($(this).scrollTop() > 120) {
            $('header').addClass('top blur');
        } else {
            $('header').removeClass('top blur');
        }
    } else {
        if ($(this).scrollTop() > 65) {
            $('header').addClass('top blur');
        } else {
            $('header').removeClass('top blur');
        }
    }
});

// Seach box is active
$('#search').focus( function() {
    if ($('#mobile-menu').css('display') != 'none') {
        $('#before-header, header, main, footer').addClass('blured');
    } else {
        $('#before-header, header, nav, main, footer').addClass('blured');
    }
    $('body').css("overflow", "hidden");
    $('.search-box').addClass('search-box-focus');
    $('.search-delete-text').hide();
    $(this).attr("placeholder","Название, категория или каталожный номер");
});
$('#search').blur( function() {
    setTimeout( function() {
        if ($('#search').data('focus')) {
            $('#search').focus().removeData('focus');
        } else {
            if (!$('nav').hasClass('opened')) {
                if ($('#mobile-menu').css('display') != 'none') {
                    $('#before-header, header, main, footer').removeClass('blured');
                } else {
                    $('#before-header, header, nav, main, footer').removeClass('blured');
                }
            }
            $('body').css("overflow", "overlay");
            $('.search-box').removeClass('search-box-focus');
            $('#search').val('').attr("placeholder","Поиск по каталогу");
            $('#ac').hide().html('');
            $('#mobile-search').removeClass('active');
        }
    }, 100);
});


// Search
$("#searchform").submit( function() {
    event.preventDefault();
    if (document.querySelector('#searchform').reportValidity()) {
        $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
        var data = $('#search').val();
        $('main').load( 'search.php', { q: data }, function() {
            $('.active').removeClass('active');
            $('#search').blur().val('');
        });
    }
});
$('.search-submit-button').click(function () {
        if (document.querySelector('#searchform').reportValidity()) {
            $('#search').submit();
        } else {
            $('#search').data('focus', true);
        }
});    
$('.search-delete-text').click( function() {
    $('#search').data('focus', true).val('');
    $('.search-delete-text').hide();
    $('#ac').hide().html('');
});

// Autocomplete
$('#search').keyup( function(e) {
    switch(e.which)
    {
        case 37 : // Arrow left
            break;
        case 39 : // Arrow right
            break;
        case 38 : // Arrow up
            break;
        case 40 : // Arrow down
            break;
        case 13 : // Enter
            break;
        default:
            var value = $(this).val();
            if ( value.length > 2 ) {
                $('#ac').show();
                $('#ac').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
                var data = $(this).val();       
                $('#ac').load( 'ac.php', { q: data });
            } else {
                $('#ac').hide().html('');
            }
            if ($('#search').val() == '') {
                $('.search-delete-text').hide();
            } else {        
                $('.search-delete-text').show();
            }
            return;
    }
    e.preventDefault();
});

// Main page ---------------------------------------------------------------------------------------------------------------------------------
$('#logo, #medeo, #mobile-home').click( function() {
    $('.active').removeClass('active');
    $(this).addClass('active');
    $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
    $('main').load('start_page.php');
    history.pushState(null, "", '/');        
    $('nav').removeClass('opened');
    $('#before-header, header, main, footer').removeClass('blured');
    $('body').css("overflow", "overlay");
});

// Nav
$('nav > div > div:first-child').click( function() {
    $('.nav-active').removeClass('nav-active');
    $('.active').removeClass('active');
    $(this).parent().addClass('nav-active');
    if ($('#mobile-menu').css('display') != 'none') {
        $('#mobile-nav').addClass('active');
    }
});

$('nav > div > div:last-child > div > div:first-child').click( function() {
$('.active').removeClass('active');
if ($('#mobile-menu').css('display') == 'none') {
    $(this).addClass('active');
    $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
    var data = $(this).text();
    $('main').load( 'search.php', { cat: data } );
    history.pushState({ cat: data }, "", "/" + data);
} else {
    $(this).parent().addClass('active');
    $('#mobile-nav').addClass('active');
}
});

$('nav > div > div:last-child > div > div:last-child > div').click( function() {
    $('.active').removeClass('active');
    $(this).parent().parent().children().first().addClass('active');
    $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
    var data1 = $(this).text();
    var data2 = $(this).parent().parent().children().first().text();
    $('main').load( 'search.php', { subcat: data1, cat: data2 } );
    history.pushState({ subcat: data1, cat: data2 }, "", "/" + data2 + "_" + data1);
    $(this).parent().removeClass('active');
    $('nav').removeClass('opened');
    $('#before-header, header, main, footer').removeClass('blured');
    $('body').css("overflow", "overlay");
});

// YaMaps and contacts
function contactsPage() {
    $('main').load('contacts.html', function() {
        $.getScript('https://api-maps.yandex.ru/2.1/?apikey=793bffb9-d244-43e3-bc32-d42a596d4374&lang=ru_RU', function() {
            ymaps.ready(init);
            function init() {
                var myMap = new ymaps.Map("map", {
                    center: [55.833398, 49.049641],
                    controls: ['zoomControl'],
                    zoom: 16
                });
                var myGeoObject = new ymaps.GeoObject({	
                    geometry: {
                        type: "Point",
                        coordinates: [55.834735, 49.049587]
                    },		
                    properties: {
                        iconContent: "MEDEO"
                    }
                }, {
                    preset: 'islands#darkBlueStretchyIcon'
                });
                myMap.geoObjects.add(myGeoObject);
            }
        })
    });      
}

// Mobile Menu
$('#contacts, #mobile-contacts').click( function() {
    $('.active').removeClass('active');
    $(this).addClass('active');
    $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
    contactsPage();
    history.pushState( null , "", "/КОНТАКТЫ" );
    $('nav').removeClass('opened');
    $('#before-header, header, main, footer').removeClass('blured');
    $('body').css("overflow", "overlay");
});

$('#mobile-search').click( function() {
    if ($('.search-box').hasClass('search-box-focus')) {
    } else {
        $('#search').focus();
        $('.active').removeClass('active');
        $(this).addClass('active');
        $('nav').removeClass('opened');
    }
});

$('#mobile-nav').click( function() {
    if ($('nav').hasClass('opened')) {
        $('.active').removeClass('active');
        $('nav').removeClass('opened');
        $('#before-header, header, main, footer').removeClass('blured');
        $('body').css("overflow", "overlay");
    } else {
        $('.active').removeClass('active');
        $(this).addClass('active');
        $('nav').addClass('opened');
        $('#before-header, header, main, footer').addClass('blured');
        $('body').css("overflow", "hidden");
    }
});

// History        
window.onpopstate = function() {
    $('.active').removeClass('active');
    if (history.state != null) {
        $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
        $('main').load( 'search.php', history.state);
    } else if (window.location.pathname == '/%D0%9A%D0%9E%D0%9D%D0%A2%D0%90%D0%9A%D0%A2%D0%AB') {
        contactsPage();
    } else {
        $('main').html("<div class='lds-ellipsis'><div></div><div></div><div></div><div></div></div>");
        $('main').load('start_page.php');
    }        
};