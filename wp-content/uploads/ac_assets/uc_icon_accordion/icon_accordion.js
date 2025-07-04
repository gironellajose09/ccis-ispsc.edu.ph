
function UCPostAccordion(objAccordion){
  
  var g_activeClass;
  var g_dataLinkNum, g_accItem, g_scrollOffset;
  
  //scroll func
  function scrollToTop(){
    var panel = jQuery(this).closest(".uc_ac_box");
    
    jQuery('html, body').animate({
      scrollTop: panel.offset().top + g_scrollOffset
    }, 400);
  }
  
  function onItemClick(){
    
    var objCaption = jQuery(this);
    var objContent = objCaption.next();
    var objItem = objCaption.parent();
 
    var isActive = objItem.hasClass(g_activeClass);
    var objOpened = objAccordion.find("."+g_activeClass);
    var dataCloseothers = objAccordion.data("closeothers");
    
    if(isActive == true){              
      objItem.removeClass(g_activeClass);
      objContent.slideUp();        	            	
      return(true);
    }
   
    if(dataCloseothers == true){
  
      objOpened.removeClass(g_activeClass);
      objOpened.find(".uc_content").slideUp();
      
    }
    objItem.addClass(g_activeClass);

    var dataScroll = objAccordion.data('scroll');
    
    if (dataScroll == "desktop"){
      if(window.matchMedia("(min-width: 1024px)").matches){
        objContent.slideDown(scrollToTop);
      }else{
        objContent.slideDown();
      }
    }
    
    if (dataScroll == "mobile"){
      if(window.matchMedia("(max-width: 1024px)").matches){
        objContent.slideDown(scrollToTop);
      }else{
        objContent.slideDown();
      }
    }
    
    if (dataScroll == "desktop+mobile"){
      objContent.slideDown(scrollToTop);
    }
    
    if (dataScroll == "off" || dataScroll == undefined){
      objContent.slideDown();
    }
    
    return false;
  }
  
  function linkToSlideScroll(){    
    jQuery('html, body').animate({
      scrollTop: g_accItem.offset().top + g_scrollOffset
    }, 400);
  }
  
  //on trigger link click
  
  function onLinkClick(e){
    
    var objLink = jQuery(this);
    
    var dataAccName = objAccordion.data('name');
    var dataLinkName = objLink.data('name');
    g_dataLinkNum = objLink.data('num');
    g_accItem = objAccordion.find('.uc_ac_box .uc_trigger').eq(g_dataLinkNum - 1);
    var accItemNumber = objAccordion.find('.uc_ac_box').length;
    
    if(dataLinkName == undefined || g_dataLinkNum == '' || g_dataLinkNum > accItemNumber){
      return(false);
    }
    
    var accItemContent = g_accItem.next();
    var accItemParent = g_accItem.parent();
    
    e.preventDefault();
    if(dataLinkName == dataAccName){
      if(accItemParent.hasClass("uc-item-active")){
        linkToSlideScroll();
      }else{
        onItemClick();	      
        accItemContent.slideDown(linkToSlideScroll);
        accItemParent.addClass("uc-item-active");
      }
    }else{
      return(false);
    }
  }
  
  /**
  * init open links
  */
  function initLinks(){
    
    
    var objLinks = jQuery('.ue-link-goto-item').not(".uc-link-inited");
    
    if(objLinks.length == 0){
      return(false);
    }
    
    var elementName = objAccordion.data("name");
    
    jQuery.each(objLinks, function(index, linkElement){
      
      var objLink = jQuery(linkElement);
      
      var name = objLink.data("name");
      
      if(name != elementName)
      return(true);
      
      objLink.addClass("uc-link-inited");
      
      objLink.on("click", onLinkClick);
      
    });
    
    
  }
  
  
  function runPostAccordion(){
    
    //init globals
    
    g_activeClass = "uc-item-active";
    
    g_scrollOffset = objAccordion.data('offset');
    
    initLinks();

    
    //init events
    
    objAccordion.on("click", ".uc_trigger", onItemClick);
    objAccordion.on("ucclick", ".uc_trigger", onItemClick);
  }
  
  runPostAccordion();
  
}