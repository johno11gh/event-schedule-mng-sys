$(function(){
  //フッター下部固定
  var $ftr = $('#footer');
  if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
    $ftr.attr({'style': 'position: fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'});
  }

  //メニュートリガー用
  $('.js-toggle-sp-menu').on('click', function(){
    $(this).toggleClass('active');
    $('.js-toggle-sp-menu-target').toggleClass('active');
  });
  $('.js-toggle-menu-link').click('on', function(){
    $('.js-toggle-sp-menu').removeClass('active');
    $('.js-toggle-sp-menu-target').removeClass('active');
  });

  //月ごとスケジュール表スイッチ
  $('.js-schedule-change').click(function(){
    $('.js-schedule-change-target').removeClass('action');
  });
  $('.js-this-month').click(function(){
    $('.js-this-month-target').addClass('action');
  });
  /*for (var i = 1; i < 12; i++) {
    $('.js-monthlater').eq(i).click(function(){
      $('.js-monthlater-target').eq(i).addClass('action');
    });
  }*/
  $('.js-1month-later').click(function(){
    $('.js-1month-later-target').addClass('action');
  });
  $('.js-2month-later').click(function(){
    $('.js-2month-later-target').addClass('action');
  });
  $('.js-3month-later').click(function(){
    $('.js-3month-later-target').addClass('action');
  });
  $('.js-4month-later').click(function(){
    $('.js-4month-later-target').addClass('action');
  });
  $('.js-5month-later').click(function(){
    $('.js-5month-later-target').addClass('action');
  });
  $('.js-6month-later').click(function(){
    $('.js-6month-later-target').addClass('action');
  });
  $('.js-7month-later').click(function(){
    $('.js-7month-later-target').addClass('action');
  });
  $('.js-8month-later').click(function(){
    $('.js-8month-later-target').addClass('action');
  });
  $('.js-9month-later').click(function(){
    $('.js-9month-later-target').addClass('action');
  });
  $('.js-10month-later').click(function(){
    $('.js-10month-later-target').addClass('action');
  });
  $('.js-11month-later').click(function(){
    $('.js-11month-later-target').addClass('action');
  });
  $('.js-12month-later').click(function(){
    $('.js-12month-later-target').addClass('action');
  });


  //シップブロックセレクトボックス 動的生成関数
  $('#ship').change(function(){
    var getShip = document.getElementById('ship');
    var getShipOptions = Number(getShip.options.selectedIndex);
    var getBlock = document.getElementById('block');
    //console.log(getShip.options[getShip.options.selectedIndex]);//this.options[this.options.selectedIndex]でoptionタグと中身月取得できる　
    //例 <option>ship1</option> this.options.selectedIndexだけでoptionタグの中のvalueだけ取得できる 例 3 6 12まで
    console.log(getShipOptions);

    //通常鯖ブロック選択肢生成
    if(getShipOptions >= 1  && getShipOptions <= 10){
      // option要素を削除（方法はいろいろありますが）
       while (0 < getBlock.childNodes.length) {
        getBlock.removeChild(getBlock.childNodes[0]);
       }
       //繰り返し関数によりブロックをつくる
       //PC,PS4鯖
       for (var i = 1; i <= 8; i++) {
         // option要素を生成
         var option = document.createElement('option');
         var text = document.createTextNode('旧PSO2:B-'+i);
         // option要素を追加
         option.appendChild(text);
         option.setAttribute('value', i);
         getBlock.appendChild(option);
       }
       //PSvita鯖
       for (var i = 1; i <= 12; i++) {
       // option要素を生成
        var option = document.createElement('option');
        var text = document.createTextNode(100+i);
        //option.appendChild(text);
        // option要素を追加
        //getBlock.appendChild(option);
      }
      for (var i = 1; i <= 78; i++){
        //option生成
        var option = document.createElement('option');
        var text = document.createTextNode('NGS:B-'+i)
        option.appendChild(text);
        option.setAttribute('value', 200+i);
        getBlock.appendChild(option);
      }
    }
    if(getShipOptions == 11){//チャレンジブロック選択肢生成
      // option要素を削除（方法はいろいろありますが）
       while (0 < getBlock.childNodes.length) {
        getBlock.removeChild(getBlock.childNodes[0]);
       }
       //処理を変更する場合,function.phpのvalidChallengeBlockも同時に修正すること
       //繰り返し関数によりブロックをつくる クラウド供用
       for (var i = 1; i <= 6; i++) {
       // option要素を生成
        var option = document.createElement('option');
        var text = document.createTextNode(600+i);
        option.appendChild(text);
        // option要素を追加
        getBlock.appendChild(option);
      }
       //繰り返し関数によりブロックをつくる　Vita供用
       for (var i = 1; i <= 6; i++) {
       // option要素を生成
        var option = document.createElement('option');
        var text = document.createTextNode(800+i);
        option.appendChild(text);
        // option要素を追加
        getBlock.appendChild(option);
      }
    }
    if(getShipOptions == 12){//バトルブロック選択肢生成
      // option要素を削除（方法はいろいろありますが）
      //処理を変更する場合,function.phpのvalidBattleBlockも同時に修正すること
       while (0 < getBlock.childNodes.length) {
        getBlock.removeChild(getBlock.childNodes[0]);
       }
       //繰り返し関数によりブロックをつくる
       for (var i = 1; i <= 8; i++) {
       // option要素を生成
        var option = document.createElement('option');
        var text = document.createTextNode(900+i);
        option.appendChild(text);
        // option要素を追加
        getBlock.appendChild(option);
      }
    }
  });

  //=========modal===========================
  $('.js-modal-toggle').click(function(){
    var target = $(this).data('target');
    //console.log(target);
    var modal = document.getElementsByClassName(target);
    //console.log(modal);
    $(modal).modal();
    //let index = $(this).parent().;
    //console.log(index);
    //var target = $(this).data('modal-link');

    /*
    // 上記で取得した要素と同じclass名を持つ要素を取得
    let modalTaget = document.getElementsByClassName('.js-modal-target-id' + target);
    //console.log(modalTarget);
    //onsole.log(data-modal-link);
    var modalIndex = $(this).parent().index(); // 何番目のモーダルボタンかを取得
    console.log(modalIndex);

    */
  });


  //=================終わり=========================
});


//=========clock===========================
function set2fig(num) {
   // 桁数が1桁だったら先頭に0を加えて2桁に調整する
   var ret;
   if( num < 10 ) { ret = "0" + num; }
   else { ret = num; }
   return ret;
}
function showClock2() {
   var nowTime = new Date();
   var nowHour = set2fig( nowTime.getHours() );
   var nowMin  = set2fig( nowTime.getMinutes() );
   var nowSec  = set2fig( nowTime.getSeconds() );
   var msg = "現在の時刻:" + nowHour + ":" + nowMin + ":" + nowSec ;
   //document.getElementById("RealtimeClockArea2").innerHTML = msg;
}
//setInterval('showClock2()',1000);
