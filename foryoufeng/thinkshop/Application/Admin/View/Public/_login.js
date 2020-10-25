$(function() {
    if (!$('body').hasClass("body-public-login")) {
        return false;
    }


    //刷新验证码
    $(function() {
        $(".reload-verify").on('click', function() {
            var verifyimg = $(".verifyimg").attr("src");
            if (verifyimg.indexOf('?') > 0) {
                $(".verifyimg").attr("src", verifyimg + '&random=' + Math.random());
            } else {
                $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/, '') + '?' + Math.random());
            }
        });
    });


    //背景粒子效果
    particlesJS('particles-js', {
        particles: {
            color: '#46BCF3',
            shape: 'circle', // "circle", "edge" or "triangle"
            opacity: 1,
            size: 2,
            size_random: true,
            nb: 200,
            line_linked: {
                enable_auto: true,
                distance: 100,
                color: '#46BCF3',
                opacity: .8,
                width: 1,
                condensed_mode: {
                enable: false,
                rotateX: 600,
                rotateY: 600
                }
        },
        anim: {
            enable: true,
            speed: 1
        }
        },
        interactivity: {
            enable: true,
            mouse: {
            distance: 250
        },
        detect_on: 'canvas', // "canvas" or "window"
            mode: 'grab',
            line_linked: {
            opacity: .5
        },
        events: {
            onclick: {
            enable: true,
            mode: 'push', // "push" or "remove" (particles)
            nb: 4
            }
        }
        },
        /* Retina Display Support */
        retina_detect: true
    });

});