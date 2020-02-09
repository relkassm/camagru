// $.ajaxPrefilter(function( options, original_Options, jqXHR ) {
//     options.async = true;
// });

function display(n)
{
  var x = 'com'.concat(n);
  var id = 'id'.concat(n);
  if (document.getElementById(x).style.display == 'inline-block')
  {
    document.getElementById(x).style.display = 'none';
    document.getElementById(id).src = 'ressources/comment-off.png'
  }
  else
  {
    document.getElementById(x).style.display = 'inline-block';
    document.getElementById(id).src = 'ressources/comment-on.png'
  }
}

function myAjax(n) 
{
    $.ajax({
         type: "POST",
         url: 'homepage.php',
         data:{action:'fLike'.concat(n)},
         success:function(data) 
         {
          $("#xd".concat(n)).load(location.href+" #xd".concat(n).concat(">*",""));
         }
    });
}

function dspComments(n)
{
  var x = 'cmt'.concat(n);
  if (document.getElementById(x).style.display == 'inline-block')
  {
    document.getElementById(x).style.display = 'none';
  }
  else
  {
    document.getElementById(x).style.display = 'inline-block';
  }
}

$(document).ready(function() {

  $(".form-comment").submit(function(e) {

      e.preventDefault();

      var form = $(this);
      var id = form.attr('id');

      $.ajax(
      {
       type: "POST",
       url: 'homepage.php',
       data: form.serialize(),
       success: function(data)
       {
          $("#xd".concat(id)).load(location.href+" #xd".concat(id).concat(">*",""));
       }
      });
  });
});

  function display_nav(id)
  {
    document.getElementById(id).style.display = 'inline-block';
  }