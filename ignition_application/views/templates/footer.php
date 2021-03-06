    <hr>

    <footer class="footer">
      <div class="row">
        <div class="col-sm-4">
          <p>Created by <a href="http://www.clidus.com/" target="_blank">Joshua Marketis</a>.</p> 
          <p>Logo design by <a href="http://www.michaelorson.com/" target="_blank">Michael Orson</a>.</p> 
        </div>
        <div class="col-sm-5">
          <p>Video game data provided by the top men at <a href="http://www.giantbomb.com" target="_blank">Giant Bomb</a>.</p>
          <p>Built on <a href="http://www.codeigniter.com/" target="_blank">CodeIgniter</a> and <a href="http://www.ignitionpowered.co.uk/" target="_blank">Ignition</a>.</p>
        </div>
        <div class="col-sm-3 socialIcons">
          <a href="https://www.facebook.com/gamingwithlemons" target="_blank"><img src="/images/social/facebook.jpeg" /></a>
          <a href="https://twitter.com/gaminglemons" target="_blank"><img src="/images/social/twitter.jpeg" /></a>
          <a href="https://plus.google.com/+Gamingwithlemons" target="_blank"><img src="/images/social/google.jpeg" /></a>
          <a href="https://github.com/Clidus/gwl" target="_blank"><img src="/images/social/github.jpeg" /></a>
        </div>
      </div>
    </footer>
  </div>

  <!-- Javascript-->
  <script>
    <?php 
      if($pagetemplate == "Collection") {
        echo "var UserID = " . $user->UserID . ";";
      } 
    ?>
  </script>
  <script src="/js/jquery-2.0.3.min.js"></script>
  <script src="/bootstrap/js/bootstrap.min.js"></script>
  <script src="/js/global.js?v=2"></script>
  <?php 
    if($pagetemplate == "Search" || $pagetemplate == "Game") 
    { 
      echo "<script src='/js/game.js?v=2'></script>"; 
    }
    else if($pagetemplate == "Admin") 
    { 
      echo "<script src='/js/admin.js?v=2'></script>"; 
    } 
    else if($pagetemplate == "Collection") 
    { 
      echo "<script src='/js/collection.js?v=2'></script>"; 
    } 
    if($pagetemplate == "User" || $pagetemplate == "Collection") 
    { 
      echo "<script src='/js/user.js?v=2'></script>"; 
    } 
    if($pagetemplate == "User" || $pagetemplate == "Game" || $pagetemplate == "BlogPost" || $pagetemplate == "UserHome")
    {
      echo "<script src='/js/jquery.autogrow-textarea.js'></script>"; 
      echo "<script src='/js/comments.js?v=3'></script>"; 
    }
  ?>
  <script>
    $(function() {
      <?php
          switch($pagetemplate)
          {
            case "Game":
            case "BlogPost":
            case "UserHome":
              echo "$('.textAreaAutoGrow').autogrow();";
              break;
            case "User":
              echo "$('.textAreaAutoGrow').autogrow();";
              echo "$('#navFeed').addClass('active');";
              break;
            case "Collection":
              echo "loadCollection();";
              echo "$('#navCollection').addClass('active');";
              break;
            case "Settings":
              echo "$('#dateFormat').val('" . $user->DateTimeFormat . "');";
              echo "$('#navSettings').addClass('active');";
              break;
            case "ImageUpload":
            case "Password":
              echo "$('#navSettings').addClass('active');";
              break;
          }
      ?>
    });
    
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-19451189-7', 'gamingwithlemons.com');
    ga('send', 'pageview');
  </script>
</body>
</html>
