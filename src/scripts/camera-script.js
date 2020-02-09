var video = document.getElementById('video');
var mediaStream = new MediaStream();

var canvas = document.getElementById('pic-container');
var canvas2 = document.getElementById('pic-container2');

var context = canvas.getContext('2d');
var context2 = canvas2.getContext('2d');

var pokemon = 0;
var file = document.getElementById('inp');

var flag = 0;

file.value = '';

  function ft_cam()
  {
    canvas1_overvideo(0);
    canvas2_overvideo(0);
    if (flag == 1)
    {
      context.translate(800, 0);
      context.scale(-1, 1);
      flag = 0;
    }
    
//Get Camera's Data

    navigator.getMedia =  navigator.getUserMedia ||
                navigator.webkitGetUserMedia ||
                navigator.mozGetUserMedia ||
                navigator.msGetUserMedia;

    navigator.getMedia
    (
      { video: true, audio: false },
      function(mediaStream){  video.srcObject = mediaStream;  video.play(); },
      function(error){alert('Unexpected Error Occured, Try Allowing Camera Usage')}
    );

//Put Camera Data On Canvas1

    document.getElementById('pic-button').addEventListener('click', function()
    {
      context.drawImage(video, 0, 0, 800, 600);
      var picURL = canvas.toDataURL();
      document.getElementById('hiddenURL').value = picURL;
    });
  }

  

//Upload Local File On Canvas2

  file.onchange = function(e) {
      var img = new Image();
      img.onload = draw;
      img.onerror = failed;
      img.src = URL.createObjectURL(this.files[0]);
  };
    function draw() {
      canvas2.width = this.width;
      canvas2.height = this.height;
      context2.drawImage(this, 0, 0);
      canvas2_overvideo(1);
    }
    function failed() {
      alert("Please chose a valid image file");
      file.value = '';
      canvas2_overvideo(0);
  }

//Get Canvas2's URL

  document.getElementById('upload-button').addEventListener('click', function()
  {
    if (file.value != '')
    {
      var picURL2 = canvas2.toDataURL();
      document.getElementById('hiddenURL2').value = picURL2;
    }
    else
      document.getElementById('hiddenURL2').value = "";
  });


  function checks()
  {
  	if ((document.getElementById('check0') && document.getElementById('check0').checked) 
  		|| (document.getElementById('check1') && document.getElementById('check1').checked)
  		|| (document.getElementById('check2') && document.getElementById('check2').checked)
  		|| (document.getElementById('check3') && document.getElementById('check3').checked)
  		|| (document.getElementById('check4') && document.getElementById('check4').checked))
  	{
  		document.getElementById('delete').style.display = 'block';	
  	}
  	else	
  		document.getElementById('delete').style.display = 'none';
  }


  function sub2() 
  {
      $.ajax({
           type: "POST",
           url: 'camera.php',
           data:{action:'ft1'},
           success:function(result) 
           {
              $("#gal").load(location.href+" #gal>*","");
           }
      });
  }

  function sub3() 
  {
      $.ajax({
           type: "POST",
           url: 'camera.php',
           data:{action:'ft2'},
           success:function(result) 
           {
              $("#gal").load(location.href+" #gal>*","");
           }
      });
  }

//Filter Functions

  function ft1()
  {
    video.style.filter = 'contrast(90%) brightness(120%) saturate(110%)';
    context.filter = 'contrast(90%) brightness(120%) saturate(110%)';
    ft_cam();
  }

  function ft2()
  {
    video.style.filter = 'contrast(150%) saturate(110%)';
    context.filter = 'contrast(150%) saturate(110%)';
    ft_cam();
  }

  function ft3()
  {
    video.style.filter = 'contrast(85%) brightness(110%) saturate(75%) sepia(22%)';
    context.filter = 'contrast(85%) brightness(110%) saturate(75%) sepia(22%)';
    ft_cam();
  }

  function ft4()
  {
    video.style.filter = 'contrast(110%) brightness(110%) saturate(130%) invert(15%)';
    context.filter = 'contrast(110%) brightness(110%) saturate(130%) invert(15%)';
    ft_cam();
  }

  function ft5()
  {
    video.style.filter = 'contrast(110%) brightness(110%) sepia(30%) grayscale(100%)';
    context.filter = 'contrast(110%) brightness(110%) sepia(30%) grayscale(100%)';
    ft_cam();
  }

  function canvas1_overvideo(n)
  {
    if (n == 1)
    {
      canvas.style.display = 'block';
      canvas2.style.display = 'none';
      video.style.display = 'none';
      document.getElementById('save-button').style.display = 'block';
      document.getElementById('pic-button').style.display = 'none';
    }
    else
    {
      canvas.style.display = 'none';
      canvas2.style.display = 'none';
      video.style.display = 'block';
      document.getElementById('save-button').style.display = 'none';
      document.getElementById('pic-button').style.display = 'block';
    }
  }

function canvas2_overvideo(n)
  {
    if (n == 1)
    {
      canvas.style.display = 'none';
      canvas2.style.display = 'block';
      video.style.display = 'none';
      document.getElementById('upload-button').disabled = false;
      document.getElementById('upload-button').style.backgroundColor = "#000000";
    }
    else
    {
      canvas.style.display = 'none';
      canvas2.style.display = 'none';
      video.style.display = 'block';
      document.getElementById('upload-button').disabled = true;
      document.getElementById('upload-button').style.backgroundColor = "#555555";
    }
  }

function p(n)
{
  document.getElementById('pok1').style.border = "solid #DDDDDD 0.1vw";
  document.getElementById('pok2').style.border = "solid #DDDDDD 0.1vw";
  document.getElementById('pok3').style.border = "solid #DDDDDD 0.1vw";
  document.getElementById('pok4').style.border = "solid #DDDDDD 0.1vw";
  document.getElementById('pok5').style.border = "solid #DDDDDD 0.1vw";
  document.getElementById('pok6').style.border = "solid #DDDDDD 0.1vw";
  document.getElementById('pok7').style.border = "solid #DDDDDD 0.1vw";
  document.getElementById('pic-button').disabled = false;
  document.getElementById('pic-button').style.backgroundColor = "#000000";
  var pokem = 'ressources/frames/pok'.concat(n);
  var pokemon = pokem.concat('.png');
  var pok = 'pok'.concat(n);
  document.getElementById('hiddenURL2').value = pokemon;
  document.getElementById(pok).style.border = "dashed #FF0000 0.15vw";
}

function draw_png()
{
  png = new Image();
  png.src = 'pictures/final.png';
  png.onload=function()
  {
    context.translate(800, 0);
    context.scale(-1, 1);
    flag = 1;
    context.drawImage(png, 0, 0, 800, 600);
    var picURL3 = canvas.toDataURL('pictures/final.png');
    document.getElementById('hiddenURL3').value = picURL3;
    canvas1_overvideo(1);
  };
}

