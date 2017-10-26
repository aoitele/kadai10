<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--    <meta name="description" content="おひとり様のグルメ巡りをサポートするサイトです">-->

    <title>MeGURU</title>

    <script src=../js/jquery-2.1.3.min.js></script>



    <!-- Bootstrap Core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" id="themesid">

    <!-- Custom CSS -->
    <link href="../css/landing-page.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet">

    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">


    <nav class="navbar-default navbar-fixed-top top nav" role="navigation">
        <div class="navbar-topnav">
            <!--            ロゴとトグルボタンの設置-->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand topnav" href="">MeGURU</a>
            </div>
            <!--            コンテンツへのリンクナビ-->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="#about">About</a>
                    </li>
                    <li>
                        <a href="#service">Service</a>
                    </li>
                    <li>
                        <a href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.navbar-topnav -->
    </nav>

    <a name="about"></a>
    <div class="intro-header">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="intro-message">
                        <h1>MeGURU</h1>
                        <h3>おひとり様のグルメプラットフォーム</h3>
                        <hr class="intro-divider">
                        <ul class="list-inline intro-social-buttons">
                            <li>
                                <a class="btn btn-default btn-lg"><i class="fa fa-handshake-o" aria-hidden="true"></i><span class="network">Map Direction</span></a>
                            </li>
                            <li>
                                <a href="https://twitter.com/SBootstrap" class="btn btn-default btn-lg"><i class="fa fa-twitter fa-fw"></i> <span class="network">Twitter</span></a>
                            </li>
                            <li>
                                <button class="btn btn-default btn-lg" data-toggle="collapse" data-target="#hyoji"><i class="fa fa-twitter fa-fw"></i> <span class="network">お気に入りリスト</span></button>
                            </li>
                            <!--トグルでページ下部にリストが出る、それをハッシュタグをつけて呟けるようにする-->

                        </ul>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container -->

    </div>
    <!-- /.intro-header -->

</head>

