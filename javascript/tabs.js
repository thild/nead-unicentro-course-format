$(document).ready(function() {

  var date = new Date();
  var minutes = 5;
  date.setTime(date.getTime() + (minutes * 60 * 1000));
  var cookieKey = "tabs-nead_unicentro"; //+ config.courseId;

  if (location.hash !== '') {
          $('a[href="' + location.hash + '"]').tab('show');
          window.scrollTo(0, 0);
  }
  else {
          var tab = $.cookie(cookieKey);
          if (tab) {
                  $('[href=' + tab + ']').tab('show');
                  location.hash = tab;
          }
  }
 // add a hash to the URL when the user clicks on a tab
  $('a[data-toggle="tab"]').on('click', function(e) {
    history.pushState(null, null, $(this).attr('href'));
    $.cookie(cookieKey, $(this).attr('href'), { expires: date });
  });

  // Event setup using a convenience method
  $("a[data-tablink='']" ).click(function() {
    window.scrollTo(0, 0);
  });  
  
  // navigate to a tab when the history changes
  window.addEventListener("popstate", function(e) {
    var activeTab = $('[href=' + location.hash + ']');
    $.cookie(cookieKey, location.hash, { expires: date });
    if (activeTab.length) {
      activeTab.tab('show');
    } else {
      $('.nav-tabs a:first').tab('show');
    }
  });
});
 
