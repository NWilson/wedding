(function($){ $(document).ready(function(){

$('a.needspassword').each(function(idx,elt){
  $(elt).click(function(evt){
    var cook = $.cookie('password');
    if (cook && cook+'' != '')
      return;

    evt.preventDefault();
    $('#passwordprompt').remove();
    $('body').append(
'<div id="passwordprompt"><div>'+
'<p>Please enter the password printed on your invitation:</p><form action="'+$(this).attr('href')+'" method="POST">'+
'<p><input type="text" name="password"></input></p>'+
'<p><button type="submit">Done</button></p>'+
'</form></div></div>'
    );
  });
});

});})(jQuery);
