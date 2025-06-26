function customSelect(customSelect){
  
  var g_select;  

  /*
  * create all elements for custom select
  */
  function setHtml(){

    var i, j, l, ll, selElmnt, a, b, c;
    
    l = g_select.length;
  
    for (i = 0; i < l; i++) {
  
      selElmnt = g_select[i].getElementsByTagName("select")[0];
      ll = selElmnt.length;
      
      /* For each element, create a new DIV that will act as the selected item: */
      a = document.createElement("DIV");
  
      a.setAttribute("class", "select-selected");
  
      a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  
      g_select[i].appendChild(a);
      
      /* For each element, create a new DIV that will contain the option list: */
      b = document.createElement("DIV");
  
      b.setAttribute("class", "select-items select-hide");
      
      for (j = 1; j < ll; j++) {
        
        /* For each option in the original select element, create a new DIV that will act as an option item: */
        c = document.createElement("DIV");
        c.innerHTML = selElmnt.options[j].innerHTML;
        
        c.addEventListener("click", function(e) {  

          var clickedIndex = jQuery(this).index();
          var objOriginSelectItem = jQuery(selElmnt.options[clickedIndex + 1]);
          var originSelectVal = objOriginSelectItem.val();
 
          jQuery(selElmnt).val(originSelectVal).trigger('change');

          onCustomSelectItemClick(this);
          
        });
        
        b.appendChild(c);
  
      }
      
      g_select[i].appendChild(b);
      
      a.addEventListener("click", function(e) {
        onCustomSelectClick(e, this)
      });
  
    }

  }
  
  /*
  * When an item is clicked, update the original select box, and the selected item:
  */
  function onCustomSelectItemClick(selectedElement){
    
    var y, i, k, s, h, sl, yl;

    s = selectedElement.parentNode.parentNode.getElementsByTagName("select")[0];

    sl = s.length;

    h = selectedElement.parentNode.previousSibling;

    for (i = 0; i < sl; i++) {

      if (s.options[i].innerHTML == selectedElement.innerHTML) {

        s.selectedIndex = i;
        
        h.innerHTML = selectedElement.innerHTML;

        y = selectedElement.parentNode.getElementsByClassName("same-as-selected");

        yl = y.length;

        for (k = 0; k < yl; k++) {
          y[k].removeAttribute("class");
        }

        selectedElement.setAttribute("class", "same-as-selected");

        break;
      }
    }

    h.click();
    
  }
  
  /*
  * When the select box is clicked, close any other select boxes, and open/close the current select box:
  */
  function onCustomSelectClick(e, selectedElement){
    
    e.stopPropagation();
    
    selectedElement.nextSibling.classList.toggle("select-hide");

    selectedElement.classList.toggle("select-arrow-active");
    
  }

  /*
  * A function that will close all select boxes in the document, except the current select box:
  */
  function closeAllSelect(elmnt) {
    
    var x, y, i, xl, yl, arrNo = [];

    x = jQuery(g_select).find(".select-items");
    y = jQuery(g_select).find(".select-selected");

    xl = x.length;
    yl = y.length;

    for (i = 0; i < yl; i++) {

      if (elmnt == y[i]) {
        arrNo.push(i)
      } else {
        y[i].classList.remove("select-arrow-active");
      }
    }

    for (i = 0; i < xl; i++) {

      if (arrNo.indexOf(i)) {
        x[i].classList.add("select-hide");
      }

    }
  }
  
  
  function init(){
    
    //init vars
    g_select = document.querySelectorAll(customSelect);

    setHtml();
    
    //init events
    document.addEventListener("click", closeAllSelect);
    
  }
  
  init();
  
}