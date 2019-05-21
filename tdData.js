function displaySwitch(id) {
  divObj = document.getElementsByTagName('xxxxx');
  matchObj= new RegExp('^' + id + ".");

  for(i=0; i < divObj.length; i++) {
    if ( divObj[i].id.match(matchObj) ) {
      if ( divObj[i].style.display == "none" ) {
        divObj[i].style.display="inline";
        document.getElementById(id).innerHTML = "詳細非表示";
      } else {
        divObj[i].style.display="none";
        document.getElementById(id).innerHTML = "詳細表示";
      }
    }
  }
}

