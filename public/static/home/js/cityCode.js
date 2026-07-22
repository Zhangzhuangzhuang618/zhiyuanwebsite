// 主站需要定位
// 城市站不需要定位
$(function() {

    var aa = localStorage.getItem('cityCode');
    console.log(aa)
    if (aa) {
        var bb = aa.split(',')
        var localCity = bb[0];
        var cityText = bb[1];
    }
	// console.log(localCity)

    if (localCity && localCity!='finished') {
    // if (localCity) {	
        if (cityText) {
            // $('#city-now').html(cityText);
        }
        $('body .setUrl').each(function(index, el) { //遍历需要修改的url
            var url = $(this).attr('href');
            var domain = url.split('.com')[1];
            var newLink = "http://" + localCity + ".zhiranbj.com" + domain;
            $(this).attr('href', newLink);
        });
    } else {
        var citysearch = new AMap.CitySearch();
        citysearch.getLocalCity(function(status, result) {
            if (status === 'complete' && result.info === 'OK') {
                if (result && result.city && result.bounds) {
                    var locationCity2 = result.city.replace("市", '');
                    $('.city-list-box .setCity').each(function(index, el) { //遍历城市
                        if (locationCity2 == $(this).html()) {
                            $('#city-now').html(locationCity2);
                            var code = $(this).attr('city-code');
                            var arr = [];
                            arr.push(code, locationCity2)
                            console.log(arr)
                            set_city("cityCode", arr);
                            console.log('定位城市 '+code)
                            console.log(localStorage.getItem("cityCode"))
                            // if (code) { //跳转到定位城市网址
                            //   window.location.href = "http://" + code + ".zhiranbj.com"
                            // }else{
                            //     window.location.href = "http://beijing.zhiranbj.com"
                            // }
                        }
                    });
                }
            } else {
            	console.log('定位失败')
            	
                // window.location.href = "http://beijing.zhiranbj.com"
            }
        });
    }

    $('.go-btn').click(function(event) {
        var code = $(this).attr('city-code');
        var arr = [];
        var city = $('#shi-input').val();
        if (!code) {
            return false;
        } else {
            arr.push(code, city)
            set_city("cityCode", arr);
        }
    });


    $(".setCity").click(function(event) {
        if ($(this).hasClass('not')) {
            // alert('网站建设中...')
            layer.msg('网站建设中...敬请期待！')
            return false
        }
        var cityCode = $(this).attr('city-code');
        var cityText = $(this).text();
        $('#city-now').html(cityText);
        var arr = [];
        arr.push(cityCode, cityText)
        set_city("cityCode", arr);
        $('body .setUrl').each(function(index, el) {
            var url = $(this).attr('href');
            var domain = url.split('.com')[1];
            var newLink = "http://" + cityCode + ".zhiranbj.com" + domain;
            console.log(newLink);
            $(this).attr('href', newLink);
        });
    });

    function set_city(key, value) {
        if (key == '') return false;
        localStorage.setItem(key, value);
    }
})