<body>

    <!-- JS -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwDjDmo5BM-NcYeYMGm3c6lgg0DxQT89c&libraries=places&callback=initMap" async defer></script>

    <div id="content_wrapper">

        <input id="origin-input" class="controls" type="text" placeholder="今の気分を入力してください">

        <div id="map_canvas" max-width:none;></div>

        <!--Twitterタイムラインの取得-->
        <div id="twitter_timeline" max-width:none;>
            <a class="twitter-timeline" href="https://twitter.com/hashtag/%E3%82%B0%E3%83%AB%E3%83%A1%E3%80%80%E6%9D%B1%E4%BA%AC" data-widget-id="920619769023229954">#グルメ　東京 のツイート</a>
            <script>
                ! function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0],
                        p = /^http:/.test(d.location) ? 'http' : 'https';
                    if (!d.getElementById(id)) {
                        js = d.createElement(s);
                        js.id = id;
                        js.src = p + "://platform.twitter.com/widgets.js";
                        fjs.parentNode.insertBefore(js, fjs);
                    }
                }(document, "script", "twitter-wjs");

            </script>




        </div>
    </div>

    

    <script>
        //        MAP生成
        var map;
        var service;
        var bounce_act;

        //GoogleMapsAPIのURLパラメータにコールバック関数としてinitMap()を実行
        //Main:位置情報を取得する処理 //getCurrentPosition :or: watchPosition
        function initMap() {
            navigator.geolocation.getCurrentPosition(mapsInit, mapsError, set);
        };

        //1．位置情報の取得に成功した時の処理
        function mapsInit(position) {
            //lat=緯度、lon=経度 を取得
            var lat = position.coords.latitude;
            var lon = position.coords.longitude;

            var pyrmont = new google.maps.LatLng(lat, lon);
            map = new google.maps.Map(document.getElementById('map_canvas'), {
                center: pyrmont,
                zoom: 15
            });
            var request = {
                location: pyrmont,
                radius: '300',
                types: ['restaurant'],
            };

            service = new google.maps.places.PlacesService(map);
            service.nearbySearch(request, callback);

            function callback(results, status) {
                for (var i = 0; i < results.length; i++) {
                    createMarker(results[i]);
                }
            };

            infowindow = new google.maps.InfoWindow();

            function createMarker(place) {
                var marker = new google.maps.Marker({
                    position: place.geometry.location,
                    map: map,
                    animation: google.maps.Animation.DROP,
                    title: place.name,
                    placeId: place.place_id,

                });


                //JSONからのデータ取得と表示を行う
                google.maps.event.addListener(marker, 'click', function() {
                    var request_detail = {
                        reference: place.reference
                    };
                    var bounce_act = true;

                    function toggleBounce() {
                        if (marker.getAnimation() !== null) {
                            marker.setAnimation(null);
                        } else {
                            marker.setAnimation(google.maps.Animation.BOUNCE);
                        }
                    };

                    var service1 = new google.maps.places.PlacesService(map);
                    service1.getDetails(request_detail, function(place, status) {
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            content = place.name + "<br />";
                            content += place.vicinity + "<br />";
                            content += place.formatted_phone_number + "<br />";
                            content += '<a href="' + place.website + '" target=blank>' + place.website + '</a><br />';
                            content += "評価:" + place.rating + "<br />";
                            content += place.types.toString() + "<br />";
                            content += '<a href ="">' + "ブックマーク" + '</a>';
                            infowindow.setContent(content);

                        }
                    });
                    infowindow.open(map, this);

                });
            };
        };

        //        マーカーがバウンドしなくなっているので修正 // データベース作る、ブックマーク機能をつける


        //2． 位置情報の取得に失敗した場合の処理
        function mapsError(error) {
            var e = "";
            if (error.code == 1) { //1＝位置情報取得が許可されてない（ブラウザの設定）
                e = "位置情報が許可されてません";
            }
            if (error.code == 2) { //2＝現在地を特定できない
                e = "現在位置を特定できません";
            }
            if (error.code == 3) { //3＝位置情報を取得する前にタイムアウトになった場合
                e = "位置情報を取得する前にタイムアウトになりました";
            }
            alert("エラー：" + e);
        };

        //3.位置情報取得オプション
        var set = {
            enableHighAccuracy: true, //より高精度な位置を求める
            maximumAge: 20000, //最後の現在地情報取得が20秒以内であればその情報を再利用する設定
            timeout: 10000 //10秒以内に現在地情報を取得できなければ、処理を終了
        };
        
    </script>
    
    <input id="post_info">
    
   
   <div id="store" class="collapse">

 <table class="table">
            <thead>
                <th>名前</th>
                <th>住所</th>
                <th>TEL</th>
                <th>HP</th>
                <th>評価</th>
            </thead>

<!--
            <?php
        foreach($rows as $row){
            ?>
-->
                <tr>
                    <td>
<!--                        <? echo $row['id']; ?>-->a
                    </td>

                    <td>
<!--                        <? echo htmlspecialchars($row['name'],ENT_QUOTES,'UTF-8'); ?>-->a
                    </td>
                    <td>
<!--                        <? echo htmlspecialchars($row['email']);?>-->a
                    </td>
                    <td>
<!--                        <? echo htmlspecialchars($row['naiyou'],ENT_QUOTES,'UTF-8'); ?>-->a
                    </td>
                </tr>  
<!--
                <?}
    ?>
-->
        </table>

    </div>




    <footer>
        <div class="cotainer">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="list-inline">
                        <li><a href="#">Home</a></li>
                        <li class="footer-list-divider">&sdot;</li>
                        <li><a href="#">About</a></li>
                        <li class="footer-list-divider">&sdot;</li>
                        <li><a href="#">Service</a></li>
                        <li class="footer-list-divider">&sdot;</li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                    <p class="copyright text-muted small">Copyright &copy; MeGURU 2017 All Rights Reserved</p>
                </div>
            </div>
        </div>
    </footer>


    <!--
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
-->



</body>


<script>   
    //テーブルのトグル機能
    var speed = 500; //表示アニメのスピード（ミリ秒）;
    
    </script>

</html>
