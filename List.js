function getList (id, listType, listWhere) {
  var url = "List.php";

  var innerHTML = document.getElementById(id).innerHTML;
  if ( 0 != innerHTML.length )
    return;

  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    var id = arguments[0];
    return function() {
      switch(xhr.readyState){
        case 4:
          if(xhr.status == 0){
            alert("XHR 通信失敗");
          }else{
          if((200 <= xhr.status && xhr.status < 300) || (xhr.status == 304)){
            document.getElementById(id).innerHTML = xhr.responseText;
          }else{
            alert("その他の応答:" + xhr.status);
          }
        }
        break;
      }
    };
  }(id);

  xhr.open("POST" , url);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
  xhr.send("listType=" + listType + "&listWhere=" + listWhere);
} 

(function() {
  var menu = document.getElementById('tab_menu1');
  var content = document.getElementById('tab_content1');
  var menus = menu.getElementsByTagName('a');
  var current; // 現在の状態を保持する変数
  for (var i = 0, l = menus.length;i < l; i++){
    tab_init(menus[i], i);
  }
  function tab_init(link, index){
    var id = link.hash.slice(1);
    var page = document.getElementById(id);
    if (!current){ // 状態の初期化
      current = {page:page, menu:link};
      page.style.display = 'block';
      link.className = 'active';
    } else {
      page.style.display = 'none';
    }
    link.onclick = function(){
      current.page.style.display = 'none';
      current.menu.className = '';
      page.style.display = 'block';
      link.className = 'active';
      current.page = page;
      current.menu = link;
      return false;
    };
  }
})();
