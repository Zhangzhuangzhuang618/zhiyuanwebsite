
    var swiper3 = new Swiper('.plano', {
        slidesPerView: 4,
        spaceBetween: 22,
        noSwiping : true,
        speed:600,
        noSwipingClass : 'stop-swiping',
        navigation: {
           nextEl: '.plano-right',
           prevEl: '.plano-left',
        },
    }); 
   var swiper = new Swiper('.product-lines', {

        spaceBetween: 20,

        noSwiping : true,
        speed:600,
        noSwipingClass : 'stop-swiping',
        navigation: {
           nextEl: '.btn-right',
           prevEl: '.btn-left',
        },
    });
    var swiper2 = new Swiper('.banner', {
        slidesPerView: 1,
        loop: true,
        effect : 'fade',
        autoplay:true,
        pagination: {
        el: '.swiper-pagination',
        clickable: true,
        },
        navigation: {
        nextEl: '.banner-next',
        prevEl: '.banner-left',
        }
    });
    var swiper3 = new Swiper('.news-slide', {
        slidesPerView: 4,
        loop: true,
        autoplay:true,
        direction: 'vertical',
    });

    var swiper4 = new Swiper('.swiper-hot-news', {
        slidesPerView: 1,
        loop: true,
        autoplay:true,
        pagination: {
        el: '.swiper-pagination',
        clickable: true,
        },
        navigation: {
        nextEl: '.hot-news-btn-right',
        prevEl: '.hot-news-btn-left',
        }
    });
    // var swiper5 = new Swiper('.case-video-swiper', {
    //     slidesPerView: 3,
    //     // autoplay:true,
    //     spaceBetween: 20,
    //     navigation: {
    //     nextEl: '.case-video-next',
    //     prevEl: '.case-video-prev',
    //     },
    // });

    var swiper5 = new Swiper('.swiper-1 .case-video-swiper', {
        slidesPerView: 3,
        spaceBetween: 20,
        navigation: {
        nextEl: '.swiper-1 .case-video-next',
        prevEl: '.swiper-1 .case-video-prev',
        },
    });
    var swiper6 = new Swiper('.swiper-2 .case-video-swiper', {
        slidesPerView: 3,
        spaceBetween: 20,
        navigation: {
        nextEl: '.swiper-2 .case-video-next',
        prevEl: '.swiper-2 .case-video-prev',
        },
    });
    var swiper6 = new Swiper('.swiper-3 .case-video-swiper', {
        slidesPerView: 4,
        spaceBetween: 22,
        navigation: {
        nextEl: '.swiper-3 .case-video-next',
        prevEl: '.swiper-3 .case-video-prev',
        },
    });
    var swiper7 = new Swiper('.swiper-4 .case-video-swiper', {
        slidesPerView: 4,
        spaceBetween: 22,
        navigation: {
        nextEl: '.swiper-4 .case-video-next',
        prevEl: '.swiper-4 .case-video-prev',
        },
    });
    var swiper8 = new Swiper('.swiper-5 .case-video-swiper', {
        slidesPerView: 4,
        spaceBetween: 22,
        navigation: {
        nextEl: '.swiper-5 .case-video-next',
        prevEl: '.swiper-5 .case-video-prev',
        },
    });
    var swiper9 = new Swiper('.swiper-6 .case-video-swiper', {
        slidesPerView: 4,
        spaceBetween: 22,
        navigation: {
        nextEl: '.swiper-6 .case-video-next',
        prevEl: '.swiper-6 .case-video-prev',
        },
    });
    var swiper10 = new Swiper('.case-site-swiper', {
        slidesPerView: 4,
        spaceBetween: 21,
        navigation: {
        nextEl: '#bz-btn-next',
        prevEl: '#bz-btn-prev',
        },
        breakpoints: {
            1200: {
                slidesPerView: 3, // 屏幕宽度小于600px时，每个视图显示2个幻灯片
                spaceBetween: 20, // 幻灯片之间的间距调整为20
            },
            760: {
                slidesPerView: 2, // 屏幕宽度小于320px时，每个视图显示1个幻灯片
                spaceBetween: 10, // 幻灯片之间的间距调整为10
            }
        }
    });
    var swiper11 = new Swiper('.order-swiper .swiper-container', {
          direction: 'vertical',
          autoplay:true,
          loop:true
    });
    var swiper11 = new Swiper('.order-swiper1 .swiper-container', {
          direction: 'vertical',
          autoplay:true,
          loop:true
    });
    var swiper12 = new Swiper('.hot-case-video', {
        slidesPerView: 4,
        loop: true,
        autoplay:true,
        direction: 'vertical',
        spaceBetween: 10,
    });
	var swiper13 = new Swiper('.House-swiper', {
        slidesPerView: 1,
        loop: true,
        autoplay:true,
        pagination: {
        el: '.swiper-pagination',
        clickable: true,
        },
      
    });
   
    var swiper14 = new Swiper('.form-tel', {
        autoplay:true,
        pagination: {
        el: '.swiper-pagination',
        clickable: true,
        },
    	
    });
    var swiper15 = new Swiper('.index-case', {
        slidesPerView: 4,
        spaceBetween:22,
        loop:true,
        navigation: {
        nextEl: '.case-tj .index-case-next',
        prevEl: '.case-tj .index-case-prev',
        },


    });
    var swiper16 = new Swiper('.index-case01', {
        slidesPerView: 4,
        spaceBetween:22,
        loop:true,
        navigation: {
        nextEl: '.case-tj .index-case-next',
        prevEl: '.case-tj .index-case-prev',
        },
       observerParents: true,
  
    });
    var swiper17 = new Swiper('.index-case02', {
        slidesPerView: 4,
        spaceBetween:22,
        loop:true,
        navigation: {
        nextEl: '.case-tj .index-case-next',
        prevEl: '.case-tj .index-case-prev',
        },
        observerParents: true,
    
    });
    var swiper18 = new Swiper('.index-case03', {
        slidesPerView: 4,
        spaceBetween:22,
        loop:true,
        navigation: {
        nextEl: '.case-tj .index-case-next',
        prevEl: '.case-tj .index-case-prev',
        },
        observerParents: true,

    });

    
